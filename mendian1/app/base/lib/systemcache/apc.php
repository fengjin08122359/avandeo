<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2013 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class base_systemcache_apc extends base_systemcache_abstract implements base_interface_systemcache
{

    function __construct($prefix) 
    {
        $this->prefix = $prefix;
    }//End Function

    public function fetch($key, &$value, $timeout_version=null) 
    {
        if(!apc_exists($this->create_key($key))){
            base_kvstore::instance($this->prefix)->fetch($key,$val);
            if(!is_null($val)){
               $this->store($key,$val);
            }
        }

        $data = apc_fetch($this->create_key($key));
        if($data !== false){
            $store = unserialize($data);    //todo：反序列化
            if($timeout_version < $store['dateline']){
                if($store['ttl'] > 0 && ($store['dateline']+$store['ttl']) < time()){
                    return false;
                }
                $value = $store['value'];
                return true;
            }
        }
        return false;
    }//End Function

    public function store($key, $value, $ttl=0) 
    {
        $store['key'] = $this->create_key($key);
        $store['value'] = $value;
        $store['dateline'] = time();
        $store['ttl'] = $ttl;
        $store['prefix'] = $this->prefix;

        $ret = apc_store($store['key'], serialize($store), 0);

        return $ret;
    }//End Function

    public function delete($key) 
    {
        return apc_delete($this->create_key($key));
    }//End Function

    public function recovery($record)
    {
        $key = $record['key'];
        $store['value'] = $record['value'];
        $store['dateline'] = $record['dateline'];
        $store['ttl'] = $record['ttl'];
        $store['prefix'] = $this->prefix;
        $ret = apc_store($this->create_key($key), serialize($store), 0);
        return $ret;
    }//End Function


    /**
     * 检查APC缓存是否存在
     * @param  string $key   KEY值
     */ 
    public function exists($key) {
        return apc_exists($key); 
    } 

}//End Class
