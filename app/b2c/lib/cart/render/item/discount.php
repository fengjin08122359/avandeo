<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/**
 * cart render item goods
 * $ 2010-05-06 11:23 $
 */

class b2c_cart_render_item_discount
{
    public $app = 'b2c';
    public $file = 'site/cart/item/discount/index.html';
    #public $wap_file = 'wap/cart/item/index.html';
    public $index = 300; // 所处位置


    public function _get_minicart_view() {
        $arr = array(
            'file'=>'site/cart/mini/item/discount.html',
            #'wap_file'=>'wap/cart/mini/item/goods.html',
            'index'=>200,
        );
        return $arr;
    }
}

