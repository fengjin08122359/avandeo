<?php
/**
 * 线下体验店前台页面
 * @author	qianzedong	qianzedong@shopex.cn
 */
class b2c_ctl_site_store extends b2c_frontpage{
	public function __construct($app)
	{
		parent::__construct($app);
		//面包屑
		$GLOBALS['runtime']['path'][] = array('link'=>$this->gen_url(array('app'=>'b2c','ctl'=>'site_default')),'title'=>'首页');
		$GLOBALS['runtime']['path'][] = array('link'=>$this->gen_url(array('app'=>'b2c','ctl'=>'site_store')),'title'=>$this->app->_('线下体验店'));
	}
	/**
	 * 线下体验店列表页
	 * @param $page 分页
	 */
	public function index()
	{
		$filter = array();
		$store_data = $this->app->model('store')->getList('*',$filter);
		$this->_handle($store_data);
		$this->pagedata['store_data'] = $store_data;
		$this->set_tmpl('store_list');
        $this->page('site/store/list.html');
	}
	public function map($city,$district,$address)
	{
		$this->pagedata['map_data'] = array(
			'city'=>$city,
			'district'=>$district,
			'address'=>$address
		);
		$this->display('site/store/map.html');
	}
	/**
	 * 处理列表信息
	 * @param $store_data 体验店列表
	 */
	private function _handle(&$store_data)
	{
		foreach($store_data as &$v)
		{
			$area = explode(':',$v['area']);
			$v['area_num'] = $area[2];
			$area = explode('/',$area[1]);
			$v['province'] = $area[0];
			$province = app::get('ectools')->model('regions')->dump(array('local_name'=>$v['province']));
			$v['province_num'] = $province['region_id'];
			$v['city'] = $area[1];
			$city = app::get('ectools')->model('regions')->dump(array('local_name'=>$v['city']));
			$v['city_num'] = $city['region_id'];
			$v['district'] = $area[2];
			$city = app::get('ectools')->model('regions')->dump(array('local_name'=>$v['district']));
			$v['district_num'] = $city['region_id'];
		}
	}
	/**
	 * 线下体验店内容页
	 * @param $store_id 店铺ID
	 */
	public function glist($store_id)
	{
		$store_data = $this->app->model('store')->dump($store_id);
		$store_data_list = array(0=>$store_data);
		$this->_handle($store_data_list);
		$store_data = $store_data_list[0];
		$this->pagedata['store_data'] = $store_data;
		$GLOBALS['runtime']['path'][] = array('link'=>$this->gen_url(array('app'=>'b2c','ctl'=>'site_store','act'=>'glist','arg0'=>$store_data['store_id'])),'title'=>$this->app->_($store_data['name']));
		$this->set_tmpl('store');
        $this->page('site/store/store.html');
	}
}