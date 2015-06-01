<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 

/* TODO: Add code here */
class desktop_finder_roles{
    var $column_control = '角色操作';
    function __construct($app){
        $this->app= $app;
        $this->obj_roles = kernel::single('desktop_roles');
    }
        
    function column_control($row){
    	//$this->app->setconf('default_store_roles','');
        $render = $this->app->render();
        $user_id=(int)$_SESSION['account']['user_data']['user_id'];
        $role_id=app::get('desktop')->model('hasrole')->getRow("*",array('user_id'=>$user_id));
        $set_role_id=$this->app->getconf('default_store_roles');
        if(!empty($role_id)){
        	$r_id=app::get('storelist')->model('storelist_roles')->getRow("*",array('role_id'=>intval($role_id['role_id'])));
        }
        $app=substr($_SERVER['QUERY_STRING'],4,7);
        $render->pagedata['app']=$app;
        
        $render->pagedata['set_role_id']=(int)$set_role_id;
        $render->pagedata['role_id']=intval($role_id['role_id']);
        $render->pagedata['r_id'] = $r_id?$r_id:NULL;
        
        $render->pagedata['rs_id']=$row['role_id'];
        return $render->fetch('users/href.html');
      }
    
    function detail_indo($param_id){
        $render = $this->app->render();
        $opctl = &$this->app->model('roles');
        $menus = $this->app->model('menus');
        $sdf_roles = $opctl->dump($param_id);
        $render->pagedata['roles'] = $sdf_roles;
        $workground = unserialize($sdf_roles['workground']);
        foreach((array)$workground as $v){
            #$sdf = $menus->dump($v);
            $menuname = $menus->getList('*',array('menu_type' =>'menu','permission' => $v));
            foreach($menuname as $val){
                $menu_workground[] = $val['workground'];
            }
        }
        $menu_workground = array_unique((array)$menu_workground);
        #print_r($menu_workground);exit;
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
                    $__widgets[] = $widget;
            }
        $render->pagedata['workgrounds'] = $workgrounds;
        $render->pagedata['adminpanels'] = $this->obj_roles->get_adminpanel($param_id,$workground,$flg);
        $render->pagedata['widgets'] = $__widgets;
        $render->pagedata['others'] = $this->obj_roles->get_others($workground,$othersflg);
        $render->pagedata['othersflg'] = $othersflg;
        $render->pagedata['flg'] = $flg;
        echo $render->fetch('users/users_roles.html');
    }
}
?>
