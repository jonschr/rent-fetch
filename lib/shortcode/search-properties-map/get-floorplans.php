<?php

function rentfetch_get_floorplans_array() {
    
    global $floorplans;
    
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
    
    return $floorplans;
}

/**
 * Get the floorplans using the default function and make them available globally
 */
add_action( 'init', 'rentfetch_set_floorplans' );
function rentfetch_set_floorplans() {
    
    global $rentfetch_floorplans;
    $rentfetch_floorplans = rentfetch_get_floorplans_array();
    
}

/**
 * Get the floorplans from the global variable, and return those for a particulat property
 */
add_action( 'wp_footer', 'rentfetch_get_floorplans' );
function rentfetch_get_floorplans( $property_id = null ) {
    
    global $rentfetch_floorplans;
    $property_id = intval( $property_id );
    
    if ( $property_id )
        return $rentfetch_floorplans[$property_id];
                
    return $rentfetch_floorplans;
    
}