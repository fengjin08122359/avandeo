<?php
/**
* 门店相关信息
* @author qianzedong <qianzedong@shopex.cn>
*/
class storelist_store
{
	/**
	 * 门店主角色的conf
	 * @var string
	 */
	public static $store_owner_conf = 'default_store_roles';

    /**
     * 操作员的信息,取自desktop_user
     * @var obj
     */
    public $user = null;

	/**
	 * 门店ID
     * 如果等于0,则为平台人员
	 * @var int
	 */
	public $store_id = 0;

	/**
	 * 是否是门店主
	 * @var bool
	 */
	public $is_store_owner = false;

	/**
	 * 门店所属角色列表
	 * @var array
	 */
	public $role_list = array();

	/**
	 * 门店所属职员列表
	 * @var array
	 */
	public $staff_list = array();

	/**
	 * 门店分享区域
	 * @var array
	 */
	public $share_area = array();

	/**
	 * 门店基本属性
	 * @var array
	 */
	public $store = array();

	/**
	 * 初始化基本信息
	 */
	public function __construct($app)
    {

        $this->app = $app;
        $this->user = kernel::single('desktop_user');
        $this->__store_init();

    }

    /**
     * 店铺信息初始化
     */
    private function __store_init()
    {

    	//是否门店主
    	$desktop_hasrole = app::get('desktop')->model('hasrole');
    	$filter = array('user_id'=>$this->user->user_id);
    	$roles_data = $desktop_hasrole->getList('*',$filter);
    	$roles = array();
    	foreach ($roles_data as $val) {
    		$roles[] = $val['role_id'];
    	}
    	$owner_role = app::get('desktop')->getConf($this->store_owner_conf);
    	if(in_array($owner_role,$roles))
    	{
    		$this->is_store_owner = true;
    	}

    	//门店ID
    	$storelist_storelist_relatMdl = app::get('storelist')->model('storelist_relat');
    	$filter = array('oper_id'=>$this->user->user_id);
    	$relat = $storelist_storelist_relatMdl->dump($filter);
    	if($relat && isset($relat['stores_id'])){
    		$this->store_id = $relat['stores_id'];
    	}

    	//是门店人员就继续,否则视为平台人员
    	if( $this->store_id === 0 )
    	{
    		return false;
    	}

    	//门店所属角色列表
    	$this->role_list = array();
    	$storelist_storelist_rolesMdl = app::get('storelist')->model('storelist_roles');
    	$filter = array('stores_id'=>$this->store_id);
    	$roles_data = $storelist_storelist_rolesMdl->getList('*',$filter);
    	foreach ($roles_data as $val) {
    		$this->role_list[] = $val['role_id'];
    	}

    	//所属职员列表
    	$this->staff_list = array();
    	$filter = array('stores_id'=>$this->store_id);
    	$staff_data = $storelist_storelist_relatMdl->getList('*',$filter);
    	foreach ($staff_data as $val) {
    		$this->staff_list[] = $val['oper_id'];
    	}

    	//门店分享区域
    	$this->share_area = array();
    	$storelist_storelist_itemMdl = app::get('storelist')->model('storelist_item');
    	$filter = array('store_id'=>$this->store_id);
    	$item_data = $storelist_storelist_itemMdl->getList('*',$filter);
    	foreach ($item_data as $val) {
    		$this->share_area[] = $val['region_id'];
    	}

    	//门店基本属性
    	$storelist_storelist = app::get('storelist')->model('storelist');
    	$this->store = $storelist_storelist->dump($this->store_id);

    	return $this;
    }
 
}