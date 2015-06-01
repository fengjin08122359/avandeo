<?php
class storelist_ctl_admin_store_lv extends desktop_controller{
	function __construct($app){
		parent::__construct($app);
		$this->tablePre=kernel::database()->perfix;
	}
	public function index(){
		//获取当前登录用户的ID
		$user_id=$_SESSION['account']['user_data']["user_id"];
		$is_super=app::get('desktop')->model('users')->dump(intval($user_id));
		$store_role=app::get('storelist')->model('storelist_relat')->getRow("*",array('oper_id'=>(int)$user_id));
		$role_id=app::get('desktop')->model('hasrole')->getRow("*",array('user_id'=>intval($store_role['oper_id'])));
		$r_ids=app::get('desktop')->getconf('default_store_roles');
		$roles_id=app::get('desktop')->model('hasrole')->getRow("*",array('user_id'=>intval($user_id)));
		$r_id=app::get('storelist')->model('storelist_roles')->getRow("*",array('role_id'=>intval($roles_id['role_id'])));
		if($store_roles){
			foreach($store_roles as $s){
				$n_role_ids[]=$s['role_id'];
			}
		}
		if(!empty($n_role_ids)){
			$u_ids=kernel::database()->select("SELECT * FROM ".$this->tablePre."desktop_hasrole WHERE role_id IN(".implode(",",$n_role_ids).")");
		}
		if($u_ids){
			foreach($u_ids as $u){
				$uu_id[]=$u['user_id'];
			}
		}
		if(count($uu_id)>1 || intval($role_id['role_id'])==intval($r_ids)){
			$arr=array(
					'title'=>app::get('storelist')->_('销售等级'),
					
					'base_filter'=>$filter,
					'use_buildin_export'=>false,
					'use_buildin_recycle'=>false
			);
		}else if($is_super['super']==1){
			$arr=array(
            'title'=>app::get('storelist')->_('销售等级'),
            'actions'=>array(
                         array('label'=>app::get('b2c')->_('添加销售等级'),'href'=>'index.php?app=storelist&ctl=admin_store_lv&act=addnew','target'=>'dialog::{width:680,height:250,title:\''.app::get('b2c')->_('添加销售等级').'\'}'),
                        ),
			
            );
		}else if(intval($roles_id['role_id'])!=intval($r_ids) && empty($r_id)){
			
			$arr=array(
            'title'=>app::get('storelist')->_('销售等级'),
            'actions'=>array(
                         array('label'=>app::get('b2c')->_('添加销售等级'),'href'=>'index.php?app=storelist&ctl=admin_store_lv&act=addnew','target'=>'dialog::{width:680,height:250,title:\''.app::get('b2c')->_('添加销售等级').'\'}'),
                        )
            );
		}else{
			$arr=array(
					'title'=>app::get('storelist')->_('销售等级'),
					
					'base_filter'=>$filter,
					'use_buildin_export'=>false,
					'use_buildin_recycle'=>false
			);
		}
		
		$this->finder('storelist_mdl_store_lv',$arr);
	}
	/**
	 * 
	 * 编辑销售等级
	 * @param unknown $stroe_lv_id
	 */
	public  function  edit($stroe_lv_id){
		$this->pagedata['row']=app::get('storelist')->model('store_lv')->dump(intval($stroe_lv_id));
		
		$this->display('admin/store/lv.html');
	}
	/*
	 * 
	 * 添加销售等级
	 */
	public function addnew(){
		$this->display('admin/store/lv.html');
	}
	/**
	 * 
	 * 保存数据
	 */
	public function save(){
		
		$this->begin();
		if(!isset($_POST['store_lv_id']) || $_POST['store_lv_id']==''){
			//insert
			if((int)$_POST['small_value']>(int)$_POST['max_value']){
				$this->end(false,$this->app->_('最小值不能大于最大值'));
			}
			$flag=$this->app->model('store_lv')->insert($_POST);
		}else{
			if($_POST['small_value']>$_POST['max_value']){
				$this->end(false,$this->app->_('最小值不能大于最大值'));
			}
			//update
			$flag=$this->app->model('store_lv')->update($_POST,array('store_lv_id'=>intval($_POST['store_lv_id'])));
		}
		if($flag){
			$msg=$this->app->_('操作成功');
		}else{
			$msg=$this->app->_('操作失败');
		}
		$this->end($flag,$msg);
	}
	
}
?>