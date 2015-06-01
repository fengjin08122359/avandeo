<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/**
 * @table magicvars;
 *
 * @package Schemas
 * @version $
 * @copyright 2003-2009 ShopEx
 * @license Commercial
 */
$db['storelist']=array(
		'columns' =>array(
			'store_id' => 
	    array (
	      'type' => 'number',
	      'required' => true,
	      'pkey' => true,
	      'extra' => 'auto_increment',
	      'editable' => false,
	    	'label' => app::get('storelist')->_('编号'),
	      'comment' => app::get('storelist')->_('编号'),
	    ),	
		'store_name'=>array(
				'type' => 'varchar(30)',
				'required' => true,
				'label' => app::get('storelist')->_('门店名称'),
				'width' => 110,
				'editable' => true,
				'in_list' => true,
				'default_in_list' => true,
				'comment' => app::get('storelist')->_('门店名称'),
	    ),
		'region_id'=>array(
				'type' => 'longtext',
				'required' => false,
				'label' => app::get('storelist')->_('选择的区域'),
				'width' => 110,
				'editable' => false,
				//'in_list' => true,
				//'default_in_list' => true,
				'comment' => app::get('storelist')->_('选择的区域'),
		),
		'reg_id'=>array(
			'type' => 'number',
			'required' => true,
			'label' => app::get('storelist')->_('默认区域ID'),
			'width' => 110,
			'editable' => false,
			//'in_list' => true,
			//'default_in_list' => true,
			'comment' => app::get('storelist')->_('默认区域ID'),
		),
		'r_id'=>array(
			'type' => 'longtext',
			'required' => true,
			'label' => app::get('desktop')->_('区域'),
			'width' => 110,
			'editable' => false,
			//'in_list' => true,
			//'default_in_list' => true,
			'comment' => app::get('storelist')->_('区域'),
		),
		'default_pass'=>array(
				'type'=>'varchar(255)',
				'required' => false,
				'label' => app::get('storelist')->_('默认密码'),
				'width' => 110,
				'editable' => true,
				'in_list' => true,
				'default_in_list' => true,
				'comment' => app::get('storelist')->_('默认密码'),
		),
		'area'=>array(
			'type'=>'region',
			'required' => true,
			'label' => app::get('storelist')->_('默认选择区域'),
			'width' => 110,
			'editable' => true,
			'filtertype' => 'yes',
			'sdfpath' => 'consignee/area',
			'in_list' => true,
			'default_in_list' => true,
			'comment' => app::get('storelist')->_('默认选择区域'),
		),
		'area_name'=>array(
			'type'=>'varchar(255)',
			'required' => false,
			'label' => app::get('storelist')->_('地区名称'),
			'width' => 110,
			'editable' => true,
			'in_list' => true,
			'default_in_list' => true,
			'comment' => app::get('storelist')->_('地区名称'),
		),
		'area_value'=>array(
			'type'=>'longtext',
			'required' => false,
			'label' => app::get('storelist')->_('默认区域选择的值'),
			'width' => 110,
			'editable' => true,
			//'in_list' => true,
			//'default_in_list' => true,
			'comment' => app::get('storelist')->_('默认区域选择的值'),
		),
	),
	'engine' => 'innodb',
	'version' => '$Rev: 40912 $',
	'comment' => app::get('storelist')->_('门店区域表'),
);
?>