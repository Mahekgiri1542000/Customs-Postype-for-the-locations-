<?php

// =============================================================================
// FUNCTIONS.PHP
// -----------------------------------------------------------------------------
// Overwrite or add your own custom functions to Pro in this file.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Parent Stylesheet
//   02. Additional Functions
// =============================================================================

// Enqueue Parent Stylesheet
// =============================================================================

add_filter( 'x_enqueue_parent_stylesheet', '__return_true' );

/**
 * Store and load ACF Local JSON in the child theme acf-json folder.
 * This keeps field definitions version-controlled and reduces DB lookups.
 */
add_filter(
	'acf/settings/save_json',
	static function () {
		return get_stylesheet_directory() . '/acf-json';
	}
);

add_filter(
	'acf/settings/load_json',
	static function ( $paths ) {
		$paths[] = get_stylesheet_directory() . '/acf-json';
		return $paths;
	}
);



// =============================================================================
// Local Authority System â€” Final Merged Spec
// thefloor4u.com Â· WordPress + ACF Pro + Themeco Pro Child Theme
// =============================================================================
// TABLE OF CONTENTS
//   01. Register Custom Post Types        (Step 1)
//   02. Register Taxonomy: community      (Step 2)
//   03. ACF Group: Location  group_loc_v1 (Step 5)
//   04. ACF Group: Service   group_svc_v1 (Step 6)
//   05. ACF Group: Project   group_prj_v1 (Step 7)
//   06. ACF Group: Community group_com_v1 (Step 8)
//   07. JSON-LD Schema Output             (Step 9)
//   08. Dev Seeder (remove before launch)
// =============================================================================


// =============================================================================
// 01. REGISTER CUSTOM POST TYPES
// =============================================================================

/**
 * Register location, service, and project custom post types.
 */
function floor4u_register_cpts() {

	// -- CPT: location ---------------------------------------------------
	register_post_type(
		'location',
		array(
			'label'              => 'Locations',
			'public'             => true,
			'has_archive'        => true,
			'rewrite'            => array( 'slug' => 'locations' ),
			'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'show_in_rest'       => true,
			'publicly_queryable' => true,
		)
	);

	// -- CPT: service ----------------------------------------------------
	register_post_type(
		'service',
		array(
			'label'              => 'Services',
			'public'             => true,
			'has_archive'        => true,
			'rewrite'            => array( 'slug' => 'services' ),
			'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'show_in_rest'       => true,
			'publicly_queryable' => true,
		)
	);

	// -- CPT: project ----------------------------------------------------
	register_post_type(
		'project',
		array(
			'label'              => 'Projects',
			'public'             => true,
			'has_archive'        => true,
			'rewrite'            => array( 'slug' => 'projects' ),
			'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'show_in_rest'       => true,
			'publicly_queryable' => true,
		)
	);
}
add_action( 'init', 'floor4u_register_cpts' );


// =============================================================================
// 02. REGISTER TAXONOMY: community
// =============================================================================

/**
 * Register the community micro-geo taxonomy.
 * Attached to both location AND project.
 */
function floor4u_register_taxonomies() {
	register_taxonomy(
		'community',
		array( 'location', 'project' ),
		array(
			'label'              => 'Communities',
			'hierarchical'       => true,
			'public'             => true,
			'publicly_queryable' => true,
			'show_in_rest'       => true,
			'rewrite'            => array( 'slug' => 'community' ),
		)
	);
}
add_action( 'init', 'floor4u_register_taxonomies' );


// =============================================================================
// 03. ACF GROUP: LOCATION  (group_loc_v1)
// =============================================================================

/**
 * Register all four ACF field groups.
 * Wrapped in function_exists check for safety.
 */
function floor4u_register_acf_groups() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// -----------------------------------------------------------------------
	// Helper: conditional logic for branch OR hybrid loc_mode
	// -----------------------------------------------------------------------
	$branch_or_hybrid = array(
		array(
			array(
				'field'    => 'field_loc_mode',
				'operator' => '==',
				'value'    => 'branch',
			),
		),
		array(
			array(
				'field'    => 'field_loc_mode',
				'operator' => '==',
				'value'    => 'hybrid',
			),
		),
	);

	// =======================================================================
	// GROUP: LOCATION V3 (group_loc_v3) - 5 tabs
	// V3 simplified fields used by Cornerstone Dynamic Content.
	// =======================================================================
	acf_add_local_field_group( array(
		'key'      => 'group_loc_v3',
		'title'    => 'Location Page V3',
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'location' ) ) ),
		'active'   => true,
		'fields'   => array(
			array( 'key' => 'tab_loc_v3_mode', 'label' => 'Operational Mode', 'type' => 'tab' ),
			array(
				'key'      => 'field_loc_mode_v3',
				'label'    => 'Page Mode',
				'name'     => 'loc_mode',
				'type'     => 'select',
				'required' => 1,
				'choices'  => array(
					'branch' => 'Physical Store / Showroom',
					'city'   => 'Service Area City',
					'hybrid' => 'Hybrid',
				),
				'return_format' => 'value',
			),
			array(
				'key'           => 'field_loc_emit_lb_v3',
				'label'         => 'Emit LocalBusiness Schema',
				'name'          => 'loc_emit_lb',
				'type'          => 'true_false',
				'required'      => 1,
				'default_value' => 0,
			),

			array( 'key' => 'tab_loc_v3_identity', 'label' => 'Page Identity & Hero', 'type' => 'tab' ),
			array( 'key' => 'field_location_headline', 'label' => 'Location Headline', 'name' => 'location_headline', 'type' => 'text', 'required' => 1 ),
			array( 'key' => 'field_hero_intro', 'label' => 'Hero Intro', 'name' => 'hero_intro', 'type' => 'textarea', 'required' => 0 ),
			array( 'key' => 'field_hero_cta', 'label' => 'Hero CTA', 'name' => 'hero_cta', 'type' => 'link', 'required' => 0 ),

			array( 'key' => 'tab_loc_v3_geo', 'label' => 'Geographic Identity', 'type' => 'tab' ),
			array( 'key' => 'field_address_city', 'label' => 'Address City', 'name' => 'address_city', 'type' => 'text', 'required' => 1 ),
			array( 'key' => 'field_postal_code', 'label' => 'Postal Code', 'name' => 'postal_code', 'type' => 'text', 'required' => 0 ),
			array(
				'key'               => 'field_latitude',
				'label'             => 'Latitude',
				'name'              => 'latitude',
				'type'              => 'number',
				'required'          => 0,
				'conditional_logic' => array(
					array( array( 'field' => 'field_loc_mode_v3', 'operator' => '==', 'value' => 'branch' ) ),
					array( array( 'field' => 'field_loc_mode_v3', 'operator' => '==', 'value' => 'hybrid' ) ),
				),
			),
			array(
				'key'               => 'field_longitude',
				'label'             => 'Longitude',
				'name'              => 'longitude',
				'type'              => 'number',
				'required'          => 0,
				'conditional_logic' => array(
					array( array( 'field' => 'field_loc_mode_v3', 'operator' => '==', 'value' => 'branch' ) ),
					array( array( 'field' => 'field_loc_mode_v3', 'operator' => '==', 'value' => 'hybrid' ) ),
				),
			),
			array( 'key' => 'field_google_place_id', 'label' => 'Google Place ID', 'name' => 'google_place_id', 'type' => 'text', 'required' => 0 ),

			array( 'key' => 'tab_loc_v3_signals', 'label' => 'Local Search Signals', 'type' => 'tab' ),
			array( 'key' => 'field_city_intro_prose', 'label' => 'City Intro Prose', 'name' => 'city_intro_prose', 'type' => 'wysiwyg', 'required' => 0 ),
			array( 'key' => 'field_highway_directions', 'label' => 'Highway Directions', 'name' => 'highway_directions', 'type' => 'textarea', 'required' => 0 ),
			array( 'key' => 'field_local_landmarks', 'label' => 'Local Landmarks', 'name' => 'local_landmarks', 'type' => 'textarea', 'required' => 0 ),
			array(
				'key'           => 'field_communities_served',
				'label'         => 'Communities Served',
				'name'          => 'communities_served',
				'type'          => 'taxonomy',
				'taxonomy'      => 'community',
				'field_type'    => 'checkbox',
				'save_terms'    => 1,
				'load_terms'    => 1,
				'return_format' => 'object',
				'required'      => 0,
			),
			array( 'key' => 'field_location_map_embed', 'label' => 'Map Embed', 'name' => 'location_map_embed', 'type' => 'textarea', 'required' => 0 ),

			array( 'key' => 'tab_loc_v3_rels', 'label' => 'Relationships', 'type' => 'tab' ),
			array(
				'key'           => 'field_featured_services',
				'label'         => 'Featured Services',
				'name'          => 'featured_services',
				'type'          => 'relationship',
				'post_type'     => array( 'service' ),
				'return_format' => 'post_object',
				'required'      => 0,
			),
			array(
				'key'           => 'field_featured_projects',
				'label'         => 'Featured Projects',
				'name'          => 'featured_projects',
				'type'          => 'relationship',
				'post_type'     => array( 'project' ),
				'return_format' => 'post_object',
				'max'           => 6,
				'required'      => 0,
			),
			array(
				'key'        => 'field_local_reviews',
				'label'      => 'Local Reviews',
				'name'       => 'local_reviews',
				'type'       => 'repeater',
				'required'   => 0,
				'sub_fields' => array(
					array( 'key' => 'field_reviewer_name', 'label' => 'Reviewer Name', 'name' => 'reviewer_name', 'type' => 'text' ),
					array( 'key' => 'field_review_text', 'label' => 'Review Text', 'name' => 'review_text', 'type' => 'textarea' ),
					array( 'key' => 'field_review_rating', 'label' => 'Review Rating', 'name' => 'review_rating', 'type' => 'number', 'min' => 1, 'max' => 5 ),
					array( 'key' => 'field_review_featured', 'label' => 'Featured', 'name' => 'featured', 'type' => 'true_false' ),
					array( 'key' => 'field_review_date', 'label' => 'Date', 'name' => 'date', 'type' => 'date_picker', 'return_format' => 'Ymd', 'display_format' => 'F j, Y' ),
				),
			),
			array(
				'key'        => 'field_local_faqs',
				'label'      => 'Local FAQs',
				'name'       => 'local_faqs',
				'type'       => 'repeater',
				'required'   => 0,
				'sub_fields' => array(
					array( 'key' => 'field_faq_question', 'label' => 'Question', 'name' => 'faq_question', 'type' => 'text' ),
					array( 'key' => 'field_faq_answer', 'label' => 'Answer', 'name' => 'faq_answer', 'type' => 'wysiwyg' ),
					array( 'key' => 'field_faq_featured', 'label' => 'Featured', 'name' => 'featured', 'type' => 'true_false' ),
				),
			),
			array(
				'key'        => 'field_schema_same_as_links',
				'label'      => 'Schema sameAs Links',
				'name'       => 'schema_same_as_links',
				'type'       => 'repeater',
				'required'   => 0,
				'sub_fields' => array(
					array( 'key' => 'field_same_as_url', 'label' => 'URL', 'name' => 'same_as_url', 'type' => 'url' ),
				),
			),
		),
	) );

	// =======================================================================
	// GROUP: LOCATION  (group_loc_v1) â€” 7 tabs
	// =======================================================================
	acf_add_local_field_group( array(
		'key'                   => 'group_loc_v1',
		'title'                 => 'Location SEO',
		'location'              => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'location' ) ) ),
		'position'              => 'normal',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
		'fields'                => array(

			// -----------------------------------------------------------------
			// TAB 1 â€” Identity
			// -----------------------------------------------------------------
			array( 'key' => 'tab_loc_identity', 'label' => 'Identity', 'type' => 'tab' ),

			array(
				'key'          => 'field_loc_h1',
				'label'        => 'H1 Headline',
				'name'         => 'loc_h1',
				'type'         => 'text',
				'required'     => 1,
				'instructions' => 'Visible page H1.',
			),
			array(
				'key'          => 'field_loc_intro',
				'label'        => 'Intro',
				'name'         => 'loc_intro',
				'type'         => 'wysiwyg',
				'required'     => 1,
				'instructions' => 'Opening local intro.',
			),
			array(
				'key'           => 'field_loc_status',
				'label'         => 'Status',
				'name'          => 'loc_status',
				'type'          => 'select',
				'required'      => 1,
				'choices'       => array(
					'active'      => 'Active',
					'coming_soon' => 'Coming Soon',
					'internal'    => 'Internal',
				),
				'allow_null'    => 0,
				'return_format' => 'value',
			),
			array(
				'key'           => 'field_loc_cta_1_text',
				'label'         => 'CTA Primary Text',
				'name'          => 'loc_cta_1_text',
				'type'          => 'text',
				'required'      => 1,
				'default_value' => 'Request an Estimate',
			),
			array(
				'key'          => 'field_loc_cta_1_url',
				'label'        => 'CTA Primary URL',
				'name'         => 'loc_cta_1_url',
				'type'         => 'url',
				'required'     => 1,
				'instructions' => 'Estimate or contact page URL.',
			),
			array(
				'key'      => 'field_loc_cta_2_text',
				'label'    => 'CTA Secondary Text',
				'name'     => 'loc_cta_2_text',
				'type'     => 'text',
				'required' => 0,
			),
			array(
				'key'      => 'field_loc_cta_2_url',
				'label'    => 'CTA Secondary URL',
				'name'     => 'loc_cta_2_url',
				'type'     => 'url',
				'required' => 0,
			),

			// -----------------------------------------------------------------
			// TAB 2 â€” Geography
			// -----------------------------------------------------------------
			array( 'key' => 'tab_loc_geo', 'label' => 'Geography', 'type' => 'tab' ),

			array(
				'key'          => 'field_loc_city_name',
				'label'        => 'City Name',
				'name'         => 'loc_city_name',
				'type'         => 'text',
				'required'     => 1,
				'instructions' => 'Target city.',
			),
			array(
				'key'           => 'field_loc_city_type',
				'label'         => 'City Type',
				'name'          => 'loc_city_type',
				'type'          => 'select',
				'required'      => 1,
				'choices'       => array(
					'city'     => 'City',
					'village'  => 'Village',
					'town'     => 'Town',
					'township' => 'Township',
				),
				'allow_null'    => 0,
				'return_format' => 'value',
			),
			array(
				'key'          => 'field_loc_county_name',
				'label'        => 'County Name',
				'name'         => 'loc_county_name',
				'type'         => 'text',
				'required'     => 1,
				'instructions' => 'Target county.',
			),
			array(
				'key'           => 'field_loc_state_code',
				'label'         => 'State Code',
				'name'          => 'loc_state_code',
				'type'          => 'text',
				'required'      => 1,
				'default_value' => 'IL',
			),
			array(
				'key'           => 'field_loc_state_name',
				'label'         => 'State Name',
				'name'          => 'loc_state_name',
				'type'          => 'text',
				'required'      => 1,
				'default_value' => 'Illinois',
			),
			array(
				'key'           => 'field_loc_country_code',
				'label'         => 'Country Code',
				'name'          => 'loc_country_code',
				'type'          => 'text',
				'required'      => 1,
				'default_value' => 'US',
			),
			array(
				'key'          => 'field_loc_area_summary',
				'label'        => 'Service Area Summary',
				'name'         => 'loc_area_summary',
				'type'         => 'wysiwyg',
				'required'     => 1,
				'instructions' => 'City and nearby coverage copy.',
			),
			array(
				'key'           => 'field_loc_area_type',
				'label'         => 'Service Area Type',
				'name'          => 'loc_area_type',
				'type'          => 'select',
				'required'      => 1,
				'choices'       => array(
					'city_only'             => 'City Only',
					'city_plus_communities' => 'City + Communities',
					'regional'              => 'Regional',
				),
				'allow_null'    => 0,
				'return_format' => 'value',
			),
			array(
				'key'          => 'field_loc_zips',
				'label'        => 'ZIP Codes',
				'name'         => 'loc_zips',
				'type'         => 'repeater',
				'required'     => 0,
				'instructions' => 'One ZIP per row.',
				'layout'       => 'table',
				'sub_fields'   => array(
					array(
						'key'   => 'field_loc_zip_code',
						'label' => 'ZIP Code',
						'name'  => 'zip_code',
						'type'  => 'text',
					),
				),
			),
			array(
				'key'          => 'field_loc_highways',
				'label'        => 'Highway Directions',
				'name'         => 'loc_highways',
				'type'         => 'textarea',
				'required'     => 0,
				'instructions' => 'Route access proximity signals. Renders near bottom of page.',
			),
			array(
				'key'        => 'field_loc_landmarks_local',
				'label'      => 'Local Landmarks',
				'name'       => 'loc_landmarks_local',
				'type'       => 'repeater',
				'required'   => 0,
				'layout'     => 'table',
				'sub_fields' => array(
					array( 'key' => 'field_loc_lml_name', 'label' => 'Name', 'name' => 'name', 'type' => 'text' ),
					array( 'key' => 'field_loc_lml_type', 'label' => 'Type', 'name' => 'type', 'type' => 'text' ),
				),
			),
			array(
				'key'        => 'field_loc_landmarks_nearby',
				'label'      => 'Nearby Landmarks',
				'name'       => 'loc_landmarks_nearby',
				'type'       => 'repeater',
				'required'   => 0,
				'layout'     => 'table',
				'sub_fields' => array(
					array( 'key' => 'field_loc_lmn_name', 'label' => 'Name', 'name' => 'name', 'type' => 'text' ),
					array( 'key' => 'field_loc_lmn_type', 'label' => 'Type', 'name' => 'type', 'type' => 'text' ),
				),
			),
			array(
				'key'          => 'field_loc_communities',
				'label'        => 'Communities Served',
				'name'         => 'loc_communities',
				'type'         => 'taxonomy',
				'taxonomy'     => 'community',
				'field_type'   => 'checkbox',
				'save_terms'   => 1,
				'load_terms'   => 1,
				'required'     => 1,
				'return_format' => 'object',
				'instructions' => 'Must have term syncing to create true DB relationship.',
			),
			array(
				'key'           => 'field_loc_nearby',
				'label'         => 'Nearby Locations',
				'name'          => 'loc_nearby',
				'type'          => 'relationship',
				'post_type'     => array( 'location' ),
				'return_format' => 'object',
				'required'      => 0,
			),

			// -----------------------------------------------------------------
			// TAB 3 â€” Business Support
			// -----------------------------------------------------------------
			array( 'key' => 'tab_loc_biz', 'label' => 'Business Support', 'type' => 'tab' ),

			array(
				'key'           => 'field_loc_mode',
				'label'         => 'Page Mode',
				'name'          => 'loc_mode',
				'type'          => 'select',
				'required'      => 1,
				'choices'       => array(
					'city'         => 'Service Area City (V3)',
					'service_area' => 'Service Area City',
					'branch'       => 'Physical Store / Showroom',
					'hybrid'       => 'Hybrid',
				),
				'allow_null'    => 0,
				'return_format' => 'value',
				'instructions'  => 'Drives schema output and conditional field visibility.',
			),
			array(
				'key'          => 'field_loc_biz_name',
				'label'        => 'Business Name',
				'name'         => 'loc_biz_name',
				'type'         => 'text',
				'required'     => 1,
				'instructions' => 'Real provider name.',
			),
			array(
				'key'          => 'field_loc_biz_phone',
				'label'        => 'Business Phone',
				'name'         => 'loc_biz_phone',
				'type'         => 'text',
				'required'     => 1,
				'instructions' => 'Display format e.g. (815) 555-0100.',
			),
			array(
				'key'          => 'field_loc_biz_phone_raw',
				'label'        => 'Business Phone Raw',
				'name'         => 'loc_biz_phone_raw',
				'type'         => 'text',
				'required'     => 1,
				'instructions' => 'tel format e.g. +18155550100.',
			),
			array(
				'key'          => 'field_loc_biz_url',
				'label'        => 'Business URL',
				'name'         => 'loc_biz_url',
				'type'         => 'url',
				'required'     => 1,
				'instructions' => 'Real provider website URL.',
			),
			array(
				'key'      => 'field_loc_gbp_url',
				'label'    => 'Google Business Profile URL',
				'name'     => 'loc_gbp_url',
				'type'     => 'url',
				'required' => 0,
			),
			array(
				'key'          => 'field_loc_place_id',
				'label'        => 'Google Place ID',
				'name'         => 'loc_place_id',
				'type'         => 'text',
				'required'     => 0,
				'instructions' => 'Entity linking identifier.',
			),
			array(
				'key'           => 'field_loc_emit_lb',
				'label'         => 'Emit LocalBusiness Schema',
				'name'          => 'loc_emit_lb',
				'type'          => 'true_false',
				'required'      => 1,
				'default_value' => 0,
				'instructions'  => 'Set TRUE only for real physical showroom pages. Never enable for service-area city pages.',
			),

			// Conditional address / geo fields â€” visible only on branch or hybrid.
			array(
				'key'               => 'field_loc_addr_1',
				'label'             => 'Street Address',
				'name'              => 'loc_addr_1',
				'type'              => 'text',
				'required'          => 0,
				'conditional_logic' => $branch_or_hybrid,
			),
			array(
				'key'               => 'field_loc_addr_2',
				'label'             => 'Address Line 2',
				'name'              => 'loc_addr_2',
				'type'              => 'text',
				'required'          => 0,
				'conditional_logic' => $branch_or_hybrid,
			),
			array(
				'key'               => 'field_loc_addr_city',
				'label'             => 'Address City',
				'name'              => 'loc_addr_city',
				'type'              => 'text',
				'required'          => 0,
				'conditional_logic' => $branch_or_hybrid,
			),
			array(
				'key'               => 'field_loc_addr_state',
				'label'             => 'Address State',
				'name'              => 'loc_addr_state',
				'type'              => 'text',
				'required'          => 0,
				'conditional_logic' => $branch_or_hybrid,
			),
			array(
				'key'               => 'field_loc_addr_zip',
				'label'             => 'Postal Code',
				'name'              => 'loc_addr_zip',
				'type'              => 'text',
				'required'          => 0,
				'conditional_logic' => $branch_or_hybrid,
			),
			array(
				'key'               => 'field_loc_lat',
				'label'             => 'Latitude',
				'name'              => 'loc_lat',
				'type'              => 'number',
				'required'          => 0,
				'conditional_logic' => $branch_or_hybrid,
			),
			array(
				'key'               => 'field_loc_lng',
				'label'             => 'Longitude',
				'name'              => 'loc_lng',
				'type'              => 'number',
				'required'          => 0,
				'conditional_logic' => $branch_or_hybrid,
			),
			array(
				'key'               => 'field_loc_hours',
				'label'             => 'Business Hours',
				'name'              => 'loc_hours',
				'type'              => 'repeater',
				'required'          => 0,
				'layout'            => 'table',
				'conditional_logic' => $branch_or_hybrid,
				'sub_fields'        => array(
					array(
						'key'     => 'field_loc_hours_day',
						'label'   => 'Day',
						'name'    => 'day',
						'type'    => 'select',
						'choices' => array(
							'monday'    => 'Monday',
							'tuesday'   => 'Tuesday',
							'wednesday' => 'Wednesday',
							'thursday'  => 'Thursday',
							'friday'    => 'Friday',
							'saturday'  => 'Saturday',
							'sunday'    => 'Sunday',
						),
						'return_format' => 'value',
					),
					array(
						'key'   => 'field_loc_hours_closed',
						'label' => 'Closed',
						'name'  => 'closed',
						'type'  => 'true_false',
					),
					array(
						'key'               => 'field_loc_hours_open',
						'label'             => 'Open',
						'name'              => 'open',
						'type'              => 'text',
						'instructions'      => 'e.g. 08:00',
						'conditional_logic' => array( array( array( 'field' => 'field_loc_hours_closed', 'operator' => '==', 'value' => '0' ) ) ),
					),
					array(
						'key'               => 'field_loc_hours_close',
						'label'             => 'Close',
						'name'              => 'close',
						'type'              => 'text',
						'instructions'      => 'e.g. 17:00',
						'conditional_logic' => array( array( array( 'field' => 'field_loc_hours_closed', 'operator' => '==', 'value' => '0' ) ) ),
					),
				),
			),

			// -----------------------------------------------------------------
			// TAB 4 â€” Services
			// -----------------------------------------------------------------
			array( 'key' => 'tab_loc_services', 'label' => 'Services', 'type' => 'tab' ),

			array(
				'key'           => 'field_loc_svc_primary',
				'label'         => 'Primary Service',
				'name'          => 'loc_svc_primary',
				'type'          => 'relationship',
				'post_type'     => array( 'service' ),
				'return_format' => 'object',
				'max'           => 1,
				'required'      => 1,
				'instructions'  => 'The primary service this location is optimised for.',
			),
			array(
				'key'           => 'field_loc_svcs',
				'label'         => 'Services Available',
				'name'          => 'loc_svcs',
				'type'          => 'relationship',
				'post_type'     => array( 'service' ),
				'return_format' => 'object',
				'required'      => 1,
				'instructions'  => 'All services offered at this location. Never hardcode service content.',
			),
			array(
				'key'          => 'field_loc_svcs_intro',
				'label'        => 'Services Intro',
				'name'         => 'loc_svcs_intro',
				'type'         => 'wysiwyg',
				'required'     => 0,
				'instructions' => 'Optional section copy above the service cards.',
			),

			// -----------------------------------------------------------------
			// TAB 5 â€” Proof
			// -----------------------------------------------------------------
			array( 'key' => 'tab_loc_proof', 'label' => 'Proof', 'type' => 'tab' ),

			array(
				'key'          => 'field_loc_prj_intro',
				'label'        => 'Projects Intro',
				'name'         => 'loc_prj_intro',
				'type'         => 'textarea',
				'required'     => 0,
				'instructions' => 'Short section intro above project cards.',
			),
			array(
				'key'           => 'field_loc_prjs',
				'label'         => 'Featured Projects',
				'name'          => 'loc_prjs',
				'type'          => 'relationship',
				'post_type'     => array( 'project' ),
				'return_format' => 'object',
				'max'           => 6,
				'required'      => 0,
				'instructions'  => 'Max 6. Never hardcode project content.',
			),
			array(
				'key'          => 'field_loc_reviews',
				'label'        => 'Reviews',
				'name'         => 'loc_reviews',
				'type'         => 'repeater',
				'required'     => 0,
				'instructions' => 'Local reviews only. Sort: featured first, then newest date.',
				'layout'       => 'row',
				'sub_fields'   => array(
					array( 'key' => 'field_loc_rev_name',     'label' => 'Name',     'name' => 'name',     'type' => 'text' ),
					array( 'key' => 'field_loc_rev_city',     'label' => 'City',     'name' => 'city',     'type' => 'text' ),
					array( 'key' => 'field_loc_rev_body',     'label' => 'Body',     'name' => 'body',     'type' => 'textarea' ),
					array( 'key' => 'field_loc_rev_rating',   'label' => 'Rating',   'name' => 'rating',   'type' => 'number', 'min' => 1, 'max' => 5 ),
					array( 'key' => 'field_loc_rev_url',      'label' => 'URL',      'name' => 'url',      'type' => 'url' ),
					array(
						'key'     => 'field_loc_rev_source',
						'label'   => 'Source',
						'name'    => 'source',
						'type'    => 'select',
						'choices' => array( 'google' => 'Google', 'houzz' => 'Houzz', 'yelp' => 'Yelp', 'other' => 'Other' ),
						'return_format' => 'value',
					),
					array( 'key' => 'field_loc_rev_date',     'label' => 'Date',     'name' => 'date',     'type' => 'date_picker', 'return_format' => 'Ymd', 'display_format' => 'F j, Y' ),
					array( 'key' => 'field_loc_rev_service',  'label' => 'Service',  'name' => 'service',  'type' => 'relationship', 'post_type' => array( 'service' ), 'return_format' => 'object', 'max' => 1 ),
					array( 'key' => 'field_loc_rev_featured', 'label' => 'Featured', 'name' => 'featured', 'type' => 'true_false' ),
				),
			),
			array(
				'key'      => 'field_loc_gallery_ba',
				'label'    => 'Before / After Gallery',
				'name'     => 'loc_gallery_ba',
				'type'     => 'gallery',
				'required' => 0,
			),
			array(
				'key'        => 'field_loc_trust',
				'label'      => 'Trust Bullets',
				'name'       => 'loc_trust',
				'type'       => 'repeater',
				'required'   => 0,
				'layout'     => 'table',
				'sub_fields' => array(
					array( 'key' => 'field_loc_trust_text', 'label' => 'Text', 'name' => 'text', 'type' => 'text' ),
				),
			),
			array(
				'key'          => 'field_loc_faq_intro',
				'label'        => 'FAQ Intro',
				'name'         => 'loc_faq_intro',
				'type'         => 'textarea',
				'required'     => 0,
			),
			array(
				'key'          => 'field_loc_faqs',
				'label'        => 'FAQs',
				'name'         => 'loc_faqs',
				'type'         => 'repeater',
				'required'     => 0,
				'instructions' => 'Page-supporting FAQs only. Sort: featured first.',
				'layout'       => 'row',
				'sub_fields'   => array(
					array( 'key' => 'field_loc_faq_q',        'label' => 'Question', 'name' => 'q',        'type' => 'text' ),
					array( 'key' => 'field_loc_faq_a',        'label' => 'Answer',   'name' => 'a',        'type' => 'wysiwyg' ),
					array( 'key' => 'field_loc_faq_service',  'label' => 'Service',  'name' => 'service',  'type' => 'relationship', 'post_type' => array( 'service' ), 'return_format' => 'object', 'max' => 1 ),
					array( 'key' => 'field_loc_faq_featured', 'label' => 'Featured', 'name' => 'featured', 'type' => 'true_false' ),
				),
			),

			// -----------------------------------------------------------------
			// TAB 6 â€” Links
			// -----------------------------------------------------------------
			array( 'key' => 'tab_loc_links', 'label' => 'Links', 'type' => 'tab' ),

			array(
				'key'           => 'field_loc_articles',
				'label'         => 'Supporting Articles',
				'name'          => 'loc_articles',
				'type'          => 'relationship',
				'post_type'     => array( 'post' ),
				'return_format' => 'object',
				'required'      => 0,
			),
			array(
				'key'           => 'field_loc_svcs_footer',
				'label'         => 'Footer Services',
				'name'          => 'loc_svcs_footer',
				'type'          => 'relationship',
				'post_type'     => array( 'service' ),
				'return_format' => 'object',
				'required'      => 0,
			),
			array(
				'key'           => 'field_loc_prjs_more',
				'label'         => 'Related Projects',
				'name'          => 'loc_prjs_more',
				'type'          => 'relationship',
				'post_type'     => array( 'project' ),
				'return_format' => 'object',
				'required'      => 0,
			),

			// -----------------------------------------------------------------
			// TAB 7 â€” Schema
			// -----------------------------------------------------------------
			array( 'key' => 'tab_loc_schema', 'label' => 'Schema', 'type' => 'tab' ),

			array(
				'key'           => 'field_loc_schema_city',
				'label'         => 'Emit City Node',
				'name'          => 'loc_schema_city',
				'type'          => 'true_false',
				'required'      => 1,
				'default_value' => 1,
			),
			array(
				'key'           => 'field_loc_schema_place',
				'label'         => 'Emit Place Node',
				'name'          => 'loc_schema_place',
				'type'          => 'true_false',
				'required'      => 1,
				'default_value' => 1,
			),
			array(
				'key'           => 'field_loc_schema_svcs',
				'label'         => 'Emit Service Nodes',
				'name'          => 'loc_schema_svcs',
				'type'          => 'true_false',
				'required'      => 1,
				'default_value' => 1,
			),
			array(
				'key'           => 'field_loc_schema_area',
				'label'         => 'Emit areaServed',
				'name'          => 'loc_schema_area',
				'type'          => 'true_false',
				'required'      => 1,
				'default_value' => 1,
			),
			array(
				'key'        => 'field_loc_sameas',
				'label'      => 'sameAs URLs',
				'name'       => 'loc_sameas',
				'type'       => 'repeater',
				'required'   => 0,
				'layout'     => 'table',
				'sub_fields' => array(
					array( 'key' => 'field_loc_sameas_url', 'label' => 'URL', 'name' => 'url', 'type' => 'url' ),
				),
			),
			array(
				'key'          => 'field_loc_schema_extra',
				'label'        => 'Extra Schema Type',
				'name'         => 'loc_schema_extra',
				'type'         => 'text',
				'required'     => 0,
				'instructions' => 'Rare override only.',
			),
		), // end fields
	) ); // end group_loc_v1


	// =======================================================================
	// GROUP: SERVICE  (group_svc_v1) â€” 5 tabs
	// =======================================================================
	acf_add_local_field_group( array(
		'key'      => 'group_svc_v1',
		'title'    => 'Service SEO',
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'service' ) ) ),
		'active'   => true,
		'fields'   => array(

			// TAB: Identity
			array( 'key' => 'tab_svc_identity', 'label' => 'Identity', 'type' => 'tab' ),
			array( 'key' => 'field_svc_name',    'label' => 'Service Name',    'name' => 'svc_name',    'type' => 'text',     'required' => 1 ),
			array( 'key' => 'field_svc_h1',      'label' => 'H1 Headline',     'name' => 'svc_h1',      'type' => 'text',     'required' => 1 ),
			array( 'key' => 'field_svc_summary',  'label' => 'Summary',         'name' => 'svc_summary', 'type' => 'textarea', 'required' => 1 ),
			array( 'key' => 'field_svc_body',     'label' => 'Body',            'name' => 'svc_body',    'type' => 'wysiwyg',  'required' => 1 ),
			array( 'key' => 'field_svc_type',     'label' => 'Service Type',    'name' => 'svc_type',    'type' => 'text',     'required' => 1, 'instructions' => 'Schema.org service type e.g. FlooringInstallation.' ),
			array(
				'key'     => 'field_svc_status',
				'label'   => 'Status',
				'name'    => 'svc_status',
				'type'    => 'select',
				'required' => 1,
				'choices' => array( 'active' => 'Active', 'inactive' => 'Inactive', 'coming_soon' => 'Coming Soon' ),
				'return_format' => 'value',
			),

			// TAB: Content
			array( 'key' => 'tab_svc_content', 'label' => 'Content', 'type' => 'tab' ),
			array( 'key' => 'field_svc_problems',  'label' => 'Problems Solved', 'name' => 'svc_problems',  'type' => 'wysiwyg',  'required' => 0 ),
			array( 'key' => 'field_svc_benefits',  'label' => 'Benefits',        'name' => 'svc_benefits',  'type' => 'wysiwyg',  'required' => 0 ),
			array(
				'key'        => 'field_svc_materials',
				'label'      => 'Materials',
				'name'       => 'svc_materials',
				'type'       => 'repeater',
				'required'   => 0,
				'layout'     => 'table',
				'sub_fields' => array(
					array( 'key' => 'field_svc_mat_name', 'label' => 'Name', 'name' => 'name', 'type' => 'text' ),
				),
			),
			array( 'key' => 'field_svc_uses', 'label' => 'Common Uses', 'name' => 'svc_uses', 'type' => 'wysiwyg', 'required' => 0 ),
			array(
				'key'        => 'field_svc_faqs',
				'label'      => 'FAQs',
				'name'       => 'svc_faqs',
				'type'       => 'repeater',
				'required'   => 0,
				'layout'     => 'row',
				'sub_fields' => array(
					array( 'key' => 'field_svc_faq_q', 'label' => 'Question', 'name' => 'q', 'type' => 'text' ),
					array( 'key' => 'field_svc_faq_a', 'label' => 'Answer',   'name' => 'a', 'type' => 'wysiwyg' ),
				),
			),

			// TAB: Relationships
			array( 'key' => 'tab_svc_rels', 'label' => 'Relationships', 'type' => 'tab' ),
			array( 'key' => 'field_svc_locs',    'label' => 'Locations',        'name' => 'svc_locs',    'type' => 'relationship', 'post_type' => array( 'location' ), 'return_format' => 'object', 'required' => 0 ),
			array( 'key' => 'field_svc_prjs',    'label' => 'Projects',         'name' => 'svc_prjs',    'type' => 'relationship', 'post_type' => array( 'project' ),  'return_format' => 'object', 'required' => 0 ),
			array( 'key' => 'field_svc_related', 'label' => 'Related Services', 'name' => 'svc_related', 'type' => 'relationship', 'post_type' => array( 'service' ),  'return_format' => 'object', 'required' => 0 ),

			// TAB: Media
			array( 'key' => 'tab_svc_media', 'label' => 'Media', 'type' => 'tab' ),
			array( 'key' => 'field_svc_img',     'label' => 'Featured Image', 'name' => 'svc_img',     'type' => 'image',   'required' => 0, 'return_format' => 'array' ),
			array( 'key' => 'field_svc_gallery', 'label' => 'Gallery',        'name' => 'svc_gallery', 'type' => 'gallery', 'required' => 0 ),

			// TAB: Schema
			array( 'key' => 'tab_svc_schema', 'label' => 'Schema', 'type' => 'tab' ),
			array( 'key' => 'field_svc_schema', 'label' => 'Emit Schema',   'name' => 'svc_schema', 'type' => 'true_false', 'required' => 1, 'default_value' => 1 ),
			array(
				'key'     => 'field_svc_hours_mode',
				'label'   => 'Hours Mode',
				'name'    => 'svc_hours_mode',
				'type'    => 'select',
				'required' => 1,
				'choices' => array( 'business' => 'Use Business Hours', 'custom' => 'Custom', 'na' => 'N/A' ),
				'default_value' => 'business',
				'return_format' => 'value',
			),
			array(
				'key'               => 'field_svc_hours',
				'label'             => 'Custom Hours',
				'name'              => 'svc_hours',
				'type'              => 'repeater',
				'required'          => 0,
				'layout'            => 'table',
				'conditional_logic' => array( array( array( 'field' => 'field_svc_hours_mode', 'operator' => '==', 'value' => 'custom' ) ) ),
				'sub_fields'        => array(
					array( 'key' => 'field_svc_hr_day',    'label' => 'Day',    'name' => 'day',    'type' => 'select', 'choices' => array( 'monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday', 'sunday' => 'Sunday' ), 'return_format' => 'value' ),
					array( 'key' => 'field_svc_hr_closed', 'label' => 'Closed', 'name' => 'closed', 'type' => 'true_false' ),
					array( 'key' => 'field_svc_hr_open',   'label' => 'Open',   'name' => 'open',   'type' => 'text', 'conditional_logic' => array( array( array( 'field' => 'field_svc_hr_closed', 'operator' => '==', 'value' => '0' ) ) ) ),
					array( 'key' => 'field_svc_hr_close',  'label' => 'Close',  'name' => 'close',  'type' => 'text', 'conditional_logic' => array( array( array( 'field' => 'field_svc_hr_closed', 'operator' => '==', 'value' => '0' ) ) ) ),
				),
			),
			array(
				'key'        => 'field_svc_sameas',
				'label'      => 'sameAs URLs',
				'name'       => 'svc_sameas',
				'type'       => 'repeater',
				'required'   => 0,
				'layout'     => 'table',
				'sub_fields' => array(
					array( 'key' => 'field_svc_sameas_url', 'label' => 'URL', 'name' => 'url', 'type' => 'url' ),
				),
			),
		),
	) ); // end group_svc_v1


	// =======================================================================
	// GROUP: PROJECT  (group_prj_v1) â€” 5 tabs
	// =======================================================================
	acf_add_local_field_group( array(
		'key'      => 'group_prj_v1',
		'title'    => 'Project SEO',
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'project' ) ) ),
		'active'   => true,
		'fields'   => array(

			// TAB: Identity
			array( 'key' => 'tab_prj_identity', 'label' => 'Identity', 'type' => 'tab' ),
			array( 'key' => 'field_prj_title',   'label' => 'Project Title', 'name' => 'prj_title',   'type' => 'text',     'required' => 1 ),
			array( 'key' => 'field_prj_summary', 'label' => 'Summary',       'name' => 'prj_summary', 'type' => 'textarea', 'required' => 1 ),
			array( 'key' => 'field_prj_date',    'label' => 'Project Date',  'name' => 'prj_date',    'type' => 'date_picker', 'required' => 0, 'return_format' => 'Ymd', 'display_format' => 'F j, Y' ),
			array(
				'key'     => 'field_prj_status',
				'label'   => 'Status',
				'name'    => 'prj_status',
				'type'    => 'select',
				'required' => 1,
				'choices' => array( 'published' => 'Published', 'draft' => 'Draft', 'archived' => 'Archived' ),
				'return_format' => 'value',
			),

			// TAB: Geography
			array( 'key' => 'tab_prj_geo', 'label' => 'Geography', 'type' => 'tab' ),
			array( 'key' => 'field_prj_city',   'label' => 'City',     'name' => 'prj_city',   'type' => 'text', 'required' => 1 ),
			array( 'key' => 'field_prj_county', 'label' => 'County',   'name' => 'prj_county', 'type' => 'text', 'required' => 0 ),
			array( 'key' => 'field_prj_state',  'label' => 'State',    'name' => 'prj_state',  'type' => 'text', 'required' => 1 ),
			array(
				'key'           => 'field_prj_loc',
				'label'         => 'Location Page',
				'name'          => 'prj_loc',
				'type'          => 'relationship',
				'post_type'     => array( 'location' ),
				'return_format' => 'object',
				'max'           => 1,
				'required'      => 1,
				'instructions'  => 'Tie this project to its parent location page.',
			),
			array(
				'key'           => 'field_prj_coms',
				'label'         => 'Communities',
				'name'          => 'prj_coms',
				'type'          => 'taxonomy',
				'taxonomy'      => 'community',
				'field_type'    => 'checkbox',
				'save_terms'    => 1,
				'load_terms'    => 1,
				'required'      => 0,
				'return_format' => 'object',
			),

			// TAB: Service Proof
			array( 'key' => 'tab_prj_proof', 'label' => 'Service Proof', 'type' => 'tab' ),
			array(
				'key'           => 'field_prj_svcs',
				'label'         => 'Services Used',
				'name'          => 'prj_svcs',
				'type'          => 'relationship',
				'post_type'     => array( 'service' ),
				'return_format' => 'object',
				'required'      => 1,
				'instructions'  => 'Which services were performed in this project.',
			),
			array( 'key' => 'field_prj_scope',     'label' => 'Scope',     'name' => 'prj_scope',     'type' => 'wysiwyg',  'required' => 0 ),
			array( 'key' => 'field_prj_challenge', 'label' => 'Challenge', 'name' => 'prj_challenge', 'type' => 'wysiwyg',  'required' => 0 ),
			array( 'key' => 'field_prj_result',    'label' => 'Result',    'name' => 'prj_result',    'type' => 'wysiwyg',  'required' => 0 ),
			array(
				'key'        => 'field_prj_materials',
				'label'      => 'Materials Used',
				'name'       => 'prj_materials',
				'type'       => 'repeater',
				'required'   => 0,
				'layout'     => 'table',
				'sub_fields' => array(
					array( 'key' => 'field_prj_mat_name', 'label' => 'Name', 'name' => 'name', 'type' => 'text' ),
				),
			),

			// TAB: Media
			array( 'key' => 'tab_prj_media', 'label' => 'Media', 'type' => 'tab' ),
			array( 'key' => 'field_prj_img',     'label' => 'Featured Image', 'name' => 'prj_img',     'type' => 'image',   'required' => 0, 'return_format' => 'array' ),
			array( 'key' => 'field_prj_gallery', 'label' => 'Gallery',        'name' => 'prj_gallery', 'type' => 'gallery', 'required' => 0 ),
			array( 'key' => 'field_prj_ba',      'label' => 'Has Before/After', 'name' => 'prj_ba', 'type' => 'true_false', 'required' => 1 ),
			array(
				'key'               => 'field_prj_before',
				'label'             => 'Before Image',
				'name'              => 'prj_before',
				'type'              => 'image',
				'required'          => 0,
				'return_format'     => 'array',
				'conditional_logic' => array( array( array( 'field' => 'field_prj_ba', 'operator' => '==', 'value' => '1' ) ) ),
			),
			array(
				'key'               => 'field_prj_after',
				'label'             => 'After Image',
				'name'              => 'prj_after',
				'type'              => 'image',
				'required'          => 0,
				'return_format'     => 'array',
				'conditional_logic' => array( array( array( 'field' => 'field_prj_ba', 'operator' => '==', 'value' => '1' ) ) ),
			),

			// TAB: Schema
			array( 'key' => 'tab_prj_schema', 'label' => 'Schema', 'type' => 'tab' ),
			array( 'key' => 'field_prj_schema', 'label' => 'Emit Schema', 'name' => 'prj_schema', 'type' => 'true_false', 'required' => 1, 'default_value' => 1 ),
			array(
				'key'        => 'field_prj_sameas',
				'label'      => 'sameAs URLs',
				'name'       => 'prj_sameas',
				'type'       => 'repeater',
				'required'   => 0,
				'layout'     => 'table',
				'sub_fields' => array(
					array( 'key' => 'field_prj_sameas_url', 'label' => 'URL', 'name' => 'url', 'type' => 'url' ),
				),
			),
		),
	) ); // end group_prj_v1


	// =======================================================================
	// GROUP: COMMUNITY  (group_com_v1) â€” 4 tabs
	// Attaches to taxonomy term edit screens for the "community" taxonomy.
	// =======================================================================
	acf_add_local_field_group( array(
		'key'      => 'group_com_v1',
		'title'    => 'Community Meta',
		'location' => array( array( array( 'param' => 'taxonomy', 'operator' => '==', 'value' => 'community' ) ) ),
		'active'   => true,
		'fields'   => array(

			// TAB: Identity
			array( 'key' => 'tab_com_identity', 'label' => 'Identity', 'type' => 'tab' ),
			array( 'key' => 'field_com_name',   'label' => 'Community Name', 'name' => 'com_name',   'type' => 'text', 'required' => 1 ),
			array(
				'key'     => 'field_com_type',
				'label'   => 'Community Type',
				'name'    => 'com_type',
				'type'    => 'select',
				'required' => 1,
				'choices' => array( 'neighborhood' => 'Neighborhood', 'subdivision' => 'Subdivision', 'district' => 'District', 'zone' => 'Zone', 'other' => 'Other' ),
				'return_format' => 'value',
			),
			array( 'key' => 'field_com_city',   'label' => 'City',   'name' => 'com_city',   'type' => 'text', 'required' => 1 ),
			array( 'key' => 'field_com_county', 'label' => 'County', 'name' => 'com_county', 'type' => 'text', 'required' => 0 ),
			array( 'key' => 'field_com_state',  'label' => 'State',  'name' => 'com_state',  'type' => 'text', 'required' => 1 ),

			// TAB: Content
			array( 'key' => 'tab_com_content', 'label' => 'Content', 'type' => 'tab' ),
			array( 'key' => 'field_com_intro',   'label' => 'Intro',   'name' => 'com_intro',   'type' => 'wysiwyg', 'required' => 0 ),
			array( 'key' => 'field_com_housing', 'label' => 'Housing', 'name' => 'com_housing', 'type' => 'wysiwyg', 'required' => 0 ),
			array(
				'key'        => 'field_com_landmarks',
				'label'      => 'Landmarks',
				'name'       => 'com_landmarks',
				'type'       => 'repeater',
				'required'   => 0,
				'layout'     => 'table',
				'sub_fields' => array(
					array( 'key' => 'field_com_lm_name', 'label' => 'Name', 'name' => 'name', 'type' => 'text' ),
					array( 'key' => 'field_com_lm_type', 'label' => 'Type', 'name' => 'type', 'type' => 'text' ),
				),
			),
			array(
				'key'        => 'field_com_zips',
				'label'      => 'ZIP Codes',
				'name'       => 'com_zips',
				'type'       => 'repeater',
				'required'   => 0,
				'layout'     => 'table',
				'sub_fields' => array(
					array( 'key' => 'field_com_zip_code', 'label' => 'ZIP Code', 'name' => 'zip_code', 'type' => 'text' ),
				),
			),

			// TAB: Links
			array( 'key' => 'tab_com_links', 'label' => 'Links', 'type' => 'tab' ),
			array( 'key' => 'field_com_locs', 'label' => 'Locations', 'name' => 'com_locs', 'type' => 'relationship', 'post_type' => array( 'location' ), 'return_format' => 'object', 'required' => 0 ),
			array( 'key' => 'field_com_svcs', 'label' => 'Services',  'name' => 'com_svcs', 'type' => 'relationship', 'post_type' => array( 'service' ),  'return_format' => 'object', 'required' => 0 ),
			array( 'key' => 'field_com_prjs', 'label' => 'Projects',  'name' => 'com_prjs', 'type' => 'relationship', 'post_type' => array( 'project' ),  'return_format' => 'object', 'required' => 0 ),

			// TAB: Schema
			array( 'key' => 'tab_com_schema', 'label' => 'Schema', 'type' => 'tab' ),
			array( 'key' => 'field_com_schema_place', 'label' => 'Emit Place Node', 'name' => 'com_schema_place', 'type' => 'true_false', 'required' => 1, 'default_value' => 1 ),
			array(
				'key'        => 'field_com_sameas',
				'label'      => 'sameAs URLs',
				'name'       => 'com_sameas',
				'type'       => 'repeater',
				'required'   => 0,
				'layout'     => 'table',
				'sub_fields' => array(
					array( 'key' => 'field_com_sameas_url', 'label' => 'URL', 'name' => 'url', 'type' => 'url' ),
				),
			),
		),
	) ); // end group_com_v1

} // end floor4u_register_acf_groups()
add_action( 'acf/init', 'floor4u_register_acf_groups' );


// =============================================================================
// 07. JSON-LD SCHEMA OUTPUT
// =============================================================================

/**
 * Build OpeningHoursSpecification nodes from a loc_hours repeater value.
 *
 * @param array $hours_rows Rows from get_field('loc_hours').
 * @return array
 */
function floor4u_build_opening_hours( $hours_rows ) {
	$nodes = array();
	if ( empty( $hours_rows ) || ! is_array( $hours_rows ) ) {
		return $nodes;
	}
	foreach ( $hours_rows as $row ) {
		if ( ! empty( $row['closed'] ) ) {
			continue;
		}
		$nodes[] = array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => 'https://schema.org/' . ucfirst( strtolower( $row['day'] ) ),
			'opens'     => $row['open'],
			'closes'    => $row['close'],
		);
	}
	return $nodes;
}

/**
 * Build Service schema nodes from a relationship field value.
 *
 * @param array  $services       Post objects.
 * @param string $provider_id    Schema @id of the provider node.
 * @param array  $area_served    Strings for areaServed.
 * @param bool   $emit_area      Whether to include areaServed on service nodes.
 * @return array
 */
function floor4u_build_service_nodes( $services, $provider_id, $area_served = array(), $emit_area = true ) {
	$nodes = array();
	if ( empty( $services ) || ! is_array( $services ) ) {
		return $nodes;
	}
	foreach ( $services as $item ) {
		$post = $item instanceof WP_Post ? $item : get_post( (int) $item );
		if ( ! $post || 'service' !== $post->post_type ) {
			continue;
		}
		$node = array(
			'@type'    => 'Service',
			'@id'      => get_permalink( $post ) . '#service',
			'name'     => get_the_title( $post ),
			'url'      => get_permalink( $post ),
			'provider' => array( '@id' => $provider_id ),
		);
		if ( $emit_area && ! empty( $area_served ) ) {
			$node['areaServed'] = array_values( $area_served );
		}
		$nodes[] = $node;
	}
	return $nodes;
}

/**
 * Output JSON-LD schema for Location single pages.
 * Hooked to wp_head. Only runs on is_singular('location').
 */
function floor4u_output_location_schema() {
	if ( ! is_singular( 'location' ) ) {
		return;
	}
	$post_id = get_the_ID();
	if ( ! $post_id ) {
		return;
	}

	// Pull field values.
	$loc_mode     = (string) get_field( 'loc_mode', $post_id );
	$emit_lb      = (bool)   get_field( 'loc_emit_lb', $post_id );
	$city_name    = trim( (string) get_field( 'loc_city_name', $post_id ) );
	$county_name  = trim( (string) get_field( 'loc_county_name', $post_id ) );
	$state_code   = trim( (string) get_field( 'loc_state_code', $post_id ) ) ?: 'IL';
	$country_code = trim( (string) get_field( 'loc_country_code', $post_id ) ) ?: 'US';
	$biz_name     = trim( (string) get_field( 'loc_biz_name', $post_id ) ) ?: apply_filters( 'floor4u_schema_provider_name', 'Floor 4U' );
	$biz_url      = trim( (string) get_field( 'loc_biz_url', $post_id ) ) ?: home_url( '/' );
	$page_url     = (string) get_permalink( $post_id );
	$page_title   = (string) get_the_title( $post_id );
	$page_desc    = wp_strip_all_tags( (string) get_field( 'loc_intro', $post_id ) );

	// Boolean schema switches (default true).
	$emit_city    = (bool) get_field( 'loc_schema_city',  $post_id );
	$emit_place   = (bool) get_field( 'loc_schema_place', $post_id );
	$emit_svcs    = (bool) get_field( 'loc_schema_svcs',  $post_id );
	$emit_area    = (bool) get_field( 'loc_schema_area',  $post_id );

	// Use ACF defaults (true_false with default_value=1) â€” treat 0 only when explicitly unchecked.
	// If field has never been saved it returns '' â€” treat that as true.
	$emit_city  = ( '' === get_field( 'loc_schema_city', $post_id ) )  ? true : $emit_city;
	$emit_place = ( '' === get_field( 'loc_schema_place', $post_id ) ) ? true : $emit_place;
	$emit_svcs  = ( '' === get_field( 'loc_schema_svcs', $post_id ) )  ? true : $emit_svcs;
	$emit_area  = ( '' === get_field( 'loc_schema_area', $post_id ) )  ? true : $emit_area;

	$sameas_raw  = get_field( 'loc_sameas', $post_id );
	$sameas_urls = array();
	if ( is_array( $sameas_raw ) ) {
		foreach ( $sameas_raw as $row ) {
			if ( ! empty( $row['url'] ) ) {
				$sameas_urls[] = esc_url_raw( $row['url'] );
			}
		}
	}

	$area_served = array_filter( array( $city_name, $county_name ) );

	$provider_id  = rtrim( $biz_url, '/' ) . '/#provider';
	$webpage_id   = $page_url . '#webpage';
	$place_id     = $page_url . '#place';
	$admin_id     = $page_url . '#city';

	$graph = array();

	// WebPage node â€” always present.
	$webpage_node = array(
		'@type'       => 'WebPage',
		'@id'         => $webpage_id,
		'url'         => $page_url,
		'name'        => $page_title,
		'description' => $page_desc,
		'isPartOf'    => array( '@id' => home_url( '/' ) . '#website' ),
	);
	if ( $emit_place ) {
		$webpage_node['mainEntity'] = array( '@id' => $place_id );
	}
	$graph[] = $webpage_node;

	// Organization / provider reference node â€” always present.
	$provider_node = array(
		'@type' => 'Organization',
		'@id'   => $provider_id,
		'name'  => $biz_name,
		'url'   => $biz_url,
	);
	if ( ! empty( $sameas_urls ) ) {
		$provider_node['sameAs'] = $sameas_urls;
	}
	$graph[] = $provider_node;

	// -----------------------------------------------------------------
	// BRANCH / HYBRID with loc_emit_lb = true â†’ LocalBusiness
	// -----------------------------------------------------------------
	if ( in_array( $loc_mode, array( 'branch', 'hybrid' ), true ) && $emit_lb ) {
		$lb_id = $page_url . '#localbusiness';

		$lb_node = array(
			'@type' => 'FlooringStore',
			'@id'   => $lb_id,
			'name'  => $biz_name,
			'url'   => $page_url,
		);

		// Address.
		$addr_1    = trim( (string) get_field( 'loc_addr_1', $post_id ) );
		$addr_city = trim( (string) get_field( 'loc_addr_city', $post_id ) );
		$addr_zip  = trim( (string) get_field( 'loc_addr_zip', $post_id ) );
		$addr_2    = trim( (string) get_field( 'loc_addr_2', $post_id ) );

		if ( $addr_1 || $addr_city || $addr_zip ) {
			$post_addr = array(
				'@type'           => 'PostalAddress',
				'addressLocality' => $addr_city ?: $city_name,
				'addressRegion'   => $state_code,
				'postalCode'      => $addr_zip,
				'addressCountry'  => $country_code,
			);
			if ( $addr_1 ) {
				$post_addr['streetAddress'] = $addr_2 ? $addr_1 . ', ' . $addr_2 : $addr_1;
			}
			$lb_node['address'] = $post_addr;
		}

		// GeoCoordinates.
		$lat = get_field( 'loc_lat', $post_id );
		$lng = get_field( 'loc_lng', $post_id );
		if ( '' !== (string) $lat && '' !== (string) $lng ) {
			$lb_node['geo'] = array(
				'@type'     => 'GeoCoordinates',
				'latitude'  => (float) $lat,
				'longitude' => (float) $lng,
			);
		}

		// Opening hours.
		$hours_rows = get_field( 'loc_hours', $post_id );
		$ohs        = floor4u_build_opening_hours( (array) $hours_rows );
		if ( ! empty( $ohs ) ) {
			$lb_node['openingHoursSpecification'] = $ohs;
		}

		// areaServed on LocalBusiness if enabled.
		if ( $emit_area && ! empty( $area_served ) ) {
			$lb_node['areaServed'] = array_values( $area_served );
		}

		if ( ! empty( $sameas_urls ) ) {
			$lb_node['sameAs'] = $sameas_urls;
		}

		$graph[] = $lb_node;

		// For branch/hybrid, make the LB the canonical provider for service nodes.
		$provider_id = $lb_id;

	} else {
		// -----------------------------------------------------------------
		// SERVICE AREA (or branch/hybrid with emit_lb = false)
		// -----------------------------------------------------------------

		if ( $emit_city ) {
			$graph[] = array(
				'@type'            => 'City',
				'@id'              => $admin_id,
				'name'             => $city_name,
				'containedInPlace' => array(
					'@type' => 'AdministrativeArea',
					'name'  => $county_name,
				),
			);
		}

		if ( $emit_place ) {
			$place_node = array(
				'@type'            => 'Place',
				'@id'              => $place_id,
				'name'             => $city_name ?: $page_title,
				'containedInPlace' => array(
					'@type' => 'AdministrativeArea',
					'name'  => $county_name,
				),
			);
			if ( $emit_area && ! empty( $area_served ) ) {
				$place_node['areaServed'] = array_values( $area_served );
			}
			$graph[] = $place_node;
		}
	}

	// Service nodes â€” attached to both branches.
	if ( $emit_svcs ) {
		$svc_posts    = get_field( 'loc_svcs', $post_id );
		$service_nodes = floor4u_build_service_nodes( (array) $svc_posts, $provider_id, $area_served, $emit_area );
		if ( ! empty( $service_nodes ) ) {
			$graph = array_merge( $graph, $service_nodes );
		}
	}

	$schema = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);

	echo "\n" . '<script type="application/ld+json">'
		. wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT )
		. '</script>' . "\n";
}

	/**
	 * V2 + V3 location schema generator.
	 *
	 * Strategy notes:
	 * 1) GBP sync: google_place_id in @id links website and GBP entity.
	 * 2) AI readiness: explicit services + geographies improve local retrieval.
	 * 3) sameAs: citation/profile URLs unify entity signals across platforms.
	 */
	function floor4u_generate_location_schema() {
		if ( ! is_singular( 'location' ) ) {
			return;
		}

		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return;
		}

		$loc_mode = (string) get_field( 'loc_mode', $post_id );
		if ( 'service_area' === $loc_mode ) {
			$loc_mode = 'city';
		}
		$emit_lb = (bool) get_field( 'loc_emit_lb', $post_id );

		$business_name  = trim( (string) get_field( 'business_name', $post_id ) );
		$business_name  = $business_name ?: trim( (string) get_field( 'loc_biz_name', $post_id ) );
		$business_name  = $business_name ?: 'The Floor 4 U';
		$business_type  = trim( (string) get_field( 'schema_business_type', $post_id ) ) ?: 'FlooringStore';
		$phone_raw      = trim( (string) get_field( 'phone_number_raw', $post_id ) );
		$phone_raw      = $phone_raw ?: trim( (string) get_field( 'loc_biz_phone_raw', $post_id ) );
		$street_address = trim( (string) get_field( 'street_address', $post_id ) );
		$street_address = $street_address ?: trim( (string) get_field( 'loc_addr_1', $post_id ) );
		$address_city   = trim( (string) get_field( 'address_city', $post_id ) );
		$address_city   = $address_city ?: trim( (string) get_field( 'loc_city_name', $post_id ) );
		$address_state  = trim( (string) get_field( 'address_state', $post_id ) );
		$address_state  = $address_state ?: trim( (string) get_field( 'loc_state_code', $post_id ) );
		$address_state  = $address_state ?: 'IL';
		$postal_code    = trim( (string) get_field( 'postal_code', $post_id ) );
		$postal_code    = $postal_code ?: trim( (string) get_field( 'loc_addr_zip', $post_id ) );
		$price_range    = trim( (string) get_field( 'price_range', $post_id ) ) ?: '$$';
		$place_id       = trim( (string) get_field( 'google_place_id', $post_id ) );
		$place_id       = $place_id ?: trim( (string) get_field( 'loc_place_id', $post_id ) );
		$headline       = trim( (string) get_field( 'location_headline', $post_id ) );
		$headline       = $headline ?: trim( (string) get_field( 'loc_h1', $post_id ) );
		$latitude       = get_field( 'latitude', $post_id );
		$longitude      = get_field( 'longitude', $post_id );
		if ( '' === (string) $latitude ) {
			$latitude = get_field( 'loc_lat', $post_id );
		}
		if ( '' === (string) $longitude ) {
			$longitude = get_field( 'loc_lng', $post_id );
		}
		$county_name = trim( (string) get_field( 'loc_county_name', $post_id ) );

		$page_url = get_permalink( $post_id );
		$entity_id = $place_id
			? 'https://www.google.com/maps/search/?api=1&query=Google&query_place_id=' . rawurlencode( $place_id )
			: $page_url;

		$name = $business_name;
		if ( $headline ) {
			$name .= ' - ' . $headline;
		}

		$schema = array(
			'@context'   => 'https://schema.org',
			'@type'      => $business_type,
			'@id'        => $entity_id,
			'name'       => $name,
			'url'        => $page_url,
			'telephone'  => $phone_raw,
			'priceRange' => $price_range,
		);

		$area_names = array();
		if ( $address_city ) {
			$area_names[] = $address_city;
		}
		if ( $county_name ) {
			$area_names[] = $county_name;
		}
		$terms = get_the_terms( $post_id, 'community' );
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$area_names[] = $term->name;
			}
		}
		$area_names = array_values( array_unique( array_filter( $area_names ) ) );
		if ( ! empty( $area_names ) ) {
			$schema['areaServed'] = $area_names;
		}

		$emit_base_schema = true;

		// City mode: emit non-LocalBusiness graph nodes only.
		if ( 'city' === $loc_mode ) {
			$graph = array(
				array(
					'@type' => 'WebPage',
					'@id'   => $page_url . '#webpage',
					'url'   => $page_url,
					'name'  => get_the_title( $post_id ),
				),
				array(
					'@type' => 'AdministrativeArea',
					'@id'   => $page_url . '#city',
					'name'  => $address_city,
				),
				array(
					'@type'            => 'Place',
					'@id'              => $page_url . '#place',
					'name'             => $address_city,
					'containedInPlace' => array( '@id' => $page_url . '#city' ),
				),
			);

			$featured_services = get_field( 'featured_services', $post_id );
			if ( empty( $featured_services ) ) {
				$featured_services = get_field( 'loc_svcs', $post_id );
			}
			if ( is_array( $featured_services ) ) {
				foreach ( $featured_services as $svc ) {
					$svc_post = $svc instanceof WP_Post ? $svc : get_post( (int) $svc );
					if ( $svc_post ) {
						$graph[] = array(
							'@type' => 'Service',
							'@id'   => get_permalink( $svc_post ) . '#service',
							'name'  => get_the_title( $svc_post ),
							'url'   => get_permalink( $svc_post ),
						);
					}
				}
			}

			echo "\n" . '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
			$emit_base_schema = false;
		} elseif ( in_array( $loc_mode, array( 'branch', 'hybrid' ), true ) && $emit_lb ) {
			$schema['address'] = array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => $street_address,
				'addressLocality' => $address_city,
				'addressRegion'   => $address_state,
				'postalCode'      => $postal_code,
				'addressCountry'  => 'US',
			);
			if ( '' !== (string) $latitude && '' !== (string) $longitude ) {
				$schema['geo'] = array(
					'@type'     => 'GeoCoordinates',
					'latitude'  => (float) $latitude,
					'longitude' => (float) $longitude,
				);
			}
			$hours = floor4u_build_opening_hours( (array) get_field( 'loc_hours', $post_id ) );
			if ( ! empty( $hours ) ) {
				$schema['openingHoursSpecification'] = $hours;
			}

			$featured_services = get_field( 'featured_services', $post_id );
			if ( empty( $featured_services ) ) {
				$featured_services = get_field( 'loc_svcs', $post_id );
			}
			if ( is_array( $featured_services ) && ! empty( $featured_services ) ) {
				$catalog_items = array();
				foreach ( $featured_services as $svc ) {
					$svc_post = $svc instanceof WP_Post ? $svc : get_post( (int) $svc );
					if ( ! $svc_post ) {
						continue;
					}
					$catalog_items[] = array(
						'@type'       => 'Service',
						'name'        => get_the_title( $svc_post ),
						'description' => wp_strip_all_tags( (string) $svc_post->post_excerpt ?: (string) $svc_post->post_content ),
					);
				}
				if ( ! empty( $catalog_items ) ) {
					$schema['hasOfferCatalog'] = array(
						'@type'           => 'OfferCatalog',
						'name'            => 'Flooring and Remodeling Services',
						'itemListElement' => $catalog_items,
					);
				}
			}
		}

		if ( $emit_base_schema ) {
			$local_reviews = get_field( 'local_reviews', $post_id );
			if ( empty( $local_reviews ) ) {
				$local_reviews = get_field( 'loc_reviews', $post_id );
			}
			if ( is_array( $local_reviews ) && ! empty( $local_reviews ) ) {
				$reviews = array();
				foreach ( $local_reviews as $review ) {
					$reviewer_name = ! empty( $review['reviewer_name'] ) ? $review['reviewer_name'] : ( $review['name'] ?? '' );
					$review_text   = ! empty( $review['review_text'] ) ? $review['review_text'] : ( $review['body'] ?? '' );
					$review_rating = ! empty( $review['review_rating'] ) ? (int) $review['review_rating'] : (int) ( $review['rating'] ?? 0 );
					if ( ! $reviewer_name || ! $review_text ) {
						continue;
					}
					$reviews[] = array(
						'@type'      => 'Review',
						'author'     => array( '@type' => 'Person', 'name' => $reviewer_name ),
						'reviewBody' => wp_strip_all_tags( (string) $review_text ),
						'reviewRating' => array(
							'@type'       => 'Rating',
							'ratingValue' => max( 1, min( 5, $review_rating ) ),
						),
					);
				}
				if ( ! empty( $reviews ) ) {
					$schema['review'] = $reviews;
				}
			}

			$same_as_rows = get_field( 'schema_same_as_links', $post_id );
			if ( empty( $same_as_rows ) ) {
				$same_as_rows = get_field( 'loc_sameas', $post_id );
			}
			if ( is_array( $same_as_rows ) ) {
				$links = array();
				foreach ( $same_as_rows as $row ) {
					$url = ! empty( $row['same_as_url'] ) ? $row['same_as_url'] : ( $row['url'] ?? '' );
					if ( $url ) {
						$links[] = esc_url_raw( $url );
					}
				}
				if ( ! empty( $links ) ) {
					$schema['sameAs'] = array_values( array_unique( $links ) );
				}
			}

			echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
		}

		$local_faqs = get_field( 'local_faqs', $post_id );
		if ( empty( $local_faqs ) ) {
			$local_faqs = get_field( 'loc_faqs', $post_id );
		}
		if ( is_array( $local_faqs ) && ! empty( $local_faqs ) ) {
			$faq_entities = array();
			foreach ( $local_faqs as $faq ) {
				$question = ! empty( $faq['faq_question'] ) ? $faq['faq_question'] : ( $faq['q'] ?? '' );
				$answer   = ! empty( $faq['faq_answer'] ) ? $faq['faq_answer'] : ( $faq['a'] ?? '' );
				if ( ! $question || ! $answer ) {
					continue;
				}
				$faq_entities[] = array(
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( (string) $question ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => wp_kses_post( $answer ),
					),
				);
			}
			if ( ! empty( $faq_entities ) ) {
				$faq_schema = array(
					'@context'    => 'https://schema.org',
					'@type'       => 'FAQPage',
					'mainEntity'  => $faq_entities,
				);
				echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
			}
		}
	}
	add_action( 'wp_head', 'floor4u_generate_location_schema', 30 );

/**
 * Output JSON-LD schema for Service single pages.
 * Emits WebPage + Service + provider reference.
 */
function floor4u_output_service_schema() {
	if ( ! is_singular( 'service' ) ) {
		return;
	}
	$post_id = get_the_ID();
	if ( ! $post_id ) {
		return;
	}

	$emit = get_field( 'svc_schema', $post_id );
	// Treat unsaved (empty string) as true.
	if ( '' !== (string) $emit && ! $emit ) {
		return;
	}

	$page_url    = (string) get_permalink( $post_id );
	$page_title  = (string) get_the_title( $post_id );
	$provider_url = apply_filters( 'floor4u_schema_provider_url', home_url( '/' ) );
	$provider_id  = rtrim( $provider_url, '/' ) . '/#provider';

	$svc_type = trim( (string) get_field( 'svc_type', $post_id ) ) ?: 'Service';

	$graph = array(
		array(
			'@type'       => 'WebPage',
			'@id'         => $page_url . '#webpage',
			'url'         => $page_url,
			'name'        => $page_title,
			'description' => wp_strip_all_tags( (string) get_field( 'svc_summary', $post_id ) ),
			'isPartOf'    => array( '@id' => home_url( '/' ) . '#website' ),
			'mainEntity'  => array( '@id' => $page_url . '#service' ),
		),
		array(
			'@type'    => 'Service',
			'@id'      => $page_url . '#service',
			'name'     => get_field( 'svc_name', $post_id ) ?: $page_title,
			'url'      => $page_url,
			'serviceType' => $svc_type,
			'provider' => array( '@id' => $provider_id ),
		),
		array(
			'@type' => 'Organization',
			'@id'   => $provider_id,
			'name'  => apply_filters( 'floor4u_schema_provider_name', 'Floor 4U' ),
			'url'   => $provider_url,
		),
	);

	echo "\n" . '<script type="application/ld+json">'
		. wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
		. '</script>' . "\n";
}
add_action( 'wp_head', 'floor4u_output_service_schema', 30 );

/**
 * Output JSON-LD schema for Project single pages.
 * Emits WebPage + CreativeWork + related service references.
 */
function floor4u_output_project_schema() {
	if ( ! is_singular( 'project' ) ) {
		return;
	}
	$post_id = get_the_ID();
	if ( ! $post_id ) {
		return;
	}

	$emit = get_field( 'prj_schema', $post_id );
	if ( '' !== (string) $emit && ! $emit ) {
		return;
	}

	$page_url    = (string) get_permalink( $post_id );
	$page_title  = (string) get_the_title( $post_id );
	$provider_id = rtrim( apply_filters( 'floor4u_schema_provider_url', home_url( '/' ) ), '/' ) . '/#provider';

	$work_node = array(
		'@type'       => 'CreativeWork',
		'@id'         => $page_url . '#project',
		'name'        => get_field( 'prj_title', $post_id ) ?: $page_title,
		'description' => wp_strip_all_tags( (string) get_field( 'prj_summary', $post_id ) ),
		'url'         => $page_url,
		'creator'     => array( '@id' => $provider_id ),
	);

	// Service references.
	$svc_posts = get_field( 'prj_svcs', $post_id );
	if ( is_array( $svc_posts ) && ! empty( $svc_posts ) ) {
		$svc_refs = array();
		foreach ( $svc_posts as $sp ) {
			$sp = $sp instanceof WP_Post ? $sp : get_post( (int) $sp );
			if ( $sp ) {
				$svc_refs[] = array( '@id' => get_permalink( $sp ) . '#service' );
			}
		}
		if ( ! empty( $svc_refs ) ) {
			$work_node['about'] = $svc_refs;
		}
	}

	// Location reference.
	$loc_posts = get_field( 'prj_loc', $post_id );
	if ( ! empty( $loc_posts ) ) {
		$loc_p = is_array( $loc_posts ) ? reset( $loc_posts ) : $loc_posts;
		$loc_p = $loc_p instanceof WP_Post ? $loc_p : get_post( (int) $loc_p );
		if ( $loc_p ) {
			$work_node['locationCreated'] = array( '@id' => get_permalink( $loc_p ) . '#place' );
		}
	}

	$prj_city = trim( (string) get_field( 'prj_city', $post_id ) );
	if ( $prj_city ) {
		$work_node['contentLocation'] = array( '@type' => 'City', 'name' => $prj_city );
	}

	$graph = array(
		array(
			'@type'      => 'WebPage',
			'@id'        => $page_url . '#webpage',
			'url'        => $page_url,
			'name'       => $page_title,
			'isPartOf'   => array( '@id' => home_url( '/' ) . '#website' ),
			'mainEntity' => array( '@id' => $page_url . '#project' ),
		),
		$work_node,
	);

	echo "\n" . '<script type="application/ld+json">'
		. wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
		. '</script>' . "\n";
}
add_action( 'wp_head', 'floor4u_output_project_schema', 30 );


// =============================================================================
// 08. DEV SEEDER â€” Remove before launch
// Trigger: /wp-admin/?seed_floor4u=1  (admin users only, runs once)
// =============================================================================


// =============================================================================
// DEV ONLY â€” Dummy Data Seeder
// Remove after testing or set FLOOR4U_ENABLE_SEEDER to false in wp-config.php
// Trigger URL: /wp-admin/?seed_floor4u=1
// =============================================================================

/**
 * Seeds test content for Local Authority System V3.
 *
 * Creates:
 *  - 5 Community taxonomy terms
 *  - 6 Service CPT posts
 *  - 8 Project CPT posts
 *  - 3 Location CPT posts (service_area / branch / hybrid)
 *
 * Triggered by visiting: /wp-admin/?seed_floor4u=1
 * Safe to run multiple times â€” checks for existing seed data first.
 * DELETE this function block when going live.
 */
function floor4u_seed_dummy_data() {
	// Only run in wp-admin and only when triggered.
	if ( ! is_admin() || ! isset( $_GET['seed_floor4u'] ) ) {
		return;
	}

	$force_seed = isset( $_GET['seed_floor4u_force'] ) && '1' === (string) $_GET['seed_floor4u_force'];

	// Simple capability check â€” admin only.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Not allowed.' );
	}

	// Prevent running twice in the same environment.
	if ( get_option( 'floor4u_seed_done' ) && ! $force_seed ) {
		wp_die( '<h2>Floor 4U Seeder</h2><p>Dummy data was already seeded. Delete the option <code>floor4u_seed_done</code> from the wp_options table to re-run.</p><p><a href="/wp-admin/">Back to Admin</a></p>' );
	}

	// -------------------------------------------------------------------------
	// 1. Community taxonomy terms
	// -------------------------------------------------------------------------
	$communities = array(
		'Briarcliff Estates',
		'Frankfort Square',
		'Lincoln Crossing',
		'Hickory Creek Village',
		'Old Town New Lenox',
	);

	$community_term_ids = array();
	foreach ( $communities as $community_name ) {
		$result = wp_insert_term( $community_name, 'community' );
		if ( ! is_wp_error( $result ) ) {
			$community_term_ids[] = $result['term_id'];
		} elseif ( isset( $result->error_data['term_exists'] ) ) {
			$community_term_ids[] = (int) $result->error_data['term_exists'];
		}
	}

	// -------------------------------------------------------------------------
	// 2. Service CPT posts
	// -------------------------------------------------------------------------
	$services_data = array(
		array(
			'title'   => 'LVP Installation',
			'excerpt' => 'Professional luxury vinyl plank installation for kitchens, bathrooms, and basements throughout the Chicago south suburbs.',
		),
		array(
			'title'   => 'Hardwood Floor Installation',
			'excerpt' => 'Solid and engineered hardwood installation by certified flooring specialists serving Will County.',
		),
		array(
			'title'   => 'Hardwood Floor Refinishing',
			'excerpt' => 'Restore worn hardwood floors to like-new condition with our dust-minimized sanding and refinishing service.',
		),
		array(
			'title'   => 'Tile & Stone Installation',
			'excerpt' => 'Ceramic, porcelain, and natural stone tile installation for floors, showers, and backsplashes.',
		),
		array(
			'title'   => 'Carpet Installation',
			'excerpt' => 'Full-service carpet installation including removal, disposal, and tack-strip replacement.',
		),
		array(
			'title'   => 'Laminate Flooring Installation',
			'excerpt' => 'Cost-effective laminate flooring installation with a wide selection of wood-look and stone-look options.',
		),
	);

	$service_post_ids = array();
	foreach ( $services_data as $service ) {
		$existing = get_page_by_title( $service['title'], OBJECT, 'service' );
		if ( $existing ) {
			$service_post_ids[] = $existing->ID;
			continue;
		}
		$post_id = wp_insert_post( array(
			'post_title'   => $service['title'],
			'post_excerpt' => $service['excerpt'],
			'post_content' => '<p>' . $service['excerpt'] . ' Contact us today for a free in-home estimate.</p>',
			'post_status'  => 'publish',
			'post_type'    => 'service',
		) );
		if ( ! is_wp_error( $post_id ) ) {
			$service_post_ids[] = $post_id;
		}
	}

	// -------------------------------------------------------------------------
	// 3. Project CPT posts
	// -------------------------------------------------------------------------
	$projects_data = array(
		array(
			'title' => 'LVP Install â€” Frankfort Ranch Home',
			'city'  => 'Frankfort',
			'content' => 'Installed 1,200 sq ft of waterproof LVP throughout the main level of a ranch-style home in Frankfort IL. Subfloor leveling included.',
		),
		array(
			'title' => 'Hardwood Refinish â€” New Lenox Colonial',
			'city'  => 'New Lenox',
			'content' => 'Refinished original red oak hardwood floors in a 1990s colonial. Two coats of satin water-based polyurethane applied.',
		),
		array(
			'title' => 'Tile Shower â€” Frankfort Master Bath',
			'city'  => 'Frankfort',
			'content' => 'Full master bathroom tile installation including a custom 36"x72" porcelain tile shower with niche.',
		),
		array(
			'title' => 'Carpet Replacement â€” Mokena Townhome',
			'city'  => 'Mokena',
			'content' => 'Replaced builder-grade carpet in all three bedrooms and staircase of a townhome in Mokena IL.',
		),
		array(
			'title' => 'LVP â€” Finished Basement in New Lenox',
			'city'  => 'New Lenox',
			'content' => 'Installed 850 sq ft of rigid-core LVP in a finished basement. Included transition strips to adjacent tile.',
		),
		array(
			'title' => 'Hardwood Install â€” Manhattan New Build',
			'city'  => 'Manhattan',
			'content' => 'Installed 2,000 sq ft of 4" white oak hardwood in new construction in Manhattan IL.',
		),
		array(
			'title' => 'LVP Kitchen & Dining â€” Tinley Park',
			'city'  => 'Tinley Park',
			'content' => 'Replaced vinyl tile with continuous LVP run through kitchen and dining room, no visible seam at threshold.',
		),
		array(
			'title' => 'Laminate Stairs & Hallway â€” Orland Park',
			'city'  => 'Orland Park',
			'content' => 'Laminate stair treads, risers, and hallway to match existing main level flooring.',
		),
	);

	$project_post_ids = array();
	foreach ( $projects_data as $project ) {
		$existing = get_page_by_title( $project['title'], OBJECT, 'project' );
		if ( $existing ) {
			$project_post_ids[] = $existing->ID;
			continue;
		}
		$post_id = wp_insert_post( array(
			'post_title'   => $project['title'],
			'post_content' => $project['content'],
			'post_status'  => 'publish',
			'post_type'    => 'project',
		) );
		if ( ! is_wp_error( $post_id ) ) {
			// Store the city on each project so the template can display it.
			update_field( 'field_prj_city', $project['city'], $post_id );
			update_field( 'field_prj_status', 'published', $post_id );
			$project_post_ids[] = $post_id;
		}
	}

	// -------------------------------------------------------------------------
	// 4. Location CPT posts
	// -------------------------------------------------------------------------
	$locations_data = array(
		array(
			'title'       => 'Flooring Contractor in Frankfort IL',
			'loc_mode'    => 'service_area',
			'emit_lb'     => 0,
			'city'        => 'Frankfort',
			'postal'      => '60423',
			'lat'         => '',
			'lng'         => '',
			'county'      => 'Will County',
			'headline'    => 'Professional Flooring Contractor in Frankfort IL',
			'hero_intro'  => 'Floor 4U serves Frankfort and surrounding communities with expert hardwood, LVP, tile, and carpet installation. Family-owned and locally operated.',
			'intro_prose' => '<p>Frankfort IL is one of Will County\'s most desirable communities, known for its historic downtown, top-rated schools, and established neighborhoods like <strong>Briarcliff Estates</strong> and <strong>Frankfort Square</strong>. Floor 4U has completed dozens of flooring projects throughout the village â€” from LVP installs in new construction on Laraway Road to hardwood refinishing in older homes near the Center Road business district.</p><p>Whether you\'re remodeling your kitchen, upgrading a basement, or refinishing original hardwood, our team serves Frankfort homeowners with free in-home estimates and same-week scheduling.</p>',
			'landmarks'   => "Near Lincoln-Way East High School\nClose to Frankfort Park District Recreation Center\nMinutes from the Frankfort Historic Downtown District",
			'directions'  => "Located in the Will County service area\nEasily accessible from US-45 and IL-30\nServing Frankfort from our base in the southwest suburbs",
			'services'    => array( 0, 1, 2 ),
			'projects'    => array( 0, 1, 2 ),
			'communities' => array( 0, 1 ),
		),
		array(
			'title'       => 'Floor 4U Showroom â€” New Lenox IL',
			'loc_mode'    => 'branch',
			'emit_lb'     => 1,
			'city'        => 'New Lenox',
			'postal'      => '60451',
			'lat'         => 41.5087,
			'lng'         => -87.9870,
			'county'      => 'Will County',
			'headline'    => 'Floor 4U â€” New Lenox IL Flooring Showroom',
			'hero_intro'  => 'Visit our New Lenox showroom to see hundreds of flooring samples in person. Hardwood, LVP, tile, carpet, and laminate â€” all in one location.',
			'intro_prose' => '<p>Our New Lenox showroom on Route 30 serves homeowners across the southwest suburbs. Browse hundreds of in-stock flooring samples including waterproof LVP, engineered hardwood, porcelain tile, and premium carpet. Our design consultants are on-site Monday through Friday to help you choose the perfect floor for every room.</p>',
			'landmarks'   => "Adjacent to New Lenox Commons\nNear Lincoln-Way Central High School\nOff US-30 (Lincoln Highway)",
			'directions'  => "Located directly on US-30 (Lincoln Highway)\n2 miles east of I-355 exit 5\nEasily visible from Route 30 westbound",
			'services'    => array( 0, 1, 3 ),
			'projects'    => array( 1, 4, 5 ),
			'communities' => array( 2, 4 ),
		),
		array(
			'title'       => 'Flooring Services in Mokena IL',
			'loc_mode'    => 'hybrid',
			'emit_lb'     => 1,
			'city'        => 'Mokena',
			'postal'      => '60448',
			'lat'         => 41.5281,
			'lng'         => -87.8848,
			'county'      => 'Will County',
			'headline'    => 'Expert Flooring Installation in Mokena IL',
			'hero_intro'  => 'Floor 4U serves Mokena homeowners with premium flooring installation and a local showroom nearby. Schedule a free estimate today.',
			'intro_prose' => '<p>Mokena is one of Will County\'s fastest-growing communities, and Floor 4U has been a trusted flooring partner for Mokena homeowners for years. From new construction installs in <strong>Hickory Creek Village</strong> to full hardwood refinishing projects near Old Frankfort Pike, our crews know the Mokena market.</p>',
			'landmarks'   => "Near Mokena Community Park\nClose to Mokena Elementary School District 159\nOff Wolf Road near US-30 interchange",
			'directions'  => "Accessible from I-80 exit 140B (Laraway Road)\nSouth of US-30 off Wolf Road\nServes all of Mokena and surrounding areas",
			'services'    => array( 0, 4, 5 ),
			'projects'    => array( 3, 6, 7 ),
			'communities' => array( 3, 0 ),
		),
	);

	// Add more locations so the seeder creates 10 test records total.
	$extra_locations = array(
		array( 'city' => 'Tinley Park', 'postal' => '60477', 'county' => 'Cook County', 'mode' => 'service_area', 'lat' => '', 'lng' => '' ),
		array( 'city' => 'Orland Park', 'postal' => '60462', 'county' => 'Cook County', 'mode' => 'branch', 'lat' => 41.6303, 'lng' => -87.8539 ),
		array( 'city' => 'Manhattan', 'postal' => '60442', 'county' => 'Will County', 'mode' => 'service_area', 'lat' => '', 'lng' => '' ),
		array( 'city' => 'Joliet', 'postal' => '60435', 'county' => 'Will County', 'mode' => 'hybrid', 'lat' => 41.5250, 'lng' => -88.0817 ),
		array( 'city' => 'Homer Glen', 'postal' => '60491', 'county' => 'Will County', 'mode' => 'service_area', 'lat' => '', 'lng' => '' ),
		array( 'city' => 'Lockport', 'postal' => '60441', 'county' => 'Will County', 'mode' => 'branch', 'lat' => 41.5895, 'lng' => -88.0578 ),
		array( 'city' => 'Palos Park', 'postal' => '60464', 'county' => 'Cook County', 'mode' => 'hybrid', 'lat' => 41.6673, 'lng' => -87.8359 ),
	);

	foreach ( $extra_locations as $idx => $row ) {
		$locations_data[] = array(
			'title'       => 'Flooring Services in ' . $row['city'] . ' IL',
			'loc_mode'    => $row['mode'],
			'emit_lb'     => in_array( $row['mode'], array( 'branch', 'hybrid' ), true ) ? 1 : 0,
			'city'        => $row['city'],
			'postal'      => $row['postal'],
			'lat'         => $row['lat'],
			'lng'         => $row['lng'],
			'county'      => $row['county'],
			'headline'    => 'Top Rated Flooring Contractor in ' . $row['city'] . ' IL',
			'hero_intro'  => 'Floor 4U serves ' . $row['city'] . ' with LVP, hardwood, tile, and carpet installation backed by local crews and clean job sites.',
			'intro_prose' => '<p>Homeowners in ' . $row['city'] . ' trust Floor 4U for straightforward pricing, clear timelines, and expert installation. We serve neighborhoods across ' . $row['county'] . ' and surrounding communities.</p>',
			'landmarks'   => "Near downtown " . $row['city'] . "\nClose to major schools and parks\nServing residential neighborhoods throughout the area",
			'directions'  => "Easy access from local arterials and highways\nServing all of " . $row['city'] . " and nearby communities\nFast estimate scheduling available",
			'services'    => array( $idx % 6, ( $idx + 1 ) % 6, ( $idx + 2 ) % 6 ),
			'projects'    => array( $idx % 8, ( $idx + 2 ) % 8, ( $idx + 4 ) % 8 ),
			'communities' => array( $idx % 5, ( $idx + 1 ) % 5 ),
		);
	}

	$created_locations = 0;
	$target_locations  = count( $locations_data );

	foreach ( $locations_data as $loc ) {
		$existing = get_page_by_title( $loc['title'], OBJECT, 'location' );
		if ( $existing ) {
			continue;
		}

		$loc_post_id = wp_insert_post( array(
			'post_title'   => $loc['title'],
			'post_content' => '',
			'post_status'  => 'publish',
			'post_type'    => 'location',
		) );

		if ( is_wp_error( $loc_post_id ) ) {
			continue;
		}

		$created_locations++;

		// ACF field updates use field keys for reliability.
		update_field( 'field_loc_mode',         $loc['loc_mode'],    $loc_post_id );
		update_field( 'field_loc_emit_lb',      $loc['emit_lb'],     $loc_post_id );
		update_field( 'field_loc_h1',           $loc['headline'],    $loc_post_id );
		update_field( 'field_loc_intro',        $loc['hero_intro'],  $loc_post_id );
		update_field( 'field_loc_city_name',    $loc['city'],        $loc_post_id );
		update_field( 'field_loc_addr_zip',     $loc['postal'],      $loc_post_id );
		update_field( 'field_loc_area_summary', $loc['intro_prose'], $loc_post_id );
		update_field( 'field_loc_highways',     $loc['directions'],  $loc_post_id );
		update_field( 'field_loc_county_name',  $loc['county'],      $loc_post_id );
		update_field( 'field_loc_status',       'active',            $loc_post_id );
		update_field( 'field_loc_biz_name',     'Floor 4U',          $loc_post_id );
		update_field( 'field_loc_biz_phone',    '(815) 555-0100',    $loc_post_id );
		update_field( 'field_loc_biz_phone_raw', '+18155550100',     $loc_post_id );
		update_field( 'field_loc_biz_url',      home_url( '/' ),     $loc_post_id );

		// V3 simplified field set (for Cornerstone bindings).
		$loc_mode_v3 = 'service_area' === $loc['loc_mode'] ? 'city' : $loc['loc_mode'];
		update_field( 'field_loc_mode_v3',      $loc_mode_v3,        $loc_post_id );
		update_field( 'field_loc_emit_lb_v3',   $loc['emit_lb'],     $loc_post_id );
		update_field( 'field_location_headline', $loc['headline'],    $loc_post_id );
		update_field( 'field_hero_intro',       wp_strip_all_tags( $loc['hero_intro'] ), $loc_post_id );
		update_field( 'field_address_city',     $loc['city'],        $loc_post_id );
		update_field( 'field_postal_code',      $loc['postal'],      $loc_post_id );
		update_field( 'field_google_place_id',  sanitize_title( $loc['city'] ) . '-place-id', $loc_post_id );
		update_field( 'field_city_intro_prose', $loc['intro_prose'], $loc_post_id );
		update_field( 'field_local_landmarks',  $loc['landmarks'],   $loc_post_id );
		update_field( 'field_highway_directions', $loc['directions'], $loc_post_id );
		update_field( 'field_location_map_embed', '<iframe src="https://www.google.com/maps?q=' . rawurlencode( $loc['city'] . ' IL' ) . '&output=embed" width="600" height="350" style="border:0;" loading="lazy"></iframe>', $loc_post_id );

		// Seed local landmarks repeater.
		if ( ! empty( $loc['landmarks'] ) ) {
			$lm_rows = array();
			foreach ( explode( "\n", $loc['landmarks'] ) as $lm ) {
				$lm = trim( $lm );
				if ( $lm ) {
					$lm_rows[] = array( 'name' => $lm, 'type' => 'landmark' );
				}
			}
			update_field( 'field_loc_landmarks_local', $lm_rows, $loc_post_id );
		}

		if ( '' !== (string) $loc['lat'] ) {
			update_field( 'field_loc_lat', $loc['lat'], $loc_post_id );
			update_field( 'field_loc_lng', $loc['lng'], $loc_post_id );
			update_field( 'field_latitude', $loc['lat'], $loc_post_id );
			update_field( 'field_longitude', $loc['lng'], $loc_post_id );
		}

		// Map service indexes to actual post IDs.
		$linked_services = array();
		foreach ( $loc['services'] as $idx ) {
			if ( isset( $service_post_ids[ $idx ] ) ) {
				$linked_services[] = $service_post_ids[ $idx ];
			}
		}
		if ( ! empty( $linked_services ) ) {
			update_field( 'field_loc_svcs', $linked_services, $loc_post_id );
			update_field( 'field_loc_svc_primary', array( $linked_services[0] ), $loc_post_id );
			update_field( 'field_featured_services', $linked_services, $loc_post_id );
		}

		// Map project indexes to actual post IDs.
		$linked_projects = array();
		foreach ( $loc['projects'] as $idx ) {
			if ( isset( $project_post_ids[ $idx ] ) ) {
				$linked_projects[] = $project_post_ids[ $idx ];
			}
		}
		if ( ! empty( $linked_projects ) ) {
			update_field( 'field_loc_prjs', $linked_projects, $loc_post_id );
			update_field( 'field_featured_projects', $linked_projects, $loc_post_id );
		}

		// Assign community terms.
		$linked_communities = array();
		foreach ( $loc['communities'] as $idx ) {
			if ( isset( $community_term_ids[ $idx ] ) ) {
				$linked_communities[] = $community_term_ids[ $idx ];
			}
		}
		if ( ! empty( $linked_communities ) ) {
			wp_set_post_terms( $loc_post_id, $linked_communities, 'community' );
			update_field( 'field_loc_communities', $linked_communities, $loc_post_id );
			update_field( 'field_communities_served', $linked_communities, $loc_post_id );
		}

		// V3 repeater content for reviews + FAQs + sameAs links.
		update_field(
			'field_local_reviews',
			array(
				array(
					'reviewer_name' => 'Sarah M.',
					'review_text'   => 'Great communication, clean install, and excellent result.',
					'review_rating' => 5,
					'featured'      => 1,
					'date'          => '20260301',
				),
				array(
					'reviewer_name' => 'John T.',
					'review_text'   => 'Team was on time and finished exactly when promised.',
					'review_rating' => 5,
					'featured'      => 0,
					'date'          => '20260215',
				),
			),
			$loc_post_id
		);

		update_field(
			'field_local_faqs',
			array(
				array(
					'faq_question' => 'How long does LVP installation take in ' . $loc['city'] . '?',
					'faq_answer'   => '<p>Most single-floor installs are completed in 1-2 days depending on prep and furniture moves.</p>',
					'featured'     => 1,
				),
				array(
					'faq_question' => 'Do you provide free estimates?',
					'faq_answer'   => '<p>Yes. We provide no-obligation in-home estimates with material and labor options.</p>',
					'featured'     => 0,
				),
			),
			$loc_post_id
		);

		update_field(
			'field_schema_same_as_links',
			array(
				array( 'same_as_url' => 'https://www.houzz.com/pro/floor4u' ),
				array( 'same_as_url' => 'https://www.yelp.com/biz/floor-4-u' ),
			),
			$loc_post_id
		);
	}

	// Mark seeder as done so it won't run a second time.
	update_option( 'floor4u_seed_done', true );

	wp_die(
		'<h2 style="font-family:sans-serif">&#10003; Floor 4U Dummy Data Seeded</h2>
		<ul style="font-family:sans-serif;line-height:2">
			<li>5 Community terms created</li>
			<li>6 Service posts created</li>
			<li>8 Project posts created</li>
			<li>' . (int) $target_locations . ' Location records targeted</li>
			<li>' . (int) $created_locations . ' new Location records created in this run</li>
		</ul>
		<p style="font-family:sans-serif"><a href="/wp-admin/">&#8592; Back to Admin</a></p>'
	);
}
add_action( 'admin_init', 'floor4u_seed_dummy_data' );

