<?php
/**
 * 新闻表
 * @author qianzedong <qianzedong@shopex.cn>
 */

$db['news'] = array(
	'columns' => array(
		'news_id' => array(
			'type' => 'bigint unsigned',
			'required' => true,
			'pkey' => true,
			'extra' => 'auto_increment',
			'label' => 'ID',
			'comment' => app::get('storelist')->_('ID'),
			'hidden' => true,
			'in_list' => false,
		),
		'user_id' => array(
			'type' => 'table:users@desktop',
			'label' => '发送人',
			'comment' => app::get('storelist')->_('添加人'),
			'default' => 0,
			'width' => '100',
			'in_list' => false,
		),
		'title' => array(
			'type' => 'varchar(50)',
			'required' => true,
			'label' => '信息',
			'comment' => app::get('storelist')->_('标题'),
			'width' => '400',
			'in_list' => true,
			'default_in_list' => true,
		),
		'contents' => array(
			'type' => 'text',
			'required' => true,
			'label' => '内容',
			'comment' => app::get('storelist')->_('内容'),
			'in_list' => false,
		),
		'addtime' => array(
			'type' => 'time',
			'label' => app::get('storelist')->_('时间'),
			'comment' => app::get('storelist')->_('时间'),
			'width' => '200',
			'in_list' => true,
			'default_in_list' => true,
		),
	),
	'index' => array(
	),
	'engine' => 'innodb',
	'version' => '$Rev: 1 $',
	'comment' => app::get('storelist')->_('门店新闻表'),
);