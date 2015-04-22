<?php
/**
 * Created by PhpStorm.
 * User: chugongbiao
 * Date: 15/1/19
 * Time: 下午11:24
 */

class b2c_order_beforecreate_discount{

    function __construct($app)
    {
        $this->app = $app;
    }

    /*
     * 修改订单信息
     */
    public function generate( &$sdf )
    {
        if( !is_array($sdf['order_objects']) || count($sdf['order_objects'])>1 ) return false;
        foreach( $sdf['order_objects'] as $key => $row ) {
            if(!$row['obj_type'] != 'discount'){
                continue;
            }
        }

        $arr = array(
            'order_id' => $sdf['order_id'],
            'amount' => $row['amount'],
            'discount_id' => $row['goods_id'],
            'nums' => $row['quantity'],
            'pay_status' => '0',
            'member_id' => $sdf['member_id'],
            'createtime'=>time()
        );
        $model = $this->app->model('discount_orders');
        if(!$model->save($arr)){
            return false;
        }
        return true;
    }
    #End Func
}