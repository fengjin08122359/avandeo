<?php

class designer_mdl_members extends dbeav_model {
	var $has_many = array(
		'goods' => 'member_goods:contrast:member_id^member_id',
		'case'  => 'case:contrast:member_id^member_id',
	);
	var $subSdf = array(
		'default' => array(
			'goods'  => array('*'),
			'case'   => array('*', array('goods'   => '*')),
		),
	);
	public function __construct($app) {
		parent::__construct($app);
	}
	//获取所有设计师id和name并组合成一个供html_option调用的数组
	public function glist() {
		$members = $this->getList('member_id,name');
		foreach ($members as $member) {
			$membersOption[$member['member_id']] = $member['name'];
		}
		return $membersOption;
	}
}
?>
