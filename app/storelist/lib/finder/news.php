<?php
/**
 * 新闻公告的finder
 */
class storelist_finder_news
{
	public $column_edit = "编辑";
	public $column_view = "查看";

	public function __construct($app)
    {
        $this->app = $app;
        $store = kernel::single('storelist_store');
        if($store->store_id > 0)
        {
            $this->column_edit = null;
        }
    }

    /**
     * 编辑
     */
    public function column_edit($row)
    {
    	$string = '<a target="dialog::{title:\''.$this->column_edit.'\', width:980, height:600}" href="index.php?app=storelist&ctl=admin_news&act=handle&p[0]='.$row['news_id'].'&finder_id='.$_GET['_finder']['finder_id'].'">'.$this->column_edit.'</a>';
    	return $string;
    }

    /**
     * 查看
     */
    public function column_view($row)
    {
    	$string = '<a target="_blank" href="index.php?app=storelist&ctl=admin_news&act=view&p[0]='.$row['news_id'].'">'.$this->column_view.'</a>';
    	return $string;
    }

}