<?php
/**
* style的data
*/
class b2c_style_data
{
	public $app;
	private $_status = array();
	public function __construct()
	{
		$this->app = &app::get('b2c');
		$this->set_status();
	}
	public function set_status()
	{
		$status = array(
			$this->app->_('banner大图'),
			$this->app->_('最流行风排行榜'),
		);
		for($i=0;$i<count($status);$i++)
		{
			$this->_status[1<<$i] = $status[$i];
		}
	}
	public function get_status()
	{
		return $this->_status;
	}
}