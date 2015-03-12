<?php
class apiactionlog_router_logging{

    private static $_api_type="response";
    private static $_rsp_service_mapping = array(
        'api.ectools.get_payments'=>'payments',
        'api.b2c.update_store'=>'goods',
        'api.b2c.order'=>'order',
        'api.b2c.payment'=>'order_payment',
        'api.b2c.delivery'=>'order_delivery',
        'api.b2c.aftersale'=>'order_aftersale',
        'api.b2c.reship'=>'order_reship',
        'api.b2c.refund'=>'order_refund'
    );

    private static $_res_service=array(
        'store.trade.add'=>'b2c_apiv_apis_20_ome_order',
        'store.trade.update'=>'b2c_apiv_apis_20_ome_orderupdate',
        'store.trade.aftersale.add'=>'b2c_apiv_apis_20_ome_aftersales',
        'store.trade.refund.add'=>'apiactionlog_router_refund',
        );

    public function save_log($service,$method,$data){
        $type = isset(self::$_rsp_service_mapping[$service]) ? self::$_rsp_service_mapping[$service] : '';
        $api_mdl = app::get('apiactionlog')->model('apilog');
        if($type){
            $services = kernel::single('apiactionlog_router_'.$type);
            if(method_exists($services,$method)){
                $api_save_data = $services->$method($data);
                $result = $api_mdl->save($api_save_data); 
                return $result;
            }
        } 
    }

    public function request_log($method,$params,$rpc_id){
        $class = isset(self::$_res_service[$method]) ? self::$_res_service[$method] : '';
        $api_mdl = app::get('apiactionlog')->model('apilog');
        if($class){
            $obj = kernel::single($class);
            $title = $obj->get_title();
            $time = time();
            $original_bn = $params['tid'];
            if(is_null($rpc_id)){
                $microtime = utils::microtime();
                $rpc_id = str_replace('.','',strval($microtime));
                $randval = uniqid('', true);
                $rpc_id .= strval($randval);
                $rpc_id = md5($rpc_id);
                $data = array(
                    'apilog'=>$rpc_id,
                    'calltime'=>$time,
                    'params'=>$params,
                    'api_type'=>'request',
                    'msg_id'=>'',
                    'worker'=>$method,
                    'original_bn'=>$original_bn,
                    'task_name'=>$title,
                    'log_type'=>'order',
                    'createtime'=>$time,
                    'last_modified'=>$time,
                    'retry'=>$retry?$retry:0,
                );

            }else{
                $arr_pk = explode('-', $rpc_id);
                $rpc_id = $arr_pk[0];
                $tmp = $api_mdl->getList('*', array('apilog'=>$rpc_id));
                if($tmp && $tmp[0]['status'] !='sending'){
                    $retry =$tmp[0]['retry']+1;
                }
                $data = array(
                    'apilog_id'=>$tmp[0]['apilog_id'],
                    'apilog'=>$rpc_id,
                    'calltime'=>$time,
                    'api_type'=>'request',
                    'worker'=>$method,
                    'original_bn'=>$original_bn,
                    'task_name'=>$title,
                    'log_type'=>'order',
                    'createtime'=>$time,
                    'last_modified'=>$time,
                );

            }
            $result = $api_mdl->save($data); 
            $rpc_id = $rpc_id."-".$time;
            return $rpc_id;

        }

    }

    function update($data,$id,$time){
        $api_mdl = app::get('apiactionlog')->model('apilog');
        $api_mdl->update($data,array('apilog'=>$id,'calltime'=>$time,'api_type'=>'request'));
        return true;
    }
}
