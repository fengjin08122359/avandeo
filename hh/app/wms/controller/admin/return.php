<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class wms_ctl_admin_return extends desktop_controller{

    
    /**
     * 构造方法
     * @params object app object
     * @return null
     */
    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }

   function index(){
        $this->finder('b2c_mdl_reship',array(
            'title'=>app::get('b2c')->_('退换货单'),
            'allow_detail_popup'=>true,
            'use_buildin_recycle'=>false,
            'use_view_tab'=>true,
            'params'=>array(
                'bill_type' => 'reship',
            )
            ));
    }
}
