<?php 
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 *
 *
 * @package default
 * @author kxgsy163@163.com
 */
class couponlog_mdl_order_coupon_user extends dbeav_model
{
    
    var $defaultOrder = array('id',' DESC');
    public function modifier_member_id( $cols ) {
        if( $cols ) {
            return kernel::single('b2c_user_object')->get_member_name(null,$cols); 
        } else {
            return '非会员顾客';
        }
    }
	
	/**
     * 重写搜索的下拉选项方法
     * @param null
     * @return null
     */
    public function searchOptions(){
        $columns = array();
        foreach($this->_columns() as $k=>$v){
            if(isset($v['searchtype']) && $v['searchtype']){
                if ($k == 'member_id')
                {
                    $columns['member_key'] = $v['label'];
                }
                else
                    $columns[$k] = $v['label'];
            }
        }

        return $columns;
    }
	
	public function _filter($filter,$tableAlias=null,$baseWhere=null){
        $where = array();
        $store = kernel::single('storelist_store');
        if($store->store_id > 0){
            $Coupon_region = app::get('b2c')->model('coupons_region');
            $coupon_list_tmp = $Coupon_region->getList('distinct cpns_id',array('region_id|in' => $store->share_area));
            $cpns_ids_str = "'0'";
            if(is_array($coupon_list_tmp) && count($coupon_list_tmp) > 0){
                foreach($coupon_list_tmp as $clkey => $clvalue){
                    $cpns_ids_str .= ",'".$clvalue['cpns_id']."'";
                }
                unset($coupon_list_tmp);
            }
            $where[] = "cpns_id in (".$cpns_ids_str.")";
        }

        if($filter['area_fee_conf']['area_fee']['areaGroupId']){
            $obj_area = app::get('ectools')->model('regions');
            $area_ids_arr = $obj_area->getALLEndChildByRegionTreeList($filter['area_fee_conf']['area_fee']['areaGroupId']);
            $Coupon_region2 = app::get('b2c')->model('coupons_region');
            $coupon_list_tmp2 = $Coupon_region2->getList('distinct cpns_id',array('region_id|in' => $area_ids_arr));
            $cpns_ids_str2 = "'0'";
            if(is_array($coupon_list_tmp2) && count($coupon_list_tmp2) > 0){
                foreach($coupon_list_tmp2 as $cl2key => $cl2value){
                    $cpns_ids_str2 .= ",'".$cl2value['cpns_id']."'";
                }
                unset($coupon_list_tmp2);
            }
            $where[] = "cpns_id in (".$cpns_ids_str2.")";
        }

        if($filter['member_key']){
            $aData = app::get('pam')->model('members')->getList('member_id',array('login_account|has' => $filter['member_key']));
            if($aData){
                foreach($aData as $key=>$val){
                    $member[$key] = $val['member_id'];
                }
                $filter['member_id'] = $member;
            }
            else{
                return 0;
            }
            unset($filter['member_key']);
        }
        $filter = parent::_filter($filter);

        if(count($where) > 0){
            $filter .= ' and '.implode($where,' and ');
        }
        return $filter;
    } 
}
