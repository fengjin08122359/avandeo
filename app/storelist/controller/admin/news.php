<?php
/**
 * 门店新闻或公告
 * @author qianzedong <qianzedong@shopex.cn>
 */
class storelist_ctl_admin_news extends desktop_controller{
	
	/**
	 * 新闻列表
	 */
	public function index()
	{
		$actions = array();
		$finder = array(
	        'title'=>app::get('storelist')->_('新闻公告'),
	        'use_buildin_recycle'=>false,
		);

		$store = kernel::single('storelist_store');
		if(!$store->store_id)
		{
			$actions[] = array('label'=>app::get('storelist')->_('添加'),'icon'=>'add.gif','href'=>'index.php?app=storelist&ctl=admin_news&act=handle','target'=>'dialog::{title:\'添加\', width:980, height:600}');
        	$finder['use_buildin_recycle'] = true;
        }

        $finder['actions'] = $actions;
        $this->finder('storelist_mdl_news',$finder);
	}

	/**
	 * 查看新闻
	 * @param  int $news_id 新闻ID
	 */
	public function view($news_id)
	{
		$news = $this->app->model('news')->dump($news_id);
		$news['addtime'] = date('Y/m/d H:i',$news['addtime']);
		$this->pagedata['news'] = $news;
		$this->singlepage('admin/news/view.html');
	}

	/**
	 * 新闻操作
	 */
	public function handle($news_id = 0)
	{
		$storelist_newsMdl = $this->app->model('news');
		$this->begin();
		if($_POST)
		{
			$sdf = $_POST;
			$sdf['addtime'] = $_SERVER['REQUEST_TIME'];
			if($this->verfiy($sdf))
			{
				$res = $storelist_newsMdl->save($sdf);
				if($res)
				{
					$this->end(true,app::get('storelist')->_('成功'));
				}else{
					$this->end(false,app::get('storelist')->_('失败'));
				}
			}else{
				$this->end(false,app::get('storelist')->_('验证失败'));
			}
		}
		if($news_id > 0)
		{
			$this->pagedata['news'] = $storelist_newsMdl->dump($news_id);
		}
        $this->page('admin/news/handle.html');
	}

	private function verfiy($sdf)
	{
		return true;
	}

}