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