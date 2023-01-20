<?php

namespace eightb\home_value;

/**
	@brief		Home value plugin.
	@since		2016-12-09 20:28:13
**/
class Home_Value
	extends \plainview\sdk_eightb_home_value\wordpress\base
{
	use \eightb\home_plugin_1\client\base_trait;
	use admin_menu_trait;
	use shortcodes_trait;

	use \plainview\sdk_eightb_home_value\wordpress\traits\debug;

	public $plugin_version = EIGHTB_HOME_VALUE_PLUGIN_VERSION;

	/**
		@brief		The options that we use. Combined local and site.
		@details	Note the "No text" values. Those help HV detect whether a lang file should be loaded from disk.
		@since		2016-12-25 17:12:10
	**/
	public static $options = [
		/**
			@brief		The placeholder text for the address input.
			@since		2017-08-08 02:37:54
		**/
		'address_search_form_address_input_placeholder' => 'No text',
		/**
			@brief		The text on the submit button for the address search.
			@since		2017-08-08 02:37:54
		**/
		'address_search_form_submit_button_text' => 'No text',
		/**
			@brief		List of e-mail recipients for new leads.
			@since		2016-12-25 14:47:38
		**/
		'email_new_lead_recipients' => '',
		/**
			@brief		The e-mail of the new lead e-mail sender.
			@since		2017-01-09 23:59:56
		**/
		'email_new_lead_sender_email' => '',
		/**
			@brief		The name of the new lead e-mail sender.
			@since		2017-01-09 23:59:56
		**/
		'email_new_lead_sender_name' => '',
		/**
			@brief		Subject line for new lead e-mails.
			@since		2016-12-25 14:50:41
		**/
		'email_new_lead_subject' => 'No text',
		/**
			@brief		Text for new lead e-mail.
			@since		2016-12-25 14:50:41
		**/
		'email_new_lead_text' => 'No text',
		/**
			@brief		The Google Places API key used to get the address from the form.
			@since		2016-12-11 19:59:14
		**/
		'google_api_key'	=> 'AIzaSyAiWYbPJcpcZ95q8HLgHTbGNu7zWLBrDxY',
		/**
			@brief		The Home Value API key, used to get the home values.
			@since		2017-02-01 22:14:16
		**/
		'home_value_api_key' => '',
		/**
			@brief		The text shown to the user in the lead form when the address was found.
			@since		2017-08-10 07:28:56
		**/
		'lead_form_address_found_text' => 'No text',
		/**
			@brief		The text shown to the user in the lead form when the address Wasn't found.
			@since		2017-08-10 07:28:56
		**/
		'lead_form_address_not_found_text' => 'No text',
		/**
			@brief		On the lead collection form, the placeholder text for the e-mail input.
			@since		2017-08-08 05:33:32
		**/
		'lead_form_email_placeholder' => 'No text',
		/**
			@brief		On the lead collection form, the placeholder text for the first name input.
			@since		2017-08-08 05:33:32
		**/
		'lead_form_first_name_placeholder' => 'No text',
		/**
			@brief		On the lead collection form, is the first name input required?
			@since		2017-08-08 02:35:19
		**/
		'lead_form_first_name_required' => 'on',
		/**
			@brief		On the lead collection form, is the first name input visible?
			@since		2017-08-08 02:35:19
		**/
		'lead_form_first_name_visible' => 'on',
		/**
			@brief		On the lead collection form, the placeholder text for the last name input.
			@since		2017-08-08 05:33:32
		**/
		'lead_form_last_name_placeholder' => 'No text',
		/**
			@brief		On the lead collection form, is the last name input required?
			@since		2017-08-08 02:35:19
		**/
		'lead_form_last_name_required' => 'on',
		/**
			@brief		On the lead collection form, is the last name input visible?
			@since		2017-08-08 02:35:19
		**/
		'lead_form_last_name_visible' => 'on',
		/**
			@brief		On the lead collection form, the placeholder text for the phone input.
			@since		2017-08-08 05:33:32
		**/
		'lead_form_phone_placeholder' => 'No text',
		/**
			@brief		On the lead collection form, is the phone input required?
			@since		2017-08-08 02:35:19
		**/
		'lead_form_phone_required' => 'on',
		/**
			@brief		On the lead collection form, is the phone input visible?
			@since		2017-08-08 02:35:19
		**/
		'lead_form_phone_visible' => 'on',
		/**
			@brief		On the lad collection form, the text for the submit button.
			@since		2017-08-08 06:09:17
		**/
		'lead_form_submit_button_text' => 'No text',
		/**
			@brief		Which blog to send all of the leads.
			@since		2016-12-09 20:44:58
		**/
		'lead_pool_blog' => 0,
		/**
			@brief		Enqueue our css file?
			@since		2017-01-09 21:45:28
		**/
		'load_css' => true,
		/**
			@brief		Webhook URLs to which to send new leads.
			@since		2017-10-11 20:41:27
		**/
		'new_lead_webhooks' => '',
		/**
			@brief		The text is shown to the users wihtout a valid address after lead input.
			@since		2017-08-06 05:04:36
		**/
		'no_address_page_text' => 'No text',
		/**
			@brief		When the results left are filled up.
			@details	If less than the monthly free amount of search results, it will be topped up to that amount every 30 days.
						This variable stores when the results are topped up.
			@since		2017-02-15 13:35:21
		**/
		'refill_date' => 0,
		/**
			@brief		How many search results the API key is still valid for.
			@since		2017-02-05 20:29:22
		**/
		'results_left' => 0,
		/**
			@brief		The page for showing the home value.
			@since		2017-08-06 04:55:51
		**/
		'show_value_page' => 'No text',
	];

	/**
		@brief		Custom activation.
		@since		2017-08-06 04:59:40
	**/
	public function activate_plugin()
	{
		$options_to_delete = [
			'address_user_info_text',
		];

		foreach( $options_to_delete as $key )
		{
			$this->delete_local_option( $key );
			$this->delete_site_option( $key );
		}

	}

	/**
		@brief		Allow subclasses to do things after the admin menu tabs.
		@since		2017-03-10 00:10:42
	**/
	public function admin_menu_tabs_action()
	{
	}

	/**
		@brief		eightb_home_value_prepare_settings_tabs
		@since		2017-08-02 21:27:09
	**/
	public function eightb_home_value_prepare_settings_tabs( $action )
	{
		$tabs = $action->tabs;

		$tabs->tab( 'general' )
			->callback_this( 'admin_general_settings' )
			->heading_( '%s general settings', $this->full_plugin_name() )
			->name_( 'General' )
			->sort_order( 10 );

		$tabs->tab( 'forms' )
			->callback_this( 'admin_forms_settings' )
			->heading_( '%s form settings', $this->full_plugin_name() )
			->name_( 'Forms' )
			->sort_order( 20 );

		$tabs->tab( 'emails' )
			->callback_this( 'admin_emails_settings' )
			->heading_( '%s e-mail settings', $this->full_plugin_name() )
			->name_( 'E-mails' )
			->sort_order( 30 );

		$tabs->default_tab( 'general' );
	}

	/**
		@brief		Return the proper plugin name.
		@since		2017-03-05 14:25:05
	**/
	public function full_plugin_name()
	{
		return '8b Home Value';
	}

	/**
		@brief		Return an instance of the API.
		@since		2017-03-07 22:35:13
	**/
	public function get_api()
	{
		if ( isset( $this->__home_value_api ) )
			return $this->__home_value_api;

		$this->__home_value_api = new classes\API();
		$this->__home_value_api->plugin = $this;
		$this->__home_value_api->key = $this->get_home_value_api_key();
		return $this->__home_value_api;
	}

	/**
		@brief		Return the _GET key used when the server sends a new API request.
		@details	The default is the plugin prefix.
		@since		2017-03-07 22:33:53
	**/
	public function get_api_init_key()
	{
		return 'hv_api_key';
	}

	/**
		@brief		Convenience function to return the HV api key.
		@details	Used by the premium plugin to self-activate.
		@since		2017-02-09 22:29:53
	**/
	public function get_home_value_api_key()
	{
		return $this->get_site_option( 'home_value_api_key' );
	}

	/**
		@brief		Return an instance of the Lead class.
		@since		2017-03-09 22:01:19
	**/
	public function get_lead()
	{
		return new classes\Lead();
	}

	/**
		@brief		Return the name of the JS variable where we keep our localization data.
		@since		2017-03-07 22:19:51
	**/
	public function get_localize_script_variable()
	{
		return 'eightb_home_value_data';
	}

	/**
		@brief		Return the prefix used for shortcodes.
		@since		2017-03-05 11:33:05
	**/
	public function get_plugin_prefix()
	{
		return '8b_home_value';
	}

	/**
		@brief		Init plugin.
		@since		2017-08-02 21:25:46
	**/
	public function init_plugin()
	{
		$this->add_action( 'eightb_home_value_prepare_settings_tabs' );
	}

	/**
		@brief		General function to replace any HV API key info shortcodes in a text.
		@since		2017-02-09 21:43:36
	**/
	public function replace_api_text( $text, $options = [] )
	{
		$form = $this->form();
		$options = array_merge( [
			'form' => true,		// Add the form tags?
		], $options );

		$home_value_api_key = $this->get_site_option( 'home_value_api_key' );

		$renewal_url = \eightb\home_value\classes\Premium::$server_url . '/renew';
		$renewal_url = add_query_arg( 'key', $home_value_api_key, $renewal_url );

		$refresh_button = $form->primary_button( 'refresh_valuations' )
			->value( 'Refresh valuations status' );

		// While we're here, we might as well check the form for action.
		if ( $form->is_posting() )
		{
			$form->post();
			if ( $refresh_button->pressed() )
			{
				try
				{
					$this->get_api()->status();
				}
				catch ( \Exception $e )
				{
				}
				// Only process this once per request.
				$_POST = [];
			}
		}


		foreach( [
			'home_value_api_key' => $home_value_api_key,
			'refill_date' => date( 'M j', intval($this->get_site_option( 'refill_date', time() )) ),
			'refresh_button' => $refresh_button->display_input(),
			'results_left' => intval( $this->get_site_option( 'results_left' ) ),
			'renewal_link' => $renewal_url,
		] as $key => $value )
			$text = str_replace( '[' . $key . ']', $value, $text );

		$text = sprintf( "%s%s%s",
			( $options[ 'form' ] ? $form->open_tag() : '' ),
			$text,
			( $options[ 'form' ] ? $form->close_tag() : '' )
		);

		return $text;
	}

	/**
		@brief		Return the complete namespace and name of a subclass.
		@details	This is used by the plugin1 trait to create local classes from within the trait.
		@since		2017-03-10 00:14:12
	**/
	public function subclass( $extra )
	{
		return __NAMESPACE__ . $extra;
	}

}
