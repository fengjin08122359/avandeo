<?php
class wap_ctl_admin_theme_manage extends desktop_controller{

    var $workground = 'wap.workground.theme';

    /*
     * @param object $app
     */
    function __construct($app)
    {
        parent::__construct($app);
        $this->_request = kernel::single('base_component_request');
        $this->_response = kernel::single('base_component_response');
    }//End Function

    //列表
    public function index()
    {
        //finder
        kernel::single('wap_theme_install')->check_install();
        $actions = array(
                    array('label'=>app::get('wap')->_('上传模板'),'href'=>'index.php?app=wap&ctl=admin_theme_manage&act=swf_upload','target'=>'dialog::{title:\''.app::get('wap')->_('上传模板').'\',width:400,height:280}'),
                    array('label'=>app::get('wap')->_('删除'),'icon'=>'del.gif','confirm'=>app::get('wap')->_('确定删除选中项？删除后不可从回收站恢复'),'submit'=>'?app=wap&ctl=admin_theme_manage&act=delete')
                );

        $this->finder('wap_mdl_themes',array('title'=>app::get('wap')->_('模板管理'), 'actions'=>$actions,'use_buildin_recycle'=>false));

    }//End Function

    //flash上传
    public function swf_upload()
    {
        $this->pagedata['ssid'] = kernel::single('base_session')->sess_id();
        $this->pagedata['swf_loc'] = kernel::router()->app->res_url;
        $this->pagedata['upload_max_filesize'] = kernel::single('wap_theme_install')->ini_get_size('upload_max_filesize');
        $this->display('admin/theme/manage/swf_upload.html');
    }//End Function

    public function upload()
    {
        $themeInstallObj = kernel::single('wap_theme_install');
        $res = $themeInstallObj->install($_FILES['Filedata'],$msg);
        if($res){
            $theme_url = defined('THEMES_IMG_URL') ? THEMES_IMG_URL : kernel::base_url(1) . '/wap_themes';
            $img = $theme_url.'/' . $res['theme'] . '/preview.jpg';
            echo '<img src="'.$img.'" onload="$(this).zoomImg(50,50);" />';
        }else{
            echo $msg;
        }
    }//End Function

    protected function check($theme,&$msg='')
    {
        if(empty($theme)){
            $msg = app::get('wap')->_('缺少参数');
            return false;
        }
        /** 权限校验 **/
        if($theme && preg_match('/(\..\/){1,}/', $theme)){
            $msg = app::get('wap')->_('非法操作');
            return false;
        }
        return true;
    }//End Function

    public function delete()
    {
        $this->begin();
        $post = $this->_request->get_post();
        foreach ((array)$post['theme'] as $theme){
            if(!$this->check($theme,$msg))   $this->_error($msg);
        }
        if(app::get('wap')->model('themes')->delete_file(array('theme'=>$post['theme']))){
            $this->end(true, app::get('wap')->_('删除成功'), 'index.php?app=wap&ctl=admin_theme_manage&act=index');
        }else{
            $this->end(false, app::get('wap')->_('删除失败'));
        }
    }//End Function

    public function set_default()
    {
        $this->begin('javascript:finderGroup["'.$_GET['finder_id'].'"].refresh();');
        $theme = $this->_request->get_get('theme');
        if(!$this->check($theme,$msg))   $this->_error($msg);
        if($theme){
            if(kernel::single('wap_theme_base')->set_default($theme)){
                $this->end(true, app::get('wap')->_('设置成功'));
            }else{
                $this->end(false, app::get('wap')->_('设置失败'));
            }
        }
    }//End Function



    public function bak() {
        $this->begin();
        $theme = $this->_request->get_get('theme');
        if(!$this->check($theme,$msg))   $this->_error($msg);
        $data = kernel::single('wap_theme_tmpl')->make_configfile($theme);

        if(kernel::single('wap_theme_file')->bak_save($theme, $data)){
            $this->end(true, app::get('wap')->_('备份成功！'));
        }else{
            $this->end(false, app::get('wap')->_('备份失败！'));
        }
    }

    //模板维护
    public function maintenance()
    {
        $theme = $this->_request->get_get('theme');
        if (!$theme){
            if(is_dir(WAP_THEME_DIR)){
                kernel::single('wap_theme_base')->maintenance_theme_files(WAP_THEME_DIR);
            }
        }else{
            kernel::single('wap_theme_base')->maintenance_theme_files($theme);
        }
    }//End Function


    public function reset() {
        $this->begin();
        $theme = $this->_request->get_get('theme');
        $loadxml = $this->_request->get_get('rid');
        if(!$this->check($theme,$msg))   $this->_error($msg);
        if(kernel::single("wap_theme_install")->init_theme($theme, true, false, $loadxml)) {
            $this->end(true, app::get('wap')->_('还原成功！'));
        } else {
            $this->end(false, app::get('wap')->_('还原失败！'));
        }
    }

    public function cache_version()
    {
        $theme = $this->_request->get_get('theme');
        if(!$this->check($theme,$msg))   $this->_error($msg);
        $this->begin();
        $this->end(kernel::single('wap_theme_tmpl')->touch_theme_tmpl($theme));
    }//End Function

    public function download()
    {
        $theme = $this->_request->get_get('theme');
        if(!$this->check($theme,$msg))   $this->_error($msg);
        kernel::single('wap_theme_tmpl')->output_pkg($theme);
        exit;
    }//End Function

}

