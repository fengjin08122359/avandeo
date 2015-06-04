<?php
/**
 * 
 * 此脚本根据门店主的销售额自动评级
 */

set_time_limit(0);
$root_dir = realpath(dirname(__FILE__) . '/../../../');
require_once( $root_dir . '/config/config.php');
require ($root_dir . '/app/base/kernel.php');
define('APP_DIR', $root_dir . "/app/");
include_once (APP_DIR . "/base/defined.php");
include_once (APP_DIR . "/base/lib/http.php");
if (!kernel :: register_autoload()){
	require (APP_DIR . '/base/autoload.php');
}
cachemgr::init(false);
//表名前缀
$table_pre_fix=kernel::database()->prefix;
$m = date('Y-m-d', mktime(0,0,0,date('m')-1,1,date('Y'))); //上个月的开始日期
$t = date('t',strtotime($m)); //上个月共多少天

$start = date('Y-m-d', mktime(0,0,0,date('m')-1,1,date('Y'))); //上个月的开始日期

$end = date('Y-m-d', mktime(0,0,0,date('m')-1,$t,date('Y'))); //上个月的结束日期
$from_time=(int)strtotime($start);
$end_time=(int)strtotime($end)+86399;
//检测上个是否已经结算过，已结算不用写入表
if(file_exists($root_dir."/app/storelist/data/".date("Y-m-d",$end_time).".txt")){
	echo "Last month settlement!";
	exit();
}
$orders_arr=kernel::database()->select("SELECT * FROM ".$table_pre_fix."b2c_orders WHERE store_id!=0");
if($orders_arr){
	foreach($orders_arr as $s){
		$store_id[]=$s['store_id'];
	}
}
if(!empty($store_id)){
	$new_store_id=array_unique($store_id);
}

if(!empty($new_store_id)){
	sort($new_store_id);
	foreach($new_store_id as $v){
	
		$sum[$v]=kernel::database()->select("SELECT SUM(total_amount) AS total_amount FROM ".$table_pre_fix."b2c_orders WHERE store_id ={$v} AND pay_status='1' AND ship_status='1' AND status='finish' AND createtime>={$from_time} AND createtime<={$end_time}");
	}
}

if($sum){
	foreach($sum as $k=>$s_sum){
		foreach($s_sum as $ss){
			$sum_arr[$k]=$ss;
		}
	}
}

$n_arr=array();
if($sum_arr){
	 foreach($sum_arr as $vk=>$lv){
				if($lv['total_amount']!=NULL){
					$n_arr[$vk]['total_amount']=$lv['total_amount'];
					$n_arr[$vk]['name']=__getGrade($lv['total_amount']);
				}
	} 
}

if(!empty($n_arr)){
	$time=time();
	foreach($n_arr as $key=>$v_n){
		$flag=kernel::database()->exec("INSERT INTO ".$table_pre_fix."storelist_store_statis (store_id,month_volume,create_time,name) VALUES({$key},'{$v_n['total_amount']}',{$end_time},'{$v_n['name']}')");
	}
	if($flag){
		if(!is_dir($root_dir."/app/storelist/data")){
			$is_mkdir=mkdir($root_dir."/app/storelist/data");
		}
		if($is_mkdir)file_put_contents($root_dir."/app/storelist/data/".date("Y-m-d",$end_time).".txt",$n_arr);
	}
}
/*
 * 
 * 获取销售等级名称
 */
function __getGrade($sales){
	
	if(!$sales)return false;
	$table_pre_fix=kernel::database()->prefix;
	//获取销售等级
	$sales_lv=app::get('storelist')->model('store_lv')->getList("*");
	
	$max_value=kernel::database()->selectRow("SELECT name,max_value  FROM ".$table_pre_fix."storelist_store_lv WHERE max_value =(SELECT MAX(max_value)FROM ".$table_pre_fix."storelist_store_lv)");
	$small_value=kernel::database()->selectRow("SELECT name,small_value  FROM ".$table_pre_fix."storelist_store_lv WHERE small_value =(SELECT MIN(small_value)FROM ".$table_pre_fix."storelist_store_lv)");
	 if($sales_lv){
	 	foreach($sales_lv as $k=>$vk){
	 		if((int)$sales>=(int)$vk['small_value'] && (int)$sales<=(int)$vk['max_value']){
	 			return $vk['name'];
	 		}else if((int)$sales>(int)$max_value['max_value']){
	 			return $max_value['name'];
	 		}else if((int)$sales<(int)$small_value['small_value']){
	 			return $small_value['name'];
	 		}
	 	}
	 	
	 }
	
}
?>