<?php
class b2c_finder_extend_coupons{

    function get_extend_colums(){
        $db['coupons']=array (
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