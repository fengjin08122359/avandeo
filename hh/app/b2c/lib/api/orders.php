<?php

class b2c_api_orders{
		/**
	 * 构造方法
	 * @param object
	 * @return null
	 */
	public function __construct($app){
		$this->app = $app;
	}

	public function get_order_info(){
		$_post_params = $_POST;
		$_arr_response = array();
        $obj_order_create = kernel::single("b2c_order_create");
        $result = $obj_order_create->save($_post_params);
        if($result){
        //	error_log('111'."\r\n",3,__FILE__.'.log');
        	$response = array(
			  'res'=>'true',
		    );
	        
        }else{
        	//error_log('111'."\r\n",3,__FILE__.'.log');
        	$response = array(
			  'res'=>'false',
		    );
        }
        //error_log(''.var_export($response,1)."\r\n",3,__FILE__.'.log');
        header('Content-Type:text/jcmd; charset=utf-8');
		echo json_encode($response);exit;
	}
}