<?php

namespace eightb\home_value;

use \Exception;

/**
	@brief		All admin menu functions.
	@since		2016-12-09 20:29:44
 **/
trait admin_menu_trait {
	/**
		@brief		admin_emails_settings
		@since		2017-08-02 21:43:12
	 **/
	public function admin_emails_settings() {
		$keys_to_save = [
			'email_new_lead_recipients',
			'email_new_lead_sender_email',
			'email_new_lead_sender_name',
			'email_new_lead_subject',
			'email_new_lead_text',
		];

		$get = 'get_local_option';
		$set = 'update_local_option';

		if (is_network_admin()) {
			$get = 'get_site_option';
			$set = 'update_site_option';
		}

		$form = $this->form();
		$r = '';

		$fs = $form->fieldset('fs_new_lead');
		$fs->legend->label_('New lead e-mail');

		$fs->markup('m_new_lead_email_text')
			->p_('These are the settings for the e-mail sent when a new lead is created.');

		$email_new_lead_sender_email = $fs->text('email_new_lead_sender_email')
			->description_('Send the e-mail from this e-mail address. Note that this value may be restricted by your webhost.')
			->label_('Sender e-mail')
			->size(64)
			->value($this->$get('email_new_lead_sender_email'));

		$email_new_lead_sender_name = $fs->text('email_new_lead_sender_name')
			->description_('Send the e-mail with this sender name.')
			->label_('Sender name')
			->size(64)
			->value($this->$get('email_new_lead_sender_name'));

		$email_new_lead_recipients = $fs->textarea('email_new_lead_recipients')
			->description_('To which e-mail addresses shall new leads be sent? One e-mail address per line. Shortcodes allowed.')
			->label_('New lead recipients')
			->placeholder("email@address.com")
			->rows(5, 40)
			->value($this->$get('email_new_lead_recipients'));

		$email_new_lead_subject = $fs->text('email_new_lead_subject')
			->description_('Subject of the new lead e-mail. Valid shortcodes are [8b_home_value_first_name], [8b_home_value_last_name], [8b_home_value_email] and [8b_home_value_phone].')
			->label_('New lead subject')
			->size(64)
			->value($this->$get('email_new_lead_subject'));

		$email_new_lead_text = $fs->wp_editor('email_new_lead_text')
			->description_('This is the text of the e-mail for new leads that is sent to the new lead e-mail recipients. Valid shortcodes are [8b_home_value_first_name], [8b_home_value_last_name], [8b_home_value_email], [8b_home_value_phone], [8b_home_value_data_size], [8b_home_value_data_beds] and [8b_home_value_data_baths].')
			->label_('New lead e-mail')
			->rows(10)
			->set_unfiltered_value($this->$get('email_new_lead_text'));

		// Remove the "No text" and replace them with empty values.
		foreach ($form->inputs() as $input)
			if ($input->get_value() == 'No text')
				$input->value('');

		$save = $form->primary_button('save')
			->value_('Save settings');

		$this->add_reset_button($form);

		if ($form->is_posting()) {
			$form->post();
			$form->use_post_values();

			if ($save->pressed()) {
				foreach ($keys_to_save as $key)
					$this->$set($key, $$key->get_post_value());

				$r .= $this->info_message_box()
					->_('Saved!');
			}

			$r .= $this->handle_reset_button($form, $keys_to_save);
			$r .= $this->handle_copy_network_settings_button($form, $keys_to_save);

			$_POST = [];
			echo $r .= $this->admin_emails_settings();
			return;
		}

		if (is_network_admin())
			$r .= $this->p_('These are the global settings. Each blog has the possibility of specifying their own settings, but if a setting or a text is not found locally, it will be taken from the global settings.');

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		admin_forms_settings
		@since		2017-08-02 21:42:48
	 **/
	public function admin_forms_settings() {
		$keys_to_save = [
			'address_search_form_address_input_placeholder',
			'address_search_form_submit_button_text',
			'lead_form_address_found_text',
			'lead_form_address_not_found_text',
			'lead_form_email_placeholder',
			'lead_form_first_name_visible',
			'lead_form_first_name_required',
			'lead_form_first_name_placeholder',
			'lead_form_last_name_visible',
			'lead_form_last_name_required',
			'lead_form_last_name_placeholder',
			'lead_form_phone_visible',
			'lead_form_phone_required',
			'lead_form_phone_placeholder',
			'lead_form_submit_button_text',
			'no_address_page_text',
		];

		$get = 'get_local_option';
		$set = 'update_local_option';

		if (is_network_admin()) {
			$get = 'get_site_option';
			$set = 'update_site_option';
		}

		$form = $this->form();
		$r = '';

		$fs = $form->fieldset('fs_address_form');
		$fs->legend()->label('Address form');

		$address_search_form_address_input_placeholder = $fs->text('address_search_form_address_input_placeholder')
			->label_('Address Field Placeholder')
			->value($this->$get('address_search_form_address_input_placeholder'));

		$address_search_form_submit_button_text = $fs->text('address_search_form_submit_button_text')
			->label_('Submit Button Text')
			->value($this->$get('address_search_form_submit_button_text'));

		$fs = $form->fieldset('fs_lead_form_fields');
		$fs->legend()->label('Lead Forms');

		$lead_form_email_placeholder = $fs->text('lead_form_email_placeholder')
			->label_('Email Field Placeholder')
			->value($this->$get('lead_form_email_placeholder'));

		// First name
		$lead_form_first_name_visible = $fs->checkbox('lead_form_first_name_visible')
			->label_('Show First Name Field')
			->checked($this->$get('lead_form_first_name_visible') == 'on');

		$lead_form_first_name_required = $fs->checkbox('lead_form_first_name_required')
			->label_('Require First Name')
			->checked($this->$get('lead_form_first_name_required') == 'on');

		$lead_form_first_name_placeholder = $fs->text('lead_form_first_name_placeholder')
			->label_('First Name Field Placeholder')
			->value($this->$get('lead_form_first_name_placeholder'));

		// Last name
		$lead_form_last_name_visible = $fs->checkbox('lead_form_last_name_visible')
			->label_('Show Last Name')
			->checked($this->$get('lead_form_last_name_visible') == 'on');

		$lead_form_last_name_required = $fs->checkbox('lead_form_last_name_required')
			->label_('Require Last Name Field')
			->checked($this->$get('lead_form_last_name_required') == 'on');

		$lead_form_last_name_placeholder = $fs->text('lead_form_last_name_placeholder')
			->label_('Last Name Field Placeholder')
			->value($this->$get('lead_form_last_name_placeholder'));

		// Phone
		$lead_form_phone_visible = $fs->checkbox('lead_form_phone_visible')
			->label_('Show Phone Number Field')
			->checked($this->$get('lead_form_phone_visible') == 'on');

		$lead_form_phone_required = $fs->checkbox('lead_form_phone_required')
			->label_('Require Phone Number')
			->checked($this->$get('lead_form_phone_required') == 'on');

		$lead_form_phone_placeholder = $fs->text('lead_form_phone_placeholder')
			->label_('Phone Number Placeholder')
			->value($this->$get('lead_form_phone_placeholder'));

		$lead_form_submit_button_text = $fs->text('lead_form_submit_button_text')
			->label_('Submit Button Text')
			->value($this->$get('lead_form_submit_button_text'));

		$fs = $form->fieldset('fs_lead_form_texts');
		$fs->legend()->label('Lead Form Messages');

		$lead_form_address_found_text = $fs->wp_editor('lead_form_address_found_text')
			->description_('This is the text shown to the user above the lead form when there was valid address found.')
			->label_('Address Found Messaging Above Lead Form')
			->rows(10)
			->set_unfiltered_value($this->$get('lead_form_address_found_text'));

		$lead_form_address_not_found_text = $fs->wp_editor('lead_form_address_not_found_text')
			->description_('This is the text shown to the user above the lead form when there is no valid address found.')
			->label_('Address NOT Found Messaging Above Lead Form')
			->rows(10)
			->set_unfiltered_value($this->$get('lead_form_address_not_found_text'));

		$fs = $form->fieldset('fs_misc');

		$no_address_page_text = $fs->wp_editor('no_address_page_text')
			->description_('This text is shown to users without a valid address after capturing the lead info.')
			->label_('Form Thank You Message When Address is Not Found')
			->rows(10)
			->set_unfiltered_value($this->$get('no_address_page_text'));

		// Remove the "No text" and replace them with empty values.
		foreach ($form->inputs() as $input)
			if ($input->get_value() == 'No text')
				$input->value('');

		$save = $form->primary_button('save')
			->value_('Save settings');

		$this->add_reset_button($form);

		if ($form->is_posting()) {
			$form->post();
			$form->use_post_values();

			if ($save->pressed()) {
				foreach ($keys_to_save as $key) {
					// Checkboxes are handled differently, due to their false nature.
					if (method_exists($$key, 'is_checked')) {
						$value = $$key->is_checked();
						$value = $value ? 'on' : 'off';
					} else
						$value = $$key->get_post_value();
					$this->$set($key, $value);
				}

				$r .= $this->info_message_box()
					->_('Saved!');
			}

			$r .= $this->handle_reset_button($form, $keys_to_save);
			$r .= $this->handle_copy_network_settings_button($form, $keys_to_save);

			$_POST = [];
			echo $r .= $this->admin_forms_settings();
			return;
		}

		if (is_network_admin())
			$r .= $this->p_('These are the global settings. Each blog has the possibility of specifying their own settings, but if a setting or a text is not found locally, it will be taken from the global settings.');

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		admin_general_settings
		@since		2017-08-02 21:29:08
	 **/
	public function admin_general_settings() {
		$get = 'get_local_option';
		$set = 'update_local_option';

		if (is_network_admin()) {
			$get = 'get_site_option';
			$set = 'update_site_option';
		}

		$form = $this->form();
		$r = '';

		if ($this->show_network_settings()) {
			$home_value_api_key = $form->text('home_value_api_key')
				->description_("This key is used to retrieve home value search results from the Home Value server. Use the checkbox below to generate or retrieve a previously generated key for this Wordpress installation. The key is attached to the domain name of this server.")
				->label_('Home Value API key')
				->size(64)
				->value($this->get_home_value_api_key());

			$generate_home_value_api_key = $form->checkbox('generate_home_value_api_key')
				->checked($this->$get('home_value_api_key') == '')
				->description_('Check & save to generate a new or retrieve your existing Home Value API key.')
				->label_('Generate or Retrieve key');

			// Only show the renew info if there is a key.
			if ($this->get_home_value_api_key() != '') {
				$text = $this->get_text_file('api_key_info');
				$text = $this->replace_api_text($text, ['form' => false]);
			} else {
				$text = $this->get_text_file('api_key_info_no_key');
			}

			$form->markup('m_hv_api_key_info')
				->p($text);

			$test_home_value = $form->secondary_button('test_home_value')
				->value_('Use after saving: test the Home Value API key');
		} else {
			$url = network_admin_url('settings.php?page=8b_home_value');
			$form->markup('m_api_for_network')
				->p_('Please visit the <a href="%s">Home Value network settings</a> page to configure your API key.', $url);
		}

		// Only network admins are allowed to lead pool.
		if (is_network_admin()) {
			$lead_pool_blog = $form->select('lead_pool_blog')
				->value($this->$get('lead_pool_blog'))
				->label_('Lead pool blog')
				->option('Lead pooling disabled', 0)
				->required();

			// Because the desc contains html, we need to handle it the long way.
			$description = $this->_(
				'To which blog will all leads automatically be pooled. This function requires the %sfree Broadcast plugin%s.',
				'<a href="https://wordpress.org/plugins/threewp-broadcast/">',
				'</a>'
			);
			$lead_pool_blog->description->label->content = $description;

			if (function_exists('ThreeWP_Broadcast')) {
				$blogs = get_sites([
					'number' => PHP_INT_MAX,
				]);
				foreach ($blogs as $blog) {
					$details = get_blog_details($blog->blog_id);
					$label = sprintf('%s (%s)', $details->blogname, $blog->blog_id);
					$lead_pool_blog->option($label, $blog->blog_id);
				}
			}
		}

		//add increase price range field : KY 06/07/2019
		$home_extra_value = $form->range('home_extra_value')
			->description_('Increase of decrease property values based on %')
			->label_('Adjust Home Values')
			->min(-100)->max(100)
			->value($this->$get('home_extra_value'));

		$create_shortcode = $form->checkbox('create_shortcode')
			->description_('Use this checkbox to create a new page with the [%s] shortcode on it.', $this->get_plugin_prefix())
			->label('Create shortcode on new page');

		$load_css = $form->checkbox('load_css')
			->checked($this->$get('load_css'))
			->description_("Load the plugin's own CSS for the front-end, or disable to style the form yourself.")
			->label_('Load plugin CSS');

		$new_lead_webhooks = $form->textarea('new_lead_webhooks')
			->description_("Optional webhooks URLs to which to send new leads. One line per URL.")
			->label_('Webhooks')
			->rows(5, 50)
			->value($this->$get('new_lead_webhooks'));

		$send_webhooks = $form->checkbox('send_webhooks')
			->description_("Send a test lead to each specified webhook upon saving this form?")
			->label_('Test webhooks');

		// Remove the "No text" and replace them with empty values.
		foreach ($form->inputs() as $input)
			if ($input->get_value() == 'No text')
				$input->value('');

		$save = $form->primary_button('save')
			->value_('Save settings');

		if ($form->is_posting()) {
			$form->post();
			$form->use_post_values();

			if (is_network_admin()) {
				foreach ([
					'lead_pool_blog',
				] as $key)
					$this->update_site_option($key, $$key->get_post_value());
			}

			if ($this->show_network_settings()) {
				$old_api_key = $this->get_home_value_api_key();
				foreach ([
					'home_value_api_key',
				] as $key)
					$this->update_site_option($key, $$key->get_post_value());
			}

			// The checkbox should override the manual HV api key.

			if (isset($generate_home_value_api_key)) {
				if ($generate_home_value_api_key->is_checked()) {
					try {
						$data = $this->get_api()->generate();

						$r .= $this->info_message_box()
							->_($data->message);

						// WP caches site options per request. The new API key is received in a different request.
						// We must clear our cache in order to get the new API key.
						$this->clear_site_option_cache(['home_value_api_key', 'results_left']);
					} catch (Exception $e) {
						print_r($e);
						exit;
						$r .= $this->error_message_box()
							->_('Unable to generate or retrieve your Home Value API key: %s', $e->getMessage());
					}
				} else {
					// New API key inputted? Refresh the status.
					$new_api_key = $this->get_home_value_api_key();
					if ($new_api_key != $old_api_key)
						if ($new_api_key != '')
							$this->get_api()->status();
				}
			}

			if ($create_shortcode->is_checked()) {
				$page_id = wp_insert_post([
					'post_title' => 'Home Value',
					'post_content' => '[8b_home_value]',
					'post_type' => 'page',
					'post_status' => 'publish',
				]);
				$r .= $this->info_message_box()
					->_('<a href="%s">A page containing the shortcode</a> has been created.', get_permalink($page_id));
			}


			foreach ([
				'load_css',
				'new_lead_webhooks',
				'home_extra_value', // save extra value : KY 06/07/2019
			] as $key) {
				$this->$set($key, $$key->get_post_value());
			}

			if ($send_webhooks->is_checked()) {
				$lead = $this->generate_random_lead();
				$this->send_lead_to_webhooks($lead);
				$r .= $this->info_message_box()
					->_('A lead with random information has been sent to the URLs in the webhook textarea.');
			}

			$r .= $this->info_message_box()
				->_('Saved!');

			if ($this->show_network_settings()) {
				if ($test_home_value->pressed()) {
					try {
						$data = $this->get_api()->status();
						$r .= $this->info_message_box()
							->_(
								'Your key seems valid and you have %s search results left before you need to purchase more search results. You will automatically receive new search results on %s.',
								intval($data->results_left),
								date('M j', $data->refill_date)
							);
					} catch (Exception $e) {
						$r .= $this->error_message_box()
							->_('Home Value API key test failure: %s', $e->getMessage());
					}
				}
			}

			$_POST = [];
			echo $r .= $this->admin_general_settings();
			return;
		}

		if (is_network_admin())
			$r .= $this->p_('These are the global settings. Each blog has the possibility of specifying their own settings, but if a setting or a text is not found locally, it will be taken from the global settings.');

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}
}
