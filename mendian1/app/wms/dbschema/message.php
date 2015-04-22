<?php

$db['message']=array (
  'columns' =>
  array (
    'MsgId' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => app::get('weixin')->_('MsgId'),
      'width' => 110,
      'editable' => false,
    ),

    'Content' =>
    array (
      'type' => 'text',
      'label' => app::get('weixin')->_('内容'),
      'width' => 110,
      'editable' => true,
      'in_list' => true,
	   'default_in_list' => true,
    ),
    'CreateTime' =>
    array (
      'type' => 'datetime',
      'label' => app::get('weixin')->_('创建时间'),
      'width' => 110,
      'editable' => false,
	  'defalut'=>false,
	  'in_list' => true,
	   'default_in_list' => true,
    ),


  ),
);

 ?>