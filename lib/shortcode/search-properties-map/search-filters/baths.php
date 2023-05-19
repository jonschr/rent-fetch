<?php

function rentfetch_search_properties_map_filters_baths() {
    
    //* get query parameters about baths
    if (isset($_GET['baths'])) {
        $bathsparam = $_GET['baths'];
        $bathsparam = explode( ',', $bathsparam );
        $bathsparam = array_map( 'esc_attr', $bathsparam );
    } else {
        $bathsparam = array();
    }
        
    // check whether beds search is enabled
    $map_search_components = get_option( 'options_map_search_components' );
    
    // bail if beds search is not enabled
    if ( !in_array( 'baths_search', $map_search_components ) )
        return;
        
    //* get information about baths from the database
    $baths = rentfetch_get_meta_values( 'baths', 'floorplans' );
    $baths = array_unique( $baths );
    asort( $baths );
    
    //* build the baths search
    echo '<div class="input-wrap input-wrap-baths">';
        echo '<div class="dropdown">';
            echo '<button type="button" class="dropdown-toggle" data-reset="Baths">Baths</button>';
            echo '<div class="dropdown-menu dropdown-menu-baths">';
                echo '<div class="dropdown-menu-items">';
                    foreach( $baths as $bath ) {
                        if ( in_array( $bath, $bathsparam ) ) {
                            printf( '<label><input type="checkbox" data-baths="%s" name="baths-%s" checked /><span>%s Bathroom</span></label>', $bath, $bath, $bath );
                        } else {
                            printf( '<label><input type="checkbox" data-baths="%s" name="baths-%s" /><span>%s Bathroom</span></label>', $bath, $bath, $bath );
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