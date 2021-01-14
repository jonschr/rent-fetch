<?php

add_action('acf/init', 'apartment_sync_add_settings');
function apartment_sync_add_settings() {

    // Check function exists.
    if( function_exists( 'acf_add_options_page' ) ) {

        // Add parent.
        $parent = acf_add_options_page(array(
            'page_title'  => __( 'Apartment Sync Settings' ),
            'menu_title'  => __( 'Apartment Sync' ),
            'redirect'    => false,
            'icon_url' => 'dashicons-update', 
        ));

        // // Add sub page.
        $child = acf_add_options_page(array(
            'page_title'  => __('Child Settings'),
            'menu_title'  => __('Child Settings'),
            'parent_slug' => $parent['menu_slug'],
        ));
    }
}