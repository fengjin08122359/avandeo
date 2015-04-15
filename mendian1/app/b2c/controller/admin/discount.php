<?php
/**
 * Created by PhpStorm.
 * User: chugongbiao
 * Date: 15/2/3
 * Time: 下午6:06
 */

class b2c_ctl_admin_discount extends desktop_controller{
    public function index(){
        $this->finder('b2c_mdl_discount',array(
            'actions'=>array(
                array(
                    'label'=>app::get('b2c')->_('添加特卖商品'),
                    'icon'=>'add.gif',
                    'href'=>'index.php?app=b2c&ctl=admin_discount&act=add',
                    'target'=>'_blank'
                )
            ),
            'title'=>app::get('b2c')->_('特卖商品列表'),
        ));
    }

    public function add(){
        $this->_info();
    }

    public function edit($discount_id){
        $this->_info($discount_id);
    }

    public function _info($discount_id=0){
        if($discount_id){
            $discount_info = app::get('b2c')->model('discount')->dump($discount_id,'*','default');
            $space_type_info = app::get('b2c')->model('space_type')->dump($discount_info['type']['type_id'],'*','default');
            $discount_info['type'] = $space_type_info;
            //处理item product
            foreach((array)$discount_info['discount_item'] as $v){
                $item_product[$v['product_id']] = array(
                    'base_num'=>$v['base_num'],
                    'item_id'=>$v['item_id']
                );
            }
            $this->pagedata['discount_product_id'] = array_keys($item_product);
            $this->pagedata['item_product'] = $item_product;
        }
        #echo "<pre>";var_export($discount_info);exit;
        $space_types = app::get('b2c')->model('space_type')->getList('type_id,name');

        foreach((array)$space_types as $t){
            $space_type_list[$t['type_id']] = $t['name'];
        }

        $this->pagedata['info'] = $discount_info;
        $this->pagedata['space_types'] = $space_type_list;
        $this->pagedata['image_dir'] = app::get('image')->res_url;
        $this->singlepage('admin/discount/info.html');
    }


    public function save(){
        $this->begin('');
        $discount = $_POST['discount'];
        $data = $this->process($discount,$msg);

        if(!$data){
            $this->end(false,app::get('b2c')->_($msg));
        }

        $mdl_discount = app::get('b2c')->model('discount');
        $mdl_discount_item = app::get('b2c')->model('discount_item');

        $arr_remove_item = array();
        if($discount['discount_id']){
            $arr_discount_item = $mdl_discount_item->getList('*',array('discount_id'=>$discount['discount_id']));
            foreach ((array)$arr_discount_item as $item){
                if (!in_array($item['product_id'],$discount['discount_item'])){
                    $arr_remove_item[] = $item['item_id'];
                }
            }
        }


        $arr_remove_image = array();
        if( $discount['discount_id'] && $discount['images'] ){
            $oImage_attach = app::get('image')->model('image_attach');
            $arr_image_attach = $oImage_attach->getList('*',array('target_id'=>$discount['discount_id'],'target_type'=>'discount'));
            $post_image_id = array();
            foreach ((array)$arr_image_attach as $_arr_image_attach){
                $post_image_id[]=$_arr_image_attach['image_id'];
                if (!in_array($_arr_image_attach['image_id'],$discount['images'])){
                    $arr_remove_image[] = $_arr_image_attach['image_id'];
                }
            }
        }
        if(!$mdl_discount->save($data)){
            $this->end(false,app::get('b2c')->_('服务器繁忙，请重试'));
        }

        //删除不被使用的图片
        if ($arr_remove_image){
            $oImage = app::get('image')->model('image');
            foreach($arr_remove_image as $_arr_remove_image){
                if(!$oImage->delete_image($_arr_remove_image,'discount')){
                    $this->end(false,app::get('b2c')->_('服务器繁忙，请重试'));
                }
            }
        }
        if($arr_remove_item){
            foreach($arr_remove_item as $v){
                if(!$mdl_discount_item->delete(array('item_id'=>$v))){
                    $this->end(false,app::get('b2c')->_('服务器繁忙，请重试'));
                }
            }
        }
        $this->end(true,app::get('b2c')->_('成功'));
    }

    public function process($data,&$msg){
        $mdl_discount = app::get('b2c')->model('discount');
        $mdl_discount_item = app::get('b2c')->model('discount_item');
        if(!$data['type']['type_id']){
            $msg = "请选择空间类型";
            return false;
        }
        //验证名称是否已经存在
        $filter['name'] = $data['name'];
        if($discount_id = $data['discount_id']){
            $filter['discount_id|noequal'] = $discount_id;
        }
        if($mdl_discount->getList('discount_id',$filter,0,1)){
            $msg = "名称已存在";
            return false;
        }
        if(!$item=$data['discount_item']){
            $msg = "请选择组合商品";
            return false;
        }
        //整理item数据
        $base_num = $data['base_num'];
        $goods_id = $data['goods_id'];
        $item_id = $data['item_id'];
        foreach($item as $product_id){
            $temp_discount_item = array(
                'discount_id'=>$discount_id,
                'goods_id'=>$goods_id[$product_id],
                'product_id'=>$product_id,
                'base_num'=>$base_num[$product_id],
            );
            if(isset($item_id[$product_id]) && $item_id[$product_id]){
                $temp_discount_item['item_id'] = $item_id[$product_id];
            }
            elseif($temp_item = $mdl_discount_item->getList('item_id',array('product_id'=>$product_id,'discount_id'=>$discount_id),0,1)){
                $temp_discount_item['item_id'] = $temp_item[0]['item_id'];
            }
            $discount_item[] =$temp_discount_item;
        }
        $images = array();
        foreach( (array)$data['images'] as $imageId ){
            $images[] = array(
                'target_type'=>'discount',
                'image_id'=>$imageId,
            );
        }
        $return_data = array(
            'name'=>$data['name'],
            'discount_item'=>$discount_item,
            'marketable'=>$data['marketable'],
            'image_default_id'=>$data['image_default'],
            'images'=>$images,
            'item_str'=>$discount_item,
            'brief'=>$data['brief'],
            'description'=>$data['description'],
            'discount' => $data['discount'],
            'type' =>array (
                'type_id' => $data['type']['type_id'],
            ),
            'props'=>$data['props'],
        );
        if($discount_id){
            $return_data['discount_id'] = $discount_id;
        }
        #error_log(var_export($return_data,true),3,"/tmp/chugongbiao.log");

        return $return_data;
    }

    public function get_props(){
        $discount = $_POST['discount'];
        $type_id = $discount['type']['type_id'];
        $space_type = app::get('b2c')->model('space_type')->dump($type_id,'*','default');
        $this->pagedata['space_type'] = $space_type;

        echo $this->fetch('admin/discount/type/props.html');
        exit;
    }

}