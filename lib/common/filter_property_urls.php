<?php

add_filter( 'apartmentsync_filter_property_url', 'apartmentsync_add_prefix_to_property_url', 10, 1 );
function apartmentsync_add_prefix_to_property_url( $url ) {
    
    // force http:// to be added to the URL if it's missing
    if (strpos($url,'http') === false)
        $url = 'http://'.$url;
        
    return $url;
    
}
