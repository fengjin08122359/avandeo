<?php
/**
 * Created by PhpStorm.
 * User: zhuxiaofeng
 * Date: 14-12-2
 * Time: 下午4:30
 */
header('Content-type:text/html;charset=utf-8');
$root_dir = realpath(dirname(__FILE__).'../');
require_once ($root_dir."./config/config.php");
//define('APP_DIR', $root_dir . "/app");
if (!defined('DATA_DIR')) {
	define('DATA_DIR', $root_dir.'/data');
}

require_once (APP_DIR.'/base/kernel.php');
if (!kernel::register_autoload()) {
	require (APP_DIR.'/base/autoload.php');
}

require_once (APP_DIR.'/base/defined.php');
error_log('dsdsdss'."\r\n", 3, __FILE__ .'.log');
?>