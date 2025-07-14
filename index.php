<?php

//Prevent direct access
defined('PLUGINPATH') or exit('No direct script access allowed');

require_once __DIR__.'/vendor/autoload.php';

use RestApi\Libraries\Apiinit;

/*
  Plugin Name: API
  Description: Rest API module for RISE CRM
  Version: 1.2.2
  Requires at least: 2.8
  Author: Themesic Interactive
  Author URL: https://1.envato.market/themesic
 */



app_hooks()->add_filter('app_filter_staff_left_menu', function ($sidebar_menu) {
	$sidebar_menu["API"] = [
		"name"     => "api",
		"url"      => "api_settings",
		"class"    => "tag",
		"position" => 6
	];

	return $sidebar_menu;
});

app_hooks()->add_filter('app_filter_app_csrf_exclude_uris', function ($urls) {
	Apiinit::check_url("RestApi");
	$urls[] = "api/*";
	return $urls;
});

register_installation_hook("RestApi", function ($item_purchase_code) {
		include PLUGINPATH . "RestApi/install/do_install.php";
});

register_uninstallation_hook("RestApi", function () {
    $dbprefix = get_db_prefix();
    $db = db_connect('default');

    $sql_query = "DELETE FROM `" . $dbprefix . "settings` WHERE `" . $dbprefix . "settings`.`setting_name`='RestApi_verification_id';";
    $db->query($sql_query);

    $sql_query = "DELETE FROM `" . $dbprefix . "settings` WHERE `" . $dbprefix . "settings`.`setting_name`='RestApi_verified';";
    $db->query($sql_query);

    $sql_query = "DELETE FROM `" . $dbprefix . "settings` WHERE `" . $dbprefix . "settings`.`setting_name`='RestApi_last_verification';";
    $db->query($sql_query);

});