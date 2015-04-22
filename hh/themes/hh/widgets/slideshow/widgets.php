<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

$setting['author']='ShopEx';

$setting['version']='v1.0';

$setting['order']=20;


$setting['name']=app::get('b2c')->_('★图文轮播广告★');

$setting['catalog']    = app::get('b2c')->_('广告相关');

$setting['description']    = app::get('b2c')->_('图文轮播广告');

$setting['usual']    = '1';

$setting['stime']='2015-2';

$setting['template'] = array(
                            'default.html'=>app::get('b2c')->_('默认')
                        );

?>
