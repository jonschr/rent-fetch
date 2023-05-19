<?php

// putting this on the init hook so that someone could remove it and readd it in whatever order they want
add_action( 'init', 'rentfetch_search_properties_map_filter_order' );
function rentfetch_search_properties_map_filter_order() {
    
    add_action( 'rentfetch_do_search_properties_map_filters', 'rentfetch_search_properties_map_filters_text_search' );
    add_action( 'rentfetch_do_search_properties_map_filters', 'rentfetch_search_properties_map_filters_beds' );
    add_action( 'rentfetch_do_search_properties_map_filters', 'rentfetch_search_properties_map_filters_baths' );
    add_action( 'rentfetch_do_search_properties_map_filters', 'rentfetch_search_properties_map_filters_neighborhoods' );
    add_action( 'rentfetch_do_search_properties_map_filters', 'rentfetch_search_properties_map_filters_property_types' );
    add_action( 'rentfetch_do_search_properties_map_filters', 'rentfetch_search_properties_map_filters_date' );
    add_action( 'rentfetch_do_search_properties_map_filters', 'rentfetch_search_properties_map_filters_price' );
    add_action( 'rentfetch_do_search_properties_map_filters', 'rentfetch_search_properties_map_filters_amenities' );
    add_action( 'rentfetch_do_search_properties_map_filters', 'rentfetch_search_properties_map_filters_pets' );
    
}