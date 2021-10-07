<?php

add_filter( 'rentfetch_filter_property_url', 'rentfetch_add_prefix_to_property_url', 10, 1 );
function rentfetch_add_prefix_to_property_url( $url ) {
    
    // force http:// to be added to the URL if it's missing
    if (strpos($url,'http') === false)
        $url = 'http://'.$url;
        
    return $url;
    
}
