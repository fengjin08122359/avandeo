<?php
class couponlog_finder_extend_order_coupon_user{

    function get_extend_colums(){
        $db['order_coupon_user']=array (
            'columns' => array (
                'area_fee' => array (
                    'type' => 'regions',
                    'label' => '优惠券适用区域',
                    'editable' => true,
                    'filtertype' => 'yes',
                    'filterdefault' => true,
                    'in_list' => true,
                    'default_in_list' => true,
                )
            )
        );
        return $db;
    }

}