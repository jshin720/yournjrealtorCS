<?php

namespace eightb\home_value\classes;

/**
	@brief		Lead custom post type.
	@since		2016-12-12 21:26:16
**/
class Lead
	extends \eightb\home_plugin_1\client\Lead
{
	// Use the post storage trait.
	use \plainview\sdk_eightb_home_value\wordpress\object_stores\Post;

	/**
		@brief		Mark this lead as CMA.
		@since		2017-02-24 23:14:12
	**/
	public function address_found( $found = true )
	{
		if ( $found )
			$tag = 'Address found';
		else
			$tag = 'Address not found';
		wp_set_object_terms( $this->id, $tag, '8b_hv_lead_tag', true );
	}

	/**
		@brief		Before saving.
		@since		2017-03-22 15:12:17
	**/
	public function before_save()
	{
		if ( $this->post_title == '' )
			$this->set( 'post_title', $this->generate_post_title() );
		return true;
	}

	/**
		@brief		Return an array of meta keys we use.
		@since		2017-03-22 13:26:38
	**/
	public function get_meta_keys()
	{
		return parent::get_meta_keys();
	}

	/**
		@brief		Return the main plugin instance.
		@since		2017-03-07 22:04:13
	**/
	public function get_plugin()
	{
		return EightB_Home_Value();
	}

	/**
		@brief		Return the post type name.
		@since		2016-12-12 21:46:04
	**/
	public static function get_post_type()
	{
		return '8b_hv_lead';
	}

	/**
		@brief		Return the name of the tag we register.
		@since		2017-03-09 22:08:24
	**/
	public function get_tag_name()
	{
		return '8b_hv_lead_tag';
	}

	/**
		@brief		Register the taxonomy.
		@since		2017-03-09 22:10:22
	**/
	public function register_taxonomies()
	{
		$labels = [
			'name'              => 'Tags',
			'singular_name'     => 'Tag',
			'search_items'      => 'Search tags',
			'all_items'         => 'All Tags',
			'parent_item'       => 'Parent Tag',
			'parent_item_colon' => 'Parent Tag:',
			'edit_item'         => 'Edit Tag',
			'update_item'       => 'Update Tag',
			'add_new_item'      => 'Add New Tag',
			'new_item_name'     => 'New Tag Name',
			'menu_name'         => 'Tags',
		];

		$args = [
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
		];

		register_taxonomy( $this->get_tag_name(), [ $this->get_post_type() ], $args );
	}

	/**
		@brief		Was the searched address found?
		@since		2017-08-03 04:38:28
	**/
	public function was_address_found()
	{
		$tag = 'Address found';
		$tags = wp_get_object_terms( $this->id, '8b_hv_lead_tag' );
		foreach( $tags as $object_tag )
			if ( $object_tag->name == $tag )
				return true;
		return false;
	}
}
