<?php
/**
 * 聊天框桌面挂件
 * @author qianzedong <qianzedong@shopex.cn>
 */
class b2c_desktop_widgets_chat implements desktop_interface_widget{

	public $order = 1;

    public function __construct($app){
        $this->app = $app;
        $this->render =  new base_render(app::get('b2c'));
        $this->user = kernel::single('desktop_user');
    }
    
    public function get_title(){
        return app::get('b2c')->_('互动平台');
    }

    public function get_html(){

        /**
         * 过滤掉没有权限的帐号
         * @param  array $ids  需要处理的帐号 一维
         * @param  array $data 数据集合
         */
        function filter_chat_permission(&$ids,$data)
        {
            $tmp_array = $result_array = array();
            foreach($data as $key => $val)
            {
                $tmp_array[$val['user_id']] = $val;
            }
            foreach ($ids as $key => $val) {
                $config = serialize($tmp_array[$val]['config']);
                if(stripos(strval($config),'b2c_desktop_widgets_chat')!==false)
                {
                    $result_array[$val] = array(
                        'user_id'=>$val,
                        'name'=>$tmp_array[$val]['name'],
                    );
                }
            }
            $ids = $result_array;
        }

        //所有操作人
        $desktop_usersMdl = app::get('desktop')->model('users');
        $users = $desktop_usersMdl->getList('name,user_id,config');
        $user_ids = array();
        foreach($users as $key => $val)
        {
            $user_ids[] = $val['user_id'];
        }

        //所有职员
        $storelist_storelist_relatMdl = app::get('storelist')->model('storelist_relat');
        $staff_data = $storelist_storelist_relatMdl->getList('oper_id');
        $staff_ids = array();
        foreach ($staff_data as $val) {
            $staff_ids[] = $val['oper_id'];
        }

        //差集,得出平台员工
        $platform_ids = array_diff($user_ids,$staff_ids);
        
        $store = kernel::single('storelist_store');
        $to_users = array();
        if($store->store_id)
        {   //门店的人
            filter_chat_permission($platform_ids,$users);
            $to_users = $platform_ids;
        }else{
            //平台的人
            filter_chat_permission($staff_ids,$users);
            $to_users = $staff_ids;
        }
        $this->render->pagedata['to_users'] = $to_users;

        $this->render->pagedata['user_id'] = $this->user->user_data['user_id'];
    	return $this->render->fetch('desktop/widgets/chat.html');

    }

    public function get_className(){
          return " valigntop chat";
    }

    public function get_width(){
          return "l-2";
    }
}
