<?php

class designer_ctl_admin_case extends desktop_controller {
	public function __construct($app) {
		parent::__construct($app);
		$this->case_model = $this->app->model('case');
		header("cache-control: no-store, no-cache, must-revalidate");
	}
	public function index() {
		$actions_base['title']               = app::get('b2c')->_('案例列表');
		//$actions_base['actions']             = $custom_actions;
		$actions_base['allow_detail_popup']  = true;
		$actions_base['use_buildin_set_tag'] = true;
		$actions_base['use_buildin_export']  = true;
		$actions_base['use_buildin_filter']  = true;
		$actions_base['use_view_tab']        = true;
		$this->finder('designer_mdl_case', $actions_base);
	}
	function add($member_id) {
		$this->pagedata['member_id'] = $member_id;
		$this->params();
		$this->display('admin/case/addcase.html');
	}
	//获取户型列表和面积列表
	function params(){
		$objHuxing = $this->app->model('huxing')->getList('*');
		$objArea = $this->app->model('area')->getList('*');
		foreach ($objHuxing as $item) {
			$this->pagedata['huxing'][$item['hx_id']] = $item['hx_name'];
		}
		foreach ($objArea as $item) {
			$this->pagedata['area'][$item['a_id']] = $item['a_value'];
		}
	}
	function edit($case_id) {
		if ($case_id) {
			$case_info = $this->case_model->dump($case_id, '*', 'default');

			foreach ($case_info['goods'] as $goods) {
				$goodslink[]           = $goods['goods_id'];
				$x[$goods['goods_id']] = $goods['x'];
				$y[$goods['goods_id']] = $goods['y'];
			}
		}
		$this->params();
		$this->pagedata['goodslink'] = $goodslink;
		$this->pagedata['case']      = $case_info;
		$this->pagedata['x'] = $x;
		$this->pagedata['y'] = $y;
		$this->display('admin/case/addcase.html');
	}
	function showimg($case_id) {
		$image_default_id = $this->case_model->getList('image_default_id', array('case_id' => $_GET['id']));

		echo base_storager::image_path($image_default_id[0]['image_default_id']);
	}
	function save() {
		$this->begin('index.php?app=designer&ctl=admin_case&act=index');
		if ($_POST && !empty($_POST)) {
			if ($_POST['case']['case_id']) {unset($_POST['case']['members']);
			}

			$caseData = $_POST['case'];
			if ($this->case_model->save($caseData)) {
				if ($_POST['linkid'] && !empty($_POST['linkid'])) {
					$case_goods_model = $this->app->model('case_goods');
					$case_goods_model->delete(array('case_id' => $caseData['case_id']));
					foreach ($_POST['linkid'] as $link_id) {
						$case_goods_data             = array();
						$case_goods_data['case_id']  = $caseData['case_id'];
						$case_goods_data['goods_id'] = $link_id;
						$case_goods_data['x']        = $_POST['linktype'][$link_id]['x'];
						$case_goods_data['y']        = $_POST['linktype'][$link_id]['y'];
						if (!$case_goods_model->save($case_goods_data)) {
							$this->end(true, app::get('b2c')->_('案例关联商品添加失败！'));
						}
					}
				}
			} else {
				$this->end(true, app::get('b2c')->_('案例添加失败！'));
			}
			$this->end(true, app::get('b2c')->_('案例添加成功！'));
		}

	}
}
