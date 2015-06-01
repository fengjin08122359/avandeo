<?php
class storelist_ctl_admin_storerole extends desktop_controller{
	public function __construct($app){
		parent::__construct($app);
		$this->tablePre=kernel::database()->prefix;
		$this->obj_roles = kernel::single('desktop_roles');
		header("cache-control: no-store, no-cache, must-revalidate");
		
	}
	public function  index(){
		$user_id=(int)$_SESSION['account']['user_data']['user_id'];
		$role_id=app::get('desktop')->model('hasrole')->getRow("*",array('user_id'=>intval($user_id)));
		if(!empty($role_id)){
			$r_id[][]=$role_id['role_id'];
		}
		//查询门店主是否添加了职员的角色
		
		//$son_role=kernel::database()->select("SELECT * FROM ".$this->tablePre."storelist_storelist_relat AS r LEFT JOIN ".$this->tablePre."storelist_storelist_roles AS ro ON r.stores_id=ro.stores_id WHERE r.oper_id={$user_id}" );
		$son_role=kernel::database()->selectRow("SELECT * FROM ".$this->tablePre."storelist_storelist_relat WHERE oper_id={$user_id}");
		if($son_role){
			
			$son_r_role=kernel::database()->select("SELECT * FROM ".$this->tablePre."storelist_storelist_roles WHERE stores_id={$son_role['stores_id']}");
		}
		if($son_r_role){
			foreach($son_r_role as $s){
				$son_id[]=$s['role_id'];
			}
		}
		
		if($son_id!=NULL && !empty($r_id)){
			
			array_push($r_id,$son_id);
		}
		//获取对象设置的公共属性
		$storeObj=kernel::single('storelist_store');
		$default_set_role_id=app::get('desktop')->getconf($storeObj::$store_owner_conf);
		if(count($r_id)>1){
			
			foreach ($r_id as $v){
				foreach($v as $vi){
					if($vi!=$default_set_role_id){
						$v_ids[]=$vi;
					}
				}
				
			}
			
			$filter = array('filter_sql'=>" role_id IN(".implode(",",$v_ids).") ");
			$this->finder('desktop_mdl_roles',array(
					'title'=>app::get('desktop')->_('角色'),
					 'actions'=>array(
					 array('label'=>app::get('storelist')->_('新建角色'),'href'=>'index.php?app=storelist&ctl=admin_adminstore&act=addroleof','target'=>'dialog::{title:\''.app::get('storelist')->_('新建角色').'\'}'),
					) ,
					'use_buildin_recycle'=>true,
					'base_filter'=>$filter
			));
		}else{
			if(intval($role_id['role_id'])!=(int)$default_set_role_id){
				
				$filter = array('filter_sql'=>"role_id=");
				$this->finder('desktop_mdl_roles',array(
						'title'=>app::get('desktop')->_('角色'),
						/* 'actions'=>array(
						 array('label'=>app::get('storelist')->_('新建角色'),'href'=>'index.php?ctl=roles&act=addnew','target'=>'dialog::{title:\''.app::get('storelist')->_('新建角色').'\'}'),
						) , */
						'use_buildin_recycle'=>false,
						'base_filter'=>$filter
				));
			}else{
				foreach($r_id as $r){
					foreach($r as $rr){
						if($rr!=$default_set_role_id){
							$rr_ids[]=$rr;
						}
							
					}
				}
				$filter = array('filter_sql'=>" role_id IN(".implode(",",$rr_ids).") ");
				$this->finder('desktop_mdl_roles',array(
						'title'=>app::get('desktop')->_('角色'),
						 'actions'=>array(
					 		array('label'=>app::get('storelist')->_('新建角色'),'href'=>'index.php?app=storelist&ctl=admin_adminstore&act=addroleof','target'=>'dialog::{title:\''.app::get('storelist')->_('新建角色').'\'}'),
						) , 
						'use_buildin_recycle'=>false,
						'base_filter'=>$filter
				));
			}
			
			
			
		}
		
		
	}
	
	public function edit($roles_id){
		$param_id = $roles_id;
        $this->begin();
        if($_POST){
        	$storeObj=kernel::single('storelist_store');
        	$default_store_role=app::get('desktop')->getconf($storeObj::$store_owner_conf);
        	$workground_arr=app::get('desktop')->model('roles')->getRow("*",array('role_id'=>intval($default_store_role)));
        	$_work=unserialize($workground_arr['workground']);
            if($_POST['role_name']==''){
                 $this->end(false,app::get('storelist')->_('工作组名称不能为空'));
            }
            if(!$_POST['workground']){
                //$_POST['workground'] = '';
                $this->end(false,app::get('storelist')->_('请至少选择一个权限'));
            }
            $opctl = app::get('desktop')->model('roles');
            $result = $opctl->check_gname($_POST['role_name']);
            //if($result && ($result!=$_POST['role_id'])) {$this->end(false,app::get('storelist')->_('该工作组名称已存在'));}
			
			/* foreach ($_POST['workground'] as $w){
				if(!in_array($w,$_work)){
					$this->end(false,app::get('storelist')->_('您只能勾选自己本身的权限或小于自己的权限'));
					exit();
				}
			}  */
            if($opctl->save($_POST)){
                 $this->end(true,app::get('storelist')->_('保存成功'));
            }else{
               $this->end(false,app::get('storelist')->_('保存失败'));
            }

            }
        else{
        $storeObj=kernel::single('storelist_store');
		$roles_id=app::get('desktop')->getconf($storeObj::$store_owner_conf);
		$w=app::get('desktop')->model('roles')->dump(intval($roles_id));
		$workground=unserialize($w['workground']);
		
		$workgrounds = app::get('desktop')->model('menus')->getList('*',array('menu_type'=>'workground','disabled'=>'false','display'=>'true'));
		$this->pagedata['workgrounds'] = $workgrounds;
		$widgets = app::get('desktop')->model('menus')->getList('*',array('menu_type'=>'widgets'));
		$this->pagedata['widgets'] = $widgets;
		$w_k=app::get('desktop')->model('roles')->dump($param_id);
		$this->pagedata['roles']=$w_k;
		$k_k=unserialize($w_k['workground']);
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
				if(in_array($val1['addon'],$workground) && in_array($val1['addon'],$k_k)){
					$html1 .= "<li style='padding-left:25px;text-align:left;'><input   class='leaf ' type='checkbox' checked='checked'  name='workground[]' value=".$val1['addon'].">".$val1['menu_title']."</li>";
				}else if(in_array($val1['addon'],$workground)){
					$html1 .= "<li style='padding-left:25px;text-align:left;'><input   class='leaf ' type='checkbox'   name='workground[]' value=".$val1['addon'].">".$val1['menu_title']."</li>";
				}
				/* foreach($workground as $w1){
					
						if($w1==$val1['addon']){
							$html1 .= "<li style='padding-left:25px;text-align:left;'><input   class='leaf ' type='checkbox' checked='checked'  name='workground[]' value=".$val1['addon'].">".$val1['menu_title']."</li>";
						}
					} */
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
				if(in_array($val2['permission'],$workground) && in_array($val2['permission'],$k_k)){
					$html2 .= "<li style='padding-left:25px;text-align:left;'><input   class='leaf ' type='checkbox' checked='checked'   name='workground[]' value=".$val2['permission'].">".$val2['menu_title']."</li>";
				}else if(in_array($val2['permission'],$workground)){
					$html2 .= "<li style='padding-left:25px;text-align:left;'><input   class='leaf ' type='checkbox'   name='workground[]' value=".$val2['permission'].">".$val2['menu_title']."</li>";
				}
				/* foreach($workground as $w2){
					
					if($w2==$val2['permission']){
						$html2 .= "<li style='padding-left:25px;text-align:left;'><input checked='checked'  class='leaf ' type='checkbox' name='workground[]' value=".$val2['permission'].">".$val2['menu_title']."</li>";
					}
				} */
					
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
			$html = $this->procHTML($item,$workground,$k_k);
			$this->pagedata['menus3'][]= $html['html'];
		}
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
function procHTML($tree,$workground,$k_k){
	
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
					if(in_array($t['permission'],$workground)&& in_array($t['permission'],$k_k) ){
						$html .= "<li style='padding-left:25px;text-align:left;'><input checked='checked'   class='leaf'  type='checkbox' name='workground[]' value=".$t['permission'].">{$t['menu_title']}</li>";
						$checkall = 'false';
					}else if(in_array($t['permission'],$workground)){
						$html .= "<li style='padding-left:25px;text-align:left;'><input    class='leaf'  type='checkbox' name='workground[]' value=".$t['permission'].">{$t['menu_title']}</li>";
						$checkall = 'false';
					}
					
				}
			}else{
				if($t['checked']){
					$html .= "<li style='padding-left:25px;text-align:left;'><input  class='parent leaf'  type='checkbox' checked='checked' name='workground[]' value=".$t['permission'].">".$t['menu_title'];
					$checkall = 'true';
				}else{
					if(in_array($t['permission'],$workground)&& in_array($t['permission'],$k_k)){
						$html .= "<li style='padding-left:25px;text-align:left;'><input   class='parent leaf' checked='checked'  type='checkbox' name='workground[]' value=".$t['permission'].">".$t['menu_title'];
						$checkall = 'false';
					}else if(in_array($t['permission'],$workground)){
						$html .= "<li style='padding-left:25px;text-align:left;'><input   class='parent leaf'   type='checkbox' name='workground[]' value=".$t['permission'].">".$t['menu_title'];
						$checkall = 'false';
					}
					/* foreach ($workground as $w4){
						if($w4==$t['permission']){
							$html .= "<li style='padding-left:25px;text-align:left;'><input   class='parent leaf' checked='checked'  type='checkbox' name='workground[]' value=".$t['permission'].">".$t['menu_title'];
							$checkall = 'false';
						}
					} */
					
				}
				$str = $this->procHTML($t['parent'],$workground,$k_k);
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