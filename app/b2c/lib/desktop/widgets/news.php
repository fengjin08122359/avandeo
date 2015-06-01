<?php
/**
 * 新闻公告桌面挂件
 * @author qianzedong <qianzedong@shopex.cn>
 */
class b2c_desktop_widgets_news implements desktop_interface_widget{
	public $order = 1;

    public function __construct($app){
        $this->app = $app;
        $this->render =  new base_render(app::get('b2c'));
    }
    public function get_title(){
        return app::get('b2c')->_('新闻公告');
    }
    public function get_html(){
    	$storelist_newsMdl = app::get('storelist')->model('news');
    	$news = $storelist_newsMdl->getList('*',array(),0,10,' news_id DESC ');
    	foreach ($news as &$val) {
    		$val['addtime'] = date('Y/m/d H:i',$val['addtime']);
    	}
    	$this->render->pagedata['news'] = $news;
    	return $this->render->fetch('desktop/widgets/news.html');
    }
    public function get_className(){
          return " valigntop news";
    }

    public function get_width(){
          return "l-1";
    }
}