<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class b2c_finder_discount{


    var $column_control = '操作';
    var $column_control_order = COLUMN_IN_HEAD;

    function column_control($row){
        return '<a href="index.php?app=b2c&ctl=admin_discount&act=edit&p[0]='.$row['discount_id'].'&finder_id='.$_GET['_finder']['finder_id'].'"  target="blank">'.app::get('b2c')->_('编辑').'</a>';
    }


}
