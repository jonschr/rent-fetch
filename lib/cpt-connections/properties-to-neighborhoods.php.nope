<?php

// connect properties and neighborhoods
// require_once( RENTFETCH_DIR . 'lib/cpt-connections/properties-to-neighborhoods.php' );
add_action( 'mb_relationships_init', 'rentfetch_connect_properties_to_neighborhoods' );
function rentfetch_connect_properties_to_neighborhoods() {
        
    MB_Relationships_API::register( [
        'id'   => 'properties_to_neighborhoods',
        'to'   => array(
            'object_type'  => 'post',
            'post_type'    => 'properties',
            'admin_column' => true, // THIS!
        ),
        'from' => array(
            'object_type'  => 'post',
            'post_type'    => 'neighborhoods',
            'admin_column' => true,  // THIS!
        ),
    ] );
}