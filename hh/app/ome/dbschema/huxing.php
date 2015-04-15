<?php


$db['huxing'] = array(
	'columns'  => array(
		'hx_id' => array(
			'type'   => 'number',
			'extra'  => 'auto_increment',
			'pkey'   => true,
			'label'  => app::get('b2c')->_('户型ID'),
		),
		'hx_name'             => array(
			'type'            => 'varchar(50)',
			'label'           => app::get('b2c')->_('户型名'),
			'width'           => 75,
			'searchtype'      => 'has',
			'editable'        => true,
			'filtertype'      => 'normal',
			'filterdefault'   => 'true',
			'in_list'         => true,
			'default_in_list' => true,
		),
	),
	'comment' => app::get('b2c')->_('户型表'),
);