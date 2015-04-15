<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author qianzedong <qianzedong@shopex.cn>
 */

function theme_widget_style_category_menu(&$setting,&$render)
{
	$cagetory_list = app::get('b2c')->model('style')->getList('*');
	foreach($cagetory_list as $v)
	{
		$data['cagetory_list'][] = array(
			'title'=>$v['name'],
			'link'=>app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_match','act'=>'plist','arg0'=>$v['style_id'])),
		);
	}
	return $data;                                        
}
?>






