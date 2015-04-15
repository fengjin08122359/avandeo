<?php

class designer_ctl_admin_members extends desktop_controller {
	public function __construct($app) {
		parent::__construct($app);
		$this->member_model = $this->app->model('members');
		header("cache-control: no-store, no-cache, must-revalidate");
	}

	function index() {
		$custom_actions[] = array(
			'label' => app::get('b2c')->_('新增'),
			'icon'  => 'add.gif',
			//'disabled'=>'true',
			'href'   => 'index.php?app=designer&ctl=admin_members&act=add',
			'target' => 'dialog::{title:\''.app::get('b2c')->_('设计师').'\',width:600,height:350}',
		);
		$actions_base['title']               = app::get('b2c')->_('设计师列表');
		$actions_base['actions']             = $custom_actions;
		$actions_base['allow_detail_popup']  = true;
		$actions_base['use_buildin_set_tag'] = true;
		$actions_base['use_buildin_export']  = true;
		$actions_base['use_buildin_filter']  = true;
		$actions_base['use_view_tab']        = true;
		$this->finder('designer_mdl_members', $actions_base);
	}
	function add() {
		$this->display('admin/members/add.html');
	}
	function edit($id) {
		if ($id) {
			$member_info = $this->member_model->dump($id, '*', 'default');
			foreach ($member_info['goods'] as $goods) {
				$goodslink[] = $goods['goods_id'];
			}
		}
		$this->pagedata['goodslink']   = $goodslink;
		$this->pagedata['member_info'] = $member_info;
		$this->display('admin/members/add.html');
	}
	function save() {
		$this->begin();
		if ($_POST && !empty($_POST)) {
			$memberInfo = $_POST['member'];
			if ($this->member_model->save($memberInfo)) {
				// if ($_POST['linkid'] && !empty($_POST['linkid'])) {
				// 	$member_goods_model = $this->app->model('member_goods');
				// 	$member_goods_model->delete(array('member_id' => $memberInfo['member_id']));
				// 	foreach ($_POST['linkid'] as $link_id) {
				// 		$member_goods_info                 = array();
				// 		$member_goods_info['member_id']    = $memberInfo['member_id'];
				// 		$member_goods_info['goods_id']     = $link_id;
				// 		$member_goods_info['related_type'] = $_POST['linktype'][$link_id];
				// 		if (!$member_goods_model->save($member_goods_info)) {
				// 			$this->end(true, app::get('b2c')->_('关联商品添加失败！'));
				// 		}
				// 	}
				// }
			} else {
				$this->end(true, app::get('b2c')->_('设计师添加失败！'));
			}
			$this->end(true, app::get('b2c')->_('设计师添加成功！'));
		}
	}

}
?>