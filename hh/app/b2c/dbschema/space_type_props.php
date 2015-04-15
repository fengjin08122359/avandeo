<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
$db['space_type_props']=array (
  'columns' => 
    array (
      'props_id' => array(
        'type' => 'number',
        'required' => true,
        'extra' => 'auto_increment',
        'label' => app::get('b2c')->_('属性id'),
        'width' => 110,
        'editable' => false,
        'pkey' => true,
        'in_list' => true,
        'default_in_list' => true,
    ),
    'type_id' => 
    array (
      'type' => 'table:space_type',
      'required' => true,
      'label' => app::get('b2c')->_('类型id'),
      'width' => 110,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'show' => array(
        'type' => 'varchar(10)',
        'required' => true,
        'default' => '',
        'in_list' => true,
        'comment' => app::get('b2c')->_('是否显示'),
    ),
    'name' => 
    array (
      'type' => 'varchar(100)',
      'required' => true,
      'default' => '',
      'label' => app::get('b2c')->_('类型名称'),
      'is_title' => true,
      'width' => 150,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'goods_p'=>array(
        'type' => 'smallint',
        'label' => app::get('b2c')->_('商品位置')
    ),
    'ordernum'=>array(
        'type' => 'int(10)',
        'default' => 0,
        'comment' => app::get('b2c')->_('排序'),
    ),
    'lastmodify' => 
    array (
      'label' => app::get('b2c')->_('最后更新时间'),
      'width' => 150,
      'type' => 'last_modify',
      'hidden' => 1,
      'in_list' => false,
    ),
  ),
  'comment' => app::get('b2c')->_('空间类型属性表'),
);
