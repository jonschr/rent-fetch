<?php

function apartmentsync_check_if_above_100( $var ) {
    
    if ( $var > 100 )
        return $var;
        
    return null;
}