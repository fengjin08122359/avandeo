<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class b2c_service_site_indexseo 
{

    public function title() 
    {
        return app::get('site')->getConf('site.name');
    }//End Function

}//End Class