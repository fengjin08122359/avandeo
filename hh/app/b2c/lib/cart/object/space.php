<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/**
 * 购物车项处理(优惠券)
 * $ 2010-05-25 14:09 $
 */
class b2c_cart_object_space implements b2c_interface_cart_object{

    private $app;
    private $member_ident; // 用户标识
    private $oCartObject;
    private $__max_goods_store = 100;
    public $intopromotion = true;

    /**
     * 构造函数
     *
     * @param $object $app  // service 调用必须的
     */
    public function __construct() {
        $this->app = app::get('b2c');

        $this->arr_member_info = kernel::single('b2c_cart_objects')->get_current_member();
        $this->member_ident = kernel::single("base_session")->sess_id();

        if( !empty($this->arr_member_info) ) {
            $oMemeberLV = $this->app->model('member_lv');
            $aMLV = $oMemeberLV->getList('dis_count',array('member_lv_id'=>$this->arr_member_info['member_lv']));
            $aMLV = $aMLV[0];
            $this->discout = (empty($aMLV['dis_count']) || $aMLV['dis_count'] > 1 || $aMLV['dis_count'] <= 0)? 1 : $aMLV['dis_count'];
        }

        $this->oCartObjects = $this->app->model('cart_objects');
        $this->omath = kernel::single('ectools_math');
    }

    /**
     * 购物车是否需要验证库存
     * @param null
     * @return boolean true or false
     */
    public function need_validate_store() {
        return true;
    }

    public function get_type() {
        return 'space';
    }

    public function get_part_type() {
        return array('space');
    }

    /**
     * 处理加入购物车商品的数据
     * @param mixed array 传入的参数
     * @return mixed array 处理后的数据
     */
    public function get_data($params=array())
    {
        return $params;
    }

    /**
     * 得到失败应该返回的url - app 数组
     * @param array
     * @return array
     */
    public function get_fail_url($data=array())
    {
        return array('app'=>'b2c', 'ctl'=>'site_cart', 'act'=>'checkout');
    }

    /**
     * 校验加入购物车数据是否符合要求-各种类型的数据的特殊性校验
     * @param array 加入购物车数据
     * @param string message 引用值
     * @return boolean true or false
     */
    public function check_object($arr_data,&$msg='')
    {
        if(empty($arr_data) || empty($arr_data['space']))
        {
            $msg = app::get('b2c')->_('组合商品数据为空！');
            return false;
        }
        return true;
    }

    /**
     * 检查库存
     * @param array 加入购物车的商品结构
     * @param array 现有购物车的数量
     * @param string message
     * @return boolean true or false
     */
    public function check_store($arr_data, $arr_carts, &$msg='')
    {
        return true;
    }

    /**
     * 添加购物车项(coupon)
     * @param array $aData // array(
     *                          'goods_id'=>'xxxx',   // 商品编号
     *                          'product_id'=>'xxxx', // 货品编号
     *                          'adjunct'=>'xxxx',    // 配件信息
     *                          'quantity'=>'xxxx',   // 购买数量
     *                        )
     * @param string message
     * @return boolean
     */
    public function add_object($aData, &$msg='', $append=true,$is_fastbuy=false)
    {
        $space = $aData['space'];
        if(!$temp_arr = app::get('b2c')->model('space')->getList('space_id,name,item_str,marketable',array('space_id'=>$space['space_id']),0,1)){
            $msg = app::get('b2c')->_('空间组合信息错误');
            return false;
        }
        $temp_arr = $temp_arr[0];
        $objIdent = $this->_generateIdent($temp_arr);
        if( $temp_arr['marketable']!=='true' ) {
            $msg = app::get('b2c')->_('该活动已经结束');
            return false;
        }

        $extends_params = array();
        foreach($temp_arr['item_str'] as $v){
            $extends_params[$v['product_id']] = array(
                'goods_id'=>$v['goods_id'],
                'product_id'=>$v['product_id'],
                'base_num'=>$v['base_num'],
            );
        }

        $aSave = array(
            'obj_ident'    => $objIdent,
            'member_ident' => $this->member_ident,
            'obj_type'     => 'space',
            'params'       => array(
                'name'  =>  $temp_arr['name'],
                'space_id'   => $temp_arr['space_id'],
                'extends_params' => $extends_params,
            ),
            'quantity'     => $space['num'],
        );

        if(kernel::single("b2c_cart_object_goods")->get_cart_status()) {
            $this->space_object[$aSave['obj_ident']] = $aSave;
            return $aSave['obj_ident'];
        }; //no database

        // 追加|更新
        if($append && !$is_fastbuy) {
            // 如果存在相同组合 则追加
            $filter = array(
                'obj_ident' => $aSave['obj_ident'],
                'member_ident' => $this->member_ident,
            );
            if ($aData = $this->oCartObjects->getList('*', $filter, 0, -1, -1)){
                reset( $aData );
                $aData = current( $aData );
                $aSave['quantity'] += $aData['quantity'];
            }
        }

        $is_save = $this->oCartObjects->save($aSave);
        if (!$is_save){
            $msg = app::get('b2c')->_('空间组合商品成功加入购物车！');
            return false;
        }

        return $aSave['obj_ident'];
    }

    // 优惠券没有更新这一说
    public function update($sIdent,$quantity) {
        $aSave = array(
            'obj_ident'    => $sIdent,
            'member_ident' => $this->member_ident,
            'obj_type'     => 'space',
        );

        $filter = array(
            'obj_ident' => $sIdent,
            'member_ident' => $this->member_ident,
            'obj_type' => 'space',
        );
        $arr_cart_object_data = $this->oCartObjects->getList('*', $filter, 0, -1, -1);
        $arr_cart_object_data = $arr_cart_object_data[0];

        if(floatval($quantity['quantity'])) $aSave['quantity'] = floatval($quantity['quantity']);
        else $aSave['quantity'] = $arr_cart_object_data['quantity'];

        unset($quantity['quantity']);
        return $this->_add($aSave,false);
    }

    /**
     * 指定的购物车优惠券
     *
     * @param string $sIdent
     * @param boolean $rich        // 是否只取cart_objects中的数据 还是完整的sdf数据
     * @return array
     */
    public function get($sIdent = null,$rich = false) {
        if(empty($sIdent)) return $this->getAll($rich);

        $aResult = $this->oCartObjects->getList('*',array(
            'obj_ident' => $sIdent,
            'member_ident'=> $this->member_ident,
        ));
        if(empty($aResult)) return array();
        if($rich) {
            $aResult = $this->_get($aResult);
            $aResult = $aResult[0];
        }

        return $aResult;
    }

    private function _get($aData) {
        $aInfo = $this->_get_basic($aData);
        $aProductId = $aInfo['productid'];
        $products_store = $tmp_products_store = array();

        $aProducts = $this->_get_products($aProductId);
        $arr_goods = $arr_products = array();
        foreach( $aData as $key => $row ) {
            //商品不存在时删除购物车内信息
            if(empty($aProducts[$row['obj_items']['products'][0]])) {
                unset($aData[$key]);continue;
            }
            foreach($row['obj_items']['products'] as $pro_id){
                if($aProducts[$pro_id]['store'] <= 0){
                    unset($aData[$key]);
                    continue 2;
                }
            }
            $extends_params = $row['params']['extends_params'];
            foreach($extends_params as $v){
                $arr_goods[$v['goods_id']][] = $key;
                if(isset($arr_products[$v['product_id']])){
                    $arr_products[$v['product_id']]['num'] = $arr_products[$v['product_id']]['num'] + $v['base_num'];
                    $arr_products[$v['product_id']]['keys'][] = $key;
                }
                else{
                    $arr_products[$v['product_id']]['num'] = $v['base_num']*$row['quantity'];
                    $arr_products[$v['product_id']]['keys'][] = $key;
                }
            }

        }

        $this->_check_goods($aData, $arr_goods);
        $this->_check_products($aData, $arr_products, $this->get_type());
        $arr_products = array();
        foreach($aData as $key => &$row) {
            foreach($row['obj_items']['products'] as $pk=>$product_id){
                $row['obj_items']['products'][$pk] = $temp_product = $aProducts[$product_id];
                $extends_params = $row['params']['extends_params'];
                $quantity = $row['quantity'];
                $row['obj_items']['products'][$pk]['base_num'] = $extends_params[$product_id]['base_num'];
                $row['obj_items']['products'][$pk]['price']['discount_price'] = $this->omath->number_multiple(array($temp_product['price']['buy_price'],(1-$row['discount']),$extends_params[$product_id]['base_num']));
                if(isset($tmp_products_store[$product_id])){
                    $tmp_products_store[$product_id]['less'] += $quantity;
                    $tmp_products_store[$product_id]['quantity'] += $quantity;
                }else {
                    $tmp_store = array(
                        'quantity' => $quantity*$extends_params[$product_id]['base_num'],
                        'store'    => $aProducts[$product_id]['store'],
                        'product_id' => $product_id,
                        'obj_ident' => $row['obj_ident'],
                        'less'      => $quantity*$extends_params[$product_id]['base_num'],
                        'base_num'=>$extends_params[$product_id]['base_num'],
                        'name'      => $aProducts[$product_id]['new_name'],
                    );
                    $tmp_products_store[$product_id] = $tmp_store;
                }

                $row['store'][] = &$products_store[$product_id]['store'];
            }
        }


        $this->get_products_real_store($tmp_products_store, $products_store);
        foreach($aData as $k=>$v){
            foreach($v['store'] as $v2){
                if(!isset($store)){
                    $store = $v2['store'];
                    $tmp_store = $v2;
                }
                if($v2['store'] < $store){
                    $store = $v2['store'];
                    $tmp_store = $v2;
                }
            }
            $aData[$k]['store'] = $tmp_store;
            unset($store);
            unset($tmp_store);
        }
        return $aData;
    }

    // 购物车里的所有组合
    public function getAll($rich = false) {

        if(kernel::single("b2c_cart_object_goods")->get_cart_status()) {
            $aResult = $this->space_object;
        } else {
            $aResult= $this->oCartObjects->getList('*',array(
                'obj_type' => 'space',
                'member_ident'=> $this->member_ident,
            ));
        }
        if(empty($aResult)) return array();
        if(!$rich) return $aResult;
        return $this->_get($aResult);
    }

    // 删除购物车中指定优惠券
    public function delete($sIdent = null) {
        if(empty($sIdent)) return $this->deleteAll();
        return $this->oCartObjects->delete(array('member_ident'=>$this->member_ident, 'obj_ident'=>$sIdent, 'obj_type'=>'space'));
    }

    // 清空购物车中优惠券数据
    public function deleteAll() {
        return $this->oCartObjects->delete(array('member_ident'=>$this->member_ident, 'obj_type'=>'space'));
    }

    // 统计购物车中组合数据
    public function count(&$aData) {
        // 购物车中不存在space商品
        if(empty($aData['object']['space'])) return false;
        $aResult = array(
            'subtotal_weight'=>0,
            'subtotal'=>0,
            'subtotal_price'=>0,
            'subtotal_consume_score'=>0,
            'subtotal_gain_score'=>0,
            'discount_amount_prefilter'=>0,
            'discount_amount_order'=>0,
            'discount_amount'=>0,
            'items_quantity'=>0,
            'items_count'=>0,
        );

        foreach($aData['object']['space'] as &$row) {
            $this->_count($row);
            $aResult['subtotal_consume_score'] += $row['subtotal_consume_score'];
            $aResult['subtotal_gain_score'] += $this->omath->number_plus( array($row['subtotal_gain_score'],$row['sales_score_order']) );

            $aResult['subtotal'] += $row['subtotal'];
            $aResult['subtotal_price'] += $row['subtotal_price'];
            $aResult['subtotal_weight'] += $row['subtotal_weight'];
            //}
            $aResult['discount_amount_prefilter'] += $row['discount_amount_prefilter'];

            $aResult['discount_amount_order'] += $row['discount_amount_order'];
            $aResult['discount_amount'] += $row['discount_amount_cart'] ;
            $aResult['items_quantity'] += $row['item_quantity_count'];
            $aResult['items_count']++;
            $aData['goods_min_buy'][$row['min_buy']['goods_id']]['info'] = $row['min_buy'];
            $aData['goods_min_buy'][$row['min_buy']['goods_id']]['real_quantity'] += $row['quantity'];
            if( $row['error_html'] ) $aResult['error_html'] = $row['error_html'];
            if($row['quantity'] > $row['store']['real']) {
                $aData['cart_status'] = 'false';
                $aData['cart_error_html'] = app::get('b2c')->_('库存错误！');
            }
        }
#echo "<pre>";var_export($aData['object']['space']);exit;
        return $aResult;
    }

    // 数据有效性，库存检查
    private function _check(&$aData) {
        //todo 暂时先不做库存检查
        /*if(empty($aData)) return array('status'=>'false','msg'=>'购物车操作失败');

        // 验证商品的正确性
        $obj_ident = $aData['obj_ident'];
        if(empty($obj_ident) || is_array($obj_ident)) return $this->get_error_msg( '参数错误' );

        //商品 是否下架 是否删除
        $oSG = $this->o_goods;
        $arr_goods_info = $this->getIdFromIdent($obj_ident);
        $goods_id = $arr_goods_info['goods_id'];
        $product_id = $arr_goods_info['product_id'];
        if( !isset($this->check_goods_info[$goods_id]) )
            $this->check_goods_info[$goods_id] = $oSG->getList('goods_id, store,nostore_sell, marketable', array('goods_id'=> "$goods_id"));

        $aResult = $this->check_goods_info[$goods_id];

        $aGoods = $aResult[0];

        if($aGoods['marketable']=='false') return $this->get_error_msg( '商品未上架' );  //未上架

        //规格商品
        $params = is_array($aData['params']) ? $aData['params'] : @unserialize($aData['params']);

        if($params['product_id']) {
            $product_id = $params['product_id'];
        }
        if(empty($product_id)) return array('status'=>'false','msg'=>'货品id为空！');

        #if( !isset($this->check_products_info[$product_id]) )
        $this->check_products_info[$product_id] = $this->o_products->getList('product_id,goods_id, store, freez, marketable', array('product_id'=>"$product_id"));

        $aResult = $this->check_products_info[$product_id];


        if(!$aResult[0]) return $this->get_error_msg( '数据读取错误！货品' );
        $arr_product = $aResult[0];

        if($arr_product['marketable']=='false') return $this->get_error_msg( '该规格商品未上架！' );   //未上架
        $arr_product['store'] = ( $aGoods['nostore_sell'] ? $this->__max_goods_store : ( empty($arr_product['store']) ? ($arr_product['store']===0 ? 0 : $this->__max_goods_store) : $arr_product['store'] -$arr_product['freez']) );

        if ( !$aGoods['nostore_sell'] ) {
            if(empty($arr_product['store'])){
                if(isset($arr_product['store']) && $arr_product['store']!=='' ) return $this->get_error_msg( '该商品已无库存！' ); //库存0
                // 检测是否够库存
            } else if($aData['quantity']>$arr_product['store']) return $this->get_error_msg( '购买数量超出库存' );

        }*/

        return true;
    }

    private function _generateIdent($aData) {
        $product_ids = array();
        foreach((array)$aData['item_str'] as $v){
            $product_ids[] = $v['product_id'];
        }
        sort($product_ids);
        return "space_".$aData['space_id']."_".implode("-",$product_ids);
    }


    public function apply_to_disabled( $data,$session,$flag ) {
        return $data;
    }

    private function _add($aSave,$append = true) {

        // 追加|更新
        if($append) {
            // 如果存在相同商品 则追加
            $filter = array(
                'obj_ident' => $aSave['obj_ident'],
                'member_ident' => $this->member_ident,
            );

            if ($aData = $this->oCartObjects->getList('*', $filter, 0, -1, -1)){
                reset( $aData );
                $aData = current( $aData );
                $aSave['quantity'] += $aData['quantity'];
            }
        }

        if( true!==($return=$this->_check($aSave)) ) {
            if( $append )
                return $return;
            else return false;
        }

        $this->oCartObjects->save($aSave);
        return $aSave;
    }

    private function _get_basic(&$aData) {

        $aResult = array();
        $aProductId = array();

        $mdl_space = app::get('b2c')->model('space');
        foreach($aData as $row) {
            if( !$this->_check( $row ) ) continue;
            if($extends_params = $row['params']['extends_params']){
                $space_product_ids = array_keys($extends_params);
                $aProductId = array_merge($aProductId,$space_product_ids);
            }
            $temp_space_info = $mdl_space->getRow('*',array('space_id'=>$row['params']['space_id']));
            $aResult[] = array(
                'obj_ident' => $row['obj_ident'],
                'obj_type' => 'space',
                'space_id'=>$temp_space_info['space_id'],
                'space_name'=>$temp_space_info['name'],
                'image_default_id'=>$temp_space_info['image_default_id'],
                'discount'=>$temp_space_info['discount'],
                'obj_items' => array(
                    'products' => $space_product_ids,
                ),
                'quantity' => $row['quantity'],
                'params' => $row['params'],
                'subtotal_consume_score' => 0,
                'subtotal_gain_score' => 0,
                'subtotal' => 0,
                'subtotal_price' => 0,
                'subtotal_weight' => 0,
                'discount_amount' => 0,
            );
        }
        // 将整理好的数据格式用引用带出
        $aData = $aResult;
        return array('productid'=>array_unique($aProductId));
    }

    function _get_products($aProductId) {
        $imageDefault = app::get('image')->getConf('image.set');
        $json = kernel::single('b2c_cart_json');
        if(empty($aProductId)) return array();
        //防sql注入处理
        foreach( $aProductId as $k=>$id ){
            $aProductId[$k] = intval($id);
        }
        $aProductId = array_unique($aProductId);
        ///////////////// 货品信息 ///////////////////////
        $sSql = "SELECT
                     g.spec_desc as spec_desc_goods,p.product_id,p.goods_id,p.bn,g.score as gain_score,p.cost,p.name, p.store, p.marketable, g.params, g.package_scale, g.package_unit, g.package_use, p.freez,
                     g.goods_type, g.nostore_sell, g.min_buy,g.type_id,g.cat_id,g.image_default_id,p.spec_info,p.spec_desc,p.price,p.weight,
                     t.setting, t.floatstore
                 FROM  sdb_b2c_products AS p
                 LEFT JOIN  sdb_b2c_goods AS g    ON p.goods_id = g.goods_id
                 LEFT JOIN sdb_b2c_goods_type AS t ON g.type_id  = t.type_id
                 WHERE product_id IN (".implode(',',$aProductId).")";
        $aProduct = $this->oCartObjects->db->select($sSql);
        ////////// 设置了的会员价 //////////////////////////
        $sSql = "SELECT p.product_id,p.price
                FROM sdb_b2c_goods_lv_price AS p
                LEFT JOIN sdb_b2c_member_lv AS lv ON p.level_id = lv.member_lv_id
                WHERE p.level_id=".(intval($this->arr_member_info['member_lv']))." AND p.product_id IN (".implode(',',$aProductId).")";

        $aPrice = $this->oCartObjects->db->select($sSql);
        $tmp = array();
        foreach($aPrice as $val) {
            $tmp[$val['product_id']] = $val;
        }
        $aPrice = $tmp;
        $tmp = null;
        $aPrice = empty($aPrice)? array() : utils::array_change_key($aPrice,'product_id');

        //////////// 获取会员折扣 //////////////////////////
        //empty($this->arr_member_info)
        if( empty($this->arr_member_info) ) { // 非登录用户
            $discount = 1;
        } else {// 登录用户
            $discount = $this->discout;
        }
        //////////// 整理数据 /////////////////////////////
        $aResult = array();
        foreach($aProduct as $row) {
            //$products_store[$row['product_id']]['store'] = $row['store'];
            if($row['marketable']=='false') {  //商品下架购物车中消失处理！
                unset($row);continue;
            }

            //商品不存在时购物车里也同时删除
            $key = array_search($row['product_id'], $aProductId);
            if($key===false) $arrDelGoods[] = $aProductId[$key];
            //商品不存在时购物车里也同时删除
            $aResult[$row['product_id']] = array(
                'bn' => $row['bn'],
                'price' => array(
                    'price' => $row['price'],
                    'cost' => $row['cost'],
                    'member_lv_price' => empty($aPrice[$row['product_id']]) ? $this->omath->number_multiple(array($row['price'],$discount) ) : $aPrice[$row['product_id']]['price'],
                    'buy_price' => empty($aPrice[$row['product_id']]) ? $this->omath->number_multiple( array($row['price'],$discount) ) : $aPrice[$row['product_id']]['price'],
                ),
                'product_id' => $row['product_id'],
                'goods_id' => $row['goods_id'],
                'goods_type' => $row['goods_type'],
                'name'=> $row['name'],
                'consume_score' => 0,
                'gain_score' => intval($row['gain_score']),
                'type_setting' => is_array($row['setting']) ? $row['setting'] : @unserialize($row['setting']),
                'type_id' => $row['type_id'],
                'cat_id' => $row['cat_id'],
                'min_buy' => $row['min_buy'],
                'spec_info' => $row['spec_info'],
                'spec_desc' => is_array($row['spec_desc']) ? $row['spec_desc'] : @unserialize($row['spec_desc']),
                'weight' => $row['weight'],
                'quantity' => 1,
                'params' => is_array($row['params']) ? $row['params'] : @unserialize($row['params']),
                'floatstore' => $row['floatstore'] ? $row['floatstore'] : 0,
                'store'=> ( $row['nostore_sell'] ? $this->__max_goods_store : ( empty($row['store']) ? (((int)$row['store']===0 && $row['store']!==null && $row['store']!=='')? 0 : $this->__max_goods_store) : $row['store'] -$row['freez']) ),
                'package_scale' => $row['package_scale'],
                'package_unit' => $row['package_unit'],
                'package_use' => $row['package_use'],
                // 'default_image' => array(
                // 'thumbnail' => $row['image_default_id'] ? $row['image_default_id'] : $imageDefault['M']['default_image'],
                // ),
            );
            // 如果货品绑定了对应的货品图片则显示对应规格关联图片
            $select_spec_private_value_id = '';
            $spec_desc_goods = '';
            if($aResult[$row['product_id']]['spec_desc']){
                $select_spec_private_value_id = reset($aResult[$row['product_id']]['spec_desc']['spec_private_value_id']);
                $spec_desc_goods = reset(unserialize($row['spec_desc_goods']));
            }
            if($select_spec_private_value_id && $default_product_image = $spec_desc_goods[$select_spec_private_value_id]['spec_goods_images']){
                $aResult[$row['product_id']]['default_image'] = array(
                    'thumbnail'=>$default_product_image,
                );
            }else{
                $aResult[$row['product_id']]['default_image'] = array(
                    'thumbnail' => $row['image_default_id'] ? $row['image_default_id'] : $imageDefault['M']['default_image'],
                );
            }

            //组合JSON格式让JS显示
            $aResult[$row['product_id']]['json_price']['price'] = $json->get_cur_order($aResult[$row['product_id']]['price']['price']);
            $aResult[$row['product_id']]['json_price']['cost'] = $json->get_cur_order($aResult[$row['product_id']]['price']['cost']);
            $aResult[$row['product_id']]['json_price']['member_lv_price'] = $json->get_cur_order($aResult[$row['product_id']]['price']['member_lv_price']);
            $aResult[$row['product_id']]['json_price']['buy_price'] = $json->get_cur_order($aResult[$row['product_id']]['price']['buy_price']);
            #$aResult[$row['product_id']]['url'] = $router->gen_url(array('app'=>'b2c','ctl'=>'site_product','full'=>1,'act'=>'index','arg'=>$aResult[$row['product_id']]['goods_id']));
            #$aResult[$row['product_id']]['thumbnail'] = base_storager::image_path( $aResult[$row['product_id']]['default_image']['thumbnail'],'s');
            $aResult[$row['product_id']]['thumbnail'] = $aResult[$row['product_id']]['default_image']['thumbnail'];
            $tmp = $aResult[$row['product_id']];
            //todo 获取积分暂时不做处理
            #$this->getScroe($tmp);
            $aResult[$row['product_id']] = $tmp;

            /*if($row['package_use']) {
                if($row['package_scale']) {
                    $aResult[$row['product_id']]['quantity'] = $row['package_scale'];
                    foreach($aResult[$row['product_id']]['price'] as &$s_v_price) {
                        $s_v_price *= $row['package_scale'];
                    }
                }
            }*/
            $tmp = $aResult[$row['product_id']]['spec_info'];
            $aResult[$row['product_id']]['new_name'] = $row['name'] . ( $tmp ? ' ('. $tmp .')' : '' );
        }
        //商品不存在时购物车里也同时删除
        if(!empty($arrDelGoods)) {
            foreach ($aResult as $key => &$val) {
                if(in_array($val['goods_id'], $arrDelGoods)) {
                    unset($aResult[$key]);
                }
            }
        }
        return $aResult;
    }

    //验证库存、是否上架商品
    protected function _check_goods( &$aData, $arr_goods ) {
        if( empty($arr_goods_id) ) return ;
        $arr_goods_id = array_keys($arr_goods);
        $arr = app::get('b2c')->model('goods')->getList('goods_id, store,nostore_sell, marketable', array('goods_id'=> $arr_goods_id));

        foreach($arr as $row) {
            $this->check_goods_info[$row['goods_id']] = $row;
            $keys = $arr_goods[$row['goods_id']];
            if( $row['marketable']=='false' ){
                foreach($keys as $key){
                    unset($aData[$key]);
                }
            }
            if( $row['nostore_sell'] )
                $this->nostore_sell[$row['goods_id']] = true;
        }
    }


    //验证库存、是否上架货品
    protected function _check_products( &$aData, $arr_products, $cur_type='' ) {
        if( empty($arr_products) ) return ;
        $all_product_ids = array_keys($arr_products);
        $arr = app::get('b2c')->model('products')->getList('product_id,goods_id, store,freez,marketable', array('product_id'=>$all_product_ids));
        foreach($arr as $row) {
            $keys = $arr_products[$row['product_id']]['keys'];
            if( $row['marketable']=='false' ){
                foreach($keys as $key){
                    unset($aData[$key]);
                }
            }

            if( !$this->nostore_sell[$row['goods_id']] ) {
                if( empty($row['store']) || $row['store']==0 || 0>$row['store']-$row['freez'] ){
                    if( $row['store']!==null && $row['store']!=='' ) {
                        foreach($keys as $key){
                            unset($aData[$key]);
                        }
                    }
                    else {
                        $row['store'] = $this->__max_goods_store;
                    }
                }else{
                    foreach($keys as $key){
                        if($arr_products[$row['product_id']]['num']>$row['store']-$row['freez']) {
                            unset($aData[$key]);
                        }
                    }
                }
            }
        }
    }

    private function _count(&$aData) {
        // 重新统计时将以下值 置为0
        $aData['subtotal_consume_score'] = 0;
        $aData['subtotal_gain_score'] = 0;
        $aData['subtotal'] = 0;  //会员价总额
        $aData['subtotal_prefilter_after'] = 0;  //会员价总额
        $aData['subtotal_price'] = 0; //商品原始价格
        $aData['subtotal_weight'] = 0;
        $aData['discount_amount'] = 0;
        $aData['discount_amount_prefilter'] = 0; //预过滤优惠
        $aData['item_quantity_count'] = 0;


        $extends_params = $aData['params']['extends_params'];
        //计算优惠金额
        foreach($aData['obj_items']['products'] as $key=>&$row) {
            $base_num = $extends_params[$row['product_id']]['base_num'];
            $aData['subtotal'] +=  $this->omath->number_multiple( array($row['price']['buy_price'],$base_num) );
            $aData['subtotal_weight'] += $this->omath->number_multiple(array($row['weight'],$base_num));
            $aData['item_quantity_count'] += $base_num;
        }
        //单个套餐的原价
        $aData['price'] = $aData['subtotal'];
        //单个套餐的优惠金额
        $space_discount = $this->omath->number_multiple(array($aData['subtotal'],(1-$aData['discount'])));
        //该套餐的总优惠金额
        $aData['discount_amount_prefilter'] = $this->omath->number_multiple(array($space_discount,$aData['quantity']));

        // 数量
        $aData['item_quantity_count'] = $this->omath->number_multiple(array($aData['item_quantity_count'],$aData['quantity']));
        $aData['subtotal'] = $this->omath->number_multiple(array($aData['subtotal'],$aData['quantity']));
        $aData['subtotal_price'] = $this->omath->number_multiple(array($aData['subtotal_price'],$aData['quantity']));
        $aData['subtotal_consume_score'] = 0;
        $aData['subtotal_weight'] = $this->omath->number_multiple( array($aData['subtotal_weight'],$aData['quantity']) );
        //商品促销之后的商品总价
        $aData['subtotal_prefilter_after'] = $this->omath->number_minus(array($aData['subtotal'],$aData['discount_amount_prefilter']));
        $aData['subtotal_gain_score'] = $this->get_score($aData['subtotal_prefilter_after']);


        #echo "<pre>";var_export($aData);exit;
    }

    private function get_score( $price ) {
        //获取商店积分规则
        if(!isset($this->site_score_policy) && empty($this->site_score_policy)) {
            $this->site_score_policy = $this->app->getConf('site.get_policy.method');
        }


        if($this->site_score_policy==1) { //不使用积分
            $gain_score = 0;
        } else if ($this->site_score_policy==2) {
            if(!isset($this->site_score_rate) && empty($this->site_score_rate)) {
                $this->site_score_rate = $this->app->getConf('site.get_rate.method');
            }
            $gain_score = $this->omath->number_multiple( array($price,$this->site_score_rate) );
        }
        return $gain_score;
    }

    private function get_products_real_store(&$tmp, &$products_store) {
        foreach($tmp as $k=>$val) {
            $store = $tmp[$k]['store'] = floor($val['store']/$val['base_num']);
            $products_store[$val['product_id']]['store'] = array(
                'real' => $store - $val['less'] + $val['quantity'],
                'less' => $val['less'],
                'store' => $store,
                'name' => $val['name'],
            );
        }
        #echo "<pre>";var_export($tmp);var_export($products_store);exit;
    }

    public function get_update_num( $data,$ident ) {
        $o_currency = kernel::single('ectools_mdl_currency');
        foreach( $data as $row ) {
            if( $row['obj_ident']!=$ident['ident'] ) continue;
            ($row['discount']==1)?$discount = 0:$discount=$this->omath->number_multiple(array($row['subtotal'],(1-$row['discount'])));
            return array(
                'buy_price'=>$o_currency->changer_odr($this->omath->number_multiple(array($row['price'],$row['discount'],$row['quantity']))),
                'consume_score'=>(float)($row['subtotal_gain_score']),
                'discount' => $o_currency->changer_odr($discount),
            );
        }
    }

    private function get_error_msg( $msg ) {
        return array('status'=>'false','msg'=>$msg);
    }
}
