<?php
class storelist_ctl_admin_analysis extends desktop_controller{
	function __construct($app){
		parent::__construct($app);
		$this->tablePre=kernel::database()->prefix;
	}
	 public function index(){
		$user_id=$_SESSION['account']['user_data']['user_id'];
		
		$u_id=app::get('desktop')->model('users')->getRow("*",array('user_id'=>intval($user_id)));
		if($u_id){
			$us_id=app::get('storelist')->model('storelist_relat')->getRow("*",array('oper_id'=>intval($user_id)));
		}
		$s_list=app::get('storelist')->model('storelist')->getRow("*",array('store_id'=>(int)$us_id['stores_id']));
		
		$role_id=app::get('desktop')->model('hasrole')->getRow("*",array('user_id'=>intval($user_id)));
		//获取对象设置的公共属性
		$storeObj=kernel::single('storelist_store');
		$default_store_role=app::get('desktop')->getconf($storeObj::$store_owner_conf);
		$r_id=app::get('storelist')->model('storelist_roles')->getRow("*",array('role_id'=>intval($role_id['role_id'])));
		//获取门店主
		$store_list=app::get('storelist')->model('storelist')->getList("store_id,store_name");
		$this->pagedata['us_id']=$us_id;
		$this->pagedata['u_id']=$u_id;
		$this->pagedata['role_id']=intval($role_id['role_id']);
		$this->pagedata['default_store_role']=intval($default_store_role);
		$this->pagedata['r_id']=$r_id;
		$this->pagedata['store_list']=$store_list;
		$this->pagedata['store_id']=isset($_POST['store_id'])?(int)$_POST['store_id']:intval($us_id['stores_id']);
		$this->page('admin/store/statis.html');
	} 
	
}
?>