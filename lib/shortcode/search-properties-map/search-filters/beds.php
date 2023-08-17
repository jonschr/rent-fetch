<?php

function rentfetch_search_properties_map_filters_beds() {
    
    // check whether beds search is enabled
    $map_search_components = get_option( 'options_map_search_components' );
    
    // this needs to be set to an array even if the option isn't set
    if ( !is_array( $map_search_components ) )
        $map_search_components = array();
    
    // bail if beds search is not enabled
    if ( !in_array( 'beds_search', $map_search_components ) )
        return;
            
    // get info about beds from the database
    $beds = rentfetch_get_meta_values( 'beds', 'floorplans' );
    $beds = array_unique( $beds );
    asort( $beds );
            
    // build the beds search
    echo '<div class="input-wrap input-wrap-beds">';
        echo '<div class="dropdown">';
            echo '<button type="button" class="dropdown-toggle" data-reset="Beds">Beds</button>';
            echo '<div class="dropdown-menu dropdown-menu-beds">';
                echo '<div class="dropdown-menu-items">';
                
                    foreach( $beds as $bed ) {
                        
                        // Check if the amenity's term ID is in the GET parameter array
                        $checked = in_array($bed, $_GET['search-beds'] ?? array());
                        
                        // skip if there's a null value for bed
                        if ( $bed === null )
                            continue;
                            
                        $label = apply_filters( 'rentfetch_get_bedroom_number_label', $label = null, $bed );
                            
                        printf( 
                            '<label>
                                <input type="checkbox" 
                                    name="search-beds[]"
                                    value="%s" 
                                    data-beds="%s" 
                                    %s />
                                <span>%s</span>
                            </label>', 
                            $bed, 
                            $bed,
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

add_filter('rentfetch_search_property_map_floorplans_query_args', 'rentfetch_search_property_map_floorplans_args_beds', 10, 1);
function rentfetch_search_property_map_floorplans_args_beds( $floorplans_args ) {
        
    if ( isset( $_POST['search-beds'] ) && is_array( $_POST['search-beds'] ) ) {
        
        // Get the values
        $beds = $_POST['search-beds'];
        
        // Escape the values
        $beds = array_map( 'sanitize_text_field', $beds );
        
        // Convert the beds query to a meta query
        $meta_query = array(
            array(
                'key' => 'beds',
                'value' => $beds,
            ),
        );
                
        // Add the meta query to the property args
        $floorplans_args['meta_query'][] = $meta_query;
        
        console_log( 'In the function');
        console_log( $floorplans_args );
        
    }
    
    return $floorplans_args;
}
