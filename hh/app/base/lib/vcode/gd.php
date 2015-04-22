<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_vcode_gd{

    function length($m = 8){
        $this->codeDir= ROOT_DIR.'/app/base/statics/code';
        if ($handle = opendir($this->codeDir)) {
            while (false !== ($file = readdir($handle))) {
                if (substr($file,-4)=='.png') {
                    $lib[] = substr($file,0,-4);
                }
            }
            closedir($handle);
        }

        $n = count($lib)-1;
        $str = '';
        for($i=0;$i<$m;$i++){
            $str.=$c = $lib[rand(0,$n)];
            $ret[] = $this->codeDir.'/'.$c.'.png';
        }
        $this->ret = &$ret;

        $this->code = $str;
    }

    function get_code(){
        return $this->code;
    }

    function display(){
        $arr = $this->ret;
        $bg = ROOT_DIR.'/app/base/statics/vcodebg.png';
        $image = imagecreatefrompng($bg);
        list($w, $baseH) = getimagesize($bg);
        header('Content-type: image/png');
        $x = 12;
        foreach($arr as $i=>$filename){
            list($w, $h) = getimagesize($filename);
            $source = imagecreatefrompng($filename);
            $t_id = imagecolortransparent($source);
            //$rotate = imagerotate($source, rand(-20,20),$t_id);
            $w2 = $w*$baseH/$h;
            imagecopyresized($image, $source, $x, 10, 0, 0, 8, 14, $w, $h);
            imagedestroy($source);
            imagedestroy($rotate);
            $x+=18;
        }
        $x+=1;
        $dst = imagecreatetruecolor($x, $baseH);
        imagecopyresampled($dst, $image, 0, 0, 0, 0, $x, $baseH, $x, $baseH);
        imagepng($dst);
        imagedestroy($image);
        imagedestroy($dst);
        exit();
    }
}

