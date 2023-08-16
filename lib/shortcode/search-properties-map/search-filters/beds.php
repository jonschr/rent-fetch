<?php

function rentfetch_search_properties_map_filters_beds() {
    
    // beds parameter
    if (isset($_GET['beds'])) {
        $bedsparam = $_GET['beds'];
        $bedsparam = explode( ',', $bedsparam );
        $bedsparam = array_map( 'esc_attr', $bedsparam );
    } else {
        $bedsparam = array();
    }
    
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
                        
                        // skip if there's a null value for bed
                        if ( $bed === null )
                            continue;
                            
                        $label = apply_filters( 'rentfetch_get_bedroom_number_label', $label = null, $bed );
                            
                        if ( in_array( $bed, $bedsparam ) ) {
                            printf( '<label><input type="checkbox" data-beds="%s" name="beds-%s" checked /><span>%s</span></label>', $bed, $bed, $label );
                        } else {
                            printf( '<label><input type="checkbox" data-beds="%s" name="beds-%s" /><span>%s</span></label>', $bed, $bed, $label );
                        }
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

add_filter( 'rentfetch_search_property_map_floorplans_query_args', 'rentfetch_search_property_map_floorplans_args_beds', 10, 1 );
function rentfetch_search_property_map_floorplans_args_beds( $floorplans_args ) {
    
    //* bedrooms
    $beds = rentfetch_get_meta_values( 'beds', 'floorplans' );
    $beds = array_unique( $beds );
    asort( $beds );
    
    // loop through the checkboxes, and for each one that's checked, let's add that value to our meta query array
    foreach ( $beds as $bed ) {
        if ( isset( $_POST['beds-' . $bed ] ) && $_POST['beds-' . $bed ] == 'on' ) {
            $bed = sanitize_text_field( $bed );
            $bedsarray[] = $bed;
        }
    }
    
    // add the meta query array to our $floorplans_args
    if ( isset( $bedsarray ) ) {
        $floorplans_args['meta_query'][] = array(
            array(
                'key' => 'beds',
                'value' => $bedsarray,
            )
        );
    }
    
    return $floorplans_args;
}