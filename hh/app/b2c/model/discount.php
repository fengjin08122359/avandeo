<?php
/**
 * Created by PhpStorm.
 * User: chugongbiao
 * Date: 15/2/4
 * Time: 下午2:36
 */

class b2c_mdl_discount extends dbeav_model{
    var $has_many = array(
        'discount_item' => 'discount_item',
        'images' => 'image_attach@image:contrast:discount_id^target_id',
    );
    var $subSdf = array(
        'default' => array(
            'discount_item'=>array(
                '*'
            ),
            'images'=>array(
                '*',array(
                    ':image'=>array('*')
                )
            )
        ),
        'delete' => array(
            'discount_item'=>array(
                '*'
            ),
            'images'=>array(
                '*'
            )
        )
    );

    function _filter($filter){
        //去空处理
        $filter = $this->_pre_filter($filter);
        //价格
        if(isset($filter['price']) && is_array($filter['price'])){
            $filter['pricefrom'] = $filter['price'][0];
            $filter['priceto']   = $filter['price'][1];
            unset($filter['price']);
        }
        if($filter['priceto'] || $filter['pricefrom']){
            if(!$filter['pricefrom']) $filter['pricefrom'] = 0.000001;
            if(!$filter['priceto']) $filter['priceto'] = 0.000001;
            $pricefrom = number_format($filter['pricefrom'],'3','.','');
            $priceto = number_format($filter['priceto'],'3','.','');
            unset($filter['pricefrom']);
            unset($filter['priceto']);
            $list = $this->getList2('*',$filter);
            $temp_discount_ids = array();
            foreach($list as $v){
                if($v['sales_price'] >= $pricefrom && $v['sales_price'] <= $priceto){
                    $temp_discount_ids[] = $v['discount_id'];
                }
            }
            if($temp_discount_ids){
                $filter['discount_id|in'] = array_merge((array)$filter['discount_id|in'],$temp_discount_ids);
                $filter['discount_id|in'] = array_merge((array)$filter['discount_id'],$filter['discount_id|in']);
            }else{
                $filter['discount_id'] = "-1";
            }
        }

        return parent::_filter($filter);
    }

    //过滤挂件中的'_ANY_'参数和参数为空的字段
    private function _pre_filter($filter=array()){
        $is_numeric = array('price','cost','mktprice','store');
        foreach($filter as $col=>$val){
            if(is_array($val)){
                foreach($val as $k=>$v){
                    if($v == '_ANY_' || $v[0] == '_ANY_' || empty($v)){
                        unset($filter[$col][$k]);
                    }
                }
            }else{
                if($val == '_ANY_' || (in_array($col,$is_numeric) && !is_numeric($val)) ){
                    unset($filter[$col]);
                }
            }
            if(is_null($filter[$col]) || $filter[$col] === '' || (is_array($filter[$col]) && empty($filter[$col])) ){
                unset($filter[$col]);
            }
        }
        return $filter;
    }

    public function getList2($cols='*',$filter=array(),$start=0,$limit=-1,$orderType=null){
        $list = parent::getList($cols,$filter,$start,$limit,$orderType);
        //计算价格
        if($list){
            $mdl_products = app::get('b2c')->model('products');
            $obj_math = kernel::single('ectools_math');
            $pro2price = array();
            foreach($list as $k=>$v){
                $temp_products = $v['item_str'];
                $total_price = 0;
                foreach($temp_products as $p){
                    if(!isset($pro2price[$p['product_id']])){
                        $temp_p = $mdl_products->dump($p['product_id'],'*','price/member_lv_price');
                        $pro2price[$p['product_id']] = $temp_p['price']['price']['current_price'];
                    }
                    $p_total_price = $obj_math->number_multiple(array($pro2price[$p['product_id']],$p['base_num']));
                    $total_price = $obj_math->number_plus(array($total_price,$p_total_price));
                }
                $list[$k]['price'] = $total_price;
                $list[$k]['sales_price'] = $obj_math->number_multiple(array($total_price,$v['discount']));
            }
        }
        return $list;
    }

    public function dump2($filter,$field = '*',$subSdf = null){
        $info = parent::dump($filter,$field,$subSdf);
        if(!$info){
            return array();
        }
        $temp_products = $info['item_str'];
        $total_price = 0;
        $mdl_products = app::get('b2c')->model('products');
        $obj_math = kernel::single('ectools_math');
        $pro2price = array();
        foreach($temp_products as $p){
            if(!isset($pro2price[$p['product_id']])){
                $temp_p = $mdl_products->dump($p['product_id'],'*','price/member_lv_price');
                $pro2price[$p['product_id']] = $temp_p['price']['price']['current_price'];
            }
            $p_total_price = $obj_math->number_multiple(array($pro2price[$p['product_id']],$p['base_num']));
            $total_price = $obj_math->number_plus(array($total_price,$p_total_price));
        }
        $info['price'] = $total_price;
        $info['sales_price'] = $obj_math->number_multiple(array($total_price,$info['discount']));
        return $info;
    }

    public function orderBy(){

    }

}