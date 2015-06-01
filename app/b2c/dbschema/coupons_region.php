<?php
$db['coupons_region'] = array(
    'columns' => array(
        'cpns_id' =>
        array (
          'type' => 'table:coupons',
          'required' => true,
          'pkey' => true,
          'label' => app::get('b2c')->_('优惠券id'),
          'editable' => false,
        ),
        'region_id' =>
        array (
            'type' => 'table:regions@ectools',
            'required' => true,
            'pkey' => true,
            'editable' => false,
            'label' => app::get('ectools')->_('地区ID'),
        ),
    ),
);