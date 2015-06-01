<?php
$db['storelist_item']=array(
		'columns' =>array(
		'items_id'=>array(
				'type' => 'number',
				'required' => true,
				'pkey' => true,
				'extra' => 'auto_increment',
				'editable' => false,
				'label' => app::get('storelist')->_('编号'),
				'comment' => app::get('storelist')->_('编号'),
		),
			'store_id'=>array(
				'type'=>'table:storelist@storelist',
				'required' => true,
				'label' => app::get('storelist')->_('门店名称'),
				'width' => 110,
				'editable' => false,
					//'in_list' => true,
					//'default_in_list' => true,
				'comment' => app::get('storelist')->_('门店名称'),
		),
				'region_id'=>array(
					'type'=>'number',
					'required' => true,
					'label' => app::get('storelist')->_('区域ID'),
					'width' => 110,
					'editable' => false,
						//'in_list' => true,
						//'default_in_list' => true,
					'comment' => app::get('storelist')->_('区域ID'),
			),
	),
		'engine' => 'innodb',
		'version' => '$Rev: 40912 $',
		'comment' => app::get('storelist')->_('门店与分享区域关联表'),
);
?>