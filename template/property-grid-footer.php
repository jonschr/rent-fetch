<?php

add_action( 'apartmentsync_single_properties_nearby_properties', 'apartmentsync_add_properties_to_neighborhood_and_property_footer' );
add_action( 'genesis_after_content_sidebar_wrap', 'apartmentsync_add_properties_to_neighborhood_and_property_footer', 0 );
function apartmentsync_add_properties_to_neighborhood_and_property_footer() {
        
    if ( !is_singular( 'neighborhoods') && !is_singular( 'properties' ) )
        return;
                        
    // if this is a property, we need to find the connected neightborhoods to use later
    if ( is_singular( 'properties' ) ) {
                
        $neighborhoods = MB_Relationships_API::get_connected( [
            'id'   => 'properties_to_neighborhoods',
            'to' => get_the_ID(),
        ] );
        
        if ( !$neighborhoods )
            return;
                    
        $connected_neighborhoods = array();
        
        foreach ( $neighborhoods as $neighborhood ) {            
            $connected_neighborhoods[] = $neighborhood->ID;
        }        
    }
    
    if ( is_singular( 'neighborhoods' ) ) {
        $connected_neighborhoods = array( get_the_ID() );
    }
    
    
    // do a floorplans query
    $args = array(
        'post_type' => 'floorplans',
        'posts_per_page' => -1,
		'orderby' => 'date', // we will sort posts by date
		'order'	=> 'ASC', // ASC or DESC
        'no_found_rows' => true,
	);
    
    //* Remove anything with an unrealistically low value for rent
    // $args['meta_query'][] = array(
    //     array(
    //         'key' => 'minimum_rent',
    //         'value' => 100,
    //         'compare' => '>',
    //     )
    // );
    
    // $args['meta_query'][] = array(
    //     array(
    //         'key' => 'maximum_rent',
    //         'value' => 100,
    //         'compare' => '>',
    //     )
    // );
    
    $query = new WP_Query( $args );

    // echo '<pre style="font-size: 14px;">';
    // print_r( $query->post );
    // echo '</pre>';
    
    // reset the floorplans array
    $floorplans = array();
    
    if( $query->have_posts() ) :
        
        // printf( '<div class="count"><h2 class="post-count"><span class="number">%s</span> results</h2><p>Note: Right now this is searching floorplans. Long-term, it will need to search the floorplans first, then do a secondary search of the associated properties.</p></div>', $numberofposts );
        
            while( $query->have_posts() ): $query->the_post();
            
                $id = get_the_ID();
                $property_id = get_post_meta( $id, 'property_id', true );
                $beds = get_post_meta( $id, 'beds', true );
                $baths = get_post_meta( $id, 'baths', true );
                $minimum_rent = get_post_meta( $id, 'minimum_rent', true );
                $maximum_rent = get_post_meta( $id, 'maximum_rent', true );
                $minimum_sqft = get_post_meta( $id, 'minimum_sqft', true );
                $maximum_sqft = get_post_meta( $id, 'maximum_sqft', true );
                $available_units = get_post_meta( $id, 'available_units', true );
                
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
                }
                
            endwhile;
        
		wp_reset_postdata();
        
	endif;
    
    
    // echo '<pre style="font-size: 14px;">';
    // print_r( $floorplans );
    // echo '</pre>';
    
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
        
        $max = max( $floorplan['maximum_rent'] );
        $min = min( $floorplan['minimum_rent'] );
        
        if ( $max == $min ) {
            $floorplans[$key]['rentrange'] = '$' . $max;
        } else {
            $floorplans[$key]['rentrange'] = '$' . $min . '-' . $max;
        }
        
        if ( $min < 100 || $max < 100 )
            $floorplans[$key]['rentrange'] = 'Pricing unavailable';
        
        $max = max( $floorplan['maximum_sqft'] );
        $min = min( $floorplan['minimum_sqft'] );
        
        if ( $max == $min ) {
            $floorplans[$key]['sqftrange'] = $max;
        } else {
            $floorplans[$key]['sqftrange'] = $min . '-' . $max;
        }
        
    }
    
    $property_ids = array_keys( $floorplans );
    if ( empty( $property_ids ) )
    $property_ids = array( '1' ); // if there aren't any properties, we shouldn't find anything – empty array will let us find everything, so let's pass nonsense to make the search find nothing
    
    // echo '<pre style="font-size: 14px;">';
    // print_r( $property_ids );
    // echo '</pre>';
    
    $number_properties = '-1';
    
    $property_footer_settings = get_field( 'property_footer_grid', 'options' );
    if ( isset( $property_footer_settings['number_properties'] ) )
        $number_properties = $property_footer_settings['number_properties'];
        
    //* The base property query
    $propertyargs = array(
        'post_type' => 'properties',
        'posts_per_page' => $number_properties,
		'orderby' => 'menu_order',
		'order'	=> 'ASC', // ASC or DESC
        'no_found_rows' => true,
        'relationship' => array(
            'id'   => 'properties_to_neighborhoods',
            'from' => $connected_neighborhoods, // You can pass object ID or full object
        ),
	);
    
    //* Add all of our property IDs into the property search
    $propertyargs['meta_query'] = array(
        array(
            'key' => 'property_id',
            'value' => $property_ids,
        ),
    );
    
    $propertyquery = new WP_Query( $propertyargs );
    
    // echo '<pre>';
    // print_r( $propertyquery );
    // echo '</pre>';
    
    $countposts = $propertyquery->post_count;
    if ( $countposts < 2 )
        return;
    
    if( $propertyquery->have_posts() ) :
        echo '<div id="neighborhood-prefooter">';
        
            // if ( is_singular( 'properties' ) )
                echo '<h2>Nearby properties</h2>';
        
            echo '<div class="properties-loop">';
        
            while( $propertyquery->have_posts() ): $propertyquery->the_post();
                $property_id = get_post_meta( get_the_ID(), 'property_id', true );
                $floorplan = $floorplans[$property_id ];
                do_action( 'apartmentsync_do_each_property', $propertyquery->post, $floorplan );
            endwhile;
        
            wp_reset_postdata();
            
        echo '</div></div>';
        
    else :
            
        // echo 'No properties found matching the current search parameters.';
        
    endif;
    
}