<?php
/**
 * Created by PhpStorm.
 * User: chugongbiao
 * Date: 15/2/3
 * Time: 下午6:06
 */

class b2c_ctl_admin_space extends desktop_controller{
    public function index(){
        $this->finder('b2c_mdl_space',array(
            'actions'=>array(
                array(
                    'label'=>app::get('b2c')->_('添加空间'),
                    'icon'=>'add.gif',
                    'href'=>'index.php?app=b2c&ctl=admin_space&act=add',
                    'target'=>'_blank'
                )
            ),
            'title'=>app::get('b2c')->_('空间列表'),
        ));
    }

    public function add(){
        $this->_info();
    }

    public function edit($space_id){
        $this->_info($space_id);
    }

    public function _info($space_id=0){
        if($space_id){
            $space = app::get('b2c')->model('space')->dump($space_id,'*','default');
            $space_type_info = app::get('b2c')->model('space_type')->dump($space['type']['type_id'],'*','default');
            $space['type'] = $space_type_info;
            //处理item product
            foreach((array)$space['space_item'] as $v){
                $item_product[$v['product_id']] = array(
                    'base_num'=>$v['base_num'],
                    'item_id'=>$v['item_id']
                );
            }
            $this->pagedata['space_product_id'] = array_keys($item_product);
            $this->pagedata['item_product'] = $item_product;
        }
        #echo "<pre>";var_export($space);exit;
        $space_types = app::get('b2c')->model('space_type')->getList('type_id,name');

        foreach((array)$space_types as $t){
            $space_type_list[$t['type_id']] = $t['name'];
        }

        $this->pagedata['info'] = $space;
        $this->pagedata['space_types'] = $space_type_list;
        $this->pagedata['image_dir'] = app::get('image')->res_url;
        $this->singlepage('admin/space/info.html');
    }


    public function save(){
        $this->begin('');
        $space = $_POST['space'];
        $data = $this->process($space,$msg);

        if(!$data){
            $this->end(false,app::get('b2c')->_($msg));
        }

        $mdl_space = app::get('b2c')->model('space');
        $mdl_space_item = app::get('b2c')->model('space_item');

        $arr_remove_item = array();
        if($space['space_id']){
            $arr_space_item = $mdl_space_item->getList('*',array('space_id'=>$space['space_id']));
            foreach ((array)$arr_space_item as $item){
                if (!in_array($item['product_id'],$space['space_item'])){
                    $arr_remove_item[] = $item['item_id'];
                }
            }
        }


        $arr_remove_image = array();
        if( $space['space_id'] && $space['images'] ){
            $oImage_attach = app::get('image')->model('image_attach');
            $arr_image_attach = $oImage_attach->getList('*',array('target_id'=>$space['space_id'],'target_type'=>'space'));
            $post_image_id = array();
            foreach ((array)$arr_image_attach as $_arr_image_attach){
                $post_image_id[]=$_arr_image_attach['image_id'];
                if (!in_array($_arr_image_attach['image_id'],$space['images'])){
                    $arr_remove_image[] = $_arr_image_attach['image_id'];
                }
            }
        }
        if(!$mdl_space->save($data)){
            $this->end(false,app::get('b2c')->_('服务器繁忙，请重试'));
        }

        //删除不被使用的图片
        if ($arr_remove_image){
            $oImage = app::get('image')->model('image');
            foreach($arr_remove_image as $_arr_remove_image){
                if(!$oImage->delete_image($_arr_remove_image,'space')){
                    $this->end(false,app::get('b2c')->_('服务器繁忙，请重试'));
                }
            }
        }
        if($arr_remove_item){
            foreach($arr_remove_item as $v){
                if(!$mdl_space_item->delete(array('item_id'=>$v))){
                    $this->end(false,app::get('b2c')->_('服务器繁忙，请重试'));
                }
            }
        }
        $this->end(true,app::get('b2c')->_('成功'));
    }

    public function process($data,&$msg){
        $mdl_space = app::get('b2c')->model('space');
        $mdl_space_item = app::get('b2c')->model('space_item');
        if(!$data['type']['type_id']){
            $msg = "请选择空间类型";
            return false;
        }
        //验证名称是否已经存在
        $filter['name'] = $data['name'];
        if($space_id = $data['space_id']){
            $filter['space_id|noequal'] = $space_id;
        }
        if($mdl_space->getList('space_id',$filter,0,1)){
            $msg = "名称已存在";
            return false;
        }
        if(!$item=$data['space_item']){
            $msg = "请选择组合商品";
            return false;
        }
        //整理item数据
        $base_num = $data['base_num'];
        $goods_id = $data['goods_id'];
        $item_id = $data['item_id'];
        foreach($item as $product_id){
            $temp_space_item = array(
                'space_id'=>$space_id,
                'goods_id'=>$goods_id[$product_id],
                'product_id'=>$product_id,
                'base_num'=>$base_num[$product_id],
            );
            if(isset($item_id[$product_id]) && $item_id[$product_id]){
                $temp_space_item['item_id'] = $item_id[$product_id];
            }
            elseif($temp_item = $mdl_space_item->getList('item_id',array('product_id'=>$product_id,'space_id'=>$space_id),0,1)){
                $temp_space_item['item_id'] = $temp_item[0]['item_id'];
            }
            $space_item[] =$temp_space_item;
        }
        $images = array();
        foreach( (array)$data['images'] as $imageId ){
            $images[] = array(
                'target_type'=>'space',
                'image_id'=>$imageId,
            );
        }
        $return_data = array(
            'name'=>$data['name'],
            'space_item'=>$space_item,
            'marketable'=>$data['marketable'],
            'image_default_id'=>$data['image_default'],
            'images'=>$images,
            'item_str'=>$space_item,
            'brief'=>$data['brief'],
            'description'=>$data['description'],
            'discount' => $data['discount'],
            'retail_store'=>$data['retail_store'],
            'type' =>array (
                'type_id' => $data['type']['type_id'],
            ),
            'props'=>$data['props'],
        );
        if($space_id){
            $return_data['space_id'] = $space_id;
        }
        #error_log(var_export($return_data,true),3,"/tmp/chugongbiao.log");

        return $return_data;
    }

    public function get_props(){
        $space = $_POST['space'];
        $type_id = $space['type']['type_id'];
        $space_type = app::get('b2c')->model('space_type')->dump($type_id,'*','default');
        $this->pagedata['space_type'] = $space_type;
        echo $this->fetch('admin/space/type/props.html');
        exit;
    }

}