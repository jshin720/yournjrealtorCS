<?php

namespace eightb\home_value;

use \Exception;

/**
	@brief		Handle all shortcodes.
	@since		2016-12-09 21:33:57
**/
trait shortcodes_trait
{
	/**
		@brief		Show the base form and the necessary javascript.
		@since		2016-12-11 13:21:19
	**/
	public function shortcode_8b_home_value()
	{
		
		
		if ( ! session_id() )
			return 'Unable to start the PHP session on this server.';

		$replacements = [
			'content' => '',		// Content should be the first text replaced.
			'google_api_key' => 'AIzaSyAiWYbPJcpcZ95q8HLgHTbGNu7zWLBrDxY',		// $this->get_site_option( 'google_api_key' )
			'js' => $this->paths( 'url' ) . '/js/js.js',
		];
		$template = '';

		$tempform = $this->form();
		$tempform->prefix( '8b_home_value' );

		// Now decide which content template to load.
		if (
			empty( $_POST )
			OR ( ! isset( $_POST[ '8b_home_value' ] ) )
			OR ( ! isset( $_POST[ '8b_home_value' ][ 'nonce' ] ) )
		)
		{
			//unset( $_SESSION[ 'zillow_8b_hv_found_address' ] );
			//unset( $_SESSION[ '8b_hv_found_address' ] );
			// Deugging the address? Try this one: 1600 Tremont Street;02120
			$template = $this->get_text_file( 'address_search_form' );
			$replacements[ 'address_search_form_address_input_placeholder' ] = $this->get_local_or_site_option( 'address_search_form_address_input_placeholder' );
			$replacements[ 'address_search_form_submit_button_text' ] = $this->get_local_or_site_option( 'address_search_form_submit_button_text' );
		}
		else
		{
			$post = $_POST[ '8b_home_value' ];

			// Check the nonce.
			$nonce_value = $post[ 'nonce' ];
			if ( ! wp_verify_nonce( $nonce_value, $this->get_nonce_key() ) )
				wp_die( 'Security check failed.' );

			$form = $this->form();
			$form->prefix( '8b_home_value' );

			// First name's first.
			if ( $this->get_local_or_site_option( 'lead_form_first_name_visible' ) == 'on' )
			{
				$ph = $this->get_local_or_site_option( 'lead_form_first_name_placeholder' );
				$form->text( 'lead_first_name' )
					->label( $ph )
					->placeholder( $ph )
					->required( $this->get_local_or_site_option( 'lead_form_first_name_required' )  == 'on' );
			}

			// Then last name.
			if ( $this->get_local_or_site_option( 'lead_form_last_name_visible' ) == 'on' )
			{
				$ph = $this->get_local_or_site_option( 'lead_form_last_name_placeholder' );
				$form->text( 'lead_last_name' )
					->label( $ph )
					->placeholder( $ph )
					->required( $this->get_local_or_site_option( 'lead_form_last_name_required' )  == 'on' );
			}

			// Then phone.
			if ( $this->get_local_or_site_option( 'lead_form_phone_visible' ) == 'on' )
			{
				$ph = $this->get_local_or_site_option( 'lead_form_phone_placeholder' );
				$form->text( 'lead_phone' )
					->label( $ph )
					->placeholder( $ph )
					->required( $this->get_local_or_site_option( 'lead_form_phone_required' )  == 'on' );
			}
			// E-mail is always required.
			$ph = $this->get_local_or_site_option( 'lead_form_email_placeholder' );
			$form->text( 'lead_email' )
				->label( $ph )
				->placeholder( $ph )
				->required();
			
			
			// And now the submit button.
			$form->primary_button( 'submit' )
				->value( $this->get_local_or_site_option( 'lead_form_submit_button_text' ) );

			if ( ! isset( $post[ 'lead_email' ] ) )
			{
				// Did we find the address?
				$address_found = false;
				$address_found_type = "nonzillow";
				// Look up the address in the API.
				$address = sanitize_text_field( $post[ 'found_address' ] );
				$_SESSION[ '8b_searched_address' ] = sanitize_text_field( $post[ 'address' ] );
				$address = explode( ';', $address );
				try
				{
					// No proper address found.
					if ( count( $address ) < 2 )
					{
						$address []= '';
					}
					$json = $this->get_api()->search( $address[0], $address[1] );
					
					$decoded = json_decode( $json->result );
					if( isset( $json->called_api ) && $json->called_api == 'zillow' ){
						$address_found_type = 'zillow';
						$this->update_site_option( 'refill_date', $json->refill_date );
						$this->update_site_option( 'results_left', $json->results_left );
						$_SESSION[ '8b_hv_found_address' ] = $decoded;
						$result = $decoded;
						$_SESSION[ 'zillow_8b_hv_found_address' ] = "zillow_data";
					}
					else
					{
						if ( ! is_object( $decoded ) )
							throw new Exception( 'Invalid JSON data.' );
	
						if ( $decoded->success != true )
							throw new Exception( 'Not success.' );
	
						// Save our new stats.
						$this->update_site_option( 'refill_date', $json->refill_date );
						$this->update_site_option( 'results_left', $json->results_left );
						$result = $decoded->result;
						$_SESSION[ '8b_hv_found_address' ] = $result;
						
						$address_found = true;
					}
				}
				catch( Exception $e )
				{
				}
				
				if ( $address_found )
				{
					$g_key = 'AIzaSyAiWYbPJcpcZ95q8HLgHTbGNu7zWLBrDxY'; // $this->get_local_option( 'google_api_key' );
					if ( $g_key == '' )
					{
						if ( ! $this->get_site_option( 'force_own_google_api_key' ) )
							$g_key = $this->get_site_option( 'google_api_key' );
					}
					$replacements[ 'address_street_view' ] = sprintf( 'https://maps.googleapis.com/maps/api/streetview?location=%s,%s&size=600x300&key=%s',
						$result->address->deliveryLine,
						$result->address->zip,
						$g_key
					);
					$data = $_SESSION[ '8b_hv_found_address' ];
					$replacements[ 'data_address' ] = sprintf( '%s, %s', $data->address->deliveryLine, $data->address->city );
					$replacements[ 'lead_form_address_found_text' ] = $this->get_local_or_site_option( 'lead_form_address_found_text' );

					$template = $this->get_text_file( 'lead_info_form_with_address' );
				}
				else if( $address_found_type == 'zillow' )
				{
					$_SESSION[ 'zillow_8b_hv_found_address' ] = "zillow_data";
					$address_found = true;
					$data = $_SESSION[ '8b_hv_found_address' ];
					$g_key = 'AIzaSyAiWYbPJcpcZ95q8HLgHTbGNu7zWLBrDxY'; // $this->get_local_option( 'google_api_key' );
					if ( $g_key == '' )
					{
						if ( ! $this->get_site_option( 'force_own_google_api_key' ) )
							$g_key = $this->get_site_option( 'google_api_key' );
					}
					$replacements[ 'address_street_view' ] = sprintf( 'https://maps.googleapis.com/maps/api/streetview?location=%s,%s&size=600x300&key=%s',
						$data->address->street,
						$data->address->zipcode,
						$g_key
					);
					
					
					$replacements[ 'data_address' ] = sprintf( '%s, %s', $data->address->street, $data->address->city );
					$replacements[ 'lead_form_address_found_text' ] = $this->get_local_or_site_option( 'lead_form_address_found_text' );

					$template = $this->get_text_file( 'lead_info_form_with_address' );
				}
				else {
					/*Custom Code Start 2019/03/11*/
					/* Also I have added string in lead_info_form_without_address.txt file for show MAP*/
					/*this code added for show map on page when address is not found with streetview Start Here*/
					$replacements[ 'address_street_view' ] = $_SESSION[ '8b_searched_address' ];
					/*this code addedd for show map on page when address is not found with streetview End Here*/
					/*Custom Code End 2019/03/11*/
					$_SESSION[ '8b_hv_found_address' ] = false;
					$replacements[ 'lead_form_address_not_found_text' ] = $this->get_local_or_site_option( 'lead_form_address_not_found_text' );
					$template = $this->get_text_file( 'lead_info_form_without_address' );
				}
				$replacements[ 'lead_form' ] = $form . '';
			}
			else
			{
				$lead = new classes\Lead();			

				// Save everything in the post that starts with "lead".
				foreach( $post as $key => $value )
				{
					if ( strpos( $key, 'lead_' ) === false )
						continue;
					$value = sanitize_text_field( $value );
					$value = stripslashes( $value );
					$lead->meta->$key = $value;
				}

				if ( $_SESSION[ '8b_hv_found_address' ] )
				{
					if( isset( $_SESSION[ 'zillow_8b_hv_found_address' ] ) ){
						// Found the address
						$post_content = $this->get_text_file( 'zillow_lead_content_address' );
					}
					else
					{
						// Found the address
						$post_content = $this->get_text_file( 'lead_content_address' );
					}
					$data = $_SESSION[ '8b_hv_found_address' ];
					$replacements = array_merge( $replacements, $this->data_to_replacements( $data ) );
				}
				else
				{
					// Did not find the address.
					$post_content = $this->get_text_file( 'lead_content_no_address' );
					// false = return n/a in everything.
					$replacements = array_merge( $replacements, $this->data_to_replacements( false ) );
				}
				$replacements = array_merge( $replacements, $lead->get_shortcodes() );
				$replacements[ 'searched_address' ] = $_SESSION[ '8b_searched_address' ];
				$post_content = $this->replace_shortcodes( $post_content, $replacements );
				
				$lead->set( 'post_content', $post_content );

				$lead->save();
				/*Custom Code Start 2019/03/11*/
				/* This script block add Address data in lead for send address data to WebHooks*/
				$lead->meta->lead_address = $_SESSION[ '8b_searched_address' ];
				/*Custom Code End 2019/03/11*/
				$this->send_lead_to_webhooks( $lead );

				// We can only set this after the lead exists.
				if ( $_SESSION[ '8b_hv_found_address' ] )
					$lead->address_found();
				else
					$lead->address_found( false );

				$this->debug( 'Maybe broadcasting lead.' );
				$lead->broadcast();
				$this->debug( 'Sending lead e-mail.' );
				$this->send_lead( $lead, $replacements );

				if ( ! $_SESSION[ '8b_hv_found_address' ] )
				{
					
					$this->debug( 'Did not find address. Contact you later.' );
					// Did not find the address.
					$replacements[ 'no_address_page_text' ] = $this->get_local_or_site_option( 'no_address_page_text' );
					$template = $this->get_text_file( 'no_address_page' );
				}
				else
				{
					if( isset( $_SESSION[ 'zillow_8b_hv_found_address' ] ) )
					{
						$this->debug( 'Zillow Address found. Show the value.' );
						$template = $this->get_text_file( 'zillow_new_show_value_page' );
						$data = $_SESSION[ '8b_hv_found_address' ];
						$data_replacements = $this->data_to_replacements( $data );
						$replacements = array_merge( $replacements, $data_replacements );
					}
					else
					{
						$this->debug( 'Address found. Show the value.' );
						// Found the address.
						
						$template = $this->get_text_file( 'new_show_value_page' );
						$data = $_SESSION[ '8b_hv_found_address' ];
						$data_replacements = $this->data_to_replacements( $data );
						$replacements = array_merge( $replacements, $data_replacements );
					}
				}
				unset( $_SESSION[ '8b_hv_found_address' ] );
				unset( $_SESSION[ 'zillow_8b_hv_found_address' ] );
			}
		}
		//
		

		// Get the current URL.
		$replacements[ 'url' ] = remove_query_arg( 'asmksb' );
		$template = $this->replace_shortcodes( $template, $replacements );
		return $template;
	}

	/**
		@brief		Convert this Home Value data to a replacements array.
		@since		2017-02-24 23:15:37
	**/
	public function data_to_replacements( $data )
	{
		$replacements = [];

		if ( ! $data )
		{
			foreach( [ 'data_address_city',
				'data_address_state',
				'data_address_street',
				'data_address_zipcode',
				'data_baths',
				'data_beds',
				'data_lotSizeSqFt',
				'data_size',
				'data_address',
				'data_valuation_low',
				'data_valuation_medium',
				'data_valuation_high' ] as $key )
				$replacements[ $key ] = 'N/A';
		}
		else if( isset( $_SESSION[ 'zillow_8b_hv_found_address' ] ) ){
			
			$extra_home_value_in_percent = $this->get_local_or_site_option( 'home_extra_value' ); //get extra home value
			$replacements[ 'data_address_city' ] = $data->address->city;
			$replacements[ 'data_address_state' ] = $data->address->state;
			$replacements[ 'data_address_street' ] = $data->address->street;
			$replacements[ 'data_address_zipcode' ] = $data->address->zipcode;
			$replacements[ 'data_address' ] = sprintf( '%s, %s', $data->address->street, $data->address->city );
			//$value = $data->zestimate->valuationRange->low->{"0"};
			$value = $data->zestimate->valuationRange->low;
			
			$value = $value * .95;
			if(!empty($extra_home_value_in_percent))
			{
				$extra_home_value_in_percent = intval($extra_home_value_in_percent);
				$extra_home_value = (($value * $extra_home_value_in_percent) / 100);
				$value = ($value + $extra_home_value);
			}
			
			$value = $this->number_format( $value );
			
			$replacements[ 'data_valuation_low' ] = $value;
			//$value = $data->zestimate->amount->{"0"};
			$value = $data->zestimate->amount;
			$value = $value * .98;
			if(!empty($extra_home_value_in_percent))
			{
				$extra_home_value_in_percent = intval($extra_home_value_in_percent);
				$extra_home_value = (($value * $extra_home_value_in_percent) / 100);
				$value = ($value + $extra_home_value);
			}
			$value = $this->number_format( $value );
			$replacements[ 'data_valuation_medium' ] = $value;
			//$value = $data->zestimate->valuationRange->high->{"0"};
			$value = $data->zestimate->valuationRange->high;
			$value = $value * 1;
			if(!empty($extra_home_value_in_percent))
			{
				$extra_home_value_in_percent = intval($extra_home_value_in_percent);
				$extra_home_value = (($value * $extra_home_value_in_percent) / 100);
				$value = ($value + $extra_home_value);
			}
			$value = $this->number_format( $value );
			$replacements[ 'data_valuation_high' ] = $value;
			$value = 'N/A';
			$replacements[ 'data_baths' ] = $value;
			$replacements[ 'data_beds' ] = $value;
			$replacements[ 'data_size' ] = $value;
		}
		else
		{
			$extra_home_value_in_percent = $this->get_local_or_site_option( 'home_extra_value' ); //get extra home value
			$replacements[ 'data_address_city' ] = $data->address->city;
			$replacements[ 'data_address_state' ] = $data->address->state;
			$replacements[ 'data_address_street' ] = $data->address->deliveryLine;
			$replacements[ 'data_address_zipcode' ] = $data->address->zip;
			/*Custom Code Start 2019/03/09 Add comparables data in existing property data Array*/
			if ( isset( $data->comparables ) && is_array( $data->comparables ) && count( $data->comparables ) > 0 )
			{
				$value = $data->comparables;
				/**
				* add extra home value price in comparables properties * dev: KY 06/07/2019
				**/
				//start
				if(!empty($extra_home_value_in_percent) && !empty($value))
				{
					foreach($value as $key => $property)
					{
						//check extra value already added
						if(!isset($property->attributes->extra_home_value))
						{
							$comparables_value = 0;
							$extra_home_value = 0;
							
							$extra_home_value_in_percent = intval($extra_home_value_in_percent);
							$salePrice = $property->attributes->salePrice;
							
							$extra_home_value = (($salePrice * $extra_home_value_in_percent) / 100);
							$comparables_value = ($salePrice + $extra_home_value);
							//update data in data obj
							$value[$key]->attributes->salePrice = $comparables_value;
							$value[$key]->attributes->extra_home_value = true;
						}
					}
				}
			} else {
				$value = array();
			}
			$replacements[ 'comparables' ] = $value;
			/*Custom Code End 2019/03/09*/			
			if ( isset( $data->attributes->baths ) )
				$value = $data->attributes->baths;
			else
				$value = 'N/A';
			$replacements[ 'data_baths' ] = $value;
			if ( isset( $data->attributes->beds ) )
				$value = $data->attributes->beds;
			else
				$value = 'N/A';
			$replacements[ 'data_beds' ] = $value;
			$value = $data->attributes->lotSize->sqft;
			$replacements[ 'data_lotSizeSqFt' ] = $this->number_format( $value );
			$value = $data->attributes->size;
			$replacements[ 'data_size' ] = $this->number_format( $value );
			$replacements[ 'data_address' ] = sprintf( '%s, %s', $data->address->street, $data->address->city );
			$value = $data->valuations->general->low;
			$value = $value * .95;
			/**
			* add extra home value price in home value low * dev: KY 06/07/2019
			**/
			//start
			if(!empty($extra_home_value_in_percent))
			{
				$extra_home_value_in_percent = intval($extra_home_value_in_percent);
				$extra_home_value = (($value * $extra_home_value_in_percent) / 100);
				$value = ($value + $extra_home_value);
			}
			//end
			$value = $this->number_format( $value );
			$replacements[ 'data_valuation_low' ] = $value;

			$value = $data->valuations->general->EMV;
			$value = $value * .98;
			/**
			* add extra home value price in home value * dev: KY 06/07/2019
			**/
			//start
			if(!empty($extra_home_value_in_percent))
			{
				$extra_home_value_in_percent = intval($extra_home_value_in_percent);
				$extra_home_value = (($value * $extra_home_value_in_percent) / 100);
				$value = ($value + $extra_home_value);
			}
			//end
			$value = $this->number_format( $value );
			$replacements[ 'data_valuation_medium' ] = $value;
			$value = $data->valuations->general->high;
			$value = $value * 1;
			/**
			* add extra home value price in home value high * dev: KY 06/07/2019
			**/
			//start
			if(!empty($extra_home_value_in_percent))
			{
				$extra_home_value_in_percent = intval($extra_home_value_in_percent);
				$extra_home_value = (($value * $extra_home_value_in_percent) / 100);
				$value = ($value + $extra_home_value);
			}
			//end
			$value = $this->number_format( $value );
			$replacements[ 'data_valuation_high' ] = $value;
		}
		return $replacements;
	}
}
