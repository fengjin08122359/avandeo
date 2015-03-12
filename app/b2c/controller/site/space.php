<?php
/**
 * Created by PhpStorm.
 * User: chugongbiao
 * Date: 15/1/27
 * Time: 下午1:59
 */

class b2c_ctl_site_space extends b2c_frontpage{

    function plist($space_id){
        if(!$space_id){
            $url = app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_space','act'=>'glist'));
            $this->_response->set_redirect($url)->send_headers();
        }
        $space_info = app::get('b2c')->model('space')->dump2($space_id,'*','default');
        if(!$space_info){
            return;
        }
        $space_type = app::get('b2c')->model('space_type')->dump($space_info['type']['type_id']);
        $space_info['props'] = $this->get_space_props($space_type['props'],$space_info['props']);
        #echo "<pre>";var_export($space_info);exit;
        if(!$temp_space_item = $this->get_space_item($space_info['space_item'],$msg)){
            return;
        }
        $space_info['space_item'] = $temp_space_item;
        $this->pagedata['page_product_basic'] = $space_info;
        $this->set_tmpl('space_plist');
        $this->page('site/space/glist.html');
    }

    public function glist($type_id=0,$orderBy=0,$page=1) {
        $params = $this->filter_decode(null,$type_id);
        $this->pagedata['filter'] = $params['params'];
        $goodsData = $this->get_goods($params['filter'],$page,$params['orderby']);
        //获取所有类型
        $this->pagedata['all_space_type'] = app::get('b2c')->model('space_type')->getList('*');
        $screen = $this->screen($type_id,$params['params']);
        $this->pagedata['screen'] = $screen['screen'];
        $this->pagedata['active_filter'] = $screen['active_filter'];
        $this->pagedata['orderby_sql'] = $params['orderby'];
        $this->pagedata['goodsData'] = $goodsData;

        //面包屑
        $GLOBALS['runtime']['path'] = $this->get_crumb($type_id);

        $this->set_tmpl('space_glist');
        $this->page('site/space/glist.html');
    }

    public function filter_decode($params=null,$cat_id){
        //获取cookie中的条件
        /*if(!$params){
            $cookie_filter = $_COOKIE['S']['GALLERY']['FILTER'];
            if($cookie_filter){
                $tmp_params = explode('&',$cookie_filter);
                foreach($tmp_params as $k=>$v){
                    $arrfilter = explode('=',$v);
                    $f_k = str_replace('[]','',$arrfilter[0]);
                    if($f_k == 'cat_id' || $f_k == 'orderby' || $f_k == 'showtype' || $f_k == 'is_store'){
                        $params[$f_k] = $arrfilter[1];
                    }else{
                        $params[$f_k][] = $arrfilter[1];
                    }
                }
            }
            if($params['cat_id'] != $cat_id){
                unset($params);
                $this->set_cookie('S[GALLERY][FILTER]','nofilter');
            }
        }*///end if
        $filter['params'] = $params;
        #分类
        $params['type_id'] = $cat_id ? $cat_id : $params['type_id'];
        if(!$params['type_id']) unset($params['type_id']);


        #排序
        $orderby = $params['orderby'];unset($params['orderby']);

        #分页,页码
        $page= $params['page'];unset($params['page']);

        $params['marketable'] = 'true';
        $tmp_filter = $params;

        #价格区间筛选
        if($tmp_filter['price']){
            $tmp_filter['price'] = explode('~',$tmp_filter['price'][0]);
        }
        $filter['filter'] = $tmp_filter;
        $filter['orderby'] = $orderby;
        $filter['page'] = $page;
        return $filter;
    }

    public function get_goods($filter,$page=1,$orderby){
        $goodsObject = kernel::single('b2c_goods_object');
        $goodsModel = app::get('b2c')->model('space');

        $page = $page ? $page : 1;
        $pageLimit = 18;
        $this->pagedata['pageLimit'] = $pageLimit;
        $goodsData = $goodsModel->getList2('*',$filter,$pageLimit*($page-1),$pageLimit,$orderby,$total=false);
        if($goodsData && $total ===false){
            $total = $goodsModel->count($filter);
        }
        $this->pagedata['total'] =  $total;
        $pagetotal= $this->pagedata['total'] ? ceil($this->pagedata['total']/$pageLimit) : 1;
        $max_pagetotal = 100;
        $this->pagedata['pagetotal'] = $pagetotal > $max_pagetotal ? $max_pagetotal : $pagetotal;
        $this->pagedata['page'] = $page;
        //分页
        $this->pagedata['pager'] = array(
            'current'=>$page,
            'total'=>$this->pagedata['pagetotal'],
            'link' =>$this->gen_url(array('app'=>'b2c', 'ctl'=>'site_space','act'=>'ajax_get_goods')),
        );
        return $goodsData;
    }

    private function screen($cat_id,$filter){
        if ( empty($cat_id) ) {
            $screen = array();
        }
        $screen['type_id'] = $cat_id;

        cachemgr::co_start();
        if(!cachemgr::get("spaceType".$cat_id, $catInfo)){
            $typeInfo = app::get("b2c")->model("space_type")->dump($cat_id,'*','default');
            cachemgr::set("spaceType".$cat_id, $typeInfo, cachemgr::co_end());
        }
        if($typeInfo['price'] && $filter['price'][0]){
            $active_filter['price']['title'] = $this->app->_('价格');
            $active_filter['price']['label'] = 'price';
            $active_filter['price']['options'][0]['data'] =  $filter['price'][0];
            foreach($typeInfo['price'] as $key=>$price){
                $price_filter = implode('~',$price);
                if($filter['price'][0] == $price_filter){
                    $typeInfo['price'][$key]['active'] = 'active';
                    $active_arr['price'] = 'active';
                }
                $active_filter['price']['options'][0]['name'] = $filter['price'][0];
            }
        }
        $screen['price'] = $typeInfo['price'];


        //扩展属性
        foreach ( $typeInfo['props'] as $p_k => $p_v){
            $props[$p_k]['name'] = $p_v['name'];
            $props[$p_k]['goods_p'] = $p_v['goods_p'];
            $props[$p_k]['show'] = $p_v['show'];
            $propsActive = array();
            if($p_v['options']){
                foreach($p_v['options'] as $propItemKey=>$propItemValue){
                    $activeKey = 'p_'.$p_v['goods_p'];
                    if($filter[$activeKey] && in_array($propItemKey,$filter[$activeKey])){
                        $active_filter[$activeKey]['title'] = $p_v['name'];
                        $active_filter[$activeKey]['label'] = $activeKey;
                        $active_filter[$activeKey]['options'][$propItemKey]['data'] =  $propItemKey;
                        $active_filter[$activeKey]['options'][$propItemKey]['name'] = $propItemValue;
                        $propsActive[$propItemKey] = 'active';
                    }
                }
            }
            $props[$p_k]['options'] = $p_v['options'];
            $props[$p_k]['active'] = $propsActive;
        }
        $screen['props'] = $props;
        //排序
        $orderBy = $this->app->model('space')->orderBy();
        $screen['orderBy'] = $orderBy;
        $this->pagedata['active_arr'] = $active_arr;
        $return['screen'] = $screen;
        $return['active_filter'] = $active_filter;
        return $return;
    }

    public function ajax_get_goods(){
        $tmp_params = $this->filter_decode($_POST);
        $params = $tmp_params['filter'];
        $orderby = $tmp_params['orderby'];
        $page = $tmp_params['page'] ? $tmp_params['page'] : 1;
        $goodsData = $this->get_goods($params,$page,$orderby);
        if($goodsData){
            $this->pagedata['goodsData'] = $goodsData;
            $view = 'site/space/type/grid.html';
            echo $this->fetch($view);
        }else{
            //后台站点设置搜索为空页面
            echo app::get('site')->getConf('errorpage.search');
        }
    }

    private function get_space_props($arrProps,$aSpace){
        if( empty($arrProps) ){
            return null;
        }
        $goodsProps = array();
        for ($i=1;$i<=30;$i++){
            if ($aSpace['p_'.$i] ){
                $propsValueId = $aSpace['p_'.$i]['value'];
                $k = $arrProps[$i]['ordernum'].'_'.$i;
                $goodsProps[$k]['name'] = $arrProps[$i]['name'];
                $goodsProps[$k]['value'] = $arrProps[$i]['options'][$propsValueId];
                //如果商品类型扩展属性改变，则商品中的设置需要重现设置，原先设置无效
                if(empty($goodsProps[$k]['name']) || empty($goodsProps[$k]['value']) ){
                    unset($goodsProps[$k]);
                    continue;
                }
            }
        }
        ksort($goodsProps);
        return $goodsProps;
    }

    private function get_space_item($space_item,&$msg){
        $mdl_goods = app::get('b2c')->model('goods');
        $mdl_products = app::get('b2c')->model('products');
        foreach($space_item as $k=>$pro){
            $temp_goods = $mdl_goods->getList('name,marketable,image_default_id',array('goods_id'=>$pro['goods_id']),0,1);
            if(!$temp_goods || $temp_goods[0]['marketable'] !== 'true'){
                $msg = "商品下架";
                return false;
            }
            $temp_p = $mdl_products->dump($pro['product_id'],'*','price/member_lv_price');
            if(!$temp_p || $temp_p['status'] !== 'true'){
                $msg = "货品下架";
                return false;
            }
            $space_item[$k]['name'] = $temp_goods[0]['name'];
            $space_item[$k]['price'] = $temp_p['price']['price']['current_price'];
            $space_item[$k]['image_default_id'] = $temp_goods[0]['image_default_id'];
        }
        return $space_item;
    }

    private function get_crumb($type_id){
        $arr[] = array('link'=>$this->gen_url(array('app'=>'b2c','ctl'=>'site_default')),'title'=>'首页');
        $arr[] = array('link'=>$this->gen_url(array('app'=>'b2c','ctl'=>'site_space','act'=>'glist')),'title'=>'生活空间');
        if($type_id){
            $type_info = app::get('b2c')->model('space_type')->getList('name',array('type_id'=>$type_id),0,1);
            $arr[] = array('link'=>$this->gen_url(array('app'=>'b2c','ctl'=>'site_space','act'=>'glist','arg0'=>$type_id)),'title'=>$type_info[0]['name']);
        }
        return $arr;
    }



}