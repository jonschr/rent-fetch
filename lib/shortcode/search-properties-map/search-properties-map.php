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
    wp_enqueue_script( 'rentfetch-slick-main-script' );
    wp_enqueue_style( 'rentfetch-slick-main-styles' );
    wp_enqueue_style( 'rentfetch-slick-main-theme' );
    
    // properties in archive
    wp_enqueue_style( 'rentfetch-properties-in-archive' );
    wp_enqueue_script( 'rentfetch-property-images-slider-init' );
    
    // favorites
    wp_enqueue_script( 'rentfetch-property-favorites-cookies' );
    wp_enqueue_script( 'rentfetch-property-favorites' );
    
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
            
    //* start with floorplans
	$floorplans_args = array(
        'post_type' => 'floorplans',
        'posts_per_page' => -1,
		'orderby' => 'date', // we will sort posts by date
		'order'	=> 'ASC', // ASC or DESC
        'no_found_rows' => true,
	);
    
    $floorplans_args = apply_filters( 'rentfetch_search_property_map_floorplans_query_args', $floorplans_args );
                    
	$floorplans_query = new WP_Query( $floorplans_args );
        
    // reset the floorplans array
    $floorplans = array();
     
	if( $floorplans_query->have_posts() ) :
                
        while( $floorplans_query->have_posts() ): $floorplans_query->the_post();
                        
            $id = get_the_ID();
            $property_id = get_post_meta( $id, 'property_id', true );
            $beds = get_post_meta( $id, 'beds', true );
            $baths = get_post_meta( $id, 'baths', true );
            $minimum_rent = get_post_meta( $id, 'minimum_rent', true );
            $maximum_rent = get_post_meta( $id, 'maximum_rent', true );
            $minimum_sqft = get_post_meta( $id, 'minimum_sqft', true );
            $maximum_sqft = get_post_meta( $id, 'maximum_sqft', true );
            $available_units = get_post_meta( $id, 'available_units', true );
            $has_specials = get_post_meta( $id, 'has_specials', true );
            
            if ( !isset( $floorplans[$property_id ] ) ) {
                $floorplans[ $property_id ] = array(
                    'id' => array( $id ),
                    'beds' => array( $beds ),
                    'baths' => array( $baths ),
                    'minimum_rent' => array( $minimum_rent ),
                    'maximum_rent' => array( $maximum_rent ),
                    'minimum_sqft' => array( $minimum_sqft ),
                    'maximum_sqft' => array( $maximum_sqft ),
                    'available_units' => array( $available_units ),
                    'has_specials' => array( $has_specials ),
                );
            } else {
                $floorplans[ $property_id ]['id'][] = $id;
                $floorplans[ $property_id ]['beds'][] = $beds;
                $floorplans[ $property_id ]['baths'][] = $baths;
                $floorplans[ $property_id ]['minimum_rent'][] = $minimum_rent;
                $floorplans[ $property_id ]['maximum_rent'][] = $maximum_rent;
                $floorplans[ $property_id ]['minimum_sqft'][] = $minimum_sqft;
                $floorplans[ $property_id ]['maximum_sqft'][] = $maximum_sqft;
                $floorplans[ $property_id ]['available_units'][] = $available_units;
                $floorplans[ $property_id ]['has_specials'][] = $has_specials;
            }
            
        endwhile;
        
		wp_reset_postdata();
        
	endif;
        
    foreach ( $floorplans as $key => $floorplan ) {
        $max = max( $floorplan['beds'] );
        $min = min( $floorplan['beds'] );
        
        if ( $max == $min ) {
            $floorplans[$key]['bedsrange'] = $max;
        } else {
            $floorplans[$key]['bedsrange'] = $min . '-' . $max;
        }
        
        $max = max( $floorplan['baths'] );
        $min = min( $floorplan['baths'] );
        
        if ( $max == $min ) {
            $floorplans[$key]['bathsrange'] = $max;
        } else {
            $floorplans[$key]['bathsrange'] = $min . '-' . $max;
        }
        
        $floorplan['maximum_rent'] = array_filter( $floorplan['maximum_rent'], 'rentfetch_check_if_above_100' );
        $floorplan['minimum_rent'] = array_filter( $floorplan['minimum_rent'], 'rentfetch_check_if_above_100' );
        
        if ( !empty( $floorplan['maximum_rent'] ) ) {
            $max = max( $floorplan['maximum_rent'] );
        } else {
            $max = 0;
        }
        
        if ( !empty( $floorplan['minimum_rent'] ) ) {
            $min = min( $floorplan['minimum_rent'] );
        } else {
            $min = 0;
        }
        
        if ( $max == $min ) {
            $floorplans[$key]['rentrange'] = '$' . $max;
        } else {
            $floorplans[$key]['rentrange'] = '$' . $min . '-' . $max;
        }
        
        if ( $min < 100 || $max < 100 )
            $floorplans[$key]['rentrange'] = apply_filters( 'rentfetch_floorplan_pricing_unavailable_text', 'Pricing unavailable' );
        
        $max = max( $floorplan['maximum_sqft'] );
        $min = min( $floorplan['minimum_sqft'] );
        
        if ( $max == $min ) {
            $floorplans[$key]['sqftrange'] = $max;
        } else {
            $floorplans[$key]['sqftrange'] = $min . '-' . $max;
        }
        
        // default value
        $floorplans[$key]['property_has_specials'] = false;
        
        // if there are specials, save that
        $has_specials = $floorplan['has_specials'];
        
        if ( in_array( true, $has_specials ) )        
            $floorplans[$key]['property_has_specials'] = true;
        
    }
    
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
    
    $propertyquery = new WP_Query( $property_args );
        
    if( $propertyquery->have_posts() ) :
        
        $numberofposts = $propertyquery->post_count;
        
        if ( $numberofposts == $properties_maximum_per_page ) {
            printf( '<div class="count"><h2 class="post-count">More than <span class="number">%s</span> properties found</h2></div>', $numberofposts );
        } else {
            printf( '<div class="count"><h2 class="post-count"><span class="number">%s</span> results</h2></div>', $numberofposts );
        }
        
        echo '<div class="properties-loop">';
            while( $propertyquery->have_posts() ): $propertyquery->the_post();
                $property_id = get_post_meta( get_the_ID(), 'property_id', true );
                $floorplan = $floorplans[$property_id ];                
                do_action( 'rentfetch_do_each_property', $propertyquery->post->ID, $floorplan );
            endwhile;
        echo '</div>';
        
		wp_reset_postdata();
        
	else :
        
		echo 'No properties with availability were found matching the current search parameters.';
        
	endif;
 
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