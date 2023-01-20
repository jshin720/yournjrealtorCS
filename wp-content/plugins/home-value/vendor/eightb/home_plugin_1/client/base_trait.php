<?php

namespace eightb\home_plugin_1\client;

trait base_trait
{
	use admin_menu_trait;
	use custom_post_type_trait;
	use email_trait;
	use shortcodes_trait;

	public function _construct()
	{
		$this->add_action( 'init' );
		$this->add_action( 'admin_enqueue_scripts', 'wp_enqueue_scripts' );
		$this->add_action( 'wp_enqueue_scripts' );
		$this->add_shortcode( $this->get_shortcode_name(), $this->get_shortcode_function() );
		$this->init_admin_menu();
		$this->init_plugin();

		ob_start();	// In order to force export downloads.
	}

	/**
		@brief		Inserts our options into the database and handles replacements of text values.
		@since		2016-12-09 22:25:41
	**/
	public function activate()
	{
		flush_rewrite_rules();

		if ( is_network_admin() )
		{
			$get = 'get_site_option';
			$set = 'update_site_option';
		}
		else
		{
			$get = 'get_local_option';
			$set = 'update_local_option';
		}

		// Replace the "empty" text values with their translated default values from disk.
		foreach( static::$options as $key => $value )
		{
			// Look for text values that are "empty" (No text).
			if ( ! $value == 'No text' )
				continue;

			// Only replace the option if it has the empty text.
			$current_value = $this->$get( $key );
			if ( $current_value != $value )
				continue;

			// Find the first best translated text, if any.
			$text = $this->get_text_file( $key );
			if ( $text !== false )
				$this->$set( $key, $text );
		}

		$this->activate_plugin();
	}

	/**
		@brief		Allow subclasses to do any extra initializing.
		@since		2017-03-24 17:48:43
	**/
	public function activate_plugin()
	{
	}

	/**
		@brief		Clear the site option cache of values.
		@since		2017-03-16 19:19:46
	**/
	public function clear_site_option_cache( $options )
	{
		if ( ! is_array( $options ) )
			$options = [ $options ];

		foreach( $options as $option_name )
		{
			$cache_key = get_current_network_id() . ':' . $this->fix_option_name( $option_name );
			wp_cache_delete( $cache_key, 'site-options' );
		}
	}

	/**
		@brief		Enqueue all JS.
		@since		2017-04-12 19:58:24
	**/
	public function enqueue_scripts()
	{
		wp_enqueue_script( $this->get_plugin_prefix(), $this->paths( 'url' ) . '/static/js.js', [ 'jquery' ], $this->plugin_version );
		wp_localize_script( $this->get_plugin_prefix(), $this->get_localize_script_variable(), [
			'action' => $this->get_plugin_prefix(),
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => $this->get_nonce_value(),
		] );
	}

	/**
		@brief		enqueue_styles
		@since		2017-04-12 19:59:41
	**/
	public function enqueue_styles()
	{
		// CSS
		$value = $this->get_local_or_site_option( 'load_css' );
		if ( ! $value )
			return;

		wp_enqueue_style( $this->get_plugin_prefix(), $this->paths( 'url' ) . '/static/css/css.css', [], $this->plugin_version );
	}

	/**
		@brief		Export the leads to a CSV file.
		@since		2016-12-23 21:09:59
	**/
	public function export_csv_to_file( $filename )
	{
		file_put_contents( $filename, "" );
		// The first line is the headers.
		$array = [ 'firstname', 'lastname', 'email', 'phone' ];
		file_put_contents( $filename, implode( ",", $array ) . "\n", FILE_APPEND );

		$page = 0;

		// Loop through all the leads.
		do
		{
			$leads = get_posts( [
				'post_type' => $this->get_lead()->get_post_type(),
				'posts_per_page'   => 100,
				'offset' => 100 * $page,
			] );			
			$lead_class = $this->get_lead();
			foreach( $leads as $lead )
			{
				$lead = $lead_class::load_from_store( $lead->ID );
				$row = [
					$lead->meta->lead_first_name,
					$lead->meta->lead_first_name,
					$lead->meta->lead_email,
					$lead->meta->lead_phone,
				];

				// Replace all commas in the columns with something else
				foreach( $row as $index => $data )
					$row[ $index ] = str_replace( ',', '.', $data );

				file_put_contents( $filename, implode( ",", $row ) . "\n", FILE_APPEND );
			}

			$page++;
		}
		while( count( $leads ) > 0 );
	}

	/**
		@brief		Createa a lead and fill it with random info.
		@since		2017-10-11 15:46:15
	**/
	public function generate_random_lead()
	{
		$r = $this->get_lead();
		foreach( $r->get_meta_keys() as $key )
			$r->meta->$key = $key . microtime();
		return $r;
	}

	/**
		@brief		Return the locally used API.
		@since		2017-03-07 22:33:53
	**/
	public function get_api()
	{
		$this->wp_die( 'Override %s / %s', __CLASS__, __FUNCTION__ );
	}

	/**
		@brief		Return the _GET key used when the server sends a new API request.
		@details	The default is the plugin prefix.
		@since		2017-03-07 22:33:53
	**/
	public function get_api_init_key()
	{
		return $this->get_plugin_prefix();
	}

	/**
		@brief		Return an option, preferrably local, else site if empty.
		@since		2016-12-23 16:12:00
	**/
	public function get_local_or_site_option( $option )
	{
		$key = $this->fix_local_option_name( $option );
		$value = get_option( $key, -101 );
		if ( $value == -101 OR $value == '' OR $value == 'No text' )
			$value = $this->get_site_option( $option );
		return $value;
	}

	/**
		@brief		Return the name of the JS variable where we keep our localization data.
		@since		2017-03-07 22:19:51
	**/
	public function get_localize_script_variable()
	{
		$this->wp_die( 'Override %s / %s', __CLASS__, __FUNCTION__ );
	}

	/**
		@brief		Return the nonce key for this user.
		@since		2016-12-11 21:53:39
	**/
	public function get_nonce_key()
	{
		return $this->paths( '__FILE__' ) . $_SERVER[ 'REMOTE_ADDR' ];
	}

	/**
		@brief		Return the user's unique nonce value.
		@since		2016-12-11 21:44:00
	**/
	public function get_nonce_value()
	{
		return wp_create_nonce( $this->get_nonce_key() );
	}

	/**
		@brief		Get the associated of this key from a file in the lang directory.
		@since		2017-02-05 22:02:12
	**/
	public function get_text_file( $key, $dir = null )
	{
		if ( $dir == '' )
			$dir = dirname( $this->paths( '__FILE__' ) );

		$filename = $dir . '/lang/' . $key . '.txt';

		if ( file_exists( $filename ) )
		{
			$contents = file_get_contents( $filename );
			$contents = trim( $contents );
			return $contents;
		}

		return false;
	}

	/**
		@brief		Return the view file.
		@since		2017-04-12 14:03:39
	**/
	public function get_view( $type )
	{
		$dir = dirname( $this->paths( '__FILE__' ) );
		$filename = sprintf( '%s/views/%s.html', $dir, $type );
		return file_get_contents( $filename );
	}

	/**
		@brief		Wordpress init hook.
		@since		2016-12-09 22:24:32
	**/
	public function init()
	{
		$this->init_custom_post_types();

		// Are we expecting an API call?
		$key = $this->get_api_init_key();
		if ( isset( $_REQUEST[ $key ] ) )
			$this->get_api()->maybe_process_api_call( $_REQUEST[ $key ] );

		// If we are looking at the front end.
		if ( ! is_admin() )
			// And the session is not started.
			if ( ! session_id() )
				session_start();
	}

	/**
		@brief		Allow subclasses to init themselves during _construct.
		@since		2017-03-07 22:32:22
	**/
	public function init_plugin()
	{
	}

	/**
		@brief		Local options are the same as the site options.
		@since		2016-12-25 16:36:54
	**/
	public function local_options()
	{
		return $this->site_options();
	}

	/**
		@brief		Format this number with commas for readability.
		@since		2016-12-28 16:35:05
	**/
	public function number_format( $amount )
	{
		return number_format( $amount );
	}

	/**
		@brief		Send this Lead to the webhooks we have stored.
		@since		2017-10-11 15:47:53
	**/
	public function send_lead_to_webhooks( $lead )
	{
		// Load the webhooks
		$webhooks = $this->get_local_or_site_option( 'new_lead_webhooks' );
		// Convert them to an array.
		$webhooks = explode( "\n", $webhooks );
		$webhooks = array_filter( $webhooks );

		// And now send them to each one.
		foreach( $webhooks as $url )
		{
			$this->debug( 'Sending lead %s to %s', $lead->meta, $url );
			wp_remote_post( $url, [
				'body' => (array) $lead->meta,
				'sslverify' => false,
				'timeout' => '30',
			] );
		}
	}

	/**
		@brief		Return true if this is a single install or in the network admin.
		@since		2017-02-05 20:24:23
	**/
	public function show_network_settings()
	{
		if ( ! $this->is_network )
			return true;
		return is_network_admin();
	}

	/**
		@brief		Return the site options.
		@details	The site options are the same as the local options, depending on whether the plugin was network activated or not.
		@since		2017-03-05 11:21:00
	**/
	public function site_options()
	{
		return array_merge( static::$options, parent::site_options() );
	}

	/**
		@brief		Enqueue scripts.
		@since		2016-12-11 20:26:35
	**/
	public function wp_enqueue_scripts()
	{
		$this->enqueue_scripts();
		$this->enqueue_styles();
	}
}
