<?php

class designer_finder_members {
	var $column_edit    = '编辑';
	var $column_addcase = '添加案例';
	function column_edit($row) {
		return '<a href="index.php?app=designer&ctl=admin_members&act=edit&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&p[0]='.$row['member_id'].'" target="dialog::{title:\''.app::get('b2c')->_('编辑设计师资料').'\', width:680, height:450}">'.app::get('b2c')->_('编辑').'</a>';
	}
	function column_addcase($row) {
		return '<a href="index.php?app=designer&ctl=admin_case&act=add&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&p[0]='.$row['member_id'].'" target="dialog::{title:\''.app::get('b2c')->_('案例详细').'\', width:680, height:450}">'.app::get('b2c')->_('添加案例').'</a>';
	}

}
?>
