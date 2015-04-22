<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['dingzhi']=array (
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
        'is_defalut' =>
        array (
            'type' => 'bool',
            'editable' => false,
            'default'=>'false',
            'label' => app::get('b2c')->_('是否为默认商品'),
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
