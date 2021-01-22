<?php

add_action( 'apartmentsync_do_save_yardi_floorplans_to_cpt', 'apartmentsync_save_yardi_floorplans_to_cpt' );
function apartmentsync_save_yardi_floorplans_to_cpt() {
        
    // get the list of properties
    $yardi_integration_creds = get_field( 'yardi_integration_creds', 'option' );
    $properties = $yardi_integration_creds['yardi_property_code'];
        
    $properties = explode( ',', $properties );
    foreach( $properties as $property ) {
        
        $floorplans = get_transient( 'yardi_floorplans_property_id_' . $property );
        
        // bail if we do not have a transient with this data in it
        if ( $floorplans == false )
            return;
            
        // console_log( $floorplans );
        
        foreach( $floorplans as $floorplan ) {
            
            $AvailabilityURL = $floorplan['AvailabilityURL'];
            $AvailableUnitsCount = $floorplan['AvailableUnitsCount'];
            $Baths = $floorplan['Baths'];
            $Beds = $floorplan['Beds'];
            $FloorplanHasSpecials = $floorplan['FloorplanHasSpecials'];
            $FloorplanId = $floorplan['FloorplanId'];
            $FloorplanImageAltText = $floorplan['FloorplanImageAltText'];
            $FloorplanImageName = $floorplan['FloorplanImageName'];
            $FloorplanImageURL = $floorplan['FloorplanImageURL'];
            $FloorplanName = $floorplan['FloorplanName'];
            $MaximumDeposit = $floorplan['MaximumDeposit'];
            $MaximumRent = $floorplan['MaximumRent'];
            $MaximumSQFT = $floorplan['MaximumSQFT'];
            $MinimumDeposit = $floorplan['MinimumDeposit'];
            $MinimumRent = $floorplan['MinimumRent'];
            $MinimumSQFT = $floorplan['MinimumSQFT'];
            $PropertyId = $floorplan['PropertyId'];
            $PropertyShowsSpecials = $floorplan['PropertyShowsSpecials'];
            $UnitTypeMapping = $floorplan['UnitTypeMapping'];
            
            // Create post object
            $floorplan_meta = array(
                'post_title'    => wp_strip_all_tags( $FloorplanName ),
                'post_status'   => 'publish',
                'post_type'     => 'floorplans',
                'meta_input'    => array(
                    'availability_url'  => $AvailabilityURL,
                    'available_units'   => $AvailableUnitsCount,
                    'baths'             => $Baths,
                    'beds'              => $Beds,
                    'has_specials'      => $FloorplanHasSpecials,
                    'floorplan_id'      => $FloorplanId,
                ),
            );
            
            // do a query to check and see if a post already exists with this info 
            $args = array(
                'post_type' => 'floorplans',
                'meta_query' => array(
                    array(
                        'key' => 'floorplan_id',
                        'value' => $FloorplanId,
                        'compare' => '=',
                    )
                )
            );
            $query = new WP_Query($args);

            // insert the post if there isn't one already (this prevents duplicates)
            if ( !$query->have_posts() )
                $post_id = wp_insert_post( $floorplan_meta );
        }
            
    }
    
    // //* Delete all floorplans (for testing)
    // $allposts = get_posts( array('post_type'=>'floorplans','numberposts'=>-1) );
    // foreach ($allposts as $eachpost) {
    //     wp_delete_post( $eachpost->ID, true );
    // }
    
}