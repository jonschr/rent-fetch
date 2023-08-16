<?php

add_shortcode( 'propertymap', 'rentfetch_propertymap' );
function rentfetch_propertymap( $atts ) {
    
    ob_start();
    
    // search scripts and styles
    wp_enqueue_style( 'rentfetch-search-properties-map' );
    
    // Localize the search filters general script, then enqueue that
    $search_options = array(
        'maximum_bedrooms_to_search' => intval( get_option( 'options_maximum_bedrooms_to_search' ) ),
    );
    wp_localize_script( 'rentfetch-search-filters-general', 'searchoptions', $search_options );
    wp_enqueue_script( 'rentfetch-search-filters-general' );
        
    wp_enqueue_script( 'rentfetch-search-properties-ajax' );
    wp_enqueue_script( 'rentfetch-search-properties-script' );
        
    // slick
    // wp_enqueue_script( 'rentfetch-slick-main-script' );
    // wp_enqueue_style( 'rentfetch-slick-main-styles' );
    // wp_enqueue_style( 'rentfetch-slick-main-theme' );
    
    // properties in archive
    wp_enqueue_style( 'rentfetch-properties-in-archive' );
    // wp_enqueue_script( 'rentfetch-property-images-slider-init' );
    
    // favorites
    // wp_enqueue_script( 'rentfetch-property-favorites-cookies' );
    // wp_enqueue_script( 'rentfetch-property-favorites' );
    
    // the map itself
    $key = apply_filters( 'rentfetch_get_google_maps_api_key', null );
    wp_enqueue_script( 'rentfetch-google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $key, array(), null, true );
            
    // Localize the google maps script, then enqueue that
    $maps_options = array(
        'json_style' => json_decode( get_option( 'options_google_maps_styles' ) ),
        'marker_url' => get_option( 'options_google_map_marker' ),
        'google_maps_default_latitude' => get_option( 'options_google_maps_default_latitude' ),
        'google_maps_default_longitude' => get_option( 'options_google_maps_default_longitude' ),
    );
    wp_localize_script( 'rentfetch-property-map', 'options', $maps_options );
    wp_enqueue_script( 'rentfetch-property-map');
    
    //* Start the form...
    echo '<div class="properties-search-wrap">';
        printf( '<form class="property-search-filters" action="%s/wp-admin/admin-ajax.php" method="POST" id="filter" style="opacity:0;">', site_url() );
        
            // This is the hook where we add all of our actions for the search filters
            do_action( 'rentfetch_do_search_properties_map_filters' );
                    
            // Buttons     
            printf( '<a href="%s" class="reset link-as-button">Reset</a>', get_permalink( get_the_ID() ) );
            echo '<button type="submit" style="display:none;">Search</button>';
            echo '<input type="hidden" name="action" value="propertysearch">';
            
        echo '</form>';
        
        //* Our container markup for the results
        echo '<div class="map-response-wrap">';
            echo '<div id="response"></div>';
            echo '<div id="map"></div>';
        echo '</div>';
    
    echo '</div>'; // .properties-search-wrap

    return ob_get_clean();
}

add_action( 'wp_ajax_propertysearch', 'rentfetch_filter_properties' ); // wp_ajax_{ACTION HERE} 
add_action( 'wp_ajax_nopriv_propertysearch', 'rentfetch_filter_properties' );
function rentfetch_filter_properties(){
            
    $floorplans = rentfetch_get_floorplans_array();
        
    $property_ids = array_keys( $floorplans );
    if ( empty( $property_ids ) )
        $property_ids = array( '1' ); // if there aren't any properties, we shouldn't find anything – empty array will let us find everything, so let's pass nonsense to make the search find nothing
        
    // set null for $properties_posts_per_page
    $properties_maximum_per_page = get_option( 'options_maximum_number_of_properties_to_show', 100 );
    
    $orderby = apply_filters( 'rentfetch_get_property_orderby', $orderby = 'menu_order' );
    $order = apply_filters( 'rentfetch_get_property_order', $order = 'ASC' );
    
    //* The base property query
    $property_args = array(
        'post_type' => 'properties',
        'posts_per_page' => $properties_maximum_per_page,
		'orderby' => $orderby,
		'order'	=> $order, // ASC or DESC
        'no_found_rows' => true,
	);
    
    //* Add all of our property IDs into the property search
    $property_args['meta_query'] = array(
        array(
            'key' => 'property_id',
            'value' => $property_ids,
        ),
    );
    
    $property_args = apply_filters( 'rentfetch_search_property_map_properties_query_args', $property_args );
    
    console_log( 'Property search args:' );
    console_log( $property_args );
    
    $propertyquery = new WP_Query( $property_args );
        
    if( $propertyquery->have_posts() ) {
        
        $count = 0;
        
        $numberofposts = $propertyquery->post_count;
        $total_posts = wp_count_posts('properties')->publish;
        
        printf( '<div class="count"><h2 class="post-count"><span class="number">%s</span> of %s properties selected</h2></div>', $numberofposts, $total_posts );
        
        echo '<div class="properties-loop">';

            while( $propertyquery->have_posts() ) {
                
                $propertyquery->the_post();
                
                $latitude = get_post_meta( get_the_ID(), 'latitude', true );
                $longitude = get_post_meta( get_the_ID(), 'longitude', true );
                
                // skip if there's no latitude or longitude
                if ( !$latitude || !$longitude )
                    continue;
                
                $class = implode( ' ', get_post_class() );
                                
                printf( 
                    '<div class="%s" data-latitude="%s" data-longitude="%s" data-id="%s" data-marker-id="%s">', 
                    $class, 
                    $latitude, 
                    $longitude,
                    $count, 
                    get_the_ID(), 
                );
                
                    echo '<div class="property-in-list">';
                        do_action( 'rentfetch_do_properties_each_list' );
                    echo '</div>';
                    echo '<div class="property-in-map" style="display:none;">';
                        do_action( 'rentfetch_do_properties_each_map' );
                    echo '</div>';
                
                echo '</div>'; // post_class
                
                $count++;
            
            } // endwhile
            
        echo '</div>';
        
		wp_reset_postdata();
        
    } else {
        echo 'No properties with availability were found matching the current search parameters.';
    }
         
	die();
}

function rentfetch_get_connected_properties_from_selected_neighborhoods() {
    
    //bail if there's no relationships installed
    if ( !class_exists( 'MB_Relationships_API' ) )
        return;
    
    $getneighborhoodsargs = array(
        'post_type' => 'neighborhoods',
        'posts_per_page' => '-1',
        'orderby' => 'name',
        'order' => 'DESC',
    );
        
    $neighborhoods = get_posts( $getneighborhoodsargs );
    $selected_neighborhoods = array();
        
    foreach ( $neighborhoods as $neighborhood ) {
                
        $neighborhood_name = $neighborhood->post_title;
        $neighborhood_id = $neighborhood->ID;
        
        if ( isset( $_POST['neighborhoods-' . $neighborhood_id ] ) && $_POST['neighborhoods-' . $neighborhood_id ] == 'on' ) {
            $neighborhood_id = sanitize_text_field( $neighborhood_id );            
            $selected_neighborhoods[] = $neighborhood_id;
        }
    }
    
    $properties = MB_Relationships_API::get_connected( [
        'id'   => 'properties_to_neighborhoods',
        'from' => $selected_neighborhoods,
    ] );
    
    $properties_connected_to_selected_neighborhoods = array();
    foreach ( $properties as $property ) {
        $properties_connected_to_selected_neighborhoods[] = intval( $property->ID );
    }
        
    array_unique( $properties_connected_to_selected_neighborhoods );
    // $properties_connected_to_selected_neighborhoods = implode( ',', $properties_connected_to_selected_neighborhoods );
    // var_dump( $properties_connected_to_selected_neighborhoods );
    
    return $properties_connected_to_selected_neighborhoods;
}