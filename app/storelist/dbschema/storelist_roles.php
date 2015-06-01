<?php
$db['storelist_roles']=array(
	'columns'=>array(
		'stores_id'=>array(
			'type'=>'number',
			'required' => true,
				//'label' => app::get('desktop')->_('默认区域ID'),
				'width' => 110,
				'editable' => false,
				//'in_list' => true,
				//'default_in_list' => true,
			'comment' => app::get('storelist')->_('门店关联ID'),
		),
			'role_id'=>array(
					'type'=>'table:roles@desktop',
					'required' => true,
					//'label' => app::get('desktop')->_('默认区域ID'),
					'width' => 110,
					'editable' => false,
					//'in_list' => true,
					//'default_in_list' => true,
					'comment' => app::get('storelist')->_('角色ID'),
		),
	),
		'engine' => 'innodb',
		'version' => '$Rev: 40912 $',
		'comment' => app::get('storelist')->_('门店与角色关联表'),
);
?>