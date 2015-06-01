<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/**
 * mdl_user
 *
 * @uses modelFactory
 * @package
 * @version $Id: mdl.user.php 1985 2008-04-28 06:36:02Z flaboy $
 * @copyright 2003-2007 ShopEx
 * @author Likunpeng <leoleegood@zovatech.com>
 * @license Commercial
 */
class storelist_mdl_storelist extends dbeav_model{
	/* 重写搜索的下拉选项方法
	* @param null
	* @return null
	*/
	public function searchOptions(){
		$columns = array();
	
		foreach($this->_columns() as $k=>$v)
		{
			if(isset($v['searchtype']) && $v['searchtype'])
			{
				$columns[$k] = $v['label'];
			}
		}
	
		$columns = array_merge(array(
				'store_name'=>app::get('storelist')->_('门店名称'),
				//'name'=>app::get('sysitem')->_('号码'),
				
		),$columns);
	
		return $columns;
	}
	/**
	 * 
	 * 重写删除方法 关联门店主的不能删除
	 * @param unknown $data
	 */
	public function pre_recycle($data){
		$storeRelatObj=$this->app->model('storelist_relat');
		$store=$storeRelatObj->getList("stores_id");
		if($store){
			foreach($store as $v){
				$stores_id[]=$v['stores_id'];
			}
		}
		 foreach($data as $d){
			if(in_array($d['store_id'],$stores_id)){
				$this->recycle_msg = $this->app->_('已经关联门店主,不能删除');
				return false;
			}
		} 
		return true;
	}
}
?>