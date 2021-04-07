<?php

add_action( 'init', 'four_star_register_taxonomies' );
function four_star_register_taxonomies() {
	register_taxonomy(
		'area',
		'neighborhoods',
		array(
			'label' 			=> __( 'Area' ),
			'rewrite' 		=> array( 'slug' => 'area' ),
			'hierarchical' 	=> true,
			'show_in_rest' 	=> true,
		)
	);
}