<?php
function theme_widget_custom_navs( &$setting,&$smarty ){
$result = $comma = "";
$total = $i = 0;
$bgimg = $setting['bgimg'] ?"bgimg ": "";
if ( is_array( $setting['menus'] ) &&( $total = count( $setting['menus'] ) ) ){
foreach ( $setting['menus'] as $m ){
if ( !trim( $m['text'] ) ){
}else{
$m['url'] = " href=\"".$m['url']."\"";
$target = $m['target'] ?" target=\"".$m['target']."\"": "";
$class = $bgimg;
$class .= !$i ?"first ": "";
$class .= $m['classname'] ?$m['classname']." ": "";
$class .= $total -1 == $i ?"last ": "";
$class = $class ?" class=\"".trim( $class )."\"": "";
$comma = $setting['comma'] &&!preg_match( "/hide/i",$m['classname'] ) ?$comma : "";
$result .= $comma."<li".$class."><a".$m['url'].$target.">".$m['text']."</a></li>";
$comma = $setting['comma'] ?"<li class=\"comma\"><span>".$setting['comma']."</span></li>": "";
++$i;
}
}
}
$tag = $setting['bold'] ?"strong": "span";
$fronttips = trim( $setting['fronttips'] ) ?"<li class=\"front".( $i ?"": " last")."\"><".$tag.">".$setting['fronttips']."</".$tag."></li>": "";
$result = !$fronttips &&!$result ?"<li class=\"".$bgimg."last\"><a href=\"".$baseurl."\">shopex</a></li>": $fronttips.$result;
return "<ul class=\"tgnavs\">".$result."</ul>";
}
?>
