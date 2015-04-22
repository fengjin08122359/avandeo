<?php
/**
 * store finder
 * @author qianzedong <qianzedong@shopex.cn>
 */
class b2c_finder_store
{
	public $column_edit = '';
	function __construct($app){
		$this->app = $app;
		$this->column_edit = app::get('b2c')->_('编辑');
	}
	public function column_edit($row)
	{
		return '<a href="index.php?app=b2c&ctl=admin_store&act=edit&p[0]='.$row['store_id'].'&_finder[finder_id]='.$_GET['_finder']['finder_id'].'" target="dialog::{title:\''.app::get('b2c')->_('编辑').'\',width:900,height:600}">'.app::get('b2c')->_('编辑').'</a>';
	}
}