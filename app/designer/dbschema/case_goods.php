<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['case_goods'] = array(
	'columns'    => array(
		'case_id'   => array(
			'type'     => 'number',
			'label'    => app::get('b2c')->_('案例ID'),
			'required' => true,
			//'is_list'  => true,
		),
		'goods_id'  => array(
			'type'     => 'number',
			'label'    => app::get('b2c')->_('商品ID'),
			'required' => true,
		),
		'x'         => array(
			'type'     => 'number',
			'label'    => app::get('b2c')->_('横坐标'),
			'required' => fal,
		),
		'y'         => array(
			'type'     => 'number',
			'label'    => app::get('b2c')->_('纵坐标'),
			'required' => true,
		),
	),
	'comment'       => app::get('b2c')->_('案例关联商品表'),
	'index'         => array(
		'ind_maincode' => array(
			'columns'     => array(
				0            => 'case_id',
				1            => 'goods_id',
			),
			'prefix' => 'UNIQUE',
		),
	),
);