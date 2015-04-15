<?php
/**
 * Created by JetBrains PhpStorm.
 * User: chenlei
 * Date: 15-2-4
 * Time: 下午3:48
 * To change this template use File | Settings | File Templates.
 */

class b2c_ctl_site_dingzhi extends b2c_frontpage{


        public function index($series_id,$type_id) {
            $db = kernel::database();
            $ds_data = $db->selectrow("SELECT * FROM sdb_b2c_dingzhi WHERE dingzhi_id='".$series_id."' AND is_defalut ='true'");

            $gid =$ds_data['goods_id'];
            $goods_mdl = $this->app->model('goods');
            $lib = kernel::single("base_storager");
            $goods = $goods_mdl->dump($gid,'*',$subsdf);


            switch($type_id){
                case false:
                    $type_s_id = 7;
                    break;

                case "baozhen":
                    $type_s_id = 18;
                    break;

                case "chuang":
                    $type_s_id = 19;
                    break;

            }


            $dz_ds_data = $db->select("SELECT goods_id,dingzhi_id FROM sdb_b2c_dingzhi WHERE dingzhi_id!='".$series_id."' AND is_defalut ='true'");


            foreach($dz_ds_data as $ke_dz=>$ve_dz){
                if($ve_dz['dingzhi_id']==1426233680){
                    continue;
                }
                $egz_data = array();
                $ei_tmp_data = $db->selectrow("SELECT image_default_id FROM sdb_b2c_goods WHERE goods_id=".$ve_dz['goods_id']);
                $egz_data['image_url'] = $lib->image_path($ei_tmp_data['image_default_id'],'b');
                $egz_data['dingzhi_id'] = $ve_dz['dingzhi_id'];
                $ot_dz_data[] =$egz_data;
            }


            $goods['ot_dz_data'] = $ot_dz_data;
            $goods['dingzhi_id'] = $ds_data['dingzhi_id'];
            $g_data = $goods_mdl->getLinkList($gid);
            foreach($g_data as $k=>$v){
                if($v['goods_1']!=$gid)$gl[$v['goods_1']] = $v['goods_1'];
                if($v['goods_2']!=$gid)$gl[$v['goods_2']] = $v['goods_2'];
            }

            foreach($gl as $ed=>$dv){
                $tmp_pj_data = array();
                $pj_goods[$ed] = $goods_mdl->dump($ed,'*',$subsdf);
                $pj_goods[$ed]['image_url'] =$lib->image_path($pj_goods[$ed]['image_default_id'],"m");
                $tmp_pj_data = $db->selectrow("SELECT * FROM sdb_b2c_products WHERE goods_id=".$ed);
                $pj_goods[$ed]['price'] = intval($tmp_pj_data['price']);
                $pj_goods[$ed]['product_id'] = $tmp_pj_data['product_id'];
            }

            $goods['gl_goods'] = $pj_goods;

            $e = $db->selectrow("SELECT product_id,spec_desc,price FROM sdb_b2c_products WHERE goods_id =".$gid);



            $product_data = $db->select("SELECT spec_value_id FROM sdb_b2c_dingzhi_index  WHERE product_id =".$e['product_id']);


            $goods['product_id'] = $e['product_id'];
            /*$spec_info_tmp_data = unserialize($e['spec_desc']);
            $spec_info_data = array_flip($spec_info_tmp_data['spec_value_id']);*/
            foreach($product_data as $dd_ke=>$dd_ve){
                $spec_info_data[$dd_ve['spec_value_id']] = 1;
            }

            $goods['price'] = intval($e['price']);

            $spec_data = $db->select("SELECT a.spec_id,spec_name FROM sdb_b2c_goods_type_spec a RIGHT JOIN sdb_b2c_specification b  ON a.spec_id = b.spec_id WHERE type_id = ".$type_s_id);


            foreach($spec_data as $ke=>$ve){
                $spec_id[] = $ve['spec_id'];
                $spec_data_tmp[$ve['spec_id']] = $ve['spec_name'];
            }


            $dingzi_data = $db->select("SELECT spec_value_id FROM sdb_b2c_dingzhi_index  WHERE dingzhi_id =".$series_id." GROUP BY spec_value_id");

            foreach($dingzi_data as $d_ke=>$d_ve){
                $spec_value_id[] = $d_ve['spec_value_id'];
            }

            $spec_value_data = $db->select("SELECT spec_value_id,spec_id,spec_value,spec_image,alias FROM sdb_b2c_spec_values WHERE spec_value_id IN (".implode(",",$spec_value_id).")");

            $goods['image_url']= $lib->image_path($goods['image_default_id'],"b");
            $goods['spec']= array();
            foreach($spec_value_data as $a=>$b){
                $goods['spec'][$b['spec_id']]['name'] =$spec_data_tmp[$b['spec_id']];

                //if($check_spec_id[$b['spec_value_id']]){
                    $tmp = array();
                    $tmp['spec_value_id'] = $b['spec_value_id'];
                    $tmp['spec_value_name'] = $b['spec_value'];
                    $tmp['spec_memo'] = $b['alias'];
                    $tmp['spec_image'] =$lib->image_path($b['spec_image'],"b");
                    if($spec_info_data[$tmp['spec_value_id']]) $tmp['defalut'] = "ture";
                    $goods['spec'][$b['spec_id']]['spec_value'][] = $tmp;
                //}
            }

			$this->rankSpec($goods);
            
            $this->pagedata['data'] = $goods;


            if(!$type_id){
                $this->page("site/dingzhi/index.html");
            }else{
                $this->page("site/dingzhi/".$type_id.".html");
            }
        }

        /**
         * 重新排序
         * @param array $goods
         */
		public function rankSpec(array &$goods)
		{
			
			$index_arr10 = array(70,98,71);
			$tmp_arr = array();
			
			foreach($index_arr10 as $index)
			{
				foreach ($goods['spec'][10]['spec_value'] as $key => $val)
				{
					if($val['spec_value_id'] == $index)
					{
						$tmp_arr[] = $val;
						unset($goods['spec'][10]['spec_value'][$key]);
						break 1;
					}
				}
				unset($key);
				unset($val);
			}
			
			foreach($tmp_arr as $val)
			{
				array_unshift($goods['spec'][10]['spec_value'],$val);
			}
			
		}
        
        public function  getProduct(){

            //$this->_response->set_header('Cache-Control', 'no-store');
            $db = kernel::database();
            $lib = kernel::single("base_storager");

            $dz = $_GET['dz'];

            $series_id = $_GET['dingzhi_id'];
            //$goods_id = $_GET['goods_id'];
            //$sql = "SELECT product_id FROM (SELECT product_id,count(product_id) AS d FROM sdb_b2c_dingzhi_index WHERE dingzhi_id =".$series_id." AND spec_value_id in(".$dz.") GROUP BY product_id )  AS c WHERE d>10";


            if($series_id==1426234706){
                $key = md5(md5($dz.$series_id));
               // base_kvstore::instance('b2c.dingzhi_s')->fetch($key, $list);
            }else{
                $key = md5($dz.$series_id);
                //base_kvstore::instance('b2c.dingzhi_s')->fetch($key, $list);
            }

            base_kvstore::instance('b2c.dingzhi_s')->fetch($key, $list);

            if($list){
                $list['price'] = intval($list['price']);
                echo json_encode($list);
            }else{
                $sql = "SELECT product_id,goods_id FROM(select count(product_id) as c,product_id,goods_id from sdb_b2c_dingzhi_index where dingzhi_id =".$series_id." AND spec_value_id IN(".$dz.") group by product_id) as d where c>10";
                $data = $db->selectrow($sql);
                $product_data = $db->selectrow("SELECT * FROM sdb_b2c_products WHERE product_id=".$data['product_id']);

                $goods_data  =$db->selectrow("SELECT image_default_id FROM sdb_b2c_goods WHERE goods_id=".$data['goods_id']);
                if($data){
                    $ouput_data['goods_id'] = $data['goods_id'];
                    $ouput_data['price'] =intval($product_data['price']);
                    $ouput_data['product_id'] = $data['product_id'];
                    $ouput_data['image_url'] = $lib->image_path($goods_data['image_default_id'],"b");;
                }
                base_kvstore::instance('b2c.dingzhi_s')->store($key, $ouput_data);
                echo json_encode($ouput_data);
            }

        }


        public function liandong(){
            $dingzhi_id = $_POST['dingzhi_id'];
            //("SELECT * FROM sdb_b2c_dingzhi_index WHERE dingzhi_id=".$dingzhi_id." AND spec_value_id = 4 ");
            $spec_id = $_POST['spec_id'];
            $spec_value_id = $_POST['spec_value_id'];
            $key1 = md5($dingzhi_id.$spec_id.$spec_value_id);
            base_kvstore::instance('b2c.dingzhi')->fetch($key1, $list);
            if(!$list){
                $sql = "SELECT spec_value_id FROM sdb_b2c_dingzhi_index WHERE product_id IN(SELECT product_id FROM sdb_b2c_dingzhi_index WHERE spec_value_id =".$spec_value_id." AND dingzhi_id=".$dingzhi_id.") AND spec_id=".$spec_id." GROUP BY spec_value_id";
                $db = kernel::database();
                $lib = kernel::single("base_storager");
                $data = $db->select($sql);
                $i = true;

                foreach($data as $key=>$value){
                    $b = $db->selectrow("SELECT spec_value_id,spec_id,spec_value,spec_image,alias FROM sdb_b2c_spec_values WHERE spec_value_id=".$value['spec_value_id']);
                    $tmp = array();
                    $tmp['spec_value_id'] = $b['spec_value_id'];
                    $tmp['spec_value_name'] = $b['spec_value'];
                    $tmp['spec_memo'] = $b['alias'];
                    $tmp['spec_image'] =$lib->image_path($b['spec_image'],"b");
                    if($i)$tmp['defalut'] = "ture";
                    $goods['spec'][] = $tmp;
                    $i=false;
                }
                base_kvstore::instance('b2c.dingzhi')->store($key1, $goods);
                echo json_encode($goods);
            }else{
                echo json_encode($list);
            }




        }





}
