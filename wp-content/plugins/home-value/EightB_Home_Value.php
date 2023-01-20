<?php
/*
Author:			8blocks
Author Email:	support@8blocks.com
Author URI:		http://8blocks.com
Description:	Home value plugin.
Plugin Name:	8b Home Value
Plugin URI:		https://homevalueplugin.com
Version:		2.32
*/

DEFINE( 'EIGHTB_HOME_VALUE_PLUGIN_VERSION', 2.32 );

require_once( __DIR__ . '/vendor/autoload.php' );

/**
	@brief		Return the instance of Home Value.
	@since		2016-12-09 19:23:38
**/
function EightB_Home_Value()
{
	return eightb\home_value\Home_Value::instance();
}

new eightb\home_value\Home_Value();