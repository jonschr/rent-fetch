<?php

add_action( 'apartmentsync_do_save_yardi_floorplans_to_cpt', 'apartmentsync_save_yardi_floorplans_to_cpt' );
function apartmentsync_save_yardi_floorplans_to_cpt() {
            
    // get the list of properties
    $yardi_integration_creds = get_field( 'yardi_integration_creds', 'option' );
    $properties = $yardi_integration_creds['yardi_property_code'];
        
    $properties = explode( ',', $properties );
    foreach( $properties as $property ) {
        
        apartmentsync_log( "Starting to save floorplans for Yardi property $property into the 'floorplans' CPT." );
        
        $floorplans = get_transient( 'yardi_floorplans_property_id_' . $property );
        
        console_log( $floorplans );
        
        // bail if we do not have a transient with this data in it
        if ( $floorplans == false ) {
            apartmentsync_log( "No transient currently set for property $property (transient should be named yardi_floorplans_property_id_$property), so we're ending the process." );
            return;
        }
            
        apartmentsync_log( "Transient found for Yardi property $property (named yardi_floorplans_property_id_$property). Looping through data." );
        
        foreach( $floorplans as $floorplan ) {
                        
            // all of the available variables
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
                    'availability_url'          => $AvailabilityURL,
                    'available_units'           => $AvailableUnitsCount,
                    'baths'                     => $Baths,
                    'beds'                      => $Beds,
                    'has_specials'              => $FloorplanHasSpecials,
                    'floorplan_id'              => $FloorplanId,
                    'maximum_deposit'           => $MaximumDeposit,
                    'maximum_rent'              => $MaximumRent,
                    'maximum_sqft'              => $MaximumSQFT,
                    'minimum_deposit'           => $MinimumDeposit,
                    'minimum_rent'              => $MinimumRent,
                    'minimum_sqft'              => $MinimumSQFT,
                    'property_id'               => $PropertyId,
                    'property_show_specials'    => $PropertyShowsSpecials,
                    'unit_type_mapping'         => $UnitTypeMapping,
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
            if ( !$query->have_posts() ) {
                $post_id = wp_insert_post( $floorplan_meta );
                apartmentsync_log( "Floorplan $FloorplanId, $FloorplanName, does not exist yet in the database. Inserting." );
            } else {
                apartmentsync_log( "Floorplan $FloorplanId, $FloorplanName, already exists in the database. Skipping." );
            }
        }
            
    }
    
    // //* Delete all floorplans (for testing)
    apartmentsync_log( "Deleting all floorplans." );
    $allposts = get_posts( array('post_type'=>'floorplans','numberposts'=>-1) );
    foreach ($allposts as $eachpost) {
        wp_delete_post( $eachpost->ID, true );
    }
    
}