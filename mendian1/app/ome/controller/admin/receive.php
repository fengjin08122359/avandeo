<?php

class ome_ctl_admin_receive extends desktop_controller {
	public function __construct($app) {
		parent::__construct($app);
		header("cache-control: no-store, no-cache, must-revalidate");
	}

	function index() {
		$actions_base['title']               = app::get('b2c')->_('收银列表');
		$actions_base['actions']             = $custom_actions;
		$actions_base['allow_detail_popup']  = true;
		$actions_base['use_buildin_set_tag'] = true;
		$actions_base['use_buildin_export']  = true;
		$actions_base['use_buildin_filter']  = true;
		$actions_base['use_view_tab']        = true;
		$this->finder('ectools_mdl_payments', $actions_base);
	}
}
?>