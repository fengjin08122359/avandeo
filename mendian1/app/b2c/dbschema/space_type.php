<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['space_type']=array (
  'columns' =>
  array (
    'type_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => app::get('b2c')->_('类型id'),
      'width' => 110,
      'editable' => false,
      'in_list' => false,
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
    'setting' =>
    array (
      'type' => 'serialize',
      'comment' => app::get('b2c')->_('类型设置'),
      'width' => 110,
      'editable' => false,
      'label' => app::get('b2c')->_('类型设置'),

    ),
    'price' =>
    array (
      'type' => 'serialize',
      'editable' => false,
      'comment' => app::get('b2c')->_('设置价格区间，用于列表页搜索使用'),
    ),
    'disabled' =>
    array (
      'type' => 'bool',
      'default' => 'false',
      'editable' => false,
    ),
    'lastmodify' =>
    array (
      'label' => app::get('b2c')->_('最后更新时间'),
      'width' => 150,
      'type' => 'time',
      'hidden' => 1,
      'in_list' => false,
      'comment' => app::get('b2c')->_('上次修改时间'),
    ),
  ),
  'index' =>
  array (
    'ind_disabled' =>
    array (
      'columns' =>
      array (
        0 => 'disabled',
      ),
    ),
  ),
  'comment' => app::get('b2c')->_('空间类型表'),
);
