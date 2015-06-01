<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 

/* TODO: Add code here */
class desktop_finder_users{
    var $column_control = '操作';
    function __construct($app){
        $this->app=$app;
    }
    
     function column_control($row){
     	$user_id=$_SESSION['account']['user_data']['user_id'];
     	$role_id=app::get('desktop')->model('hasrole')->getRow("*",array('user_id'=>intval($user_id)));
     	$setconf_role_id=app::get('desktop')->getconf('default_store_roles');
     	$r_id=app::get('storelist')->model('storelist_roles')->getRow("*",array('role_id'=>intval($role_id['role_id'])));
     	$is_super=app::get('desktop')->model('users')->dump(intval($user_id));
     	$relatObj=app::get('storelist')->model('storelist_relat');
     	$storeListObj=app::get('storelist')->model('storelist');
     	$stores_id=$relatObj->getRow("stores_id",array('oper_id'=>intval($row['user_id'])));
     	if(!empty($stores_id)){
     		$store_n=$storeListObj->getRow("store_name",array('store_id'=>intval($stores_id['stores_id'])));
     		if(!empty($store_n)){
     			$store_name=$store_n['store_name'];
     		}
     	}
     	
     	if($is_super['super']==1 ||intval($role_id['role_id'])!=intval($setconf_role_id) && empty($r_id)){
     		//$str  =  '<a href="index.php?app=storelist&ctl=admin_storelist&act=addstore&p[0]='.$row['store_id'].'&finder_id='.$_GET['_finder']['finder_id'].'" target="dialog::{title:\''.app::get('storelist')->_('编辑门店').'\'}">'.app::get('storelist')->_('编辑').'</a>';
     		return '<a href="index.php?app=desktop&ctl=users&act=edit&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&p[0]='.$row['user_id'].'" target="dialog::{title:\''.app::get('desktop')->_('编辑操作员').'\', width:680, height:450}">'.app::get('desktop')->_('编辑').'</a>'."&nbsp&nbsp&nbsp".$store_name;
     	}elseif (intval($role_id['role_id'])==intval($setconf_role_id)){
     		return '<a href="index.php?app=desktop&ctl=users&act=edit&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&p[0]='.$row['user_id'].'" target="dialog::{title:\''.app::get('desktop')->_('编辑操作员').'\', width:680, height:450}">'.app::get('desktop')->_('编辑').'</a>';
     	}
             
       
      }
    /*
    function detail_info($param_id){
    
        //获取该项记录集合
        $users = $this->app->model('users');
        $roles=$this->app->model('roles');
        $workgroup=$roles->getList('*');
        $sdf_users = $users->dump($param_id); 
        if($_POST){
            $_POST['pam_account']['account_id'] = $param_id;
            if($sdf_users['super']==1){
            $users->editUser($_POST);
            //echo "修改成功";
            }
            elseif($_POST['super'] == 0 && $_POST['role']){
            foreach($_POST['role'] as $roles){
            $_POST['roles'][]=array('role_id'=>$roles);
                }
            $users->editUser($_POST);
            $users->save_per($_POST);
                }
            else{
            echo "<script>alert('请至少选择一个工作组')</script>";
            }
                }
            //返回无内容信息
            if(empty($sdf_users)) return '无内容';   
            $hasrole=$this->app->model('hasrole');
            foreach($workgroup as $key=>$group){
            $rolesData=$hasrole->getList('*',array('user_id'=>$param_id,'role_id'=>$group['role_id']));
            if($rolesData){
            $check_id[] = $group['role_id'];
            $workgroup[$key]['checked']="true";
            }
            else{
            $workgroup[$key]['checked']="false";
            }
            
            }
            $ui= new base_component_ui($this);
            $html .= $ui->form_start(array('method'=>'post'));
            //foreach($arrGroup as  $arrVal){  $html .= $ui->form_input($arrVal); }
            $render = $this->app->render();
            $render->pagedata['workgroup'] = $workgroup; 
            $render->pagedata['account_id'] = $param_id;
            $render->pagedata['name'] = $sdf_users['name'];
            $render->pagedata['super'] = $sdf_users['super'];
            $render->pagedata['status'] = $sdf_users['status'];
            if(!$sdf_users['super']){
            $render->pagedata['per'] = $users->detail_per($check_id,$param_id);
            
           }
            $html.= $render->fetch('users/users_detail.html');
            $html .= $ui->form_end();
            return $html;
   }*/
      
}

?>
