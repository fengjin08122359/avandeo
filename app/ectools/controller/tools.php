<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class ectools_ctl_tools extends desktop_controller{

    function __construct($app) {
        parent::__construct($app);
		header("cache-control: no-store, no-cache, must-revalidate");
        $this->app = $app;
    }

    function selRegion()
    {
        //$arrGet = $this->_request->get_get();
        $path = $_GET['path'];
        $depth = $_GET['depth'];
        
        //header('Content-type: text/html;charset=utf8');
        $local = kernel::single('ectools_regions_select');
        $ret = $local->get_area_select($this->app,$path,array('depth'=>$depth));
        if($ret){
            echo '&nbsp;-&nbsp;'.$ret;exit;
        }else{
            echo '';exit;
        }
    }
	public function showRegionTreeList($serid,$multi=false,$role_id)
    {
    	if($role_id!=''){
    		$this->pagedata['role_id']=$role_id;
    	}
         if ($serid)
         {
            $this->pagedata['sid'] = $serid;
         }
         else
         {
            $this->pagedata['sid'] = substr(time(),6,4);
         }

         $this->pagedata['multi'] =  $multi;
         $this->singlepage('common/regionSelect.html');
    }
    
    
    public function getRegionById($pregionid)
    {
    	//$oDlyType = &$this->app->model('regions');
    	$obj_regions_op = kernel::service('ectools_regions_apps', array('content_path'=>'ectools_regions_operation'));
    	echo json_encode($obj_regions_op->getRegionById($pregionid));
    }
}