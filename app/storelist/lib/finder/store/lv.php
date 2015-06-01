<?php
class storelist_finder_store_lv{
	var $column_control = '操作';
	public function  __construct($app){
		$this->app=$app;
	}
	public function column_control($row){
		//当前登录用户的ID
		$user_id=$_SESSION['account']['user_data']["user_id"];
		$is_super=app::get('desktop')->model('users')->dump(intval($user_id));
		
		//查询当前门店店主的角色
		$store_role=app::get('storelist')->model('storelist_relat')->getRow("*",array('oper_id'=>(int)$user_id));
		
		$store_roles=app::get('storelist')->model('storelist_roles')->getList("role_id",array('stores_id'=>(int)$store_role['stores_id']));
		$role_id=app::get('desktop')->model('hasrole')->getRow("*",array('user_id'=>intval($store_role['oper_id'])));
		//$r_ids=app::get('desktop')->getconf('default_store_roles');
		//获取对象设置的公共属性
		$storeObj=kernel::single('storelist_store');
		
		$r_ids=app::get('desktop')->getconf($storeObj::$store_owner_conf);
		$roles_id=app::get('desktop')->model('hasrole')->getRow("*",array('user_id'=>intval($user_id)));
		$r_id=app::get('storelist')->model('storelist_roles')->getRow("*",array('role_id'=>intval($roles_id['role_id'])));
		if($store_roles){
			foreach($store_roles as $s){
				$n_role_ids[]=$s['role_id'];
			}
		}
		if(!empty($n_role_ids)){
			$u_ids=kernel::database()->select("SELECT * FROM ".kernel::database()->prefix."desktop_hasrole WHERE role_id IN(".implode(",",$n_role_ids).")");
		}
		if($u_ids){
			foreach($u_ids as $u){
				$uu_id[]=$u['user_id'];
			}
		}
		
		
		if($is_super['super']==1){
			$str  =  '<a href="index.php?app=storelist&ctl=admin_store_lv&act=edit&p[0]='.$row['store_lv_id'].'&finder_id='.$_GET['_finder']['finder_id'].'" target="dialog::{title:\''.app::get('storelist')->_('编辑').'\'}">'.app::get('storelist')->_('编辑').'</a>';
		}else if(intval($roles_id['role_id'])!=intval($r_ids) && empty($r_id)){
			$str  =  '<a href="index.php?app=storelist&ctl=admin_store_lv&act=edit&p[0]='.$row['store_lv_id'].'&finder_id='.$_GET['_finder']['finder_id'].'" target="dialog::{title:\''.app::get('storelist')->_('编辑').'\'}">'.app::get('storelist')->_('编辑').'</a>';
		}else{
			$str='';
		}
		
		
		return $str;
	}
}
?>