<?php

add_action( 'apartmentsync_do_floorplan_in_archive', 'apartmentsync_floorplan_in_archive', 10, 1 );
function apartmentsync_floorplan_in_archive( $floorplan ) {
    
    var_dump( $floorplan );
    
}