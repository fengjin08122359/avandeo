<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2013 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

interface base_interface_systemcache{

    function store($key, $value);

    function fetch($key, &$value, $timeout_version=null);

    function delete($key);

}
