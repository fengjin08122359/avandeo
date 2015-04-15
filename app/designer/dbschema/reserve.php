<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['reserve'] = array(
	'columns'     => array(
		'reserve_id' => array(
			'type'      => 'number',
			'extra'     => 'auto_increment',
			'pkey'      => true,
			'label'     => app::get('b2c')->_('预约ID'),
		),
		'name'             => array(
			'type'            => 'varchar(50)',
			'label'           => app::get('b2c')->_('客户姓名'),
			'width'           => 75,
			'searchtype'      => 'has',
			'editable'        => true,
			'filtertype'      => 'normal',
			'filterdefault'   => 'true',
			'in_list'         => true,
			'is_title'        => true,
			'default_in_list' => true,
		),
		'tel'              => array(
			'type'            => 'char(30)',
			'label'           => app::get('b2c')->_('联系电话'),
			'width'           => 75,
			'searchtype'      => 'has',
			'editable'        => true,
			'filtertype'      => 'normal',
			'filterdefault'   => 'true',
			'in_list'         => true,
			'default_in_list' => true,
		),
		'email'            => array(
			'type'            => 'varchar(50)',
			'label'           => app::get('b2c')->_('邮箱'),
			'width'           => 75,
			'searchtype'      => 'has',
			'editable'        => true,
			'filtertype'      => 'normal',
			'filterdefault'   => 'true',
			'in_list'         => true,
			'default_in_list' => true,
		),
		'member_id'        => array(
			'type'            => 'table:members',
			'sdfpath'         => 'members/member_id',
			'label'           => app::get('b2c')->_('设计师ID'),
			'searchtype'      => 'has',
			'editable'        => true,
			'filtertype'      => 'normal',
			'filterdefault'   => 'true',
			'in_list'         => true,
			'default_in_list' => true,
		),
		'huxing'           => array(
			'type'            => array('0'            => '一室一厅', '1'            => '两室一厅', '2'            => '三室两厅'),
			'label'           => app::get('b2c')->_('户型'),
			'required'        => false,
			'editable'        => true,
			'filtertype'      => 'normal',
			'filterdefault'   => 'true',
			'in_list'         => true,
			'default_in_list' => true,
		),
		'area'             => array(
			'type'            => array('0'            => '50-100', '1'            => '100-150', '2'            => '150-300'),
			'label'           => app::get('b2c')->_('面积'),
			'required'        => false,
			'editable'        => true,
			'filtertype'      => 'normal',
			'filterdefault'   => 'true',
			'in_list'         => true,
			'default_in_list' => true,
		),
		'other_demand' => array(
			'type'        => 'text',
			'label'       => app::get('b2c')->_('其他需求'),
			'required'    => false,
		),
	),
	'comment' => app::get('b2c')->_('客户预约表'),
);