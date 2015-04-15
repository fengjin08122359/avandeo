<?php
$setting['author']='shopex';
$setting['version']='1.0';
$setting['name']=app::get('b2c')->_('★自定义链接');
$setting['stime']='2015-03-23';

//,product,goods:act,
//$setting['scope']=array('');
$setting['catalog']=app::get('b2c')->_('辅助信息');

$setting['usual']    = '1';

$setting['description']    =''.app::get('b2c')->_('自定义链接');

$setting['template'] = array(
	'default.html'=>app::get('b2c')->_('默认')
);

?>
