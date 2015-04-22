<?php

class ome_ctl_admin_return extends desktop_controller {
	public function __construct($app) {
		parent::__construct($app);
		header("cache-control: no-store, no-cache, must-revalidate");
	}

	function index(){
        $this->finder('b2c_mdl_reship',array(
            'title'=>app::get('b2c')->_('退货列表'),
            'allow_detail_popup'=>true,
            'use_buildin_recycle'=>false,
            'use_view_tab'=>true,
            'params'=>array(
                'bill_type' => 'reship',
            )
            ));
    }
}
?>