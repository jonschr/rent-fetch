<?php

///////////////////////////////////////////
// SCHEDULE TASKS FOR ADDING TO DATABASE //
///////////////////////////////////////////

/**
 * Loop through the enabled integrations and start the process for each one
 */
add_action( 'rentfetch_do_chron_activation', 'rentfetch_run_chron' );
function rentfetch_run_chron() {
    
    $enabled_integrations = get_field( 'enabled_integrations', 'option' );
    $data_sync = get_field( 'data_sync', 'option' );
    
    // bail if there aren't any integrations enabled
    if ( !$enabled_integrations )
        return;
    
    foreach ( $enabled_integrations as $enabled_integration ) {
        
        // action to get the floorplans and put them in a transient
        do_action( 'rentfetch_do_save_' . $enabled_integration . '_floorplans_to_cpt' );
        
    }
}
