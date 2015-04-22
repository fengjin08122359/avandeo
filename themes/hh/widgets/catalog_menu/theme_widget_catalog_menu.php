<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author qianzedong <qianzedong@shopex.cn>
 */

function theme_widget_catalog_menu(&$setting,&$render)
{
	$data['cagetory_list'] = &app::get('b2c')->model('goods_cat')->getMap();
	$data['cat_id'] = $render->pagedata['cat_id'];
	return $data;                                        
}

function _ex_vertical_getAllChildAttr($arr,$pid,$attribute = 'cat_id'){

	
	foreach ($arr as $item) {
		if(in_array($pid, explode(',', $item['cat_path']))){
			$_return[] = $item[$attribute];
		}
	}

	return $_return;


}


function _ex_vertical_getLinkBrandIds($typeids){

	$sql = 'SELECT b.brand_id FROM '.kernel::database()->prefix.'b2c_type_brand ty_b LEFT JOIN '.kernel::database()->prefix.'b2c_brand b ON ty_b.brand_id=b.brand_id WHERE type_id  in('.implode(',',array_unique($typeids)).') order by ordernum desc';

	$res =  app::get('b2c')->model('brand')->db->select($sql );

	foreach ($res as $key => $value) {
		$_return[] = $value['brand_id'];
	}

	return array_unique($_return);

}

function _ex_vertical_getSales(){

	$goods_sales_mdl = app::get('b2c')->model('sales_rule_goods');
	$goods_sales_list = $goods_sales_mdl->getList('*');

	return $goods_sales_list;
}

?>






