<?php

class designer_mdl_case extends dbeav_model {
	var $has_many = array(
		'goods' => 'case_goods:contrast:case_id^case_id',
	);
	var $subSdf = array(
		'default' => array(
			'goods'  => array('*'),
			// 'products' => array('name'),
		),
	);
	public function __construct($app) {
		parent::__construct($app);
	}

}
?>
