<?php
/**
 * 风格搭配与产品关联表
 * @author qianzedong <qianzedong@shopex.cn>
 */
$db['styleproduct']=array (
  'columns' =>
  array (
    'styleproduct_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => app::get('b2c')->_('风格产品关联ID'),
      'width' => 150,
      'comment' => app::get('b2c')->_('风格产品关联ID'),
      'editable' => false,
      'in_list' => false,
      'default_in_list' => false,
    ),    
    'style_id' =>
    array (
      'type' => 'table:style',
      'label' => app::get('b2c')->_('风格ID'),
      'width' => 150,
      'comment' => app::get('b2c')->_('风格ID'),
      'editable' => true,
      'in_list' => true,
    ),    
    'goods_id' =>
    array (
      'type' => 'table:goods',
      'label' => app::get('b2c')->_('商品ID'),
      'width' => 150,
      'comment' => app::get('b2c')->_('商品ID'),
      'editable' => true,
      'in_list' => true,
    ),        
    'product_id' =>
    array (
      'type' => 'table:products',
      'label' => app::get('b2c')->_('货品ID'),
      'width' => 150,
      'comment' => app::get('b2c')->_('货品ID'),
      'editable' => true,
      'in_list' => true,
    ),
  ),
  'index' =>
  array (
    'ind_style' =>
    array (
      'columns' =>
      array (
        1 => 'goods_id',
      ),
    ),
  ),
  'version' => '$Rev: 40654 $',
  'comment' => app::get('b2c')->_('风格搭配与产品关联表'),
);
