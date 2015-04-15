<?php

class base_ego_syscache
{
	static 	private $__supports = array("service" => "base_syscache_service", "setting" => "base_syscache_setting");
	static 	private $__instance = array();
	private $_controller;
	private $_support_type;
	private $_handler;

	static public function instance($support_type)
	{
		if (!isset(self::$__supports[$support_type])) {
			return false;
		}

		if (!isset(self::$__instance[$support_type])) {
			self::$__instance[$support_type] = new syscache($support_type);
		}

		return self::$__instance[$support_type];
	}

	public function __construct($support_type)
	{
		$this->_support_type = $support_type;
		$this->_handler = new self::$__supports[$support_type]();

		if ($this->_handler instanceof base_interface_syscache_farmer) {
			if (defined("SYSCACHE_ADAPTER")) {
				$class_name = constant("SYSCACHE_ADAPTER");
			}
			else {
				$class_name = "base_syscache_adapter_filesystem";
			}

			$this->set_controller(new $class_name());

			if ($this->get_controller()->init($this->_handler) !== true) {
				$this->_reload();
			}

			return true;
		}
		else {
			throw new Exception("this instance must implements base_interface_fiarmer");
		}
	}

	public function _reload()
	{
		$this->get_controller()->create($this->_handler->get_data());
		$this->get_controller()->init($this->_handler);
	}

	public function set_controller($controller)
	{
		if ($controller instanceof base_interface_syscache_adapter) {
			$this->_controller = $controller;
		}
		else {
			throw new Exception("this instance must implements base_interface_syscache_adapter");
		}
	}

	public function get_controller()
	{
		return $this->_controller;
	}

	public function set_last_modify()
	{
		$this->_handler->set_last_modify();
		$this->_reload();
	}

	public function get($key)
	{
		return $this->_controller->get($key);
	}
}

class base_ego_policy
{
	public function push($params)
	{
		$params["local"] = ($params["local"] ? $params["local"] : $this->local_file);
		$params["remote"] = ($params["remote"] ? $params["remote"] : $this->remote_file);
		$params["resume"] = ($params["resume"] ? $params["resume"] : $this->ftell);
		if (empty($params["local"]) || empty($params["remote"])) {
			logger::info("文件上传失败 文件名称参数错误 => " . var_export($params, 1));
			return false;
		}

		if (!$this->policy_obj->push($params, $msg)) {
			logger::info("文件上传失败 =>" . $msg);
			return false;
		}

		return true;
	}

	public function pull($params, &$msg)
	{
		$params["local"] = ($params["local"] ? $params["local"] : $this->local_file);
		$params["remote"] = ($params["remote"] ? $params["remote"] : $this->remote_file);
		$params["resume"] = ($params["resume"] ? $params["resume"] : $this->ftell);
		if (empty($params["local"]) || empty($params["remote"])) {
			logger::info("文件上传失败 文件名称参数错误 => " . var_export($params, 1));
			return false;
		}

		if (!$this->policy_obj->pull($params, $msg)) {
			logger::info("文件下载失败 =>" . $msg);
			return false;
		}

		return true;
	}

	public function remote_file_size($filename)
	{
		return $this->policy_obj->size($filename);
	}

	public function delete_remote_file($filename = NULL)
	{
		$filename = ($filename ? $filename : $this->remote_file);
		$this->policy_obj->delete($filename);
		return true;
	}

	public function create_local_file()
	{
		$this->local_file = tempnam(TMP_DIR, "importexport");

		if (!$this->local_file) {
			return false;
		}

		$this->file = fopen($this->local_file, "w");
		return $this->file;
	}

	public function create_remote_file($params)
	{
		$this->remote_file = $params["key"] . "." . $params["filetype"];
		return $this->remote_file;
	}

	public function write_local_file($rs)
	{
		$this->ftell = ftell($this->file);

		if (!fwrite($this->file, $rs)) {
			return false;
		}

		return true;
	}

	public function size_local_file($is_format = false)
	{
		$filesize = filesize($this->local_file);

		if (!$is_format) {
			return $filesize;
		}

		$bytes = floatval($filesize);

		switch ($bytes) {
		case $bytes < 1024:
			$result = $bytes . "B";
			break;

		case $bytes > 1024:
			$result = strval(round($bytes / 1024, 2)) . "KB";
			break;

		default:
			$result = $bytes / pow(1024, 2);
			$result = strval(round($result, 2)) . "MB";
			break;
		}

		return $result;
	}

	public function close_local_file($file = NULL)
	{
		if (!$file) {
			$file = $this->file;
		}

		fclose($file);
		unlink($this->local_file);
		return true;
	}
}

class system_commerce
{
	public function get_commerce_version()
	{
		$deploy = kernel::single("base_xml")->xml2array(file_get_contents(ROOT_DIR . "/config/deploy.xml"), "base_deploy");

		if ($deploy["publish_version"] == "commerce") {
			return true;
		}

		return false;
	}
}

function ecos_cactus()
{
	$args = func_get_args();
	$app_name = $args[0];
	unset($args[0]);
	$func_name = "ecos_cactus_" . $app_name . "_" . $args[1];
	unset($args[1]);
	require_once (ROOT_DIR . "/app/base/ego/" . $app_name . "/ego.php");
	$return = call_user_func_array($func_name, $args);
	return $return;
}


?>
