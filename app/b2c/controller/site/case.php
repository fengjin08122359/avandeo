<?php
/**
 * Created by PhpStorm.
 * User: chugongbiao
 * Date: 15/1/28
 * Time: 下午3:00
 */
class b2c_ctl_site_case extends site_controller{
    public function glist(){
        $this->set_tmpl('case_list');
        $this->page('site/case/list.html');
    }
}