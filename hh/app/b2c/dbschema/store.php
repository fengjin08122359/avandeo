<?php
/**
 * 线下体验店
 * @author qianzedong <qianzedong@shopex.cn>
 */
$db['store']=array (
  'columns' =>
  array (
    'store_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => app::get('b2c')->_('ID'),
      'width' => 150,
      'comment' => app::get('b2c')->_('线下体验店ID'),
      'editable' => false,
      'in_list' => false,
      'default_in_list' => false,
    ),
    'name'	=>
    array (
      'type' => 'varchar(50)',
      'label' => app::get('b2c')->_('名称'),
      'width' => 180,
      'is_title' => true,
      'required' => true,
      'comment' => app::get('b2c')->_('线下体验店名称'),
      'editable' => true,
      'searchtype' => 'has',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'image_default_id' =>
    array (
      'type' => 'varchar(32)',
      'label' => app::get('b2c')->_('缩略图'),
      'width' => 75,
      'hidden' => true,
      'editable' => false,
      'in_list' => false,
    ),
    'image_banner_id' =>
    array (
      'type' => 'varchar(32)',
      'label' => app::get('b2c')->_('banner图'),
      'width' => 75,
      'hidden' => true,
      'editable' => false,
      'in_list' => false,
    ),
    'area'  =>
    array (
      'type' => 'varchar(50)',
      'label' => app::get('b2c')->_('area'),
      'width' => 250,
      'required' => false,
      'editable' => true,
      'searchtype' => 'has',
      'in_list' => false,
      'default_in_list' => false,
    ),
    'address'  =>
    array (
      'type' => 'varchar(255)',
      'label' => app::get('b2c')->_('地址'),
      'width' => 250,
      'required' => false,
      'comment' => app::get('b2c')->_('线下体验店地址'),
      'editable' => true,
      'searchtype' => 'has',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'phone'  =>
    array (
      'type' => 'varchar(50)',
      'label' => app::get('b2c')->_('电话号码'),
      'width' => 180,
      'required' => false,
      'comment' => app::get('b2c')->_('线下体验店电话号码'),
      'editable' => true,
      'searchtype' => 'has',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'btime'  =>
    array (
      'type' => 'varchar(50)',
      'label' => app::get('b2c')->_('营业时间'),
      'width' => 180,
      'required' => false,
      'comment' => app::get('b2c')->_('线下体验店营业时间'),
      'editable' => true,
      'searchtype' => 'has',
      'in_list' => true,
      'default_in_list' => false,
    ),
    'traffic'  =>
    array (
      'type' => 'varchar(50)',
      'label' => app::get('b2c')->_('交通'),
      'width' => 180,
      'required' => false,
      'comment' => app::get('b2c')->_('线下体验店交通'),
      'editable' => true,
      'searchtype' => 'has',
      'in_list' => true,
      'default_in_list' => false,
    ),
    'content'  =>
    array (
      'type' => 'text',
      'label' => app::get('b2c')->_('内容'),
      'width' => 180,
      'required' => false,
      'comment' => app::get('b2c')->_('线下体验店内容'),
      'editable' => true,
      'searchtype' => 'has',
      'in_list' => false,
      'default_in_list' => false,
    ),
  ),
  'index' =>
  array (
    'ind_name' =>
    array (
      'columns' =>
      array (
        0 => 'name',
      ),
    ),
  ),
  'version' => '$Rev: 40654 $',
  'comment' => app::get('b2c')->_('线下线下体验店'),
);
