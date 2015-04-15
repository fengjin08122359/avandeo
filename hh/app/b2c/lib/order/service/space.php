<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 *
 *
 * @package default
 * @author kxgsy163@163.com
 */
class b2c_order_service_space implements b2c_order_service_interface
{

    function __construct(&$app)
    {
        $this->app = $app;
        $this->oGoods = kernel::single("b2c_order_service_goods");
    }

    public function get_goods_type()
    {
        return 'space';
    }

    /*
     * return sdf order
     */
    function gen_order( $arrObjInfo=array(), &$order_data, &$msg='' ){
        $store_mark = app::get('b2c')->getConf('system.goods.freez.time');
        $order_type = $this->get_goods_type();
        $o = kernel::single('ectools_math');
        if( !$arrObjInfo || !is_array($arrObjInfo) ) return false;
        $is_freez = true;
        foreach( $arrObjInfo as $row ) {
            $arrParams = array(
                'goods_id' => $row['space_id'],
                'quantity' => $row['quantity'],
                'info' => array(),
            );//用于冻结库存


            $tmp = array();
            $package = $row['space'];
            $package_buy_price = $row['price']*$row['discount'];
            $tmp = array(
                'order_id' => $order_data['order_id'],
                'obj_type' => $order_type,
                'obj_alias' => '空间组合区块',
                'goods_id' => $row['space_id'],
                'bn' => '',
                'name' => $row['space_name'],
                'price' => $package_buy_price,
                'quantity' => $row['quantity'],
                'amount' => $package_buy_price*$row['quantity'],
                'weight' => $row['subtotal_weight'],
                'score' => $row['subtotal_gain_score'],
            );
            $order_items = array();
            foreach ($row['obj_items']['products'] as $key => $obj_items) {
                $temp_item_price = $o->number_multiple(array($obj_items['price']['buy_price'],$row['discount']));
                $order_items[] = array(
                    'products' => array('product_id'=>$obj_items['product_id']),
                    'goods_id' => $obj_items['goods_id'],
                    'order_id' => $order_data['order_id'],
                    'item_type' => 'product',
                    'bn' => $obj_items['bn'],
                    'name' => $obj_items['name'],
                    'type_id' => $obj_items['type_id'],
                    'cost' => $obj_items['price']['cost'],
                    'quantity' => $o->number_multiple(array($row['quantity'],$obj_items['base_num'])),
                    'sendnum' => 0,
                    'amount' => $o->number_multiple( array($temp_item_price,$row['quantity'],$obj_items['base_num']) ),
                    'score' => 0,
                    'price' => $temp_item_price,
                    'g_price'=>$obj_items['price']['buy_price'],
                    'weight'=>$o->number_multiple(array($obj_items['weight'],$obj_items['base_num'])),
                    'addon' => '',
                );
                $arrParams['info'][] = array(
                    'goods_id' => $obj_items['goods_id'],
                    'product_id'=>$obj_items['product_id'],
                    'base_num'=>$obj_items['base_num']*$row['quantity'],
                );
            }
            $tmp['order_items'] = $order_items;
            $tmp['bn'] = $order_items[0]['bn'];
            $order_data['order_objects'][] = $tmp;
            if ($store_mark == '1')
            {
                $is_freez = $this->freezeGoods( $arrParams );
            }elseif($store_mark == '2' && $order_data['payinfo']['pay_app_id'] == '-1'){

                $is_freez = $this->freezeGoods( $arrParams );
            }
            if (!$is_freez)
            {
                $msg = app::get('b2c')->_('空间组合商品库存不足！');
                return false;
            }

        }
        return true;
    }
    #End Func


    public function freezeGoods($arrParams=array())
    {
        if (!$arrParams) return false;
        $is_freeze_b2c = $this->doFunction( 'freezeGoods',$arrParams );
        return $is_freeze_b2c;
    }


    /**
     * @params $order_objects array() 订单对象数据结构
     **/
    public function unfreezeGoods($order_objects=array())
    {
        if (!$order_objects) return false;
        $arrParams['id'] = $order_objects['goods_id'];
        $arrParams['quantity'] = $order_objects['quantity'];
        $arrParams['info'] = array();

        if( !is_array($order_objects['order_items']) ) return false;
        foreach( $order_objects['order_items'] as $row ) {
            $arrParams['info'][] = array(
                'goods_id' => $row['goods_id'],
                'product_id' => $row['products']['product_id'],
                'base_num'=>$row['num'],
            );
        }

        $is_unfreeze = $this->doFunction( 'unfreezeGoods',$arrParams );
        return $is_unfreeze;
    }

    //////////////////////////////////////////////////////////////////////////
    // 修改数量
    ///////////////////////////////////////////////////////////////////////////
    public function minus_store($order_objects=array())
    {
        if (!$order_objects) return false;
        $arrParams['id'] = $order_objects['goods_id'];
        $arrParams['quantity'] = $order_objects['quantity'];
        $arrParams['info'] = array();

        if( !is_array($order_objects['order_items']) ) return false;
        foreach( $order_objects['order_items'] as $row ) {
            $arrParams['info'][] = array(
                'goods_id' => $row['goods_id'],
                'product_id' => $row['products']['product_id'],
                'base_num'=>$row['num'],
            );
        }
        $this->doFunction( 'minus_store',$arrParams );

    }

    public function recover_store($order_objects=array())
    {
        if (!$order_objects) return false;
        $arrParams['id'] = $order_objects['goods_id'];
        $arrParams['quantity'] = $order_objects['quantity'];
        $arrParams['info'] = array();

        if( !is_array($order_objects['order_items']) ) return false;
        foreach( $order_objects['order_items'] as $row ) {
            $arrParams['info'][] = array(
                'goods_id' => $row['goods_id'],
                'product_id' => $row['products']['product_id'],
                'base_num'=>$row['num'],
            );
        }
        return $this->doFunction( 'recover_store',$arrParams );
    }

    public function get_order_object($arr_object=array(), &$order_items, $tml='member_order_detail')
    {
        $order_items = array();
        $objMath = kernel::single("ectools_math");
        //获取空间组合图片
        $space_info = app::get('b2c')->model('space')->getList('image_default_id',array('space_id'=>$arr_object['goods_id']),0,1);
        $arr_object['thumb'] = $space_info[0]['image_default_id'];
        #echo "<pre>";var_export($arr_object);exit;
        $cost_item = 0;
        foreach($arr_object['order_items'] as $k => $item)
        {
            if($item['addon'] && unserialize($item['addon'])){
                $gItems[$k]['minfo'] = unserialize($item['addon']);
            }else{
                $gItems[$k]['minfo'] = array();
            }
            if ($item['item_type'] == 'product')
            {
                kernel::single("b2c_order_service_goods")->get_order_object(array('goods_id' => $item['goods_id'],'product_id'=>$item['products']['product_id']), $arrGoods);
                #$order_items[$k] = $item;
                $order_items[$k]['obj_id'] = $item['obj_id'];
                $order_items[$k]['obj_title'] = '空间组合('.$arr_object['name'].')包含的商品';
                $order_items[$k]['goods_id'] = $item['goods_id'];
                $order_items[$k]['product_id'] = $item['products']['product_id'];
                $order_items[$k]['bn'] = $item['bn'];
                $order_items[$k]['category']['cat_name'] = $arr_object['obj_type'];
                $order_items[$k]['price'] = $item['price'];
                $order_items[$k]['quantity'] = $item['quantity'];
                $order_items[$k]['sendnum'] = $item['sendnum'];
                $order_items[$k]['small_pic'] = $arrGoods['image_default_id'];
                $order_items[$k]['nums'] = $item['quantity']/$arr_object['quantity'];
                $order_items[$k]['obj_type'] = $this->get_goods_type();
                $order_items[$k]['total_amount'] = $objMath->number_multiple(array($item['price'], $item['quantity']));
                $order_items[$k]['link'] = $arrGoods['link_url'];
                $order_items[$k]['item_id'] = $item['item_id'];
                $cost_item = $objMath->number_plus(array($cost_item,$order_items[$k]['total_amount']));
                if (isset($item['products']['spec_info']) && $item['products']['spec_info'])
                {
                    $order_items[$k]['name'] = $item['products']['name'] . '(' . $item['products']['spec_info'] . ')';
                }
                else
                {
                    $order_items[$k]['name'] = $item['products']['name'];
                }
                // 判断是否有goods_type
                if (isset($arrGoods['type']['floatstore']) && $arrGoods['type']['floatstore'])
                {
                    $order_items[$k]['floatstore'] = 1;
                }
                else
                {
                    $order_items[$k]['floatstore'] = 0;
                }
            }

        }
        $render = $this->app->render();

        $arr_object['total_amount'] = $objMath->number_multiple(array($arr_object['price'], $arr_object['quantity']));
        $arr_object['order_items'] = $order_items;
        $arr_object['total_cost_item'] = $cost_item;
        $render->pagedata['space_order'] = $arr_object;

        //默认图片
        $imageDefault = app::get('image')->getConf('image.set');
        $render->pagedata['default_image'] = $imageDefault['S']['default_image'];
        $render->pagedata['res_url'] = app::get('b2c')->res_url;
        if( strpos($tml,'admin')!==false ) {
            $tpl = 'admin/order/space/'.$tml.'.html';
        } else {
            $tpl = 'site/member/space/'.$tml.'.html';
        }

        return $render->fetch( $tpl );
    }

    public function get_default_dly_order_info($val_list=array(), &$data)
    {
        $objMath = kernel::single("ectools_math");
        $order_item = app::get('b2c')->model('order_items');
        foreach($val_list['order_items'] as $k => $item)
        {
            if ($item['item_type'] == 'product')
            {
                if (!$item['products'])
                {
                    $tmp = $order_item->getList('*', array('item_id'=>$item['item_id']));
                    $item['products']['bn'] = $tmp[0]['bn'];
                    $item['products']['spec_info'] = $tmp[0]['bn'];
                }

                //订单商品名称
                $data['order_name'] .= app::get('b2c')->_('名称：%s',$item['name']);
                //订单商品名称+数量
                $data['order_name_a'] .= app::get('b2c')->_('名称：%s&nbsp;&nbsp;数量：%s',$item['name'],$item['quantity'])."\n";
                //订单商品名称+规格+数量
                $data['order_name_as'].= app::get('b2c')->_('名称：%s&nbsp;&nbsp;规格：%s&nbsp;&nbsp;数量：%s',$item['name'],$item['products']['spec_info'],$item['quantity'])."\n";
                //订单商品名称+货号+数量
                $data['order_name_ab'].= app::get('b2c')->_("名称：%s&nbsp;&nbsp;货号：%s&nbsp;&nbsp;数量：%s",$item['name'],$item['products']['bn'],$item['quantity'])."\n";
            }
        }
    }

    /*
     * 处理 延伸到b2c商品
     */
    private function doFunction( $func,$arr )
    {
        if( is_array($arr['info']) ) {
            foreach( $arr['info'] as $row ) {
                $arrParams = array(
                    'goods_id'=>$row['goods_id'],
                    'product_id'=>$row['product_id'],
                    'quantity' => $row['base_num'],
                );
                //b2c库存处理
                if( method_exists($this->oGoods,$func) ){
                    if(!$this->oGoods->$func( $arrParams )){
                        return false;
                    }
                }
            }
        }
        return true;
    }
    #End Func

    /*
     * @$nonGoods_extends['delivery_finish']  全部发完
     * @$nonGoods_extends['delivery_start']   未发过
     * @$nonGoods_extends['delivery_process'] 发过但未发完
     */
    public function store_change( $sdf,$type,$nonGoods_extends ) {
        if( $sdf['goods_id'] ) {

            $arr = kernel::single('b2c_order_checkorder')->checkOrderFreez( $type,$sdf['order_id'] );
            if( $nonGoods_extends['delivery_finish'] ) {
                foreach( $arr as $key => $val ) {
                    if( !$val ) continue;
                    switch($key) {
                        case "freez":
                            $this->freezeGoods( $sdf );break;
                        case "unfreez":
                            $this->unfreezeGoods( $sdf );break;
                        case "store":
                            $this->minus_store( $sdf );break;
                        case "unstore":
                            #$this->recover_store( $sdf );break;
                    }
                }
            }
        }
    }

    public function check_freez($arrParams)
    {
        if (!$arrParams) return false;
        $is_freeze = false;
        $o = $this->app->model('giftpackage');
        if ( isset($arrParams['goods_id']) && $arrParams['goods_id'] )
            $is_freeze = $o->check_freez($arrParams['goods_id'], $arrParams['quantity']);

        $is_freeze_b2c = $this->doFunction( 'check_freez',$arrParams );
        return $is_freeze&&$is_freeze_b2c;
    }

    public function get_aftersales_order_info($arr_data)
    {
        $order_items = array();
        if (!$arr_data)
            return $order_items;

        $objMath = kernel::single("ectools_math");
        $_obj_aftersales_rtn_p = app::get('aftersales')->model('return_product');
        foreach($arr_data['order_items'] as $k => $item)
        {
            if($item['addon'] && unserialize($item['addon'])){
                $gItems[$k]['minfo'] = unserialize($item['addon']);
            }else{
                $gItems[$k]['minfo'] = array();
            }

            if ($item['item_type'] == 'product')
            {
                kernel::single("b2c_order_service_goods")->get_order_object(array('goods_id' => $item['goods_id'],'product_id'=>$item['products']['product_id']), $arrGoods);
                if (!$order_items[$item['products']['product_id']]){
                    $order_items[$item['products']['product_id']] = $item;
                    $order_items[$item['products']['product_id']]['obj_title'] = '礼包('.$arr_object['name'].')包含的商品';
                    $order_items[$item['products']['product_id']]['goods_id'] = $item['goods_id'];
                    $order_items[$item['products']['product_id']]['product_id'] = $item['products']['product_id'];
                    $order_items[$item['products']['product_id']]['bn'] = $item['bn'];
                    $order_items[$item['products']['product_id']]['category']['cat_name'] = $arr_object['obj_type'];
                    $order_items[$item['products']['product_id']]['price'] = $item['price'];
                    $order_items[$item['products']['product_id']]['quantity'] = $item['quantity'];
                    $order_items[$item['products']['product_id']]['sendnum'] = $item['sendnum'];
                    $order_items[$item['products']['product_id']]['small_pic'] = $arrGoods['image_default_id'];
                    $order_items[$item['products']['product_id']]['nums'] = $item['quantity'];
                    $order_items[$item['products']['product_id']]['obj_type'] = $this->get_goods_type();
                    $order_items[$item['products']['product_id']]['total_amount'] = $objMath->number_multiple(array($item['price'], $item['quantity']));
                    $order_items[$item['products']['product_id']]['link'] = $arrGoods['link_url'];
                    $order_items[$item['products']['product_id']]['item_id'] = $item['item_id'];

                    if ($item['addon'])
                    {
                        $arrAddon = $arr_addon = unserialize($item['addon']);
                        if ($arr_addon['product_attr'])
                            unset($arr_addon['product_attr']);
                        $order_items[$item['products']['product_id']]['product']['minfo'] = $arr_addon;
                    }

                    if ($arrAddon['product_attr'])
                    {
                        foreach ($arrAddon['product_attr'] as $arr_product_attr)
                        {
                            $order_items[$item['products']['product_id']]['product']['attr'] .= $arr_product_attr['label'] . app::get('b2c')->_(":") . $arr_product_attr['value'] . app::get('b2c')->_(" ");
                        }
                    }

                    if ($order_items[$item['products']['product_id']]['product']['attr'])
                        $order_items[$item['products']['product_id']]['name'] = $item['name'] . '(' . $order_items[$item['products']['product_id']]['product']['attr'] . ')';
                    else
                        $order_items[$item['products']['product_id']]['name'] = $item['name'];
                }else{
                    $order_items[$item['products']['product_id']]['sendnum'] = floatval($objMath->number_plus(array($order_items[$item['products']['product_id']]['sendnum'],$item['sendnum'])));
                    $order_items[$item['products']['product_id']]['nums'] = floatval($item['quantity']);
                    $order_items[$item['products']['product_id']]['quantity'] = floatval($objMath->number_plus(array($order_items[$item['products']['product_id']]['quantity'],$item['quantity'])));
                }

                ### 处理已经售后的商品 ###
                $_arr_p = $_obj_aftersales_rtn_p->getList('product_data',array('order_id'=>$arr_data['order_id'],'status'=>'4'));
                if ($_arr_p){
                    $_arr_p = unserialize($_arr_p[0]['product_data']);
                    foreach($_arr_p as $_p){
                        if ($_p['bn']==$order_items[$item['products']['product_id']]['products']['bn']){
                            $order_items[$item['products']['product_id']]['quantity'] = floatval($objMath->number_minus(array($order_items[$item['products']['product_id']]['quantity'],$_p['num'])));
                            if ($order_items[$item['products']['product_id']]['quantity']<=0){
                                unset($order_items[$item['products']['product_id']]);
                                continue;
                            }
                        }
                    }
                }
                ### end ###
            }
        }

        return $order_items;
    }

    public function is_decomposition($goods_type)
    {
        return true;
    }

    public function is_item_edit($item_type)
    {
        return true;
    }

    public function cal_pmt_order($order_id,$obj_id){
        $mdl_order_objects = app::get('b2c')->model('order_objects');
        $mdl_order_items = app::get('b2c')->model('order_items');
        $oMath = kernel::single('ectools_math');
        $objRow = $mdl_order_objects->getRow('price,amount',array('order_id'=>$order_id,'obj_id'=>$obj_id));
        $itemList = $mdl_order_items->getList('amount',array('order_id'=>$order_id,'obj_id'=>$obj_id,'item_product'=>'product'));
        $itemAmount = 0;
        foreach((array)$itemList as $v){
            $itemAmount = $oMath->number_plus(array($itemAmount,$v['amount']));
        }
        $pmt_order = $oMath->number_minus(array($itemAmount,$objRow['amount']));
        return $pmt_order>0?$pmt_order:0;
    }
}
