<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License */

class b2c_ctl_site_cat extends b2c_frontpage{

    function __construct($app){
        parent::__construct($app);
        $shopname = app::get('site')->getConf('site.name');
        $this->shopname = $shopname;
        $this->set_tmpl('brandlist');
        if(isset($shopname)){
            $this->title = app::get('b2c')->_('分类页').'_'.$shopname;
            $this->keywords = app::get('b2c')->_('分类页').'_'.$shopname;
            $this->description = app::get('b2c')->_('分类页').'_'.$shopname;
        }

    }

    public function index($cat_id=0) {
		$_obj_goods_cat = $this->app->model('goods_cat');
		$_arr_goods_cat = $_obj_goods_cat->getList('*',array('cat_id'=>$cat_id));
		
		if ($_arr_goods_cat[0]['tmpl_path']){
			$this->set_tmpl_file($_arr_goods_cat[0]['tmpl_path']);
		}
		
		$default_view = $view?$view:$this->app->getConf('gallery.default_view');            
		$_arr_goods_cat[0]['link'] = app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_gallery','args'=>array($_arr_goods_cat[0]['cat_id'],$default_view) ));
            
		$this->pagedata['category'] = $_arr_goods_cat[0];
		$this->set_tmpl('cat_index');
        $this->page('site/category/index.html');

    }
}

