<?php

add_action( 'apartmentsync_do_floorplan_in_archive', 'apartmentsync_floorplan_in_archive', 10, 1 );
function apartmentsync_floorplan_in_archive( $floorplan ) {
    
    wp_enqueue_style( 'apartmentsync-floorplan-in-archive' );
    
    global $post;
    $post_id = $floorplan->ID;
    
    //* Grab the data
    $title = get_the_title( $post_id );
    $availability_url = get_field( 'availability_url', $post_id );
    $available_units = get_field( 'available_units', $post_id );
    $numberofbaths = get_field( 'baths', $post_id );
    $numberofbeds = get_field( 'beds', $post_id );
    $has_specials = get_field( 'has_specials', $post_id );
    $floorplan_id = get_field( 'floorplan_id', $post_id );
    $floorplan_image_url = get_field( 'floorplan_image_url', $post_id );
    $floorplan_image_name = get_field( 'floorplan_image_name', $post_id );
    $floorplan_image_alt_text = get_field( 'floorplan_image_alt_text', $post_id );
    $maximum_deposit = get_field( 'maximum_deposit', $post_id );
    $maximum_rent = get_field( 'maximum_rent', $post_id );
    $maximum_sqft = get_field( 'maximum_sqft', $post_id );
    $minimum_deposit = get_field( 'minimum_deposit', $post_id );
    $minimum_rent = get_field( 'minimum_rent', $post_id );
    $minimum_sqft = get_field( 'minimum_sqft', $post_id );
    $property_id = get_field( 'property_id', $post_id );
    $property_show_specials = get_field( 'property_show_specials', $post_id );
    $unit_type_mapping = get_field( 'unit_type_mapping', $post_id );
    $floorplan_source = get_post_meta( $post_id, 'floorplan_source', true );
    
    //* Markup
    if ( $title )
        printf( '<h3>%s</h3>', $title );
    
    // var_dump( $floorplan );
    
}