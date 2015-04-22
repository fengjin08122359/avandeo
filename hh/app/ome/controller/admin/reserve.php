<?php

class designer_ctl_admin_reserve extends desktop_controller {
	public function __construct($app) {
		parent::__construct($app);
		$this->reserve_model = $this->app->model('reserve');
		header("cache-control: no-store, no-cache, must-revalidate");
	}

	function index() {
		$actions_base['title']               = app::get('b2c')->_('预约列表');
		$actions_base['actions']             = $custom_actions;
		$actions_base['allow_detail_popup']  = true;
		$actions_base['use_buildin_set_tag'] = true;
		$actions_base['use_buildin_export']  = true;
		$actions_base['use_buildin_filter']  = true;
		$actions_base['use_view_tab']        = true;
		$this->finder('designer_mdl_reserve', $actions_base);
	}
}
?>