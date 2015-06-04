<?php

function ecos_cactus_desktop_finder_find_id($find_id = "")
{
	if ($find_id) {
		return $find_id;
	}
	else {
		return substr(md5($_SERVER["QUERY_STRING"]), 5, 6);
	}
}

function ecos_cactus_desktop_finder_get_args($params = array())
{
	$extends = array();

	foreach ($params as $key => $val ) {
		if (($key != "app") && ($key != "act") && ($key != "ctl") && ($key != "_finder")) {
			$extends[$key] = $val;
		}

		if ($key == "_finder") {
			break;
		}
	}

	return $extends;
}

function ecos_cactus_desktop_finder_make_get($find_id = "")
{
	$_GET["ctl"] = ($_GET["ctl"] ? $_GET["ctl"] : "default");
	$_GET["act"] = ($_GET["act"] ? $_GET["act"] : "index");
	$_GET["_finder"]["finder_id"] = $find_id;

	if ($_GET["action"]) {
		unset($_GET["action"]);
	}

	return $_GET;
}

function ecos_cactus_desktop_finder_split_model($model_name)
{
	$return = array();

	if ($p = strpos($model_name, "_mdl_")) {
		$return[0] = substr($model_name, 0, $p);
		$return[1] = substr($model_name, $p + 5);
	}
	else {
		trigger_error("finder only accept full model name: " . $full_object_name, E_USER_ERROR);
	}

	return $return;
}

function ecos_cactus_desktop_finder_get_columns($cols, $func_columns, $default_in_list, $all_cols)
{
	if ($cols) {
		return explode(",", $cols);
	}
	else {
		if ($func_columns) {
			foreach ($func_columns as $key => $func_column ) {
				$col_keys[count($col_keys)] = $key;
			}
		}

		$columns = array_merge((array) $col_keys, (array) $default_in_list);

		foreach ($all_cols as $key => $value ) {
			if (in_array($key, $columns)) {
				$return[count($return)] = $key;
			}
		}

		return $return;
	}
}

function ecos_cactus_desktop_finder_all_columns($in_list, $func_columns, $dbschema_columns)
{
	$columns = array();

	foreach ((array) $in_list as $key ) {
		$columns[$key] = &$dbschema_columns[$key];
	}

	$return = array_merge((array) $func_columns, (array) $columns);

	foreach ($return as $k => $r ) {
		if (!$r["order"]) {
			$return[$k]["order"] = 100;
		}

		$orders[count($orders)] = $return[$k]["order"];
	}

	array_multisort($orders, SORT_ASC, $return);
	return $return;
}

function ecos_cactus_desktop_finder_builder_prototype_get_view_modifier($views, $finder_aliasname, $object_name, $views_temp = array())
{
	foreach ((array) $views as $k => $view ) {
		if (!isset($view["finder"])) {
			$views_temp[$k] = $view;

			if ($view["newcount"]) {
				cacheobject::set("view_tab_{$object_name}_$k", $view["addon"], 300 + time());
			}

			cacheobject::get("view_tab_{$object_name}_$k", $views_temp[$k]["addon"]);
		}
		else if (isset($view["finder"])) {
			if (is_array($view["finder"])) {
				if (in_array($finder_aliasname, $view["finder"])) {
					$views_temp[$k] = $view;
				}
			}
			else if ($finder_aliasname == $view["finder"]) {
				$views_temp[$k] = $view;
			}
		}
	}

	return $views_temp;
}

function ecos_cactus_desktop_finder_builder_prototype_get_view_url_array($extends, $_url_array = array())
{
	if ($extends && is_array($extends)) {
		foreach ($extends as $_key => $_val ) {
			if (array_key_exists($_key, $_url_array)) {
				continue;
			}

			$_url_array[$_key] = $_val;
		}
	}

	return $_url_array;
}

function ecos_cactus_desktop_finder_builder_view_script_gen_finderoptions($__view, $__options, $finderOptions = array())
{
	$finderOptions["packet"] = ($__view ? true : false);

	if ($__options) {
		$finderOptions = array_merge($finderOptions, $__options);
	}

	$finderOptions = json_encode($finderOptions);
	return $finderOptions;
}

function ecos_cactus_desktop_check_demosite($title)
{
	if (defined("DEV_CHECKDEMO") && DEV_CHECKDEMO) {
		$title = "测试环境，请勿进行真实业务行为";
	}

	return $title;
}

function ecos_cactus_desktop_finder_builder_prototype_get_views($row, $url)
{
	parse_str($row["filter_query"], $filter);
	$views_temp = array("label" => $row["filter_name"], "optional" => "", "filter" => $filter, "filter_id" => $row["filter_id"], "addon" => "_FILTER_POINT_", "custom" => true, "href" => $url);
	return $views_temp;
}


?>
