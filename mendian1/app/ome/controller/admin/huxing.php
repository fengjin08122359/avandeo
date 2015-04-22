<?php 

class designer_ctl_admin_huxing extends desktop_controller{
	public function __construct($app) {
		parent::__construct($app);
		$this->_model = $this->app->model('huxing');
		header("cache-control: no-store, no-cache, must-revalidate");
	}
	function index() {
		$custom_actions[] = array(
			'label' => app::get('b2c')->_('新增'),
			'icon'  => 'add.gif',
			//'disabled'=>'true',
			'href'   => 'index.php?app=designer&ctl=admin_huxing&act=add',
			'target' => 'dialog::{title:\''.app::get('b2c')->_('户型').'\',width:600,height:350}',
		);
		$actions_base['title']               = app::get('b2c')->_('列表');
		$actions_base['actions']             = $custom_actions;
		$actions_base['allow_detail_popup']  = true;
		$actions_base['use_buildin_set_tag'] = true;
		$actions_base['use_buildin_export']  = true;
		$actions_base['use_buildin_filter']  = true;
		$actions_base['use_view_tab']        = true;
		$this->finder('designer_mdl_huxing', $actions_base);
	}
	function add(){
		$this->display('admin/huxing/add.html');
	}
	function save(){
		if($_POST && !empty($_POST)){
			if($this->_model->save($_POST)){
				$this->splash('success',$this->app->_('添加成功'));
			}
		}
	}
}

