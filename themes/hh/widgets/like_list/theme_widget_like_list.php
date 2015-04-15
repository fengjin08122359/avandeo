<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_like_list(&$setting, &$render) {
	$link_info = app::get('designer')->model('members')->getList('*', array('member_id|in' => $setting['linkid']));
	return $link_info;
}
?>
