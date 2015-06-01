<?php
/**
 * 门店职员与平台职员互动记录表
 * @author qianzedong <qianzedong@shopex.cn>
 */

$db['chat_record'] = array(
	'columns' => array(
		'record_id' => array(
			'type' => 'bigint unsigned',
			'required' => true,
			'pkey' => true,
			'extra' => 'auto_increment',
			'label' => 'ID',
			'comment' => app::get('storelist')->_('ID'),
			'hidden' => true,
			'in_list' => false,
		),
		'from_id' => array(
			'type' => 'table:users@desktop',
			'label' => '发送人',
			'comment' => app::get('storelist')->_('发送人'),
			'default' => 0,
			'width' => '100',
			'in_list' => true,
			'default_in_list' => true,
		),
		'to_id' => array(
			'type' => 'table:users@desktop',
			'label' => '接收人',
			'comment' => app::get('storelist')->_('接收人'),
			'default' => 0,
			'width' => '100',
			'in_list' => true,
			'default_in_list' => true,
		),
		'message' => array(
			'type' => 'varchar(100)',
			'required' => true,
			'label' => '信息',
			'comment' => app::get('storelist')->_('信息'),
			'width' => '100',
			'in_list' => false,
		),
		'addtime' => array(
			'type' => 'time',
			'label' => app::get('storelist')->_('记录时间'),
			'comment' => app::get('storelist')->_('记录时间'),
			'width' => 200,
			'in_list' => true,
			'default_in_list' => true,
		),
	),
	'index' => array(
		'ind_from_id' => array(
			'columns' => array(
				0 => 'from_id',
			),
		),
		'ind_to_id' => array(
			'columns' => array(
				0 => 'to_id',
			),
		),
		'ind_fromto_id' => array(
			'columns' => array(
				0 => 'from_id',
				1 => 'to_id',
			),
		),
		'ind_addtime' => array(
			'columns' => array(
				0 => 'addtime',
			),
		),
	),
	'engine' => 'innodb',
	'version' => '$Rev: 1 $',
	'comment' => app::get('storelist')->_('门店职员与平台职员互动记录表'),
);