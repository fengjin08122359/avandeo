<?php

class designer_finder_case {
	var $column_edit = '编辑';
	function column_edit($row) {
		return '<a href="index.php?app=designer&ctl=admin_case&act=edit&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&p[0]='.$row['case_id'].'" target="dialog::{title:\''.app::get('b2c')->_('编辑设计师案例').'\', width:680, height:450}">'.app::get('b2c')->_('编辑').'</a>';
	}

}
?>
