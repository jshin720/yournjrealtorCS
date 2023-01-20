<?php

/*
Plugin Name: WP All Import - WP Pro Real Estate 7 Add-On
Plugin URI: http://www.wpallimport.com/
Description: Supporting imports into the WP Pro Real Estate 7 theme.
Version: 1.0.6
Author: Soflyy
*/


include "rapid-addon.php";

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

$wppre_addon = new RapidAddon( 'WP Pro Real Estate 7 Add-On', 'wppre_addon' );

$wppre_addon->disable_default_images();

$wppre_addon->import_images( 'wppre_slider', 'Slider Images' );

$wppre_addon->import_files( 'wppre_files', 'Files & Documents' );

$wppre_addon->add_title( '<span style="font-size:16px">Listing Info</span>' );

$wppre_addon->add_field( '_ct_listing_alt_title', 'Alternate Title', 'text', null, 'The listing alternate title here replaces street address, e.g. Downtown Penthouse' );

$wppre_addon->add_field( '_ct_price_prefix', 'Price Prefix', 'text', null, 'Enter the price prefix text here, e.g. (From, Call for price, Price on ask).' );

$wppre_addon->add_field( '_ct_price', 'Listing Price', 'text', null, 'Enter the price here, without commas or seperators. If empty no price will be shown.' );

$wppre_addon->add_field( '_ct_price_postfix', 'Price Postfix', 'text', null, 'Enter the price postfix text here, e.g. (/month, /week, /per night).' );

$wppre_addon->add_field( '_ct_sqft', 'Building Size', 'text', null, 'Square Feet or Meters' );

$wppre_addon->add_field( '_ct_lotsize', 'Lot Size', 'text' );

$wppre_addon->add_field( '_ct_mls', 'Property ID', 'text' );

$wppre_addon->add_field( '_ct_broker', 'Broker', 'text' );

$wppre_addon->add_field( '_ct_video', 'Video URL', 'text', null, 'Supports YouTube and Vimeo.' );

if ( is_plugin_active( 'co-authors-plus/co-authors-plus.php' ) ) {

	$wppre_addon->add_field(
	        'import_authors_option',
	        'Co-Authors Plus',
	        'radio', 
	        array(
	                'import_authors' => array(
	                        'Import Multiple Authors',
	                        $wppre_addon->add_field( 'wppre_authors', 'Comma Delimited List of Users', 'text' ),
	                        'Enter the user ID, email, login, or slug of the desired user. Example: 3, user@example.com, user_login'
	                ),
	                'dont_import_authors' => array(
	                        'Use Default Author Settings',
	                        'Properties will be assigned to the user running this import.'
					),
	        )
	);

}

$wppre_addon->add_field( '_ct_ownernotes', 'Owner/Agent Notes', 'text', null, 'Not visible on the front end.' );

$wppre_addon->add_title( '<span style="font-size:16px">Rental Info</span>' );

$wppre_addon->add_field( '_ct_booking_cal_shortcode', 'Booking Calendar Shortcode', 'text', null, 'Example: [booking nummonths=1]' );

$wppre_addon->add_field( '_ct_rental_title', 'Listing Title', 'text' );

$wppre_addon->add_field( '_ct_rental_guests', 'Maximum Number of Guests', 'text', null, 'Example: 2' );

$wppre_addon->add_field( '_ct_rental_min_stay', 'Minimum Stay', 'text', null, 'Example: 2 nights' );

$wppre_addon->add_field( '_ct_rental_checkin', 'Check In Time', 'text', null, 'Example: 08:00 AM' );

$wppre_addon->add_field( '_ct_rental_checkout', 'Check Out Time', 'text', null, 'Example: 03:00 PM' );

$wppre_addon->add_field( '_ct_rental_extra_people', 'Extra Charge Per Person', 'text', null, 'Without currency symbol, commas, or separators. Example: 15' );

$wppre_addon->add_field( '_ct_rental_cleaning', 'Cleaning Fee', 'text', null, 'Without currency symbol, commas, or separators. Example: 30' );

$wppre_addon->add_field( '_ct_rental_cancellation', 'Cancellation Fee', 'text', null, 'Without currency symbol, commas, or separators. Example: 60' );

$wppre_addon->add_field( '_ct_rental_deposit', 'Security Deposit', 'text', null, 'Without currency symbol, commas, or separators. Example: 120' );

$wppre_addon->add_title( '<span style="font-size:16px">Location Settings</span>' );

$wppre_addon->add_field(
	'location_settings',
	'Location Search',
	'radio', 
	array(
		'search_by_address' => array(
			'Search by Address',
			$wppre_addon->add_options( 
				$wppre_addon->add_field(
					'wppre_property_address',
					'Property Address',
					'text'
				),
				'Google Geocode API Settings', 
				array(
					$wppre_addon->add_field(
						'address_geocode',
						'Request Method',
						'radio',
						array(
							'address_no_key' => array(
								'No API Key',
								'Limited number of requests.'
							),
							'address_google_developers' => array(
								'Google Developers API Key - <a href="https://developers.google.com/maps/documentation/geocoding/#api_key">Get free API key</a>',
								$wppre_addon->add_field(
									'address_google_developers_api_key', 
									'API Key', 
									'text'
								),
								'Up to 2500 requests per day and 5 requests per second.'
							),
							'address_google_for_work' => array(
								'Google for Work Client ID & Digital Signature - <a href="https://developers.google.com/maps/documentation/business">Sign up for Google for Work</a>',
								$wppre_addon->add_field(
									'address_google_for_work_client_id', 
									'Google for Work Client ID', 
									'text'
								), 
								$wppre_addon->add_field(
									'address_google_for_work_digital_signature', 
									'Google for Work Digital Signature', 
									'text'
								),
								'Up to 100,000 requests per day and 10 requests per second'
							)
						) // end Request Method options array
					) // end Request Method nested radio field 
				) // end Google Geocode API Settings fields
			) // end Google Gecode API Settings options panel
		), // end Search by Address radio field
		'search_by_coordinates' => array(
			'Search by Coordinates',
			$wppre_addon->add_field(
				'property_latitude', 
				'Latitude', 
				'text', 
				null, 
				'Example: 34.0194543'
			),
			$wppre_addon->add_options( 
				$wppre_addon->add_field(
					'property_longitude', 
					'Longitude', 
					'text', 
					null, 
					'Example: -118.4911912'
				), 
				'Google Geocode API Settings', 
				array(
					$wppre_addon->add_field(
						'coord_geocode',
						'Request Method',
						'radio',
						array(
							'coord_no_key' => array(
								'No API Key',
								'Limited number of requests.'
							),
							'coord_google_developers' => array(
								'Google Developers API Key - <a href="https://developers.google.com/maps/documentation/geocoding/#api_key">Get free API key</a>',
								$wppre_addon->add_field(
									'coord_google_developers_api_key', 
									'API Key', 
									'text'
								),
								'Up to 2500 requests per day and 5 requests per second.'
							),
							'coord_google_for_work' => array(
								'Google for Work Client ID & Digital Signature - <a href="https://developers.google.com/maps/documentation/business">Sign up for Google for Work</a>',
								$wppre_addon->add_field(
									'coord_google_for_work_client_id', 
									'Google for Work Client ID', 
									'text'
								), 
								$wppre_addon->add_field(
									'coord_google_for_work_digital_signature', 
									'Google for Work Digital Signature', 
									'text'
								),
								'Up to 100,000 requests per day and 10 requests per second'
							)
						) // end Geocode API options array
					) // end Geocode nested radio field 
				) // end Geocode settings
			) // end coordinates Option panel
		) // end Search by Coordinates radio field
	) // end Property Location radio field
);

$wppre_addon->add_options( null, 'Advanced Location Settings', array(
	$wppre_addon->add_field(
	        'overwrite_post_title',
	        'Overwrite Imported Post Title',
	        'radio', 
	        array(
		        	'yes' => array(
						'Use Address Returned By Google Maps as Post Title',
						$wppre_addon->add_field(
						        'address_format',
						        'Address Format',
						        'radio', 
						        array(
						                'address_city_state' => '123 Street Rd, City, State',
						                'address_city_state_country' => '123 Street Rd, City, State, Country',
					        	),
					        	'Enter in a dummy value for the post title.'
						        )),
	                'no' => 'Do Not Overwrite Imported Post Title'
	        )
	),
	$wppre_addon->add_field(
		'overwrite_taxonomies',
		'Overwrite Location Taxonomies',
		'radio', 
		array(
			'yes' => 'Use Location Info Returned By Google Maps',
		    'no' => 'Do Not Overwrite Locations Taxonomies',
		),
		'If enabled, WP All Import will use data from Google Maps and overwrite the city, state, zipcode, and country taxonomies. If you choose to manually import the location taxonomies you can do so using the Taxonomy section below.'
	),
));

$wppre_addon->set_import_function( 'wppre_addon_import' );

$wppre_addon->admin_notice();

$wppre_addon->run( array(
		"themes" => array( "WP Pro Real Estate 7" ),
		"post_types" => array( "listings" )
) );



function wppre_addon_import( $post_id, $data, $import_options, $article ) {
    
    global $wppre_addon;

    // all fields except for slider and image fields
    $fields = array(
		'_ct_listing_alt_title',
		'_ct_price_prefix',
		'_ct_price',
		'_ct_price_postfix',
		'_ct_sqft',
		'_ct_lotsize',
		'_ct_mls',
		'_ct_broker',
		'_ct_video',
		'_ct_ownernotes',
		'_ct_rental_title',
		'_ct_rental_guests',
		'_ct_rental_min_stay',
		'_ct_rental_extra_people',
		'_ct_rental_cleaning',
		'_ct_rental_cancellation',
		'_ct_rental_deposit',
		'_ct_booking_cal_shortcode',
		'_ct_rental_checkout',
		'_ct_rental_checkin'
    );
    
    // update everything in fields arrays
    foreach ( $fields as $field ) {

        if ( empty( $article['ID'] ) or $wppre_addon->can_update_meta( $field, $import_options ) ) {

        	if ( strlen( $data[$field] ) == 0 ) {

        		delete_post_meta( $post_id, $field );

        	} else {

                update_post_meta( $post_id, $field, $data[$field] );

            }
        }
    }

    // clear image fields to override import settings
    // if you don't do this the old images will stay in the
    // galleries on subsequent imports
    $fields = array(
    	'_ct_slider',
    	'_ct_files'
    );

    if ( empty( $article['ID'] ) or $wppre_addon->can_update_image( $import_options ) ) {

    	foreach ($fields as $field) {

	    	delete_post_meta($post_id, $field);

	    }

    }

	// assign property to user using Co-Authors Plus functions
    $field = 'wppre_authors';

    if ( !empty( $data[$field] ) && $data['import_authors_option'] == 'import_authors' ) {

		if ( is_plugin_active( 'co-authors-plus/co-authors-plus.php' ) ) {

			global $coauthors_plus;

			if ( !method_exists( $coauthors_plus, 'add_coauthors' ) ) {

			    $wppre_addon->log( '- <b>WARNING:</b> Cannot use Co-Author Plus, please contact WP All Import support.');

			} else {

				$wppre_addon->log( 'Assigning Property to Users' );
					
				$users = explode( ',', $data[$field] );

				$coauthors = array();

				if ( !empty( $users ) ) {

					foreach ( $users as $user_data ) {
					
						$user_data = trim( $user_data );

						$user = get_user_by( 'id', $user_data );

						if ( $user === false ) {

							$user = get_user_by( 'slug', $user_data );

						}

						if ( $user === false ) {

							$user = get_user_by( 'email', $user_data );

						}

						if ( $user === false ) {

							$user = get_user_by( 'login', $user_data );

						}

						if ( $user != false ) {

						    $wppre_addon->log( '- Adding ' . $user->data->user_nicename . ' as a co-author.' );

				            $coauthors[] = $user->data->user_login;

						} else {

						    $wppre_addon->log( '- <b>WARNING:</b> No user found searching for "'. $user_data . '".');

						}
					}

				}

				if ( !empty( $coauthors ) ) {

					$i = 0;

					foreach ( $coauthors as $user_login ) {

						if ( $i++ == 0 ) {

							$coauthors_plus->add_coauthors( $post_id, array( $user_login ), false );

						} else {

							$coauthors_plus->add_coauthors( $post_id, array( $user_login ), true );

						}

					}

				} else {

					$users = get_super_admins();

					$user = get_user_by( 'login', $users[0] );

				    $wppre_addon->log( '- Adding ' . $user->data->user_nicename . ' as sole author.' );

					$coauthors_plus->add_coauthors( $post_id, array( $user_login ), false );

				}

			}

		} else { 

		    $wppre_addon->log( '<b>WARNING:</b> Cannot add authors without Co-Authors Plus plugin activated.');
		}
	}

    // update property location
    // this is also gonna suck. the address is stored as the post title
    // and the city/state info put in as a taxonomy
    $address = $data['wppre_property_address'];

    $lat  = $data['property_latitude'];

	$long = $data['property_longitude'];
	
	$geocoding_failed = false;
    
    $wppre_addon->log( '<b>LOCATION:</b>' );

    //  build search query
    if ( $data['location_settings'] == 'search_by_address' ) {

    	$search = ( !empty( $address ) ? 'address=' . rawurlencode( $address ) : null );

    } else {

    	$search = ( !empty( $lat ) && !empty( $long ) ? 'latlng=' . rawurlencode( $lat . ',' . $long ) : null );

    }

    // build api key
    if ( $data['location_settings'] == 'search_by_address' ) {
    
    	if ( $data['address_geocode'] == 'address_google_developers' && !empty( $data['address_google_developers_api_key'] ) ) {
        
	        $api_key = '&key=' . $data['address_google_developers_api_key'];
	    
	    } elseif ( $data['address_geocode'] == 'address_google_for_work' && !empty( $data['address_google_for_work_client_id'] ) && !empty( $data['address_google_for_work_signature'] ) ) {
	        
	        $api_key = '&client=' . $data['address_google_for_work_client_id'] . '&signature=' . $data['address_google_for_work_signature'];

	    }

    } else {

    	if ( $data['coord_geocode'] == 'coord_google_developers' && !empty( $data['coord_google_developers_api_key'] ) ) {
        
	        $api_key = '&key=' . $data['coord_google_developers_api_key'];
	    
	    } elseif ( $data['coord_geocode'] == 'coord_google_for_work' && !empty( $data['coord_google_for_work_client_id'] ) && !empty( $data['coord_google_for_work_signature'] ) ) {
	        
	        $api_key = '&client=' . $data['coord_google_for_work_client_id'] . '&signature=' . $data['coord_google_for_work_signature'];

	    }

    }

    if ( !empty ( $search ) ) {
        
        // build $request_url for api call
        $request_url = 'https://maps.googleapis.com/maps/api/geocode/json?' . $search . $api_key;
        $curl        = curl_init();

        curl_setopt( $curl, CURLOPT_URL, $request_url );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );

        $wppre_addon->log( '- Getting location data from Geocoding API: '.$request_url );

        $json = curl_exec( $curl );
        curl_close( $curl );
        
        // parse api response
        if ( !empty( $json ) ) {

			$details = json_decode( $json, true );
			
			if ( array_key_exists( 'status', $details ) ) {
				if ( $details['status'] == 'INVALID_REQUEST' || $details['status'] == 'ZERO_RESULTS' || $details['status'] == 'REQUEST_DENIED' ) {
					$geocoding_failed = true;
					goto invalidrequest;
				}
			}

            $address_data = array();

            if ( !empty( $details['results'][0]['address_components'] ) ) {

				foreach ( $details['results'][0]['address_components'] as $type ) {

					// parse Google Maps output into an array we can use
					$address_data[ $type['types'][0] ] = $type['long_name'];

				}

			}

            $lat  = $details['results'][0]['geometry']['location']['lat'];

            $long = $details['results'][0]['geometry']['location']['lng'];

        	$address = $address_data['street_number'] . ' ' . $address_data['route'];

        	$city = $address_data['locality'];

        	$country = $address_data['country'];

        	$zip = $address_data['postal_code'];

        	$state = $address_data['administrative_area_level_1'];

        	$county = $address_data['administrative_area_level_2'];

        	if ( empty( $zip ) ) {

			    $wppre_addon->log( '<b>WARNING:</b> Google Maps has not returned a Postal Code for this property location.' );

        	}

        	if ( empty( $country ) ) {

			    $wppre_addon->log( '<b>WARNING:</b> Google Maps has not returned a Country for this property location.' );

        	}

        	if ( empty( $city ) ) {

			    $wppre_addon->log( '<b>WARNING:</b> Google Maps has not returned a City for this property location.' );

        	}

        	if ( empty( $address_data[street_number] ) ) {

			    $wppre_addon->log( '<b>WARNING:</b> Google Maps has not returned a Street Number for this property location.' );

        	}

        	if ( empty( $address_data[route] ) ) {

			    $wppre_addon->log( '<b>WARNING:</b> Google Maps has not returned a Street Name for this property location.' );

        	}

        }
        
    }
    
    // update location fields
    if ( !empty( $search ) ) {

		if ( $data['overwrite_post_title'] == 'yes' ) {

			if ( $data['address_format'] == 'address_city_state' ) {

				$post_title = $address . ', ' . $city . ', ' . $state;
				
			} else {

				$post_title = $address . ', ' . $city . ', ' . $state . ', ' . $country;
				
			}

		    $wppre_addon->log( '- Updating location data' );

			  $post_update = array(
			      'ID'           => $post_id,
			      'post_title'   => $post_title
			  );

			// update the post title the database
			wp_update_post( $post_update );

		}

		if ( $data['overwrite_taxonomies'] == 'yes' ) {

			$fields = array(
				'pmxi_city' => $city,
				'pmxi_state' => $state,
				'pmxi_country' => $country,
				'pmxi_zip' => $zip
			);

			foreach ($fields as $key => $value) {

		        update_post_meta( $post_id, $key, $value );

			}
		}
	}

	invalidrequest:

	if ( $geocoding_failed ) {
		delete_post_meta( $post_id, 'geolocated' );
		$wppre_addon->log( "WARNING Geocoding failed with status: " . $details['status'] );
		if ( array_key_exists( 'error_message', $details ) ) {
			$wppre_addon->log( "WARNING Geocoding error message: " . $details['error_message'] );
		}
	}
}


function wppre_slider( $post_id, $attachment_id, $image_filepath, $import_options ) {

	$current_images = get_post_meta( $post_id, '_ct_slider', true );

	$images_array = array();

	if ( !empty( $current_images ) ) {

		foreach ( $current_images as $id => $url ) {
			
			$images_array[$id] = $url;

		}

	}

	$image_url = wp_get_attachment_url( $attachment_id );

	$images_array[$attachment_id] = $image_url;

    update_post_meta( $post_id, '_ct_slider', $images_array );
}

function wppre_files( $post_id, $attachment_id, $image_filepath, $import_options ) {
    
	$current_images = get_post_meta( $post_id, '_ct_files', true );

	$images_array = array();

	if ( !empty( $current_images ) ) {

		foreach ( $current_images as $id => $url ) {
			
			$images_array[$id] = $url;

		}

	}

	$image_url = wp_get_attachment_url( $attachment_id );

	$images_array[$attachment_id] = $image_url;

    update_post_meta( $post_id, '_ct_files', $images_array );
    
}

function wppre_update_location_taxonomy( $post_id ) {

	if ( get_post_type( $post_id ) == 'listings' ) {
	
		// DRY? what's DRY?
		$city = get_post_meta( $post_id, 'pmxi_city', true );

		delete_post_meta( $post_id, 'pmxi_city' );

		$state = get_post_meta( $post_id, 'pmxi_state', true );

		delete_post_meta( $post_id, 'pmxi_state' );

		$country = get_post_meta( $post_id, 'pmxi_country', true );

		delete_post_meta( $post_id, 'pmxi_country' );

		$zip = get_post_meta( $post_id, 'pmxi_zip', true );

		delete_post_meta( $post_id, 'pmxi_zip' );

		// ok fine
		$taxonomies = array(
			'city' => $city,
			'state' => $state,
			'country' => $country,
			'zipcode' => $zip
		);

		if ( !empty( $city ) ) {

			foreach ( $taxonomies as $taxonomy_name => $term_name ) {

				$term = get_term_by( 'name', $term_name, $taxonomy_name );

				if ( $term == false ) {

					wp_insert_term( $term_name, $taxonomy_name );

					$term = get_term_by( 'name', $term_name, $taxonomy_name );

				}

				wp_set_object_terms( $post_id, $term->term_id, $taxonomy_name, true );

			}
		}
	}
}

add_action('pmxi_saved_post', 'wppre_update_location_taxonomy', 1, 1);