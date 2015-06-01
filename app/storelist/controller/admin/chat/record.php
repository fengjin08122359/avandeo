<?php
/**
 * 门店职员与平台职员互动
 * @author qianzedong <qianzedong@shopex.cn>
 */
class storelist_ctl_admin_chat_record extends desktop_controller{

	/**
	 * 记录数量
	 * @var integer
	 */
	public $recrod_limit = 50;

	/**
	 * lasttime的kvstore池名
	 * @var string
	 */
	public $kvstore_addtime = 'storelist.chat.record.lasttime';
	
	/**
	 * 获取最新记录
	 * @param int $time 为0直接取数据库,不为0增加kvstore判断
	 * @return json 记录列表
	 */
	public function ajax_get_message($time = 0)
	{
		$db = kernel::database();
		$time = intval($time);

		function get_all_userid($res,&$user_ids)
		{
			$user_ids[0] = 0;
			foreach($res as $val)
			{
				$user_ids[$val['from_id']] = intval($val['from_id']);
				$user_ids[$val['to_id']] = intval($val['to_id']);
			}
			return $user_ids;
		}

		function get_info_by_userid($infos,$uid)
		{
			foreach ($infos as $val) {
				if($val['user_id'] == $uid)
				{
					return $val;
				}
			}
			return array();
		}

		if($time === 0)
		{
			$sql = "SELECT `record_id`,`from_id`,`to_id`,`message`,`addtime` FROM `sdb_storelist_chat_record` WHERE (`to_id`={$this->user->user_id} OR `from_id`={$this->user->user_id}) ORDER BY `addtime` DESC LIMIT 0,".$this->recrod_limit;
		}else{
			base_kvstore::instance($this->kvstore_addtime)->fetch($this->user->user_id,$addtime);
			if(!$addtime)
			{
				$addtime = 0;
			}else{
				$addtime = intval($addtime);
			}
			if($time < $addtime)
			{
				$sql = "SELECT * FROM `sdb_storelist_chat_record` WHERE (`to_id`={$this->user->user_id} OR `from_id`={$this->user->user_id}) AND `addtime` > {$time} ORDER BY `addtime` DESC LIMIT 0,".$this->recrod_limit;
			}
		}

		if($sql && ($result = $db->select($sql)))
		{
			$result = array_reverse($result);

			get_all_userid($result,$user_ids);
			$user_infos = $db->select("SELECT `user_id`,`name` FROM `sdb_desktop_users` WHERE `user_id` in (".implode(',',$user_ids).")");
			foreach ($result as &$val) {
				$val['from_user'] = get_info_by_userid($user_infos,$val['from_id']);
				$val['to_user'] = get_info_by_userid($user_infos,$val['to_id']);
			}
			unset($val);
			$json = json_encode($result);
		}else{
			$json = json_encode(array());
		}

		exit($json);
	}

	/**
	 * 增加一条记录
	 * @param  array $sdf 输入结构
	 * array(
	 * 	'to_id' => 1,
	 * 	'message' => '',
	 * );
	 * @return json 返回结构
	 * array(
	 * 	'result'=>'succ',
	 * 	'result_message'=>'成功',
	 * );
	 */
	public function ajax_add()
	{
		$res = array();
		$sdf = $_POST;

		$sdf['from_id'] = $this->user->user_id;
		$sdf['addtime'] = $_SERVER['REQUEST_TIME'];

		if($this->verify($sdf))
		{
			$storelist_chat_recordMdl = $this->app->model('chat_record');
			$res = $storelist_chat_recordMdl->save($sdf);
			if($res){

				base_kvstore::instance($this->kvstore_addtime)->store($sdf['from_id'],$_SERVER['REQUEST_TIME']);
				base_kvstore::instance($this->kvstore_addtime)->store($sdf['to_id'],$_SERVER['REQUEST_TIME']);

				$sql = "SELECT * FROM `sdb_storelist_chat_record` WHERE `from_id`={$this->user->user_id} ORDER BY `addtime` DESC LIMIT 0,1";
				$lastInfo = $storelist_chat_recordMdl->db->select($sql);
				$lastInfo = $lastInfo[0];				
				
				$res = array(
					'result' => 'succ',
					'result_data' => $lastInfo,
					'result_message' => '成功',
				);

			}else{
				$res = array(
					'result' => 'fail',
					'result_message' => '存储失败',
				);
			}
		}else{
			$res = array(
				'result' => 'fail',
				'result_message' => '验证失败',
			);
		}

		exit(json_encode($res));
	}

	/**
	 * 数据验证
	 * todo
	 * @param  array $sdf 输入结构
	 * @return bool
	 */
	private function verify($sdf)
	{
		return true;
	}

}