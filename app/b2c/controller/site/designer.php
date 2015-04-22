<?php
/**
 * Created by PhpStorm.
 * User: chugongbiao
 * Date: 15/1/28
 * Time: 下午3:00
 */
class b2c_ctl_site_designer extends site_controller {
	public function __construct(&$app) {
		parent::__construct($app);
		$this->_response->set_header('Cache-Control', 'no-store');
		$this->member_model = app::get('designer')->model('members');
	}
	public function glist() {
		$this->set_tmpl('design_list');
		$this->page('site/design/list.html');
	}
	public function index() {
		$members                   = $this->member_model->getList('member_id,name,position,image_default_id');
		$this->pagedata['members'] = $members;
		$this->set_tmpl('designer_index');
		$this->page('site/designer/page-desiner.html');
	}
	public function personal($member_id) {
		$designers                   = $this->member_model->glist();
		$designerDetail              = $this->member_model->dump($member_id, '*', 'default');
		$this->pagedata['detail']    = $designerDetail;
		$this->pagedata['designers'] = $designers;
		$this->set_tmpl('designer_personal');
		$this->page('site/designer/page-personal.html');
	}
	function reserve($member_id) {
		if ($_POST && !empty($_POST)) {

			$reserver_mdl = app::get('designer')->model('reserve');
			if ($reserver_mdl->save($_POST)) {
				$url = $this->gen_url(array('app' => 'b2c', 'ctl' => 'site_designer', 'act' => 'personal', 'arg0' => $member_id));
				$this->_response->set_redirect($url)->send_headers();
			}
		}
	}
	public function detail($case_id, $member_id) {

		$designerDetail = $this->member_model->dump($member_id, '*', 'default');
		foreach ($designerDetail['case'] as &$case) {

			foreach ($case['goods'] as &$goods) {
				$goodsInfo     = $this->app->model('goods')->dump(array('goods_id' => $goods['goods_id']));
				$goods['info'] = $goodsInfo;
				$goods['info']['products'] = $this->app->model('products')->getList('*',array('goods_id' => $goods['goods_id']));
			}
			unset($goods);
		}
		unset($case);
		$this->pagedata['detail']    = $designerDetail;
		$this->pagedata['designers'] = $designerDetail;
		$this->pagedata['case_id']   = $case_id;
		$this->set_tmpl('designer_detail');
		$this->page('site/designer/page-detail.html');
	}

}