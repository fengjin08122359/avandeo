<?php
/**
 * Created by PhpStorm.
 * User: chugongbiao
 * Date: 15/1/13
 * Time: 下午12:19
 */

$db['space_item'] = array(
    'columns'=>array(
        'item_id'=>array(
            'type'=>'number',
            'pkey'=>true,
            'extra'=>'auto_increment',
            'required'=>true,
        ),
        'space_id'=>array(
            'type'=>'table:space',
            'label'=>app::get('b2c')->_('空间组合id'),
            'required'=>true,
        ),
        'goods_id'=>array(
            'type'=>'table:goods',
            'label'=>app::get('b2c')->_('商品id'),
            'required'=>true,
        ),
        'product_id'=>array(
            'type'=>'table:products',
            'label'=>app::get('b2c')->_('货品id'),
            'required'=>true,
        ),
        'base_num'=>array(
            'type'=>'number',
            'label'=>app::get('b2c')->_('商品基数'),
            'required'=>true,
            'default'=>1
        )
    ),
    'index'=>array(
        'ind_spaceid_productid'=>array(
            'columns'=>array(
                0=>'space_id',
                1=>'product_id'
            ),
            'prefix'=>'unique'
        ),
    ),
);