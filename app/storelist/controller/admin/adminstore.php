<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class storelist_ctl_admin_adminstore extends desktop_controller{
	var $workground = 'storelist_ctl_system';
	public function __construct($app)
	{
		
		parent::__construct($app);
		$this->tablePre=kernel::database()->prefix;
		$this->obj_roles = kernel::single('desktop_roles');
        header("cache-control: no-store, no-cache, must-revalidate");
	}
	function index(){
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
			$u_ids=kernel::database()->select("SELECT * FROM ".$this->tablePre."desktop_hasrole WHERE role_id IN(".implode(",",$n_role_ids).")");
		}
		if($u_ids){
			foreach($u_ids as $u){
				$uu_id[]=$u['user_id'];
			}
		}
		if(count($uu_id)>1 || intval($role_id['role_id'])==intval($r_ids)){
			
			//如果需要显示自己的用户只需把下面的注释打开即可
			//array_push($uu_id,$user_id);
				
			$filter = array('filter_sql'=>" user_id IN(".implode(",",$uu_id).") ");
			$title='添加角色';
			$t_t='职员管理';
			$a=array(
					'title'=>app::get('desktop')->_($t_t),
					'actions'=>array(
							//array('label'=>app::get('storelist')->_($title),'href'=>'index.php?app=storelist&ctl=admin_adminstore&act=addroleof','target'=>'dialog::{title:\''.app::get('storelist')->_($title).'\'}'),
							array('label'=>app::get('storelist')->_('添加职员'),'href'=>'index.php?app=storelist&ctl=admin_adminstore&act=addstaff','target'=>'dialog::{title:\''.app::get('storelist')->_('添加职员').'\'}'),
					),
					'base_filter'=>$filter,
					'use_buildin_export'=>false,
					'use_buildin_recycle'=>false
			);
		}else if($is_super['super']==1 ){
			$storelist_ids=app::get('desktop')->model('hasrole')->getList("*",array('role_id'=>intval($r_ids)));
			if($storelist_ids){
				foreach($storelist_ids as $is){
					$st_ids[]=$is['user_id'];
				}
			}
			
			$filter=array('filter_sql'=>" user_id IN(".implode(",",$st_ids).") ");
			$title='添加门店主';
			$t_t='门店主管理';
			$a=array(
					'title'=>app::get('desktop')->_($t_t),
					'actions'=>array(
							array('label'=>app::get('storelist')->_($title),'href'=>'index.php?app=storelist&ctl=admin_adminstore&act=addnew','target'=>'dialog::{title:\''.app::get('storelist')->_($title).'\'}'),
					),
					'base_filter'=>$filter,
					'use_buildin_export'=>false,
					//'use_buildin_recycle'=>false
			);
		}else if(intval($roles_id['role_id'])!=intval($r_ids) && empty($r_id)){
			$storelist_ids=app::get('desktop')->model('hasrole')->getList("*",array('role_id'=>intval($r_ids)));
			if($storelist_ids){
				foreach($storelist_ids as $is){
					$st_ids[]=$is['user_id'];
				}
			}
			
			$filter=array('filter_sql'=>" user_id IN(".implode(",",$st_ids).") ");
			$title='添加门店主';
			$t_t='门店主管理';
			$a=array(
					'title'=>app::get('desktop')->_($t_t),
					'actions'=>array(
							array('label'=>app::get('storelist')->_($title),'href'=>'index.php?app=storelist&ctl=admin_adminstore&act=addnew','target'=>'dialog::{title:\''.app::get('storelist')->_($title).'\'}'),
					),
					'base_filter'=>$filter,
					'use_buildin_export'=>false,
					//'use_buildin_recycle'=>false
			);
		}else{
			$filter=array('filter_sql'=>" user_id =".$user_id."");
			$title='添加角色';
			$t_t='职员管理';
			$a=array(
					'title'=>app::get('desktop')->_($t_t),
					/* 'actions'=>array(
							array('label'=>app::get('storelist')->_($title),'href'=>'index.php?app=storelist&ctl=admin_adminstore&act=addroleof','target'=>'dialog::{title:\''.app::get('storelist')->_($title).'\'}'),
							array('label'=>app::get('storelist')->_('添加职员'),'href'=>'index.php?app=storelist&ctl=admin_adminstore&act=addstaff','target'=>'dialog::{title:\''.app::get('storelist')->_('添加职员').'\'}'),
					), */
					'base_filter'=>$filter,
					'use_buildin_export'=>false,
					'use_buildin_recycle'=>false
			);
		}
		
		
		$this->finder('desktop_mdl_users',$a);
	}
	/**
	 * 
	 * 店主添加角色
	 */
	function addroleof(){
		//获取对象设置的公共属性
		$storeObj=kernel::single('storelist_store');
		$roles_id=app::get('desktop')->getconf($storeObj::$store_owner_conf);
		$w=app::get('desktop')->model('roles')->dump(intval($roles_id));
		$workground=unserialize($w['workground']);
		
		$workgrounds = app::get('desktop')->model('menus')->getList('*',array('menu_type'=>'workground','disabled'=>'false','display'=>'true'));
		$this->pagedata['workgrounds'] = $workgrounds;
		$widgets = app::get('desktop')->model('menus')->getList('*',array('menu_type'=>'widgets'));
		$this->pagedata['widgets'] = $widgets;
		foreach($workgrounds as $k => $v)
		{
			$workgrounds[$k]['permissions'] = $this->obj_roles->get_permission_per($v['menu_id'],array());
		}
		
		$this->pagedata['workgrounds'] = $workgrounds;
		$this->pagedata['adminpanels'] = $this->obj_roles->get_adminpanel(null,array());
		
		//$this->pagedata['others'] = $this->obj_roles->get_others();
		
		//桌面挂件权限
		$html1 = '';
		foreach($this->pagedata['widgets'] as $key1=>$val1){
			if($val1['checked']){
				$html1 .= "<li style='padding-left:25px;text-align:left;'><input  class='leaf ' type='checkbox' checked='checked' name='workground[]' value=".$val1['addon'].">".$val1['menu_title']."</li>";
				//$checkall = true;
			}else{
				
				foreach($workground as $w1){
					
						if($w1==$val1['addon']){
							$html1 .= "<li style='padding-left:25px;text-align:left;'><input   class='leaf ' type='checkbox' checked='checked'  name='workground[]' value=".$val1['addon'].">".$val1['menu_title']."</li>";
						}
					}
					//$checkall = false;
			}
		}
		$this->pagedata['menus1'] = "<ul><li>".$html1."</li></ul>";
		
		//控制面板权限
		$html2 = '';
		foreach($this->pagedata['adminpanels'] as $key2=>$val2){
			if($val2['checked']){
				$html2 .= "<li style='padding-left:25px;text-align:left;'><input  class='leaf ' type='checkbox' checked='checked' name='workground[]' value=".$val2['permission'].">".$val2['menu_title']."</li>";
				//$checkall = true;
			}else{
				
				foreach($workground as $w2){
					
					if($w2==$val2['permission']){
						$html2 .= "<li style='padding-left:25px;text-align:left;'><input checked='checked'  class='leaf ' type='checkbox' name='workground[]' value=".$val2['permission'].">".$val2['menu_title']."</li>";
					}
				}
					
				//$checkall = false;
			}
		}
		$this->pagedata['menus2'] = "<ul><li>".$html2."</li></ul>";
		
		//业务权限
		$treedata=array();
		foreach($this->pagedata['workgrounds'] as $key3=>$val3){
			$mgrpname['mgrpname'][] = $val3['menu_title'];
			$treedata[] = $this->getTree($val3['permissions'],'0');
		}
		foreach($treedata as $kmgrp=>$vmgrp){
			$treedata[$kmgrp][0]['mgrpname'] = $mgrpname['mgrpname'][$kmgrp];
		}
		foreach($treedata as $item){
			$html = $this->procHTML($item,$workground);
			$this->pagedata['menus3'][]= $html['html'];
		}
		
		/*其他权限
		 #$vv3 = $this->getTree($this->pagedata['others'],'0');
		#$base_v3 = array('property'=>array('name'=>'其他', 'hasCheckbox'=>false), 'children'=>$vv3);
		*/
		
		$this->page('admin/storelist/role_add.html');
	}
	/**
	 * 
	 * 店主添加职员
	 */
	public function  addstaff(){
	 $roles = app::get('desktop')->model('roles');
        $users = app::get('desktop')->model('users');
        if($_POST){
            $this->begin('index.php?app=storelist&ctl=admin_adminstore&act=index');
            if($users->validate($_POST,$msg)){
                if($_POST['super']==0 && (!$_POST['role'])){
                    $this->end(false,app::get('desktop')->_('请至少选择一个工作组'));
                }
                elseif($_POST['super'] == 0 && ($_POST['role'])){
                    foreach($_POST['role'] as $roles)
                    $_POST['roles'][]=array('role_id'=>$roles);
                }
                $_POST['pam_account']['createtime'] = time();
                $use_pass_data['login_name'] = $_POST['pam_account']['login_name'];
                $use_pass_data['createtime'] = $_POST['pam_account']['createtime'];
                $_POST['pam_account']['login_password'] = pam_encrypt::get_encrypted_password($_POST['pam_account']['login_password'],pam_account::get_account_type($this->app->app_id),$use_pass_data);
                //$_POST['pam_account']['account_type'] = pam_account::get_account_type($this->app->app_id);
                $_POST['pam_account']['account_type']='shopadmin';
                if($users->save($_POST)){
                    foreach(kernel::servicelist('desktop_useradd') as $key=>$service){
                        if($service instanceof desktop_interface_useradd){
                        	
                            $service->useradd($_POST);
                        }
                    }
                    if($_POST['super'] == 0){   //是超管就不保存
                        $this->save_ground($_POST);
                    }
                    $this->end(true,app::get('desktop')->_('保存成功'));
                }else{
                        $this->end(false,app::get('desktop')->_('保存失败'));
                    }
                
            }
            else{
                $this->end(false,__($msg));
            }   
        }   
        else{
        	$user_id=(int)$_SESSION['account']['user_data']['user_id'];
        	//根据user_id获取门店id
        	$st_arr=$this->app->model('storelist_relat')->getRow("*",array('oper_id'=>$user_id));
        	if($st_arr)$store_id=intval($st_arr['stores_id']);
            
           //根据门店id获取有那些职员
           	$staff_arr=$this->app->model('storelist_roles')->getList("*",array('stores_id'=>$store_id));
           	if($staff_arr){
           		foreach($staff_arr as $r){
           			$n_role_ids[]=$r['role_id'];
           		}
           	}
           	$workgroup=kernel::database()->select("SELECT * FROM ".$this->tablePre."desktop_roles WHERE role_id IN(".implode(",",$n_role_ids).")");
            
           	$this->pagedata['workgroup']=$workgroup; 
            $this->display('admin/storelist/staff_add.html');
        }   
	}
	function addnew(){
		$roles = app::get('desktop')->model('roles');
		$users = app::get('desktop')->model('users');
		if($_POST){
			
			$this->begin('index.php?app=storelist&ctl=admin_adminstore&act=index');
			if($users->validate($_POST,$msg)){
			if($_POST['super']==0 && (!$_POST['role'])){
                    $this->end(false,app::get('desktop')->_('请至少选择一个工作组'));
                }
                elseif($_POST['super'] == 0 && ($_POST['role'])){
                    foreach($_POST['role'] as $roles)
                    $_POST['roles'][]=array('role_id'=>$roles);
                }
				$_POST['pam_account']['createtime'] = time();
				$use_pass_data['login_name'] = $_POST['pam_account']['login_name'];
				$use_pass_data['createtime'] = $_POST['pam_account']['createtime'];
				$_POST['pam_account']['login_password'] = pam_encrypt::get_encrypted_password($_POST['pam_account']['login_password'],pam_account::get_account_type($this->app->app_id),$use_pass_data);
				//$_POST['pam_account']['account_type'] = pam_account::get_account_type($this->app->app_id);
				$_POST['pam_account']['account_type']='shopadmin';
				if(isset($_POST['store_id'])&& $_POST['store_id']==''){
					$this->end(false,$this->app->_('请选择门店'));
				}
				//获取对象设置的公共属性
				$storeObj=kernel::single('storelist_store');
				$workground=app::get('desktop')->getconf($storeObj::$store_owner_conf);
				$role_id=$workground;
				
				if($users->save($_POST)){
					 foreach(kernel::servicelist('desktop_useradd') as $key=>$service){
						
						if($service instanceof desktop_interface_useradd){
							$service->useradd($_POST);
						}
					} 
					
					
					//app::get('desktop')->model('hasrole')->save($roles_array);
					$users_id=(int)$_SESSION['account']['user_data']["user_id"];//查询当前登录的用户ID
					//如果当前登录的用户ID是设置过的门店ID则写入sdb_storelist_storelist_roles表中
					
					
					$roles_id=kernel::database()->selectRow("SELECT u.user_id,h.user_id,h.role_id FROM `sdb_desktop_users` AS u LEFT JOIN `sdb_desktop_hasrole` AS h ON u.user_id=h.user_id WHERE u.user_id={$users_id}");
					if((int)$roles_id['role_id']==(int)$role_id){
						$u_id=app::get('storelist')->model('storelist_relat')->getRow("*",array('oper_id'=>(int)$users_id));
						$role_array=array(
							'stores_id'=>(int)$u_id['stores_id'],
							'roles_id'=>(int)$_POST['user_id'],
						);
						app::get('storelist')->model('storelist_roles')->save($role_array);
					}else{
						$relat_array=array(
								'stores_id'=>$_POST['store_id'],
								'oper_id'=>(int)$_POST['user_id']
						);
						app::get('storelist')->model('storelist_relat')->save($relat_array);
					}
					
					
					
					if($_POST['super'] == 0){   //是超管就不保存
						$this->save_ground($_POST);
					}
					$this->end(true,app::get('storelist')->_('保存成功'));
				}else{
					$this->end(false,app::get('storelist')->_('保存失败'));
				}
	
			}
			else{
				$this->end(false,__($msg));
			}
		}
		else{
			$users_id=(int)$_SESSION['account']['user_data']["user_id"];//查询当前登录的用户ID
			
				
			$relatObj=$this->app->model('storelist_relat')->getList('stores_id');	
			$roles_id=kernel::database()->selectRow("SELECT u.user_id,h.user_id,h.role_id FROM `sdb_desktop_users` AS u LEFT JOIN `sdb_desktop_hasrole` AS h ON u.user_id=h.user_id WHERE u.user_id={$users_id}");
			if($relatObj){
				foreach($relatObj as $r){
					$store_id[]=$r['stores_id'];
				}
			}
			//获取对象设置的公共属性
			$storeObj=kernel::single('storelist_store');
			$workground=app::get('desktop')->getconf($storeObj::$store_owner_conf);
			if((int)$workground!=(int)$roles_id['role_id']){
			
				$store_list=app::get('storelist')->model('storelist')->getList("store_id,store_name");
				if($store_list){
					foreach($store_list as $k=>$l){
						if(in_array($l['store_id'],$store_id)){
							unset($store_list[$k]);
						}
					}
				}
				$this->pagedata['store_list']=$store_list;
			}
			$workgroup=$roles->getList('*');
			
			$this->pagedata['role_id']=$workground;
			$this->pagedata['workgroup']=$workgroup;
			$this->display('admin/storelist/users_add.html');
		}
	}
	private function save_ground($aData){
		
		$workgrounds = $aData['role'];
		$menus = app::get('desktop')->model('menus');
        $roles =  app::get('desktop')->model('roles');
		foreach($workgrounds as $val){
			$result = $roles->dump($val);
			$data = unserialize($result['workground']);
			foreach((array)$data as $row){
				$aPermission[] = $row;
			}
		}
		$aPermission = array_unique($aPermission);
		if($aPermission){
			$addonmethod = array();
			foreach((array)$aPermission as $key=>$val){
				$sdf = $menus->dump(array('menu_type' => 'permission','permission' => $val));
				$addon = unserialize($sdf['addon']);
				if($addon['show']&&$addon['save']){  //如果存在控制
					if(!in_array($addon['save'],$addonmethod)){
						$access = explode(':',$addon['save']);
						$classname = $access[0];
						$method = $access[1];
						$obj = kernel::single($classname);
						$obj->$method($aData['user_id'],$aData);
					}
					$addonmethod[] = $addon['save'];
				}
			}
		}
		
		
        
	}
	/**
	 * 
	 * 保存角色
	 */
	function save()
	{
		//查询门店ID
		$user_id=$_SESSION['account']['user_data']['user_id'];
		$s_arr=$this->app->model('storelist_relat')->getRow("*",array('oper_id'=>$user_id));
		if($s_arr){
			$store_id=$s_arr['stores_id'];
		}
		
		$this->begin();
		$roles = app::get('desktop')->model('roles');
		if(!$_POST['workground']){
			$this->end(false,$this->app->_('至少选择一个工作组'));
		}
		/* if($roles->validate($_POST,$msg))
		{
			if($roles->save($_POST))
				$this->end(true,app::get('desktop')->_('保存成功'));
			else
				$this->end(false,app::get('desktop')->_('保存失败'));
	
		}
		else
		{
			$this->end(false,$msg);
		} */
		$db = kernel::database();
       	$db->beginTransaction();
       	$data=array(
       		'role_name'=>$_POST['role_name'],
       		'workground'=>serialize($_POST['workground']),
       	);
       	$flag=$roles->insert($data);
       	$roles_array=array(
       		'stores_id'=>(int)$store_id,
       		'role_id'=>(int)$flag,
       	);
       	$relat=$this->app->model('storelist_roles')->insert($roles_array);
       	if($flag && $relat){
       		$db->commit();
       		$msg=$this->app->_('操作成功');
       		$this->end(true,$msg);
       	}else{
       		$db->rollback();
       		$msg=$this->app->_('操作失败');
       		$this->end(false,$msg);
       	}
       	
	}
	/**
	 * 
	 * 编辑角色
	 * @param unknown $roles_id
	 */
	function edit($roles_id){
		$param_id = $roles_id;
		$this->begin();
		if($_POST){
			if($_POST['role_name']==''){
				$this->end(false,app::get('desktop')->_('工作组名称不能为空'));
			}
			if(!$_POST['workground']){
				//$_POST['workground'] = '';
				$this->end(false,app::get('desktop')->_('请至少选择一个权限'));
			}
			$opctl = app::get('desktop')->model('roles');
			$result = $opctl->check_gname($_POST['role_name']);
			if($result && ($result!=$_POST['role_id'])) {$this->end(false,app::get('desktop')->_('该工作组名称已存在'));}
			if($opctl->save($_POST)){
				$this->end(true,app::get('desktop')->_('保存成功'));
			}else{
				$this->end(false,app::get('desktop')->_('保存失败'));
			}
	
		}
		else{
			//获取对象设置的公共属性
			$storeObj=kernel::single('storelist_store');
			$set_default_role=app::get('desktop')->getconf($storeObj::$store_owner_conf);
			$w=app::get('desktop')->model('roles')->dump(intval($set_default_role));
			$workground=unserialize($w['workground']);
			$opctl = app::get('desktop')->model('roles');
			$menus = app::get('desktop')->model('menus');
			$sdf_roles = $opctl->dump($param_id);
			$this->pagedata['roles'] = $sdf_roles;
			$work = unserialize($sdf_roles['workground']);
			foreach((array)$workground as $v){
				#$sdf = $menus->dump($v);
				$menuname = $menus->getList('*',array('menu_type' =>'menu','permission' => $v));
				foreach($menuname as $val){
					$menu_workground[] = $val['workground'];
				}
			}
			$menu_workground = array_unique((array)$menu_workground);
			$workgrounds = app::get('desktop')->model('menus')->getList('*',array('menu_type'=>'workground','disabled'=>'false','display'=>'true'));
			foreach($workgrounds as $k => $v){
				$workgrounds[$k]['permissions'] = $this->obj_roles->get_permission_per($v['menu_id'],$workground);
				if(in_array($v['workground'],(array)$menu_workground)){
					$workgrounds[$k]['checked'] = 1;
	
				}
			}
	
			$widgets = app::get('desktop')->model('menus')->getList('*',array('menu_type'=>'widgets'));
	
			foreach($widgets as $key=>$widget){
				if(in_array($widget['addon'],$workground))
					$widgets[$key]['checked'] = true;
			}
	
			$this->pagedata['widgets'] = $widgets;
			$this->pagedata['workgrounds'] = $workgrounds;
			$this->pagedata['adminpanels'] = $this->obj_roles->get_adminpanel($param_id,$workground);#print_r($workgrounds);exit;
			//$this->pagedata['others'] = $this->obj_roles->get_others($workground);
	
			//桌面挂件权限
			$html1 = '';
			$checkall = false;
			foreach($this->pagedata['widgets'] as $key1=>$val1){
				if($val1['checked']){
					$html1 .= "<li style='padding-left:25px;text-align:left;'><input  class='leaf ' type='checkbox' checked='checked' name='workground[]' value=".$val1['addon'].">".$val1['menu_title']."</li>";
					$checkall = true;
				}else{
					
					foreach ($workground as $w1){
						if($w1==$val1['addon']){
							$html1 .= "<li style='padding-left:25px;text-align:left;'><input  class='leaf ' checked='checked' type='checkbox' name='workground[]' value=".$val1['addon'].">".$val1['menu_title']."</li>";
						}
					}
					
					$checkall = false;
				}
			}
			//$this->pagedata['menus1'] = "<ul><li><input class='parent'".($checkall?" checked='checked'":"")." type=\"checkbox\">全选(桌面挂件权限)<ul>".$html1."</ul></li></ul>";
	
			//控制面板权限
			$html2 = '';
			$checkall = false;
			foreach($this->pagedata['adminpanels'] as $key2=>$val2){
				if($val2['checked']){
					$html2 .= "<li style='padding-left:25px;text-align:left;'><input  class='leaf ' type='checkbox' checked='checked' name='workground[]' value=".$val2['permission'].">".$val2['menu_title']."</li>";
					$checkall = true;
				}else{
					foreach($workground as $w2){
						foreach($work as $w2_2){
							if($w2==$w2_2){
								$html2 .= "<li style='padding-left:25px;text-align:left;'><input  class='leaf ' type='checkbox' name='workground[]' value=".$val2['permission'].">".$val2['menu_title']."</li>";
							}
						}
					}
					
					$checkall = false;
				}
			}
			//$this->pagedata['menus2'] = "<ul><li><input class='parent'".($checkall?" checked='checked'":"")." type=\"checkbox\">全选(控制面板权限)<ul>".$html2."</ul></li></ul>";
	
			//业务权限
			$treedata=array();
			foreach($this->pagedata['workgrounds'] as $key3=>$val3){//原始权限信息列表
				$mgrpname['mgrpname'][] = $val3['menu_title'];
				$treedata[] = $this->getTree($val3['permissions'],'0');
			}
			foreach($treedata as $kmgrp=>$vmgrp){//权限分组信息
				$treedata[$kmgrp][0]['mgrpname'] = $mgrpname['mgrpname'][$kmgrp];
			}
			foreach($treedata as $item){//权限列表生成
				$html = $this->procHTML($item,$workground);
				$this->pagedata['menus3'][]= $html['html'];
				$checkarr[] = $html['checkall'];
			}
			$checked_all = false;
			foreach ($checkarr as $key) {
				if($key == 'true') {
					$checked_all = true;
				}
				else {
					$checked_all = false;
				}
			}
			$this->pagedata['checked_all'] = $checked_all;
			/*其他权限
			 #$vv3 = $this->getTree($this->pagedata['others'],'0');
			#$base_v3 = array('property'=>array('name'=>'其他', 'hasCheckbox'=>false), 'children'=>$vv3);
			*/
	
			$this->page('admin/storelist/edit_roles.html');
		}
	}
	function getTree($data, $pId){
		$tree = '';
		foreach($data as $k => $v){
			if($v['parent'] == $pId){         //父亲找到儿子
				$v['parent'] = $this->getTree($data, $v['permission']);
				$tree[] = $v;
				//unset($data[$k]);
			}
		}
		return $tree;
	}
	function procHTML($tree,$workground){
		$html = '';
		$checkall = 'false';
		foreach($tree as $k=>$t){
			if($t['mgrpname']){
				$html .= "<li style='text-align:left;font-weight:bold;font-style:italic;'>".$t['mgrpname'];
			}
			if($t['parent'] == ''){
				if($t['checked']){
					$html .= "<li style='padding-left:25px;text-align:left;'><input  class='leaf'  type='checkbox' checked='checked' name='workground[]' value=".$t['permission'].">".$t['menu_title'];
					$checkall = 'true';
				}else{
					
					foreach($workground as $w3){
						if($w3==$t['permission']){
							$html .= "<li style='padding-left:25px;text-align:left;'><input    class='leaf' checked='checked' type='checkbox' name='workground[]' value=".$t['permission'].">{$t['menu_title']}</li>";
							$checkall = 'false';
						}
					}
					
				}
			}else{
				if($t['checked']){
					$html .= "<li style='padding-left:25px;text-align:left;'><input  class='parent leaf'  type='checkbox' checked='checked' name='workground[]' value=".$t['permission'].">".$t['menu_title'];
					$checkall = 'true';
				}else{
					foreach ($workground as $w4){
						if($w4==$t['permission']){
							$html .= "<li style='padding-left:25px;text-align:left;'><input   class='parent leaf' checked='checked'  type='checkbox' name='workground[]' value=".$t['permission'].">".$t['menu_title'];
							$checkall = 'false';
						}
					}
					
				}
				$str = $this->procHTML($t['parent'],$workground);
				$html .= $str['html'];
				$html = $html."</li>";
			}
		}
		//return $html ? "<ul>".$html."</ul>" : $html;
		return array(
				"html"=>"<ul>".$html."</ul>",
				"checkall"=>$checkall
		);
	}
}
?>