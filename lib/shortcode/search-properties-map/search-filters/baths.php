<?php

function rentfetch_search_properties_map_filters_baths() {
    
    // check whether baths search is enabled
    $map_search_components = get_option( 'options_map_search_components' );
    
    // this needs to be set to an array even if the option isn't set
    if ( !is_array( $map_search_components ) )
        $map_search_components = array();
    
    // bail if baths search is not enabled
    if ( !in_array( 'baths_search', $map_search_components ) )
        return;
            
    // get info about baths from the database
    $baths = rentfetch_get_meta_values( 'baths', 'floorplans' );
    $baths = array_unique( $baths );
    asort( $baths );
            
    // build the baths search
    echo '<div class="input-wrap input-wrap-baths">';
        echo '<div class="dropdown">';
            echo '<button type="button" class="dropdown-toggle" data-reset="Baths">Baths</button>';
            echo '<div class="dropdown-menu dropdown-menu-baths">';
                echo '<div class="dropdown-menu-items">';
                
                    foreach( $baths as $bath ) {
                        
                        // Check if the amenity's term ID is in the GET parameter array
                        $checked = in_array($bath, $_GET['search-baths'] ?? array());
                        
                        // skip if there's a null value for bath
                        if ( $bath === null )
                            continue;
                            
                        $label = $bath . ' Bathroom';
                            
                        printf( 
                            '<label>
                                <input type="checkbox" 
                                    name="search-baths[]"
                                    value="%s" 
                                    data-baths="%s" 
                                    %s />
                                <span>%s</span>
                            </label>', 
                            $bath, 
                            $bath,
                            $checked ? 'checked' : '', // Apply checked attribute 
                            $label
                        );
                    }
                echo '</div>';
                echo '<div class="filter-application">';
                    echo '<a class="clear" href="#">Clear</a>';
                    echo '<a class="apply" href="#">Apply</a>';
                echo '</div>';
            echo '</div>';
        echo '</div>'; // .dropdown
    echo '</div>'; // .input-wrap
        
}

add_filter('rentfetch_search_property_map_floorplans_query_args', 'rentfetch_search_property_map_floorplans_args_baths', 10, 1);
function rentfetch_search_property_map_floorplans_args_baths( $floorplans_args ) {
        
    if ( isset( $_POST['search-baths'] ) && is_array( $_POST['search-baths'] ) ) {
        
        // Get the values
        $baths = $_POST['search-baths'];
        
        // Escape the values
        $baths = array_map( 'sanitize_text_field', $baths );
        
        // Convert the baths query to a meta query
        $meta_query = array(
            array(
                'key' => 'baths',
                'value' => $baths,
            ),
        );
                
        // Add the meta query to the property args
        $floorplans_args['meta_query'][] = $meta_query;
                
    }
    
    return $floorplans_args;
}
