<?php
/**
 * Created by JetBrains PhpStorm.
 * User: chenlei
 * Date: 15-2-28
 * Time: 下午3:59
 * To change this template use File | Settings | File Templates.
 */

class b2c_goods_dingzhi{

    private $type = array( //类型影射表
        'shafa'=>'沙发',
        'baozhen'=>'抱枕',
        'chuang'=>'床',
        'hua'=>'画',
        'ditan'=>'地毯',
    ); 

    function import_data($file,$type){
        setlocale(LC_ALL, 'zh_CN');
        $mdl_b2c = app::get('b2c')->model('goods');
        $mdl_image_attach = app::get('image')->model('image_attach');
        $md_dingzhi = app::get('b2c')->model('dingzhi');
        $md_dingzhi_index = app::get('b2c')->model('dingzhi_index');
        /*$gs =array(
                '3'=>6,
                '4'=>7,
                '5'=>8,
                '6'=>9,
                '7'=>10,
                '8'=>11,
                '9'=>12,
                '10'=>13,
                '11'=>14,
                '2'=>15,
            );*/

        switch($type){

            case "shafa":
                $gs =array(
                    '2'=>5,//材料
                    '3'=>6,//材料款式
                    '4'=>1,//颜色
                    '5'=>8,//扶手
                    '6'=>9,//沙发腿
                    '7'=>10,//填充物
                    '8'=>11,//背高
                    '9'=>14,//总长
                    '10'=>12,//座高
                    '11'=>17,//转角
                    '12'=>13,//座深
                );


                $insert_type = 15;
                $image_sheet = 13;
                $cp_name_sheet = 16;
                $name_bn_sheet = 0;
                $sc_day_sheet = 17;
                $price_sheet = 1;
                $defalut_sheet = 14;
                break;

            case "baozhen":

                $gs =array(
                    '2'=>42,//填充物选择
                    '3'=>38,//枕套面料
                    '4'=>68,//枕套颜色
                    '5'=>40,//形状
                    '6'=>41,//图案
                    '7'=>44,//舒适度
                    '8'=>43,//规格尺寸

                );
                $insert_type = 11;
                $image_sheet = 9;
                $cp_name_sheet = 12;
                $name_bn_sheet = 0;
                $sc_day_sheet = 13;
                $price_sheet = 1;
                $defalut_sheet =10;
                break;

            case "chuang":

                $gs =array(
                    '2'=>73,//材料
                    '3'=>69 ,//材料款式
                    '4'=>70,//形状
                    '5'=>72,//图案
                    '6'=>71,//舒适度
                    '7'=>54,//规格尺寸
                    '8'=>74,//枕套面料
                    '9'=>56,//枕套颜色
                    '10'=>55,//枕套颜色
                    '11'=>57,//枕套颜色
                    '12'=>61,//枕套颜色
                    '13'=>60,//枕套颜色
                );
                $insert_type = 16;
                $image_sheet = 14;
                $cp_name_sheet = 17;
                $name_bn_sheet = 0;
                $sc_day_sheet = 18;
                $price_sheet = 1;
                $defalut_sheet =15;
                break;

            case "hua":

                $gs =array(
                    '2'=>79,//材料
                    '3'=>78 ,//材料款式
                    '4'=>80,//形状
                    '5'=>77,//图案
                    '6'=>82,//舒适度
                    '7'=>81,//规格尺寸
                    '8'=>83,//枕套面料
                    '9'=>14,//枕套颜色
                    '10'=>76,//枕套颜色
                );
                $insert_type = 13;
                $image_sheet = 11;
                $cp_name_sheet = 14;
                $name_bn_sheet = 0;
                $sc_day_sheet = 15;
                $price_sheet = 1;
                $defalut_sheet =12;
                break;


            case "ditan":

                $gs =array(
                    '2'=>45,//材料
                    '3'=>75 ,//材料款式
                    '4'=>47,//形状
                    '5'=>48,//图案
                    '6'=>49,//舒适度
                    '7'=>14,//规格尺寸
                    '8'=>76,//枕套面料

                );
                $insert_type = 11;
                $image_sheet = 9;
                $cp_name_sheet = 12;
                $name_bn_sheet = 0;
                $sc_day_sheet = 13;
                $price_sheet = 1;
                $defalut_sheet =10;
                break;

        }





        $handle = fopen($file,"r");
        $db = kernel::database();
        $dingzhi_id = time();
        $e_data[] = array(time().rand(5234,55500),'15000',1,2,1,2,1,2,1,2,1,2,"http://www.163.com",1);
        $e_data[] = array(time().rand(1,333),'15000',1,1,1,2,1,2,1,2,1,2,"http://www.163.com",1);
        //$e = true;
        $spec_pravite_array=array();
        //foreach($e_data as $a =>$data){
        while ($data = fgetcsv($handle)){

            switch($data[$insert_type]){

                case "1":
                    $goods_data = array();
                    $sdi_product = array();
                    $ident = substr($data[$image_sheet],strlen(kernel::base_url(1))+1);
                    $image_data = $db->selectrow("SELECT image_id FROM sdb_image_image WHERE url='".$ident."'");
                    if(!$image_data){
                        echo $ident;
                        echo "image not exist";
                        exit;
                    }
                    $goods_data['image_default_id'] =$image_data['image_id'];
                    $goods_data['name'] = $this->type[$type].$data[$name_bn_sheet];
                    $goods_data['bn'] = $data[$name_bn_sheet];
                    $goods_data['cp_name'] = $data[$cp_name_sheet];
                    $goods_data['sc_day'] = $data[$sc_day_sheet];
                    $goods_data['kbhd'] = $data[18];
                    $sdi_product['price']['price']['price'] =$data[$price_sheet];
                    $sdi_product['price']['cost']['price'] =$data[$price_sheet];
                    $sdi_product['price']['mktprice']['price'] =$data[$price_sheet];
                    $sdi_product['is_default'] = true;
                    $sdi_product['default'] = 1;
                    $goods_data['product'][0] = $sdi_product;
                    if($mdl_b2c->save($goods_data)){
                        $image_attach_data = array();
                        $image_attach_data['target_id'] = $goods_data['goods_id'];
                        $image_attach_data['target_type'] = 'goods';
                        $image_attach_data['image_id'] = $image_data['image_id'];
                        $mdl_image_attach->save($image_attach_data);
                        $em_data = array();
                        $em_data['dz_type'] =  $type;
                        $em_data['goods_id'] =  $goods_data['goods_id'];
                        $em_data['product_id'] =  $goods_data['product'][0]['product_id'];
                        $em_data['dingzhi_id'] = $dingzhi_id;
                        if($data[$defalut_sheet]) $em_data['is_defalut'] = true;
                        if($md_dingzhi->save($em_data)){
                            foreach($gs as $key=>$value){
                                $em_index_data = array();
                                $em_index_data = $em_data;
                                $spec_value_id_data = $db->selectrow("SELECT * FROM sdb_b2c_spec_values WHERE spec_id='".$value."' AND spec_value='".$data[$key]."'");
                                if(!$spec_value_id_data){
                                    echo "<pre>".print_r($data,1);
                                    echo "<hr/>";
                                    print_r('key = '.$key.',value = '.$value);
                                    echo "<hr/>";
                                    echo 'error: '.$data[$key].' not exist'.' - insert';
                                    exit;
                                }
                                $em_index_data['spec_id'] = $value;
                                $em_index_data['spec_value_id'] = $spec_value_id_data['spec_value_id'];
                                $md_dingzhi_index->insert($em_index_data);
                            }
                        }
                    }else{
                        echo "fail";
                        exit;
                    }
                    continue;
                case "2":
                    $ident = substr($data[$image_sheet],strlen(kernel::base_url(1))+1);
                    $image_data = $db->selectrow("SELECT image_id FROM sdb_image_image WHERE url='".$ident."'");
                    if(!$image_data){
                        echo $ident;
                        echo "image not exist";
                        exit;
                    }
                    $gd_tmp_data =  $db->selectrow("SELECT * FROM sdb_b2c_products WHERE bn='".$data[$name_bn_sheet]."'");
                    if(!$gd_tmp_data){
                        "modify not exist ".$data[$name_bn_sheet];
                    }
                    $goods_data['image_default_id'] =$image_data['image_id'];
                    $goods_data['name'] = "沙发".$data[$name_bn_sheet];
                    $goods_data['bn'] = $data[$name_bn_sheet];
                    $goods_data['cp_name'] = $data[$cp_name_sheet];
                    $goods_data['sc_day'] = $data[$sc_day_sheet];
                    $goods_data['kbhd'] = $data[18];
                    $sdi_product['price']['price']['price'] =$data[$price_sheet];
                    $sdi_product['price']['cost']['price'] =$data[$price_sheet];
                    $sdi_product['price']['mktprice']['price'] =$data[$price_sheet];
                    $sdi_product['is_default'] = true;
                    $sdi_product['default'] = 1;
                    $goods_data['product'][0] = $sdi_product;

                    if($mdl_b2c->update($goods_data,array('goods_id'=>$gd_tmp_data['goods_id']))){
                        $image_attach_data = array();
                        $image_attach_data['target_id'] = $gd_tmp_data['goods_id'];
                        $image_attach_data['target_type'] = 'goods';
                        $image_attach_data['image_id'] = $image_data['image_id'];
                        $mdl_image_attach->update($image_attach_data,array('target_id'=>$goods_data['goods_id'],'target_type'=>'goods'));
                        $db->exec("DELETE FROM sdb_b2c_dingzhi_index WHERE goods_id=".$gd_tmp_data['goods_id']);
                        foreach($gs as $key=>$value){
                            $em_index_data = array();
                            $em_data['dz_type'] =  $type;
                            $em_data['goods_id'] =$gd_tmp_data['goods_id'];
                            $em_data['product_id'] =$gd_tmp_data['product_id'];
                            $em_data['dingzhi_id'] =$gd_tmp_data['product_id'];
                            $em_index_data = $em_data;
                            $spec_value_id_data = $db->selectrow("SELECT * FROM sdb_b2c_spec_values WHERE spec_id='".$value."' AND spec_value='".$data[$key]."'");
                            if(!$spec_value_id_data){
                                echo "<pre>".print_r($data,1);
                                echo "<hr/>";
                                echo 'error: '.$data[$key].' not exist';
                                exit;
                            }
                            $em_index_data['spec_id'] = $value;
                            $em_index_data['spec_value_id'] = $spec_value_id_data['spec_value_id'];
                            $md_dingzhi_index->insert($em_index_data);
                        }
                    }
                    continue;
                case "3";
                    $gd_data = $db->selectrow("SELECT * FROM sdb_b2c_goods WHERE bn='".$data[$name_bn_sheet]."'");
                    $db->exec("DELETE FROM sdb_b2c_goods WHERE goods_id=".$gd_data['goods_id']);
                    $db->exec("DELETE FROM sdb_b2c_products WHERE goods_id=".$gd_data['goods_id']);
                    $db->exec("DELETE FROM sdb_b2c_dingzhi WHERE goods_id=".$gd_data['goods_id']);
                    $db->exec("DELETE FROM sdb_b2c_dingzhi_index WHERE goods_id=".$gd_data['goods_id']);
                    continue;
            }



        }


        function save_index($data,$gd_tmp_data){
            $md_dingzhi_index = app::get('b2c')->model('dingzhi_index');
            $gs =array(
                '2'=>5,//材料
                '3'=>6,//材料款式
                '4'=>1,//颜色
                '5'=>8,//扶手
                '6'=>9,//沙发腿
                '7'=>10,//填充物
                '8'=>11,//背高
                '9'=>14,//总长
                '10'=>12,//座高
                '11'=>17,//转角
                '12'=>13,//座深
            );
            foreach($gs as $key=>$value){
                $em_index_data = array();
                $em_data['goods_id'] =$gd_tmp_data['goods_id'];
                $em_data['product_id'] =$gd_tmp_data['product_id'];
                $em_data['dingzhi_id'] =$gd_tmp_data['product_id'];
                $em_index_data = $em_data;
                $spec_value_id_data = $db->selectrow("SELECT * FROM sdb_b2c_spec_values WHERE spec_id='".$value."' AND spec_value='".$data[$key]."'");
                if(!$spec_value_id_data){
                    echo "<pre>".print_r($data,1);
                    echo "<hr/>";
                    echo 'error: '.$data[$key].' not exist';
                    exit;
                }
                $em_index_data['spec_id'] = $value;
                $em_index_data['spec_value_id'] = $spec_value_id_data['spec_value_id'];
                $md_dingzhi_index->insert($em_index_data);
            }
        }


    }



}

?>