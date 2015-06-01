<?php
set_time_limit(0);
$root_dir = realpath(dirname(__FILE__) . '/../../../');
require_once( $root_dir . '/config/config.php');
require ($root_dir . '/app/base/kernel.php');
define('APP_DIR', $root_dir . "/app/");
include_once (APP_DIR . "/base/defined.php");
include_once (APP_DIR . "/base/lib/http.php");
if (!kernel :: register_autoload()){
	require (APP_DIR . '/base/autoload.php');
}
cachemgr::init(false);
app::get('desktop')->setconf('default_store_roles','');
?>