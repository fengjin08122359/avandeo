<?php
/**
 * Created by JetBrains PhpStorm.
 * User: chenlei
 * Date: 15-2-28
 * Time: 下午3:59
 * To change this template use File | Settings | File Templates.
 */

class b2c_goods_dingzhi{



    function import_data($file){
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

        $handle = fopen($file,"r");
        $db = kernel::database();
        $dingzhi_id = time();
        $e_data[] = array(time().rand(5234,55500),'15000',1,2,1,2,1,2,1,2,1,2,"http://www.163.com",1);
        $e_data[] = array(time().rand(1,333),'15000',1,1,1,2,1,2,1,2,1,2,"http://www.163.com",1);
        //$e = true;
        $spec_pravite_array=array();
        //foreach($e_data as $a =>$data){
        while ($data = fgetcsv($handle)){

            switch($data[15]){

                case "1":
                    $goods_data = array();
                    $sdi_product = array();
                    $ident = substr($data[13],strlen(kernel::base_url(1))+1);
                    $image_data = $db->selectrow("SELECT image_id FROM sdb_image_image WHERE url='".$ident."'");
                    if(!$image_data){
                        echo $ident;
                        echo "image not exist";
                        exit;
                    }
                    $goods_data['image_default_id'] =$image_data['image_id'];
                    $goods_data['name'] = "沙发".$data[0];
                    $goods_data['bn'] = $data[0];
                    $goods_data['cp_name'] = $data[16];
                    $goods_data['sc_day'] = $data[17];
                    $goods_data['kbhd'] = $data[18];
                    $sdi_product['price']['price']['price'] =$data[1];
                    $sdi_product['price']['cost']['price'] =$data[1];
                    $sdi_product['price']['mktprice']['price'] =$data[1];
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
                        $em_data['goods_id'] =  $goods_data['goods_id'];
                        $em_data['product_id'] =  $goods_data['product'][0]['product_id'];
                        $em_data['dingzhi_id'] = $dingzhi_id;
                        if($data[14]) $em_data['is_defalut'] = true;
                        if($md_dingzhi->save($em_data)){
                            foreach($gs as $key=>$value){
                                $em_index_data = array();
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
                    }else{
                        echo "fail";
                        exit;
                    }
                    continue;
                case "2":
                    $ident = substr($data[13],strlen(kernel::base_url(1))+1);
                    $image_data = $db->selectrow("SELECT image_id FROM sdb_image_image WHERE url='".$ident."'");
                    if(!$image_data){
                        echo $ident;
                        echo "image not exist";
                        exit;
                    }
                    $gd_tmp_data =  $db->selectrow("SELECT * FROM sdb_b2c_products WHERE bn='".$data[0]."'");
                    if(!$gd_tmp_data){
                        "modify not exist ".$data[0];
                    }
                    $goods_data['image_default_id'] =$image_data['image_id'];
                    $goods_data['name'] = "沙发".$data[0];
                    $goods_data['bn'] = $data[0];
                    $goods_data['cp_name'] = $data[16];
                    $goods_data['sc_day'] = $data[17];
                    $goods_data['kbhd'] = $data[18];
                    $sdi_product['price']['price']['price'] =$data[1];
                    $sdi_product['price']['cost']['price'] =$data[1];
                    $sdi_product['price']['mktprice']['price'] =$data[1];
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
                    $gd_data = $db->selectrow("SELECT * FROM sdb_b2c_goods WHERE bn='".$data[0]."'");
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