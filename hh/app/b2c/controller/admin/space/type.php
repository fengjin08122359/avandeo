<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_ctl_admin_space_type extends desktop_controller{

    var $workground = 'b2c_ctl_admin_goods';

    function index(){
        $this->finder('b2c_mdl_space_type',array('actions'=> array(
            array('label'=>app::get('b2c')->_('添加空间类型'),'icon'=>'add.gif','href'=>'index.php?app=b2c&ctl=admin_space_type&act=add','target'=>'dialog::{ title:\''.app::get('b2c')->_('添加空间类型').'\', width:800, height:300}')
        ),'title'=>app::get('b2c')->_('空间类型')));
    }

    function add(){
        $gtype = $_POST['gtype'];
        if($gtype['type_id']){
            $oType = &$this->app->model('space_type');
            $subsdf = array(
                'props'=>array('*',array('props_value'=>array('*',null, array( 0,-1,'order_by ASC' ))) )
            );
            $gtype = array_merge($oType->dump($gtype['type_id'],'*',$subsdf),$gtype );
        }
        if(is_array($gtype['props'])){
            foreach($gtype['props'] as $k=>$v){
                if(empty($k)){
                    $gtype['props'] = null;
                }
            }
        }
        $this->pagedata['gtype'] = $gtype;
        $this->page('admin/space/space_type/edit_type_edit.html');
    }

    function edit($type_id=0){
        if($type_id){
            $oType = $this->app->model('space_type');
            $subsdf = array(
                'props'=>array('*',array('props_value'=>array('*',null, array( 0,-1,'order_by ASC' ))) )
            );
            $gtype = $oType->dump($type_id,'*',$subsdf);
        }
        if(is_array($gtype['props'])){
            foreach($gtype['props'] as $k=>$v){
                if(empty($k)){
                    $gtype['props'] = null;
                }
            }
        }
        $this->pagedata['gtype'] = $gtype;

        $this->page('admin/space/space_type/edit_type_edit.html');
    }

    function check_type(){
        $oGtype = &$this->app->model('goods_type');
        $typeId = current( (array)$oGtype->dump( array( 'name'=>$_POST['name'],'type_id' ) ) );
        if( $typeId && $_POST['id'] != $typeId )
            echo 'false';
        else
            echo 'true';

    }

    function save(){
        /*$_POST = array (
            'gtype' =>
                array (
                    'type_id' => '',
                    'name' => '牛逼',
                    'props' =>
                        array (
                            'new_0' =>
                                array (
                                    'name' => '风格',
                                    'type' => '2',
                                    'options' =>
                                        array (
                                            'id' =>
                                                array (
                                                    0 => '',
                                                    1 => '',
                                                ),
                                            'value' =>
                                                array (
                                                    0 => '男性现代',
                                                    1 => '女性现代',
                                                ),
                                        ),
                                    'show' => 'on',
                                    'ordernum' => '',
                                ),
                            'new_1' =>
                                array (
                                    'name' => '到货时间',
                                    'type' => '2',
                                    'options' =>
                                        array (
                                            'id' =>
                                                array (
                                                    0 => '',
                                                    1 => '',
                                                    2 => '',
                                                ),
                                            'value' =>
                                                array (
                                                    0 => '1-2周',
                                                    1 => '2-3周',
                                                    2 => '3-4周',
                                                ),
                                        ),
                                    'show' => 'on',
                                    'ordernum' => '',
                                ),
                        ),
                    'price' =>
                        array (
                            1 =>
                                array (
                                    'min' => '1000',
                                    'max' => '5000',
                                ),
                            2 =>
                                array (
                                    'min' => '5000',
                                    'max' => '10000',
                                ),
                            3 =>
                                array (
                                    'min' => '15000',
                                    'max' => '20000',
                                ),
                            4 =>
                                array (
                                    'min' => '20000',
                                    'max' => '25000',
                                ),
                        ),
                ),
        );*/
        $gtype = $_POST['gtype'];
        $this->begin('index.php?app=b2c&ctl=admin_space_type&act=index');
        if( !$gtype['name'] ){
            $this->end(false,app::get('b2c')->_('请输入类型名称'));
        }

        $oGtype = &$this->app->model('space_type');

        $typeId = current( (array)$oGtype->dump( array( 'name'=>$gtype['name'],'type_id' ) ) );
        if( $typeId && $gtype['type_id'] != $typeId ){
            $this->end(false,app::get('b2c')->_('类型名称已存在'));
        }

        //属性
        $this->_preparedProps($gtype);
        //价格区间
        $this->_preparedPrice($gtype,$errorMsg);
        if($errorMsg) {
            $this->end(false,$errorMsg);
        }

        $this->end($oGtype->save($gtype),app::get('b2c')->_('操作成功'));
    }

    function setPropsValue(){
        reset( $_POST['gtype']['props'] );
        $this->pagedata['props_value'] = current( $_POST['gtype']['props'] );
        $this->pagedata['props_key'] = key( $_POST['gtype']['props'] );
        $this->display('admin/space/space_type/set_props_value.html');
    }

    function doSetPropsValue(){
        echo '==';
    }

    function _preparedProps(&$gtype){
        if( !$gtype['props'] ){
            $gtype['props'] = array();
            return;
        }
        $searchType = array(
            '0' => array('type' => 'input', 'search' => 'input'),
            '1' => array('type' => 'input', 'search' => 'disabled'),
            '2' => array('type' => 'select', 'search' => 'nav'),
            '3' => array('type' => 'select', 'search' => 'select'),
            '4' => array('type' => 'select', 'search' => 'disabled'),
        );
        $props = array();
        $selectIndex = 1;

        foreach( $gtype['props'] as $aProps ){
            if( !$aProps['name'] )
                continue;
            if(is_numeric($aProps['type'])) {
                $aProps = array_merge( $aProps,$searchType[$aProps['type']] );
                if($aProps['type'] == 'input') {
                    unset($aProps['options']);
                }
            }
            if( !$aProps['options'] ){
                unset($aProps['options']);
            }else{
                $tAProps = array();
                $aProps['optionIds'] = $aProps['options']['id'];
                foreach( $aProps['options']['value'] as $opk => $opv ){
                    $opv = explode('|',$opv);
                    $tAProps['options'][$opk] = $opv[0];
                    unset($opv[0]);
                    $tAProps['optionAlias'][$opk] = implode('|',(array)$opv);
                }
                $aProps['options'] = $tAProps['options'];
                $aProps['optionAlias'] = $tAProps['optionAlias'];
            }
            $aProps['ordernum']= intval( $aProps['ordernum'] );
            if( $aProps['type'] == 'input' ){
                $propskey = $inputIndex++;
            }else{
                $propskey = $selectIndex++;
            }
            $aProps['goods_p'] = $propskey;
            if(!isset($aProps['show'])){
            	$aProps['show'] = '';
            }
            $props[$propskey] = $aProps;
        }
        if( $selectIndex>31 ){
            $this->end(false,app::get('b2c')->_('选择属性不能超过30项'));
        }
        $gtype['props'] = $props;
        $props = null;
    }

    function _preparedParams(&$gtype,&$errorMsg=''){
        if( !$gtype['params'] ){
            $gtype['params'] = array();
            return ;
        }
        $params = array();
        foreach( $gtype['params'] as $aParams ){
            if( !$aParams['name'] ) {
                $errorMsg = app::get('b2c')->_('请为参数表中参数组添加参数名');
                break;
            }
            $paramsItem = array();
            foreach( $aParams['name'] as $piKey => $piName ){
                if(!$piName) {
                    $errorMsg = app::get('b2c')->_('请完成参数表中参数名');
                    break 2;
                }
                $paramsItem[$piName] = $aParams['alias'][$piKey];
            }
            if(!$aParams['group']) {
                $errorMsg = app::get('b2c')->_('请完成参数表中参数组名称');
                break;
            }
            $params[$aParams['group']] = $paramsItem;
        }
        $gtype['params'] = $params;
        $params = null;
    }

    function _preparedMinfo(&$gtype,&$errorMsg=''){
        if(!$gtype['minfo']){
            $gtype['minfo'] = array();
            return;
        }
        $minfo = $gtype['minfo'];
        foreach( $minfo as $minfoKey => $aMinfo ){
            if( !trim( $aMinfo['label'] ) ){
                unset( $gtype['minfo'][$minfoKey] );
                $errorMsg = app::get('b2c')->_('请完成必填信息名称');
                break;
            }
            if( !trim($aMinfo['name']) )
                $gtype['minfo'][$minfoKey]['name'] = 'M'.md5($aMinfo['label']);
            if( $aMinfo['type'] == 'select' )
                $gtype['minfo'][$minfoKey]['options'] = explode(',',$aMinfo['options']);
            else
                unset( $gtype['minfo'][$minfoKey]['options'] );
        }
        $gtype['minfo'] = array_values( $gtype['minfo'] );
    }

    function _preparedTab(&$gtype,$errorMsg){
        if(!$gtype['tab']){
            $gtype['tab'] = array();
            return;
        }
        $tab = $gtype['tab'];
        foreach( $tab as $tab_key => $tab_value ){
            if( !trim( $tab_value['name'] ) ){
                unset( $gtype['tab'][$tab_key] );
                $errorMsg = app::get('b2c')->_('请完成必填标签名字');
                break;
            }
        }
    }

    function _preparedPrice (&$gtype,$errorMsg){
        if(!$gtype['price']){
            $gtype['price'] = array();
            return;
        }
    }

    function _preparedSpec(&$gtype){
        if(!$gtype['spec']){
            $gtype['spec'] = array();
            return;
        }
        $spec = array();
        foreach( $gtype['spec']['spec_id'] as $k => $aSpec ){
            $spec[] = array(
                'spec_id'=>$aSpec,
                'spec_style' => $gtype['spec']['spec_type'][$k]
            );
        }
        $gtype['spec'] = $spec;
        $spec = null;
    }



    function fetchProtoTypes($link,$querystring='',$nodeType=''){
        header('Content-Type: text/html;charset=utf-8');
        $net = &kernel::single('base_httpclient');
        $cert = base_certificate::get('certificate_id');
        $token = base_certificate::get('token');
        $sc = md5('goostypefeed'.$cert.$token);
        $url = 'http://feed.shopex.cn/goodstype/'.$link.'?certificate='.$cert.'&sc='.$sc.($querystring?'&'.$querystring:'').($nodeType?'&nodeType='.$nodeType:'');
        $net->http_ver = '1.0';
        $net->defaultChunk = 30000;
        $result = $net->get($url);
        if($result = $net->get($url)){
             $script = '<SCRIPT LANGUAGE="JavaScript">loadLocalBrands();</SCRIPT><script>function checkTypeNameExists(){
                 new Request({url:\'index.php?app=b2c&ctl=admin_goods_type&act=checkTypeNameExists\',method:\'post\',data:\'gtypename=\'+$(\'gtypename\').value,evalScripts:true}).send();
             }
            $("closeftpbutton").getParent("form").store("target",{
                onComplete:function(){
                    $("closeftpbutton").getParent(".dialog").retrieve("instance").close();
                }
             });
            </script>';
            $result = str_replace('ctl=goods/gtype','app=b2c&ctl=admin_goods_type',$result);
            $result = str_replace('required="true"','vtype="required"',$result);
            $result = str_replace('"submit"','"submit" id="closeftpbutton"',$result);
            $result = preg_replace('/<SCRIPT([^>]*)>(.*?)<\/script>/Us',$script,$result);

        }
        if ($link == 'gtype.php') {
        	$result .= '<div class="table-action"><button onclick="javascript:autoFetch();" id="previous_step" class="btn btn-primary" type="button"><span><span>' . __("上一步") . '</span></span></button></div>';
        }
        if($result && false!==substr($result,'shopexfeed')){
            echo $result;
        }else{
            echo '<div style="width:300px;height:80px;"><BR><BR>'.__('因网络连接或其它原因，暂时无法获取系统默认类型信息。<BR>请稍候再试...错误信息').$net->responseCode.'</div><div style="clear:both">';
        }
    }

    function checkTypeNameExists(){
        $o = $this->app->model('goods_type');
        if($o->getList('type_id',array('name'=>$_POST['gtypename']))){
            echo '<script>alert("本类型名在系统中已存在，请更名");</script>';
        }else{
            echo '<script>alert("本类型名在系统中不存在，可正常添加");</script>';
        }
    }

    function fetchSave(){
        $this->begin('index.php?app=b2c&ctl=admin_goods_type&act=index',array(300001=>'index.php?app=b2c&ctl=admin_brand&act=fetchProtoTypes&p[0]=gtype.php&p[1]=id='.$_POST['param_id']));
        $map =  kernel::single('site_utility_xml')->xml2array($_POST['xml']);
        $gtype = $map['goodstype'];
        $gtype['name'] = $_POST['gtypename'];
        if(is_array($_POST['localbrands'])){
            foreach($_POST['localbrands'] as $kp=>$kv){
               $gtype['brand'][]['brand_id'] = $kv;
            }
        }
        $o = &$this->app->model('goods_type');
		$msg = app::get('b2c')->_('类型导入成功');
        $this->end($o->fetchSave($gtype,$msg), $msg);
    }

    function setTabContent(){
        $this->pagedata['id'] = $_GET['id'];
        $this->pagedata['content'] = $_POST['gtype']['tab'][$_GET['id']]['content'];
        $this->display('admin/goods/goods_type/form_edit_tab_content.html');
    }

}
