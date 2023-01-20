<?php

namespace eightb\home_value\classes;

/**
	@brief		Base class for the premium plugin.
	@since		2017-02-01 14:51:51
**/
class Premium
	extends \plainview\sdk_eightb_home_value\wordpress\base
{
	use \plainview\sdk_eightb_home_value\wordpress\updater\edd;

	/**
		@brief		The URL of the purchase and update server.
		@since		2017-02-05 22:25:05
	**/
	public static $server_url = 'https://homevalueplugin.com';

	/**
		@brief		All official BC plugin packs have one EDD url.
		@since		2015-10-29 12:18:23
	**/
	public function edd_get_url()
	{
		return static::$server_url;
	}

	public function edd_get_item_name()
	{
		return 'Home Value Premium';
	}

	/**
		@brief		Site options.
		@since		2017-02-01 15:56:06
	**/
	public function site_options()
	{
		return array_merge( [
			'edd_updater_license_key' => '',
		], parent::site_options() );
	}
}
