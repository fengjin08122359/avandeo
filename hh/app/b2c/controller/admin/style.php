<?php
/**
 * 风格搭配设置
 * @author qianzedong <qianzedong@shopex.cn>
 */
class b2c_ctl_admin_style extends desktop_controller
{
	/**
	 * 风格搭配列表
	 * @author qianzedong <qianzedong@shopex.cn>
	 */
	public function index()
	{
		$filter = array();
		$this->finder('b2c_mdl_style',array(
        'title'=>app::get('b2c')->_('风格配置'),
        'actions'=>array(
            array('label'=>app::get('b2c')->_('添加风格'),'icon'=>'add.gif','href'=>'index.php?app=b2c&ctl=admin_style&act=add','target'=>'dialog::{title:\''.app::get('b2c')->_('添加风格').'\',width:500,height:500}'),

        )
        ));
	}
	public function add()
	{
		$this->_handle();
	}
	public function edit($style_id)
	{
		$this->_handle($style_id);
	}
	public function _handle($style_id = 0)
	{
		if($_POST)
		{
			$this->begin();
			
			$_POST['status'] = !empty($_POST['status']) ? array_sum($_POST['status']) : 0;
			$rs = app::get('b2c')->model('style')->save($_POST);
			if($rs){
				$this->end(true,app::get('b2c')->_('成功'));
			}else{
				$this->end(false,app::get('b2c')->_('失败'));
			}			
		}else{
			$this->pagedata['status'] = kernel::single('b2c_style_data')->get_status();
			$this->pagedata['style'] = app::get('b2c')->model('style')->dump(array('style_id'=>$style_id));
			$this->pagedata['finder'] = $_GET['_finder']['finder_id'];
			$this->page('admin/style/handle.html');
		}
	}
}