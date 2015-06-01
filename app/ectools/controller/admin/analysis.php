<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class ectools_ctl_admin_analysis extends desktop_controller
{
    
    public function chart_view() 
    {
         $show = $_GET['show'];

         //todo 这里需要根据不同的需求读取数据
         if($_GET['callback']){
             $data = kernel::single($_GET['callback'])->fetch_graph_data($_GET);
         }else{
             $data = kernel::single('ectools_analysis_base')->fetch_graph_data($_GET);
         }
         
         $this->pagedata['categories']='["' . @join('","', $data['categories']) . '"]';
        
         foreach($data['data'] AS $key=>$val){
             $tmp[] = '{name:"'.addslashes($key).'",data:['.@join(',', $val).']}';
         }
         $this->pagedata['data'] = '['.@join(',', $tmp).']';

         switch($show){
            case 'line':
                $this->display("analysis/chart_type_line.html");                
                break;
            case 'column':
                $this->display("analysis/chart_type_column.html");                
                break;
            default :
                $this->display("analysis/chart_type_default.html");                
                break;
        }   
    }//End Function
	function  store_volume(){
		$show = $_GET['show'];
		$store_id=(int)$_GET['store_id'];
		$statisObj=app::get('storelist')->model('store_statis');
		$store_list=$statisObj->getList("*",array('store_id'=>$store_id));
		if($store_list){
			foreach($store_list as $v){
				$creta_time[]=date("Y年m月",$v['create_time']);
				$data[date("Y年m月",$v['create_time'])]=$v['month_volume'];
			}
		}
		if($data){
			foreach($data as $key=>$d){
				
				$tmp[] = '{name:"'.addslashes($key).'",data:['.$d.']}';
			}
		}
		
		$this->pagedata['categories']='["' . @join('","', $creta_time) . '"]';
		$this->pagedata['data'] = '['.@join(',', $tmp).']';
		switch($show){
			case 'line':
				$this->display("analysis/chart_type_line.html");
				break;
			case 'column':
				$this->pagedata['data'] = '['.@join(',', $tmp).']';
				$this->pagedata['categories']=$tmp? '销售额':"";
				$this->display("analysis/store_type_column.html");
				break;
			default :
				$this->display("analysis/chart_type_default.html");
				break;
		}
	}
}//End Class