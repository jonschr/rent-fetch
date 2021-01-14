<?php

add_action('acf/init', 'apartment_sync_add_settings');
function apartment_sync_add_settings() {

    // Check function exists.
    if( function_exists( 'acf_add_options_sub_page' ) ) {

        // Add parent.
        $parent = acf_add_options_sub_page(array(
            'title'  => __( 'Integration Settings' ),
            'parent'     => 'edit.php?post_type=floorplans',
            'capability' => 'manage_options'
        ));

    }
}