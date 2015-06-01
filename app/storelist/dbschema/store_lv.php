<?php
$db['store_lv']=array(
	'columns'=>array(
		'store_lv_id'=>array(
				'type'=>'number',
				'required' => true,
				'pkey' => true,
				'extra' => 'auto_increment',
				'editable' => false,
				'label' => app::get('storelist')->_('等级编号'),
				'comment' => app::get('storelist')->_('等级编号'),
		),
		'name'=>array(
			'type'=>'varchar(50)',
			'required' => true,
			'label' => app::get('storelist')->_('等级名称'),
			'width' => 110,
			'editable' => false,
			'in_list' => true,
			'default_in_list' => true,
			'comment' => app::get('storelist')->_('等级名称'),
		),
		'small_value'=>array(
			'type'=>'number',
			'required' => true,
			'label' => app::get('storelist')->_('最小值'),
			'width' => 110,
			'editable' => false,
			'in_list' => true,
			'default_in_list' => true,
			'comment' => app::get('storelist')->_('最小值'),
		),
		'max_value'=>array(
				'type'=>'number',
				'required' => true,
				'label' => app::get('storelist')->_('最大值'),
				'width' => 110,
				'editable' => false,
				'in_list' => true,
				'default_in_list' => true,
				'comment' => app::get('storelist')->_('最大值'),
			),
	),
		'engine' => 'innodb',
		'version' => '$Rev: 40912 $',
		'comment' => app::get('storelist')->_('销售等级表'),
);
?>