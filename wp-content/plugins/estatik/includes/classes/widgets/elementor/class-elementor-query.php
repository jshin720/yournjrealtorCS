<?php


use Elementor\Controls_Manager;

/**
 * Class Elementor_Es_Query_Widget.
 */
abstract class Elementor_Es_Query_Widget extends Elementor_Es_Base_Widget {

	/**
	 * Display properties query filter fields.
	 *
	 * @return void
	 */
	public function query_register_controls() {
		$shortcode = es_get_shortcode_instance( 'es_my_listing' );
		$attributes = $shortcode->get_attributes();

		$this->start_controls_section(
			'query_content', array( 'label' => _x( 'Query filter', 'Elementor widget section', 'es' ), )
		);

		$this->add_custom_control( 'posts_per_page', array(
			'label' => __( 'Properties per page', 'es' ),
			'type' => Controls_Manager::NUMBER,
			'default' => $attributes['posts_per_page']
		) );

		$this->add_custom_control( 'sort', array(
			'label' => __( 'Default sort', 'es' ),
			'type' => Controls_Manager::SELECT,
			'default' => $attributes['sort'],
			'options' => ests_selected( 'properties_sorting_options' )
		) );

		$this->add_custom_control( 'prop_id', array(
			'label' => __( 'Properties IDs', 'es' ),
			'type' => 'text',
			'description' => __( 'Comma separated properties IDs', 'es' ),
		) );

		$this->add_custom_control( 'es_category', array(
			'label' => __( 'Categories', 'es' ),
			'type' => Controls_Manager::SELECT2,
			'multiple' => true,
			'options' => array( '' => __( 'Choose category', 'es' ) ) + es_get_terms_list( 'es_category', false ),
		) );

		$this->add_custom_control( 'es_type', array(
			'label' => __( 'Types', 'es' ),
			'type' => Controls_Manager::SELECT2,
			'multiple' => true,
			'options' => array( '' => __( 'Choose type', 'es' ) ) + es_get_terms_list( 'es_type', false ),
		) );

		$this->add_custom_control( 'es_status', array(
			'label' => __( 'Statuses', 'es' ),
			'type' => Controls_Manager::SELECT2,
			'multiple' => true,
			'options' => array( '' => __( 'Choose status', 'es' ) ) + es_get_terms_list( 'es_status', false ),
		) );

		$this->add_custom_control( 'es_label', array(
			'label' => __( 'Labels', 'es' ),
			'type' => Controls_Manager::SELECT2,
			'multiple' => true,
			'options' => array( '' => __( 'Choose label', 'es' ) ) + es_get_terms_list( 'es_label', false ),
		) );

		$this->end_controls_section();
	}
}
