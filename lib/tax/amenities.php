<?php

add_action( 'init', 'four_star_register_amenities_taxonomy' );
function four_star_register_amenities_taxonomy() {
	register_taxonomy(
		'amenities',
		'properties',
		array(
			'label' 			=> __( 'Amenities' ),
			'rewrite' 		=> array( 'slug' => 'amenities' ),
			'hierarchical' 	=> true,
			'show_in_rest' 	=> true,
		)
	);
}