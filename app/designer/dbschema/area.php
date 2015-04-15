<?php


$db['area'] = array(
	'columns'  => array(
		'a_id' => array(
			'type'   => 'number',
			'extra'  => 'auto_increment',
			'pkey'   => true,
			'label'  => app::get('b2c')->_('ID'),
		),
		'a_value'             => array(
			'type'            => 'varchar(50)',
			'label'           => app::get('b2c')->_('面积范围值/平米'),
			'width'           => 75,
			'searchtype'      => 'has',
			'editable'        => true,
			'filtertype'      => 'normal',
			'filterdefault'   => 'true',
			'in_list'         => true,
			'default_in_list' => true,
		),
	),
	'comment' => app::get('b2c')->_('面积表'),
);