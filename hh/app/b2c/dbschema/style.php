<?php
/**
 * 风格搭配属性表
 * @author qianzedong <qianzedong@shopex.cn>
 */
$db['style']=array (
  'columns' =>
  array (
    'style_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => app::get('b2c')->_('风格ID'),
      'width' => 150,
      'comment' => app::get('b2c')->_('风格ID'),
      'editable' => false,
      'in_list' => false,
      'default_in_list' => false,
    ),
    'name'	=>
    array (
      'type' => 'varchar(50)',
      'label' => app::get('b2c')->_('风格名称'),
      'width' => 180,
      'is_title' => true,
      'required' => true,
      'comment' => app::get('b2c')->_('风格名称'),
      'editable' => true,
      'searchtype' => 'has',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'thumbnail_pic'  =>
    array(
      'type' => 'varchar(32)',
      'label' => app::get('b2c')->_('缩略图'),
      'width' => 110,
      'hidden' => true,
      'editable' => false,
      'in_list' => false,
    ),
    'banner_pic'  =>
    array(
      'type' => 'varchar(32)',
      'label' => app::get('b2c')->_('banner大图'),
      'width' => 110,
      'hidden' => true,
      'editable' => false,
      'in_list' => false,
    ),
    /**
     * status
     * 二转十 
     * 0正常 
     * 01banner大图 
     * 10最流行风排行榜
     */
    'status'  =>
    array(
        'type' => 'number',
        'label' => app::get('b2c')->_('状态'),
        'width' => 150,
        'hidden' => 1,
        'in_list' => false,
        'default'=>'0',
        'comment' => app::get('b2c')->_('状态'),
    ),
    'description' =>
    array(
      'type' => 'varchar(255)',
      'label' => app::get('b2c')->_('风格简介'),
      'width' => 300,
      'hidden' => false,
      'editable' => false,
      'in_list' => true,
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
  'comment' => app::get('b2c')->_('风格搭配属性表'),
);
