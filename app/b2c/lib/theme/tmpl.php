<?php
/**
 * Created by PhpStorm.
 * User: chugongbiao
 * Date: 15/1/27
 * Time: 下午2:03
 */

class b2c_theme_tmpl {
	public function __get_tmpl_list($ctl) {
		$arrCtl = array(
			'space_glist'       => app::get('b2c')->_('生活空间列表'),
			'space_plist'       => app::get('b2c')->_('生活空间详情'),
			'match_glist'       => app::get('b2c')->_('风格搭配列表'),
			'match_plist'       => app::get('b2c')->_('风格搭配详情'),
			'case_list'         => app::get('b2c')->_('案例列表'),
			'designer_personal' => app::get('b2c')->_('设计师个人页'),
			'designer_detail'   => app::get('b2c')->_('设计师详情页'),
			'designer_index'    => app::get('b2c')->_('设计师首页'),
			'discount_list'     => app::get('b2c')->_('折扣列表'),
			'store_list'        => app::get('b2c')->_('线下体验店列表'),
			'store'             => app::get('b2c')->_('线下体验店详情'),
		);
		return array_merge($ctl, $arrCtl);
	}
}