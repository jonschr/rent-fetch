<?php 


function rentfetch_search_properties_map_filters_text_search() {
    
    // check the query to see if we have a text-based search
    if (isset($_GET['textsearch'])) {
        $searchtext = $_GET['textsearch'];
        $searchtext = esc_attr( $searchtext );
        
    } else {
        $searchtext = null;
    }

    // check whether text-based search is enabled
    $map_search_components = get_option( 'options_map_search_components' );
    
    // this needs to be set to an array even if the option isn't set
    if ( !is_array( $map_search_components ) )
        $map_search_components = array();
    
    // bail if text-based search is not enabled
    if ( !in_array( 'text_based_search', $map_search_components ) )
        return;
                        
    $placeholder = apply_filters( 'rentfetch_search_placeholder_text', 'Search city or zipcode ...' );
    
    // build the text-based search
    echo '<fieldset>';
		printf( '<legend>%s</legend>', $placeholder );
		echo '<div class="text filter-wrap-amenities">';
            if ( $searchtext ) {
                printf( '<input type="text" name="textsearch" placeholder="%s" class="active" value="%s" />', $placeholder, $searchtext );
            } else {
                printf( '<input type="text" name="textsearch" placeholder="%s" />', $placeholder );
            }
        echo '</div>'; // .text
	echo '</fieldset>';
        
}

add_filter( 'rentfetch_search_property_map_properties_query_args', 'rentfetch_search_property_map_properties_args_text', 10, 1 );
function rentfetch_search_property_map_properties_args_text( $property_args ) {
    //* Add text-based search into the query
    $search = null;
    
    if ( isset( $_POST['textsearch'] ) ) {
        $search = $_POST['textsearch'];
        $search = sanitize_text_field( $search );
    }
        
    if ( $search != null ) {
        $property_args['s'] = $search;
        
        // force the site to use relevanssi if it's installed
        if ( function_exists( 'relevanssi_truncate_index_ajax_wrapper' ) )
            $property_args['relevanssi'] = true;
    }  
    
    return $property_args;
}
