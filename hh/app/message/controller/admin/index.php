<?php

class message_ctl_admin_index extends desktop_controller{

	function index(){
		$this->finder('message_mdl_message',array('actions'=> array(
			array('label'=>app::get('weixin')->_('添加消息'),'icon'=>'add.gif','href'=>'index.php?app=message&ctl=admin_index&act=add','target'=>'_blank')
		),'use_buildin_filter'=>true,'title'=>app::get('weixin')->_('列表')));
	}
	function add(){
			 $this->singlepage('admin/add.html');
	}


}