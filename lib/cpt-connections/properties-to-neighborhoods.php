<?php

add_action( 'mb_relationships_init', function() {
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
} );