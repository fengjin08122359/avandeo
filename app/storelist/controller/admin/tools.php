<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class storelist_ctl_admin_tools extends desktop_controller{

    function __construct($app) {
        parent::__construct($app);
		header("cache-control: no-store, no-cache, must-revalidate");
        $this->app = $app;
    }

    function getChildCount($region_id)
    {
        $regions = app::get('ectools')->model('regions');
        $cnt = $regions->count(array('p_region_id' => intval($region_id)));

        return $cnt;
    }
	public function showRegionTreeList($serid,$multi=false,$role_id,$store_id)
    {
    	if($role_id!=''){
    		$this->pagedata['role_id']=$role_id;
    	}
        if($store_id!=''){
    		$this->pagedata['store_id']=$store_id;
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
         $this->singlepage('admin/tools/regionSelect.html');
    }
    
    
    public function getRegionById($regionId)
    {
        if($_GET['store_id']){
            $store_id = $_GET['store_id'];
            $where = array('store_id|noequal' => $store_id);
        }else{
            $where = array();
        }
        $regions = app::get('ectools')->model('regions');
        $storelist_item = app::get('storelist')->model('storelist_item');
        if ($regionId)
            $aTemp = $regions->getList('region_id,p_region_id,local_name,ordernum,region_path', array('p_region_id' => $regionId), 0, -1, 'ordernum ASC,region_id ASC');
        else
            $aTemp = $regions->getList('region_id,p_region_id,local_name,ordernum,region_path', array('region_grade' => '1'), 0, -1, 'ordernum ASC,region_id ASC');
        
        if (is_array($aTemp)&&count($aTemp) > 0)
        {
            $region_id_arr_tmp = $storelist_item->getList('region_id',$where,0,-1);
            if(is_array($region_id_arr_tmp) && count($region_id_arr_tmp) > 0){
                $region_id_arr = array();
                foreach($region_id_arr_tmp as $region_id_row){
                    $region_id_arr[] = $region_id_row['region_id'];
                }
                unset($region_id_arr_tmp);
            }

            foreach($aTemp as $key => $val)
            {
                $aTemp[$key]['p_region_id']=intval($val['p_region_id']);
                $aTemp[$key]['step'] = intval(substr_count($val['region_path'],','))-1;
                $aTemp[$key]['child_count'] = $this->getChildCount($val['region_id']);
                if(is_array($region_id_arr) && count($region_id_arr) > 0 && in_array($val['region_id'],$region_id_arr)){
                    $aTemp[$key]['disable'] = '1';
                }
            }
        }
    	echo json_encode($aTemp);
    }
}