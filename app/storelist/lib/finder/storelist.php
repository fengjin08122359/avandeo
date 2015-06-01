<?php
class storelist_finder_storelist{
	var $column_control = '操作';
	var $detail_basic = '查看';
	public function  __construct($app){
		$this->app=$app;
	}
	public function column_control($row){
		//当前登录用户的ID
		$user_id=$_SESSION['account']['user_data']['user_id'];
		$role_id=app::get('desktop')->model('hasrole')->getRow("*",array('user_id'=>intval($user_id)));
		//获取对象设置的公共属性
		$storeObj=kernel::single('storelist_store');
		$setconf_role_id=app::get('desktop')->getconf($storeObj::$store_owner_conf);
		
		//if(!empty($role_id)){
			if(intval($role_id['role_id'])!=intval($setconf_role_id)){
				$str  =  '<a href="index.php?app=storelist&ctl=admin_storelist&act=addstore&p[0]='.$row['store_id'].'&finder_id='.$_GET['_finder']['finder_id'].'" target="dialog::{title:\''.app::get('storelist')->_('编辑门店').'\'}">'.app::get('storelist')->_('编辑').'</a>';
			}
		//}
		
		return $str;
	}
	function detail_basic($row){
		
		$render=$this->app->render();
		$storelistObj=$this->app->model('storelist');
		$store_id=intval($row);
		$reg_id=$storelistObj->dump($store_id);
		$reg_unserialize=unserialize($reg_id['r_id']);
		if($reg_unserialize){
			foreach($reg_unserialize as $v){
				$reg_ids[]=$v;
			}
		}
		$reg_arr=kernel::database()->select("SELECT region_id,local_name,p_region_id FROM `".kernel::database()->prefix."ectools_regions` WHERE region_id IN(".implode(",",$reg_ids).")");
		if($reg_arr){
			foreach($reg_arr as $k=>$val){
				//$p_reg_arr[$val['local_name']]=kernel::database()->selectRow("SELECT region_id,local_name,p_region_id FROM `".kernel::database()->prefix."ectools_regions` WHERE region_id={$val['p_region_id']}");
				//T=$p_reg_arr[$val['local_name']]['region_id'];
				$a=kernel::database()->selectRow("SELECT region_id,local_name,p_region_id FROM `".kernel::database()->prefix."ectools_regions` WHERE region_id={$val['p_region_id']}");
				//var_dump($a['p_region_id']);
				$one_area=kernel::database()->selectRow("SELECT region_id,local_name,p_region_id FROM `".kernel::database()->prefix."ectools_regions` WHERE region_id={$a['p_region_id']} AND p_region_id IS NULL");
				$p_reg_arr[$val['local_name']]['one'][] = $a['local_name']; 
				$p_reg_arr[$val['local_name']]['two'][] = $one_area['local_name'];
			}
		}
		
		$render->pagedata['area_list']=$p_reg_arr;
		return $render->fetch('admin/storelist/store_detail.html');
	}
}
?>