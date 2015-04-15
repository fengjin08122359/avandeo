<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['space_type_props_value']=array (
  'columns' =>
  array (
    'props_value_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => app::get('b2c')->_('属性值序号'),
      'width' => 110,
      'editable' => false,
      'in_list' => true,
  ),
    'props_id' =>
    array (
      'type' => 'table:space_type_props',
      'required' => true,
      'label' => app::get('b2c')->_('属性序号'),
      'width' => 110,
      'editable' => false,
      'in_list' => true,
      'default_in_list' => true,
  ),
    'name' =>
    array (
      'type' => 'varchar(100)',
      'required' => true,
      'default' => '',
      'label' => app::get('b2c')->_('属性值'),
      'is_title' => true,
      'width' => 150,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'order_by' => array(
        'type' => 'int',
        'required' => true,
        'default' => 0,
        'comment' => app::get('b2c')->_('排序'),
    ),
    'lastmodify' =>
    array (
      'label' => app::get('b2c')->_('更新时间'),
      'width' => 150,
      'type' => 'last_modify',
      'hidden' => 1,
      'in_list' => false,
    ),
  ),
  'comment' => app::get('b2c')->_('空间类型扩展属性值表'),
);
