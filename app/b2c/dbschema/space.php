<?php
/**
 * Created by PhpStorm.
 * User: chugongbiao
 * Date: 15/2/3
 * Time: 下午2:55
 */

$db['space'] = array(
    'columns'=>array(
        'space_id'=>array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'label' => 'id',
            'editable' => false,
            'extra' => 'auto_increment',
        ),
        'name'=>array(
            'type'=>'varchar(255)',
            'required'=>true,
            'label'=>app::get('b2c')->_('空间名称'),
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'discount'=>array(
            'type'=>'float',
            'required'=>true,
            'default'=>1,
            'label'=>app::get('b2c')->_('折扣'),
            'in_list'=>true,
            'default_in_list'=>true
        ),
        'brief'=>array(
            'type'=>'varchar(255)',
            'label'=>app::get('b2c')->_('简介'),
            'in_list'=>true,
        ),
        'item_str'=>array(
            'type'=>'serialize',
            'required'=>true,
            'label'=>app::get('b2c')->_('组合商品')
        ),
        'type_id'=>array(
            'type'=>'table:space_type',
            'label'=>app::get('b2c')->_('空间类型'),
            'sdfpath'=>'type/type_id',
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'marketable'=>array(
            'type'=>'bool',
            'required'=>true,
            'default'=>'false',
            'label'=>app::get('b2c')->_('上架状态'),
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'image_default_id'=>array(
            'type'=>'varchar(100)',
            'label'=>app::get('b2c')->_('默认图')
        ),
        'description'=>array(
            'type'=>'longtext',
            'label'=>app::get('b2c')->_('详情'),
        ),
        'retail_store'=>array(
            'type'=>'longtext',
            'label'=>app::get('b2c')->_('线下门店'),
        ),
        'p_1' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_1/value',
                'editable' => false,
            ),
        'p_2' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_2/value',
                'editable' => false,
            ),
        'p_3' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_3/value',
                'editable' => false,
            ),
        'p_4' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_4/value',
                'editable' => false,
            ),
        'p_5' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_5/value',
                'editable' => false,
            ),
        'p_6' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_6/value',
                'editable' => false,
            ),
        'p_7' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_7/value',
                'editable' => false,
            ),
        'p_8' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_8/value',
                'editable' => false,
            ),
        'p_9' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_9/value',
                'editable' => false,
            ),
        'p_10' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_10/value',
                'editable' => false,
            ),
        'p_11' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_11/value',
                'editable' => false,
            ),
        'p_12' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_12/value',
                'editable' => false,
            ),
        'p_13' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_13/value',
                'editable' => false,
            ),
        'p_14' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_14/value',
                'editable' => false,
            ),
        'p_15' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_15/value',
                'editable' => false,
            ),
        'p_16' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_16/value',
                'editable' => false,
            ),
        'p_17' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_17/value',
                'editable' => false,
            ),
        'p_18' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_18/value',
                'editable' => false,
            ),
        'p_19' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_19/value',
                'editable' => false,
            ),
        'p_20' =>
            array (
                'type' => 'number',
                'sdfpath' => 'props/p_20/value',
                'editable' => false,
            ),
        'p_21' =>
            array (
                'type' => 'varchar(255)',
                'sdfpath' => 'props/p_21/value',
                'editable' => false,
            ),
        'p_22' =>
            array (
                'type' => 'varchar(255)',
                'sdfpath' => 'props/p_22/value',
                'editable' => false,
            ),
        'p_23' =>
            array (
                'type' => 'varchar(255)',
                'sdfpath' => 'props/p_23/value',
                'editable' => false,
            ),
        'p_24' =>
            array (
                'type' => 'varchar(255)',
                'sdfpath' => 'props/p_24/value',
                'editable' => false,
            ),
        'p_25' =>
            array (
                'type' => 'varchar(255)',
                'sdfpath' => 'props/p_25/value',
                'editable' => false,
            ),
        'p_26' =>
            array (
                'type' => 'varchar(255)',
                'sdfpath' => 'props/p_26/value',
                'editable' => false,
            ),
        'p_27' =>
            array (
                'type' => 'varchar(255)',
                'sdfpath' => 'props/p_27/value',
                'editable' => false,
            ),
        'p_28' =>
            array (
                'type' => 'varchar(255)',
                'sdfpath' => 'props/p_28/value',
                'editable' => false,
            ),

        'p_29' =>
            array (
                'type' => 'varchar(255)',
                'sdfpath' => 'props/p_29/value',
                'editable' => false,
            ),
        'p_30' =>
            array (
                'type' => 'varchar(255)',
                'sdfpath' => 'props/p_30/value',
                'editable' => false,
            ),
    ),
    'index' =>
        array (
            'ind_p_1' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_1',
                        ),
                ),
            'ind_p_2' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_2',
                        ),
                ),
            'ind_p_3' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_3',
                        ),
                ),
            'ind_p_4' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_4',
                        ),
                ),
            'ind_p_5' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_5',
                        ),
                ),
            'ind_p_6' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_6',
                        ),
                ),
            'ind_p_7' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_7',
                        ),
                ),
            'ind_p_8' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_8',
                        ),
                ),
            'ind_p_9' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_9',
                        ),
                ),
            'ind_p_10' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_10',
                        ),
                ),
            'ind_p_11' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_11',
                        ),
                ),
            'ind_p_12' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_12',
                        ),
                ),
            'ind_p_13' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_13',
                        ),
                ),
            'ind_p_14' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_14',
                        ),
                ),
            'ind_p_15' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_15',
                        ),
                ),
            'ind_p_16' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_16',
                        ),
                ),
            'ind_p_17' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_17',
                        ),
                ),
            'ind_p_18' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_18',
                        ),
                ),
            'ind_p_19' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_19',
                        ),
                ),
            'ind_p_20' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_20',
                        ),
                ),
            'ind_p_21' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_21',
                        ),
                ),
            'ind_p_22' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_22',
                        ),
                ),
            'ind_p_23' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_23',
                        ),
                ),
            'ind_p_24' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_24',
                        ),
                ),
            'ind_p_25' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_25',
                        ),
                ),
            'ind_p_26' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_26',
                        ),
                ),
            'ind_p_27' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_27',
                        ),
                ),
            'ind_p_28' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_28',
                        ),
                ),
            'ind_p_29' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_29',
                        ),
                ),


            'ind_p_30' =>
                array (
                    'columns' =>
                        array (
                            0 => 'p_30',
                        ),
                ),
        )
);