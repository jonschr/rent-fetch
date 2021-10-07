<?php

/**
  * Appends the specified taxonomy term to the incoming post object. If 
 * the term doesn't already exist in the database, it will be created.
 *
 * @param    WP_Post    $post        The post to which we're adding the taxonomy term.
 * @param    string     $value       The name of the taxonomy term
 * @param    string     $taxonomy    The name of the taxonomy.
 * @access   private
 * @since    1.0.0
 */

function rentfetch_set_post_term( $post_id, $value, $taxonomy ) {

	$term = term_exists( $value, $taxonomy );
    $slug = strtolower( str_ireplace( ' ', '-', $value ) );

	// If the taxonomy doesn't exist, then we create it
	if ( 0 === $term || null === $term ) {
        
		$term = wp_insert_term(
			$value,
			$taxonomy,
			array(
				'slug' => $slug,
			)
		);
        
	}
    
    $term_id = intval( $term['term_id'] );
    $post_id = intval( $post_id );
            
	// Then we can set the taxonomy
	$term_taxonomy_ids = wp_set_object_terms( $post_id, $term_id, $taxonomy, true );
    
    // var_dump( $term_taxonomy_ids );

}