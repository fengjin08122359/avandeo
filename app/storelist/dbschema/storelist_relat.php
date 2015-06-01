<?php
$db['storelist_relat']=array(
		'columns'=>array(
			'stores_id'=>array(
				'type'=>'number',
				'required' => true,
				//'label' => app::get('desktop')->_('默认区域ID'),
				'width' => 110,
			    'editable' => false,
						//'in_list' => true,
						//'default_in_list' => true,
			  'comment' => app::get('storelist')->_('门店ID'),
		),
			'oper_id'=>array(
					'type'=>'number',
					'required' => true,
					//'label' => app::get('desktop')->_('默认区域ID'),
					'width' => 110,
					'editable' => false,
					//'in_list' => true,
					//'default_in_list' => true,
					'comment' => app::get('storelist')->_('操作员ID'),
			),
	),
		'engine' => 'innodb',
		'version' => '$Rev: 40912 $',
		'comment' => app::get('storelist')->_('门店与职员关联表'),
);
?>