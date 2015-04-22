<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['case'] = array(
	'columns'  => array(
		'case_id' => array(
			'type'   => 'number',
			'extra'  => 'auto_increment',
			'pkey'   => true,
			'label'  => app::get('b2c')->_('案例ID'),
		),
		'name'             => array(
			'type'            => 'varchar(50)',
			'label'           => app::get('b2c')->_('案例名'),
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
		// 'member_id' => array(
		// 	'type'     => 'number',
		// 	'required' => true,
		// 	// 'sdfpath'         => 'members/member_id',
		// 	'label'           => app::get('b2c')->_('设计师ID'),
		// 	'searchtype'      => 'has',
		// 	'editable'        => true,
		// 	'filtertype'      => 'normal',
		// 	'filterdefault'   => 'true',
		// 	'in_list'         => true,
		// 	'default_in_list' => true,
		// ),
		'image_default_id' => array(
			'type'            => 'varchar(32)',
			'label'           => app::get('b2c')->_('默认图片'),
			'width'           => 75,
			'hidden'          => true,
			'editable'        => false,
			'in_list'         => true,
		),
		'createtime' => array(
			'type'      => 'date',
			'label'     => '案例创建时间',
		),
		'description' => array(
			'type'       => 'text',
			'label'      => app::get('b2c')->_('案例介绍'),
			'required'   => false,
		),
		'hx_id'           => array(
			'type'            => 'table:huxing',
			'sdfpath'=>'huxing/hx_id',
			'label'           => app::get('b2c')->_('户型'),
			'required'        => false,
			'editable'        => true,
			'filtertype'      => 'normal',
			'filterdefault'   => 'true',
			'in_list'         => true,
			'default_in_list' => true,
		),
		'a_id'             => array(
			'type'            => 'table:area',
			'sdfpath'=>'area/a_id',
			'label'           => app::get('b2c')->_('面积'),
			'required'        => false,
			'editable'        => true,
			'filtertype'      => 'normal',
			'filterdefault'   => 'true',
			'in_list'         => true,
			'default_in_list' => true,
		),
	),
	'comment' => app::get('b2c')->_('设计师案例表'),
);