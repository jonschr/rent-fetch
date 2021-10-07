<?php

add_action( 'rentfetch_do_sync_logic', 'rentfetch_start_the_sync' );
function rentfetch_start_the_sync() {
    
    $enabled_integrations = get_field( 'enabled_integrations', 'option' );
    foreach ( $enabled_integrations as $enabled_integration ) {
        
        // action to get the floorplans and put them in a transient
        do_action( 'rentfetch_do_get_floorplans_' . $enabled_integration );
        do_action( 'rentfetch_do_get_properties_' . $enabled_integration );
    }
        
}