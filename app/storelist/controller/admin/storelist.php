<?php
class storelist_ctl_admin_storelist extends desktop_controller{
	//var $workground = 'desktop_ctl_system';
	public function __construct($app)
	{
		parent::__construct($app);
		/* $this->obj_roles = kernel::single('desktop_roles');
		header("cache-control: no-store, no-cache, must-revalidate"); */
	}
	public  function index(){
		//获取当前登录用户ID
		$user_id=$_SESSION['account']['user_data']['user_id'];
		
		$u_id=app::get('desktop')->model('users')->getRow("*",array('user_id'=>intval($user_id)));
		$us_id=app::get('storelist')->model('storelist_relat')->getRow("*",array('oper_id'=>intval($user_id)));
		$s_list=app::get('storelist')->model('storelist')->getRow("*",array('store_id'=>(int)$us_id['stores_id']));
		
		$role_id=app::get('desktop')->model('hasrole')->getRow("*",array('user_id'=>intval($user_id)));
		//获取对象设置的公共属性
		$storeObj=kernel::single('storelist_store');
		$default_store_role=app::get('desktop')->getconf($storeObj::$store_owner_conf);
		$r_id=app::get('storelist')->model('storelist_roles')->getRow("*",array('role_id'=>intval($role_id['role_id'])));
		if($u_id['super']==1 || intval($role_id['role_id'])!=intval($default_store_role) && empty($r_id)){
			$t=array(
					'title'=>app::get('storelist')->_('门店列表'),
					'actions'=>array(
							array('label'=>app::get('b2c')->_('添加门店'),'href'=>'index.php?app=storelist&ctl=admin_storelist&act=addstore','target'=>'dialog::{title:\''.app::get('storelist')->_('添加门店').'\'}'),
					),
					//'base_filter'=>array('store_id'=>$flag),
					'use_buildin_export'=>false
			);
		}else{
			$flag=$s_list?intval($s_list['store_id']):0;
			$t=array(
					'title'=>app::get('storelist')->_('门店列表'),
						
					'base_filter'=>array('store_id'=>$flag),
					'use_buildin_export'=>false,
					'use_buildin_recycle'=>false,
			);
		}
		
		
		$this->finder('storelist_mdl_storelist',$t);
	}
	
	/**
	 * 
	 * 添加门店
	 */
	public function addstore($store_id=''){
		if($store_id!=''){
			$row=$this->app->model('storelist')->dump(intval($store_id));
			$this->pagedata['row']=$row;
		}
		
		if($_POST){
			
			$this->begin();
			$data=$this->_checkPost($_POST);
			$storeList=array(
				'store_name'=>$data['store_name'],
				'region_id'=>$data['region_id'],
				'reg_id'=>$data['reg_id'],
				'r_id'=>$data['r_id'],
				'default_pass'=>$data['default_pass'],
				'area'=>$data['area'],
				'area_name'=>$data['area_name'],
				'area_value'=>$data['area_value'],
			);
			$storelist_item=$data['three_area'];
			$db = kernel::database();
        	$db->beginTransaction();
			 if(!isset($_POST['store']['store_id']) || $_POST['store']['store_id']==''){
				
				//insert
				$flag=$this->app->model('storelist')->insert($data);
				$storeList_num=count($storelist_item);
				for($i=0;$i<$storeList_num;$i++){
					$item=$db->exec("INSERT INTO `sdb_storelist_storelist_item` (store_id,region_id) VALUES ({$flag},{$storelist_item[$i]})");
					//unset($storelist_item[$i]);
					continue;
				}
			}else{
				$stores_id=intval($_POST['store']['store_id']);
				//update
				//如果region_id等于空说明没有选择区域并且又因为当前是update 所以直接取数据库里面的region_id
				if($data['region_id']==NULL){
					
					$d_arr=$this->app->model('storelist')->dump(intval($_POST['store']['store_id']));
					$data['region_id']=$d_arr['region_id'];
					$up_store_list=array(
							'store_name'=>$data['store_name'],
							'region_id'=>$data['region_id'],
							'reg_id'=>$data['reg_id'],
							'r_id'=>$data['r_id'],
							'default_pass'=>$data['default_pass'],
							'area'=>$data['area'],
							'area_name'=>$data['area_name'],
							'area_value'=>$data['area_value'],
						);
				}else{
					$up_store_list=array(
							'store_name'=>$data['store_name'],
							'region_id'=>$data['region_id'],
							'reg_id'=>$data['reg_id'],
							'r_id'=>$data['r_id'],
							'default_pass'=>$data['default_pass'],
							'area'=>$data['area'],
							'area_name'=>$data['area_name'],
							'area_value'=>$data['area_value'],
						);
				}
				$flag=$this->app->model('storelist')->update($up_store_list,array('store_id'=>$stores_id));
				//编辑之前先把所有的属于这个门店的数据先删除掉;
				$delete=$this->app->model('storelist_item')->delete(array('store_id'=>$stores_id));
				if($delete){
					$storeList_num=count($storelist_item);
					for($i=0;$i<$storeList_num;$i++){
						$item=$db->exec("INSERT INTO `sdb_storelist_storelist_item` (store_id,region_id) VALUES ({$stores_id},{$storelist_item[$i]})");
						//unset($storelist_item[$i]);
						continue;
					}
				}
				
				
			}
			if($flag && $item){
				$db->commit();
				$msg=$this->app->_('操作成功');
				$this->end(true,$msg);
			}else{
				$db->rollback();
				$msg=$this->app->_('操作失败');
				$this->end(false,$msg);
			}
			
			
		}
		$this->display('admin/storelist/store_add.html');
	}
	/**
	 * 
	 * 门店主修改密码
	 */
	public  function  uppass(){
		$obj_pam=app::get('pam')->model('account');
		$users = app::get('desktop')->model('users');
		if($_POST){
			
			$this->begin();
			$sdf = $users->dump((int)$_POST['user_id'],'*',array( ':account@pam'=>array('*'),'roles'=>array('*') ));
			$new_login_password=trim($_POST['new_login_password']);
			$login_password=trim($_POST['pam_account']['login_password']);
			if($login_password!=$new_login_password){
				$this->end(false,$this->app->_('两次密码不一致'));
			}
			$_POST['pam_account']['account_id'] = $_POST['user_id'];
			$use_pass_data['login_name'] = $sdf['account']['login_name'];
            $use_pass_data['createtime'] = $sdf['account']['createtime'];
            $_POST['pam_account']['login_password'] = pam_encrypt::get_encrypted_password(trim($_POST['new_login_password']),pam_account::get_account_type($this->app->app_id),$use_pass_data);
            $users->save($_POST);
            $this->end(true,app::get('storelist')->_('密码修改成功'));
		}
		$this->pagedata['user_id']=$_SESSION['account']['user_data']['user_id'];
		$this->page('admin/storelist/store_uppass.html');
	}
	/**
	 * 
	 * 检测数据POST
	 * @param unknown $data
	 */
	private  function _checkPost($data){
		
		
		$store_name=$data['store']['store_name'];
		//$s_name=kernel::database()->selectRow("SELECT store_name FROM `sdb_storelist_storelist` WHERE store_name='{$store_name}'");
		$s_name=$this->app->model('storelist')->getRow("store_name",array('store_name'=>$store_name));
		if($data['area_fee_conf'][0]['areaGroupId']!=''){
			$areaArr=explode("|",$data['area_fee_conf'][0]['areaGroupId']);
		}		
		$area_explode=explode(":",$data['area']);
		$area=$area_explode[2];
		if(!empty($areaArr)){
			$a=implode(",",$areaArr);
		}
		if($a){
			$b=explode(",",$a);
		}
		if(!isset($data['store']['store_id']) || $data['store']['store_id']==''){
			
			
			if(!empty($s_name)){
				if($store_name==$s_name['store_name']){
					//判断门店名称是否存在
					$this->end(false,$this->app->_('门店名称已存在'));
				}
			}
		}
		
		if(!preg_match("/^[\w]+$/",$data['store']['default_pass'])){
			//判断密码是否合法
			$this->end(false,$this->app->_('会员默认密码不合法,请输入数字或者字母以及下划线'));
		}
		if(!isset($data['store']['store_id']) || $data['store']['store_id']==''){	
			$reg_id=$this->app->model('storelist')->getRow("*",array('reg_id'=>intval($area)));
		}
		if(!empty($reg_id))$this->end(false,$this->app->_('此区域已被选择，请换个区域'));	
			if($b){
				foreach($b as $k=>$v){
					//判断不是数字的直接销毁掉
					if(!is_numeric($v)){
						unset($b[$k]);
					}
				}
			}
			if($b){
				//重新排序数组
				sort($b);
			}
			
			if(!empty($b)){
				$regonList=kernel::database()->select("SELECT region_id,p_region_id FROM `sdb_ectools_regions` WHERE region_id IN(".implode(",",$b).") AND p_region_id IS NULL");
			}
			if(!empty($b)){
				$count_b=count($b);
				$cb=0;//用于计数选择的是否都是全省
				for($ib=0;$ib<$count_b;$ib++){
					$nb=kernel::database()->select("SELECT region_id,p_region_id FROM `sdb_ectools_regions` WHERE region_id ={$b[$ib]} AND p_region_id IS NULL");
					if($nb){
						$cb++;
					}
				}
				if($cb==$count_b){
					
					//选择的都是全省
					$null_region_id=$b;
					$av_ids=$this->__checkArea($null_region_id);
				}else{
					//非全省 //取三级区域没有三级区域取二级区域ID
					$not_area=kernel::database()->select("SELECT region_id,p_region_id,region_path,region_grade FROM `sdb_ectools_regions` WHERE region_id IN(".implode(",",$b).")");
					
					if($not_area){
						foreach($not_area as $n_o){
							if($n_o['p_region_id']==NULL){
								$no_ids[]=$n_o['region_id'];
							}else{
								$n_ids[]=$n_o['region_id'];
							}
						}
					}
					
					$sb=kernel::database()->select("SELECT * FROM `sdb_ectools_regions` WHERE region_id IN(".implode(",",$n_ids).")");
					if(!empty($no_ids)){
						foreach ($no_ids as $nok=>$n_oo){
							foreach($sb as $sk=>$sk_v){
								if($n_oo==$sk_v['p_region_id']){
									unset($no_ids[$nok]);
								}
								if($sk_v['region_grade']==2){
									$er_id[]=$sk_v['region_id'];
								}else if($sk_v['region_grade']==3){
									$san_id[]=$sk_v['region_id'];
								}
									
								//unset($sb[$sk]);
							}
						}
						$er_ids=array_unique($er_id);
						if(!empty($san_id))$san_ids=array_unique($san_id);
					}
					
					$san_sb=kernel::database()->select("SELECT * FROM `sdb_ectools_regions` WHERE p_region_id IN(".implode(",",$er_ids).")");
					
					
					if(!empty($san_sb)){
						foreach($san_sb as $sv_k){
							$n_skt[]=$sv_k['region_id'];
							$p_region_id[]=$sv_k['p_region_id'];
						}
					}else{
						$xxt=array_merge($no_ids,$er_ids);
					}
					
					if(!empty($no_ids)&&!empty($n_ids)){
						
						$tts=array_merge($no_ids,$n_ids);
					}else{
						//选择的是二级区域;
						$tts=$n_ids;
						
					}
					
					if(!empty($p_region_id)){
						foreach($p_region_id as $sbk=>$sb_v){
							foreach($tts as $ttks=>$ttsv){
								if($ttsv==$sb_v){
									unset($tts[$ttks]);
								}
							}
						}
						
							
							$xxt=array_merge($tts,$n_skt);
							
							
						
						
					}
					
					if($xxt){
						$xxt_arr=kernel::database()->select("SELECT * FROM `sdb_ectools_regions` WHERE region_id IN(".implode(",",$xxt).")");
					}
					
					if($xxt_arr){
						foreach($xxt_arr as $xx_v){
							if($xx_v['p_region_id']==NULL){
								$xx_ids[]=$xx_v['region_id'];
							}else{
								$xx_id[]=$xx_v['region_id'];
							}
						}
					}
					
					$d_data=$this->__checkArea($xx_ids);
					if(!empty($d_data) && !empty($xx_id)){
						
						$av_ids=array_merge($d_data,$xx_id);
						
					}else if(!empty($er_ids)&& !empty($san_ids)){
						
						$av_ids=$tts;
						
						
					}else{	
						
						$av_ids=$xx_id;
					}
					
					/* foreach($not_area as $kn=>$not){
						$tt[$not['region_id']]=count(explode(",",$not['region_path']));
					} */
					
					/* foreach($tt as $kt=>$n_tt){
						
						 if($n_tt==4 || $n_tt==5){
							echo $n_tt;
							
							$av_ids[]=$kt;
						} 
						
						if($n_tt==4){
							$two_area[]=$kt;
						} 
						if($n_tt==5){
							$three_a[]=$kt;
						} 
						if($n_tt==4 || $n_tt==5){
							$o_t_area[]=$kt;
						}
					} */
					
					//查询二级区域下面是否有三级区域
					//$two_area_arr=kernel::database()->select("SELECT region_id,p_region_id FROM `sdb_ectools_regions` WHERE p_region_id IN(".implode(",",$two_area).")");
					/* if(!empty($two_area_arr)&& !empty($three_a)){
						echo "1";
						foreach($two_area_arr as $two_r){
							$av_ids_s[]=$two_r['region_id'];
						}
						$av_ids[]=array_merge($av_ids_s,$three_a);
					}else if(empty($three_a) && !empty($two_area_arr)){
						echo "2";
						foreach($two_area_arr as $two_r){
							$av_ids[]=$two_r['region_id'];
						}
					}else{
						echo "3";
						$av_ids=$two_area;
					} */
					//if(!empty($three_a) && !empty($))
				}
				
			}
			$sial_list=array();
			if(!isset($data['store']['store_id']) || $data['store']['store_id']==''){
				//插入的时候查询所有表中的数据
				$reg_ids=$this->app->model('storelist')->getList("*");
			}else{
				//编辑的时候查询不等于自己的数据
				$reg_ids=kernel::database()->select("SELECT * FROM `sdb_storelist_storelist` WHERE store_id!=".intval($data['store']['store_id'])."");
			}
			
			 if($reg_ids){
				foreach ($reg_ids as $rk=>$rg){
					//查询选择区域所选ID号
					$sial_list[]=unserialize($rg['r_id']);
					$is_region_id[]=$rg['reg_id'];
					
					
				}
			} 
			
			
			
			if(!empty($sial_list)){
				if(count($sial_list)>1){
					foreach($sial_list as $vs){
						
						foreach($vs as $sv){
							$n_sial_list[]=$sv;
						}
					}
				}else{
						foreach($sial_list as $one_s){
							$n_sial_list=$one_s;
						}
				}
				
				
				
				//查询是三级区域
				//$list[]=kernel::database()->select("SELECT region_id,region_grade FROM `sdb_ectools_regions` WHERE region_id IN(".implode(",",$n_sial_list).") AND region_grade=3");
			}
			if(!isset($data['store']['store_id']) || $data['store']['store_id']==''){
				if($n_sial_list){
					if(in_array($area,$n_sial_list)){
						$this->end(false,$this->app->_('当前选择的区域已被选择'));
					}
				}
			}else{
				if($n_sial_list){
					if(in_array($area,$n_sial_list)){
						$this->end(false,$this->app->_('当前选择的区域已被选择'));
					}
				}
			}
			if(!isset($data['store']['store_id']) || $data['store']['store_id']==''){
				if(!empty($av_ids) && !empty($n_sial_list)){
					foreach($av_ids as $av){
						foreach($n_sial_list as $gr){
							if($av==$gr){
								$this->end(false,$this->app->_('地区里面选择的区域已被选择'));
							}
								
						}
					}
				}
			} else{
				//if(empty($av_ids))$this->end(false,$this->app->_('为了防止地区重复,请重新选择地区'));
				if(!empty($av_ids) && !empty($n_sial_list)){
					foreach($av_ids as $av){
						foreach($n_sial_list as $gr){
							if($av==$gr){
								$this->end(false,$this->app->_('地区里面选择的区域已被选择'));
							}
				
						}
					}
				}
			} 
			
			
		if($av_ids){
			if(in_array($area,$av_ids)){
				$three_area=$av_ids;
				sort($three_area);
			}else{
				$three_area=$av_ids;
				array_push($three_area,$area);
				sort($three_area);
			}
		}else{
			
			$three_area=array($area);
			
		}
		
		$datas=array(
				'store_name'=>$data['store']['store_name'],
				'region_id'=>$b?serialize($b):NULL,
				'reg_id'=>$area,
				'r_id'=>$three_area?serialize($three_area):NULL,
				'default_pass'=>$data['store']['default_pass'],
				'area'=>$data['area'],
				'area_name'=>$data['area_fee_conf'][0]['areaGroupName'],
				'area_value'=>$data['area_fee_conf'][0]['areaGroupId'],
				'three_area'=>$three_area
		);
		
		return $datas;
		
	}
	/**
	 * 
	 * 根据一级地区查询二级区域
	 * @param unknown $data
	 * @return boolean
	 */
	private function __checkArea($f_area,$s_data=array()){
		if(!is_array($f_area))return false;
		$er_data=kernel::database()->select("SELECT region_id,p_region_id FROM `sdb_ectools_regions` WHERE p_region_id IN(".implode(",",$f_area).")");
		if($er_data){
			foreach($er_data as $er_v){
				$_er_ids[]=$er_v['region_id'];
			}
		}
		$arr_arr=array();
		foreach ($_er_ids as $er_vv){
			$er=kernel::database()->select("SELECT region_id FROM `sdb_ectools_regions` WHERE p_region_id ={$er_vv}");
			if(!$er){
				array_push($arr_arr,$er_vv);
			}else{
				foreach($er as $er_id){
					array_push($arr_arr,$er_id['region_id']);
				}
			}
			
		}
		asort($arr_arr);
		return $arr_arr;
		
		
	}
	/**
	 * 
	 * ajax检测默认区域是否被选择
	 */
	public  function check_area(){
		$reg_id=(int)$_POST['reg_id'];
		$re=$this->app->model('storelist')->getRow("*",array('reg_id'=>$reg_id));
		if(!empty($re)){
			echo json_encode(array('req'=>'error','msg'=>'此区域已被选择,请换个区域'));
			exit();
		}
	}
	/**
	 * 
	 * 检测数据
	 * @param unknown $data
	 * @return boolean|NULL
	 */
	private function _getData($data){

		if(!is_array($data))return false;
		$a=array();
		foreach($data as $k=>$n){
			
				$a[$k]=kernel::database()->select("SELECT region_id FROM `sdb_ectools_regions` WHERE region_path LIKE '%,".$n.",%' AND p_region_id IS NOT NULL AND region_grade=3");
				
			}
			if(!empty($a)){
				foreach($a as $k=>$a_v){
					foreach($a[$k] as $as_v){
						$av_ids[]=$as_v['region_id'];
						 
					}
					
					
				}
			}
			return $av_ids;
	}
	
	
}
?>