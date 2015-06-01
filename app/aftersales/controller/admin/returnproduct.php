<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class aftersales_ctl_admin_returnproduct extends desktop_controller{
    // public $workground = 'ectools_ctl_admin_order';

    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
        $this->arr_status = array(
            '1' => app::get('aftersales')->_('申请中'),
            '2' => app::get('aftersales')->_('审核中'),
            '3' => app::get('aftersales')->_('接受申请'),
            '4' => app::get('aftersales')->_('完成'),
            '5' => app::get('aftersales')->_('拒绝'),
        );
    }

    public function index()
    {
        $this->workground = 'ectools.wrokground.order';
        $custom_actions[] = array('label'=>app::get('b2c')->_('添加售后单'),'href'=>'index.php?app=aftersales&ctl=admin_returnproduct&act=add','target'=>'dialog::{title:\''.app::get('b2c')->_('添加售后单').'\',width:750,height:460}');

        $actions_base['actions'] = $custom_actions;


        if($_GET['action'] == 'export') $this->_end_message = '导出售后服务申请';
        $this->finder('aftersales_mdl_return_product',array(
            'title'=>app::get('aftersales')->_('售后服务管理'),
            'actions'=>$custom_actions,
            'use_buildin_set_tag'=>true,
            'use_buildin_recycle'=>false,
            'use_buildin_filter'=>true,
            'use_view_tab'=>true,
            ));
    }

    /**
     * 桌面订单相信汇总显示
     * @param null
     * @return null
     */
    public function _views(){
        $mdl_aftersales = $this->app->model('return_product');
        $sub_menu = array(
            1=>array('label'=>app::get('aftersales')->_('审核中'),'optional'=>false,'filter'=>array('status'=>2,'disabled'=>'false')),
            2=>array('label'=>app::get('aftersales')->_('接受申请'),'optional'=>false,'filter'=>array('status'=>3,'disabled'=>'false')),
            3=>array('label'=>app::get('aftersales')->_('完成'),'optional'=>false,'filter'=>array('status'=>4,'disabled'=>'false')),
            4=>array('label'=>app::get('aftersales')->_('拒绝'),'optional'=>false,'filter'=>array('status'=>5,'disabled'=>'false')),
            5=>array('label'=>app::get('aftersales')->_('全部'),'optional'=>false,'filter'=>array('disabled'=>'false')),
        );

        if(isset($_GET['optional_view'])) $sub_menu[$_GET['optional_view']]['optional'] = false;

        foreach($sub_menu as $k=>$v){
            if($v['optional']==false){
                $show_menu[$k] = $v;
                if(is_array($v['filter'])){
                    $v['filter'] = array_merge(array(),$v['filter']);
                }else{
                    $v['filter'] = array();
                }
                $show_menu[$k]['filter'] = $v['filter']?$v['filter']:null;
                if($k==$_GET['view']){
                    $show_menu[$k]['newcount'] = true;
                    $show_menu[$k]['addon'] = $mdl_aftersales->count($v['filter']);
                }
                $show_menu[$k]['href'] = 'index.php?app=aftersales&ctl=admin_returnproduct&act=index&view='.($k).(isset($_GET['optional_view'])?'&optional_view='.$_GET['optional_view'].'&view_from=dashboard':'');
            }elseif(($_GET['view_from']=='dashboard')&&$k==$_GET['view']){
                $show_menu[$k] = $v;
            }
        }

        return $show_menu;
    }

    public function save()
    {
        $rp = &$this->app->model('return_product');
        $obj_return_policy = kernel::single('aftersales_data_return_policy');

        $return_id = $_POST['return_id'];
        $status = $_POST['status'];
        $sdf = array(
            'return_id' => $return_id,
            'status' => $status,
        );
        $this->pagedata['return_status'] = $obj_return_policy->change_status($sdf);
        if ($this->pagedata['return_status'])
            $this->pagedata['return_status'] = $this->arr_status[$this->pagedata['return_status']];

        $obj_aftersales = kernel::servicelist("api.aftersales.request");
        foreach ($obj_aftersales as $obj_request)
        {
            $obj_request->send_update_request($sdf);
        }

        $this->display('admin/return_product/return_status.html');
    }

    public function add()
    {
        $this->begin('index.php?app=aftersales&ctl=admin_returnproduct&act=index');
        $order_id = $_POST['order_id'];
        if (empty($order_id)) {
            $this->display('admin/return_product/add.html');
            return;
        }
        $obj_return_policy = kernel::service("aftersales.return_policy");
        $arr_settings = array();

        if (!isset($obj_return_policy) || !is_object($obj_return_policy))
        {
            exit('售后服务应用不存在！');

        }

        if (!$obj_return_policy->get_conf_data($arr_settings))
        {
            exit('售后服务信息没有取到！');
        }

        $objOrder = app::get('b2c')->model('orders');
        $subsdf = array('order_objects'=>array('*',array('order_items'=>array('*',array(':products'=>'*')))));
        $this->pagedata['order'] = $objOrder->dump($order_id, '*', $subsdf);
        if ($this->pagedata['order']['pay_status'] == 0) {
            exit('订单未付款，不能申请售后');
        }
        if ($this->pagedata['order']['ship_status'] == 0) {
            exit('订单未发货，不能申请售后');
        }        
        // echo '<pre>';
        // print_r($this->pagedata['order']);exit;

        $this->pagedata['orderlogs'] = $objOrder->getOrderLogList($order_id);

        if(!$this->pagedata['order'])
        {
            $this->end(false,  app::get('b2c')->_('订单无效！'), array('app'=>'site','ctl'=>'default','act'=>'index'),false,true);
        }

        $order_items = array();
        // 所有的goods type 处理的服务的初始化.
        $arr_service_goods_type_obj = array();
        $arr_service_goods_type = kernel::servicelist('order_goodstype_operation');
        foreach ($arr_service_goods_type as $obj_service_goods_type)
        {
            $goods_types = $obj_service_goods_type->get_goods_type();
            $arr_service_goods_type_obj[$goods_types] = $obj_service_goods_type;
        }

        $objMath = kernel::single("ectools_math");
        $oImage = app::get('image')->model('image');
        $oGoods = app::get('b2c')->model('goods');
        $imageDefault = app::get('image')->getConf('image.set');
        $_obj_aftersales_rtn_p = app::get('aftersales')->model('return_product');
        foreach ($this->pagedata['order']['order_objects'] as $k=>$arrOdr_object)
        {
            $index = 0;
            $index_adj = 0;
            $index_gift = 0;
            $tmp_array = array();
            if($arrOdr_object['obj_type'] == 'timedbuy'){
                $arrOdr_object['obj_type'] = 'goods';
            }
            if ($arrOdr_object['obj_type'] == 'goods')
            {
                foreach($arrOdr_object['order_items'] as $key => $item)
                {
                    if ($item['item_type'] == 'product')
                        $item['item_type'] = 'goods';
                    if ($tmp_array = $arr_service_goods_type_obj[$item['item_type']]->get_aftersales_order_info($item)){
                        $tmp_array = (array)$tmp_array;
                        if (!$tmp_array) continue;

                        $product_id = $tmp_array['products']['product_id'];
                        if (!$order_items[$product_id]){
                            $tmp_array['arrNum'] = $this->intArray($tmp_array['quantity']);
                            $order_items[$product_id] = $tmp_array;
                        }else{
                            $order_items[$product_id]['sendnum'] = floatval($objMath->number_plus(array($order_items[$product_id]['sendnum'],$tmp_array['sendnum'])));
                            $order_items[$product_id]['quantity'] = floatval($objMath->number_plus(array($order_items[$product_id]['quantity'],$tmp_array['quantity'])));
                            $order_items[$product_id]['arrNum'] = $this->intArray($order_items[$product_id]['quantity']);
                        }
                        
                        // 货品图片
                        $spec_desc_goods = $oGoods->getList('spec_desc,image_default_id',array('goods_id'=>$item['goods_id']));
                        if($item['products']['spec_desc']['spec_private_value_id']){
                            $select_spec_private_value_id = reset($item['products']['spec_desc']['spec_private_value_id']);
                            $spec_desc_goods = reset($spec_desc_goods[0]['spec_desc']);
                        }
                        if($spec_desc_goods[$select_spec_private_value_id]['spec_goods_images']){
                            list($default_product_image) = explode(',', $spec_desc_goods[$select_spec_private_value_id]['spec_goods_images']);
                            $order_items[$product_id]['thumbnail_pic'] = $default_product_image;
                        }elseif($spec_desc_goods[0]['image_default_id']){
                            if( !$order_items[$product_id]['thumbnail_pic'] && !$oImage->getList("image_id",array('image_id'=>$spec_desc_goods[0]['image_default_id']))){
                                $order_items[$product_id]['thumbnail_pic'] = $imageDefault['S']['default_image'];
                            }else{
                                $order_items[$product_id]['thumbnail_pic'] = $spec_desc_goods[0]['image_default_id'];
                            }
                        }
                        //$order_items[$item['products']['product_id']] = $tmp_array;
                        
                        ### 处理已经售后的商品 ###
                        $_arr_p = $_obj_aftersales_rtn_p->getList('product_data',array('order_id'=>$order_id,'status'=>'4'));
                        if ($_arr_p){
                            $_arr_p = unserialize($_arr_p[0]['product_data']);
                            foreach($_arr_p as $_p){
                                if ($_p['bn']==$order_items[$product_id]['products']['bn']){
                                    $order_items[$product_id]['quantity'] = floatval($objMath->number_minus(array($order_items[$product_id]['quantity'],$_p['num'])));
                                    if ($order_items[$product_id]['quantity']<=0){
                                        unset($order_items[$product_id]);                                       
                                        continue;
                                    }
                                    
                                    $order_items[$product_id]['arrNum'] = $this->intArray($order_items[$product_id]['quantity']);
                                }
                            }
                        }
                        ### end ###
                    }
                }
            }
            else
            {
                if ($tmp_array = $arr_service_goods_type_obj[$arrOdr_object['obj_type']]->get_aftersales_order_info($arrOdr_object))
                {
                    $tmp_array = (array)$tmp_array;
                    if (!$tmp_array) continue;
                    foreach ($tmp_array as $tmp){
                        if (!$order_items[$tmp['product_id']]){
                            $tmp['arrNum'] = $this->intArray($tmp['quantity']);
                            $order_items[$tmp['product_id']] = $tmp;
                        }else{
                            $order_items[$tmp['product_id']]['sendnum'] = floatval($objMath->number_plus(array($order_items[$tmp['product_id']]['sendnum'],$tmp['sendnum'])));
                            $order_items[$tmp['product_id']]['nums'] = floatval($objMath->number_plus(array($order_items[$tmp['product_id']]['nums'],$tmp['nums'])));
                            $order_items[$tmp['product_id']]['quantity'] = floatval($objMath->number_plus(array($order_items[$tmp['product_id']]['quantity'],$tmp['quantity'])));
                            $order_items[$tmp['product_id']]['arrNum'] = $this->intArray($order_items[$tmp['product_id']]['quantity']);
                        }
                    }
                }
                //$order_items = array_merge($order_items, $tmp_array);
            }
        }
        $this->pagedata['order_id'] = $order_id;
        $this->pagedata['order']['items'] = $order_items;
        $this->pagedata['controller'] = 'afterlist';
        // echo "<pre>";print_r($this->pagedata);exit;
        $this->display('admin/return_product/add.html');
    } 

    public function return_save()
    {
        $this->begin('index.php?app=aftersales&ctl=admin_returnproduct&act=index');

        $obj_return_policy = kernel::service("aftersales.return_policy");
        $arr_settings = array();

        if (!isset($obj_return_policy) || !is_object($obj_return_policy))
        {
            $this->end(false,app::get('b2c')->_('售后服务应用不存在！'));

        }

        if (!$obj_return_policy->get_conf_data($arr_settings))
        {
            $this->end(false,app::get('b2c')->_('售后服务信息没有取到！'));
        }

        if (!$_POST['product_bn'])
        {
            $this->end(false,app::get('b2c')->_('您没有选择商品，请先选择商品！'));
        }

        if (!$_POST['title'])
        {
            $this->end(false,app::get('b2c')->_('请填写退货理由'));
        }

        $obj_filter = kernel::single('b2c_site_filter');
        $_POST = $obj_filter->check_input($_POST);

        $product_data = array();
        foreach ((array)$_POST['product_bn'] as $key => $val)
        {
            $item = array();
            $item['bn'] = $val;
            $item['name'] = $_POST['product_name'][$key];
            $item['num'] = intval($_POST['product_nums'][$key]);
            $item['price'] = floatval($_POST['product_price'][$key]);
            $product_data[] = $item;
        }

        $aData['order_id'] = $_POST['order_id'];
        $aData['title'] = $_POST['title'];
        $aData['type'] = $_POST['type']==2 ? 2 : 1;
        $aData['add_time'] = time();
        $aData['image_file'] = $_POST['image_id'];
        $aData['member_id'] = $_POST['member_id'];
        $aData['product_data'] = serialize($product_data);
        $aData['content'] = $_POST['content'];
        $aData['status'] = 2;

        $msg = "";
        $obj_aftersales = kernel::service("api.aftersales.request");
        if ($obj_aftersales && $obj_aftersales->generate($aData, $msg))
        {
            $obj_rpc_request_service = kernel::service('b2c.rpc.send.request');
            if ($obj_rpc_request_service && method_exists($obj_rpc_request_service, 'rpc_caller_request'))
            {
                if ($obj_rpc_request_service instanceof b2c_api_rpc_request_interface)
                    $obj_rpc_request_service->rpc_caller_request($aData,'aftersales');
            }
            else
            {
                $obj_aftersales->rpc_caller_request($aData);
            }
            $this->end(true,app::get('b2c')->_('售后服务提交成功'));
        }
        else
        {
            $this->end(false,$msg);
        }
    }


    private function intArray($int=1){
        for($i=1;$i<=$int;$i++){
            $return[$i] = $i;
        }
        return $return;
    }


    public function file_download($return_id)
    {
        $obj_return_policy = kernel::service("aftersales.return_policy");
        $obj_return_policy->file_download($return_id);
    }

    public function send_comment()
    {
        $rp = &$this->app->model('return_product');

        $return_id = $_POST['return_id'];
        $comment = $_POST['comment'];
        $arr_data = array(
            'return_id' => $return_id,
            'comment' => $comment,
        );

        $this->begin();
        if($rp->send_comment($arr_data))
        {
            $this->end(true, app::get('aftersales')->_('发送成功！'));
        }
        else
        {
            //trigger_error(__('发送失败'),E_USER_ERROR);
            $this->end(false, app::get('aftersales')->_('发送失败！'));
        }
    }

    public function settings()
    {
        $this->workground = 'site.wrokground.theme';
        if (!$_POST)
        {
            $this->pagedata['return_product']['comment'] = app::get('aftersales')->getConf('site.return_product_comment');
            $this->page('admin/setting/return_product.html');
        }
        else
        {
            $this->begin('index.php?app=aftersales&ctl=admin_returnproduct&act=settings');

            app::get('aftersales')->setConf('site.return_product_comment', $_POST['conmment']);

            $this->end(true, app::get('aftersales')->_("设置成功！"));
        }
    }
}
