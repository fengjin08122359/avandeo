<?php
function lg($obj)
{
	static $a=0;
	$a++;
	error_log("time ".$a.PHP_EOL.var_export($obj,1).PHP_EOL,3,'/data/httpd/va_wodi_hh/lg.log');
}
function debug($obj)
{
	echo "debug结果: <hr />";
	echo "<pre>";
	var_export($obj);
	die;
}
#include("app/serveradm/xhprof.php");
define('ROOT_DIR',realpath(dirname(__FILE__)));
require(ROOT_DIR.'/app/base/kernel.php');
kernel::boot();

if(defined("STRESS_TESTING")){
    b2c_forStressTest::logSqlAmount();
}
