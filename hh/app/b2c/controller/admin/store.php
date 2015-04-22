<?php
/**
 * 线下线下体验店
 * @author qianzedong <qianzedong@shopex.cn>
 */
class b2c_ctl_admin_store extends desktop_controller
{
	/**
	 * 风格搭配列表
	 * @author qianzedong <qianzedong@shopex.cn>
	 */
	public function index()
	{
		$filter = array();
		$this->finder('b2c_mdl_store',array(
        'title'=>app::get('b2c')->_('线下体验店列表'),
        'actions'=>array(
            array('label'=>app::get('b2c')->_('添加线下体验店'),'icon'=>'add.gif','href'=>'index.php?app=b2c&ctl=admin_store&act=add','target'=>'dialog::{title:\''.app::get('b2c')->_('添加线下体验店').'\',width:900,height:600}'),

        )
        ));
	}
	public function add()
	{
		$this->_handle();
	}
	public function edit($store_id)
	{
		$this->_handle($store_id);
	}
	public function _handle($store_id = 0)
	{
		if($_POST)
		{
			$this->begin();
			$rs = app::get('b2c')->model('store')->save($_POST);
			if($rs){
				$this->end(true,app::get('b2c')->_('成功'));
			}else{
				$this->end(false,app::get('b2c')->_('失败'));
			}			
		}else{
			$this->pagedata['store'] = app::get('b2c')->model('store')->dump(array('store_id'=>$store_id));
			$this->pagedata['finder'] = $_GET['_finder']['finder_id'];
			$this->page('admin/store/handle.html');
		}
	}
}