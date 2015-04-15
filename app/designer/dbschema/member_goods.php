<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['member_goods'] = array(
	'columns' => array(
		'id'     => array(
			'type'  => 'number',
			'extra' => 'auto_increment',
			'pkey'  => true,
			'label' => app::get('b2c')->_('ID'),
		),
		'member_id' => array(
			'type'     => 'number',
			'label'    => app::get('b2c')->_('会员ID'),
			'rquired'  => true,
			//'is_list'  => true,
		),
		'goods_id' => array(
			'type'    => 'number',
			'label'   => app::get('b2c')->_('商品ID'),
			'rquired' => true,
		),
		'related_type'     => array(
			'type'            => array('left'            => '单向相关', 'both'            => '双向相关'),
			'default'         => 'left',
			'label'           => app::get('b2c')->_('关联关系'),
			'width'           => 75,
			'searchtype'      => 'has',
			'editable'        => true,
			'filtertype'      => 'normal',
			'filterdefault'   => 'true',
			'in_list'         => true,
			'default_in_list' => false,
		),
	),
	'comment' => app::get('b2c')->_('相关商品表'),
);