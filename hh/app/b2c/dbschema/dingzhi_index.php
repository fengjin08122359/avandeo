<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['dingzhi_index']=array (
    'columns' =>
    array (
        'dingzhi_id' =>
        array (
            'type' => 'varchar(30)',
            'required' => true,
            'label' => app::get('b2c')->_('定制ID'),
            'width' => 110,
            'editable' => false,
            'hidden' => true,
            'in_list' => false,
        ),
        'goods_id' =>
        array (
            'type' => 'bigint unsigned',
            'required' => true,
            'label' => app::get('b2c')->_('商品id'),
            'width' => 75,
            'editable' => false,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'product_id' =>
        array (
            'type' => 'bigint unsigned',
            'required' => true,
            'label' => app::get('b2c')->_('货品id'),
            'width' => 75,
            'editable' => false,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'spec_id' =>
        array (
            'type' => 'bigint unsigned',
            'required' => true,
            'label' => app::get('b2c')->_('规格ID'),
            'width' => 75,
            'editable' => false,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'spec_value_id' =>
        array (
            'type' => 'bigint unsigned',
            'required' => true,
            'label' => app::get('b2c')->_('规格值ID'),
            'width' => 75,
            'editable' => false,
            'in_list' => true,
            'default_in_list' => true,
        ),
    ),
    'index' =>
    array (
        'ind_dingzhi_id' =>
        array (
            'columns' =>
            array (
                0 => 'dingzhi_id',
            ),
        ),
    ),
    'version' => '$Rev$',
    'comment' => app::get('b2c')->_('定制器表'),
);
