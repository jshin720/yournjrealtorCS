<?php

namespace eightb\home_value\classes;

/**
	@brief		The Home Value API for searches and other things.
	@since		2017-02-01 22:58:36
**/
class API
	extends \eightb\home_plugin_1\client\API
{
	/**
		@brief		Accept the validated API data from the server.
		@since		2017-03-09 22:48:22
	**/
	public function process_api_call( $data )
	{
		// Set our new API key.
		$this->plugin->update_site_option( 'home_value_api_key', $data->home_value_api_key );

		// Maybe set the Google API key
		$current_google_api_key = $this->plugin->get_site_option( 'google_api_key' );
		if ( $current_google_api_key == '' )
			$this->plugin->update_site_option( 'google_api_key', $data->google_api_key );

		// And update the results left.
		$this->plugin->update_site_option( 'results_left', $data->results_left );
	}

	/**
		@brief		Ask the server to generate a new (or retrieve our existing) key.
		@since		2017-02-03 19:49:34
	**/
	public function generate()
	{
		// The e-mail address of the admin user.
		$user = wp_get_current_user();
		$email = $user->data->user_email;

		$access_key = $this->generate_transient_access_key();

		$data = [
			'access_key' => $access_key,
			'action' => 'create_key',
			'email' => $email,
			'url' => wp_login_url(),		// This is where the new key will be sent.
		];

		return $this->api_call( $data );
	}

	/**
		@brief		Return the API url.
		@details	Based on the Premium server URL.
		@since		2017-02-05 22:33:00
	**/
	public static function get_url()
	{
		return Premium::$server_url . '/api';
	}

	/**
		@brief		Do a search.
		@since		2017-02-07 23:14:21
	**/
	public function search( $street, $zip )
	{
		$r = $this->api_call( [
			'action' => 'search',
			'street' => $street,
			'zip' => $zip,
		] );
		return $r;
	}

	/**
		@brief		Ask for a status of the key.
		@since		2017-02-01 23:06:44
	**/
	public function status()
	{
		$r = $this->api_call( [
			'action' => 'status',
		] );
		//print_r( $r );
		//die("111111111");
		$this->plugin->update_site_option( 'refill_date', $r->refill_date );
		$this->plugin->update_site_option( 'results_left', $r->results_left );
		$this->plugin->clear_site_option_cache( [ 'refill_date', 'results_left' ] );
		return $r;
	}
}
