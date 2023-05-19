<?php

function rentfetch_search_properties_map_filters_amenities() {
    
    // check whether beds search is enabled
    $map_search_components = get_option( 'options_map_search_components' );
        
    // bail if beds search is not enabled
    if ( !in_array( 'amenities_search', $map_search_components ) )
        return;
                
    //* figure out how many amenities to show
    $number_of_amenities_to_show = get_option( 'options_number_of_amenities_to_show' );
    
    //* get information about amenities from the database
    $amenities = get_terms( 
        array(
            'taxonomy'      => 'amenities',
            'hide_empty'    => true,
            'number'        => $number_of_amenities_to_show,
            'orderby'       => 'count',
            'order'         => 'DESC',
        ),
    );
        
    //* build amenities search
    if ( !empty( $amenities ) && taxonomy_exists( 'amenities' ) ) {
        echo '<div class="input-wrap input-wrap-amenities">';
            echo '<div class="dropdown">';
                echo '<button type="button" class="dropdown-toggle" data-reset="Amenities">Amenities</button>';
                echo '<div class="dropdown-menu dropdown-menu-amenities">';
                    echo '<div class="dropdown-menu-items">';
                        foreach( $amenities as $amenity ) {
                            $name = $amenity->name;
                            $amenity_term_id = $amenity->term_id;
                            printf( '<label><input type="checkbox" data-amenities="%s" data-amenities-name="%s" name="amenities-%s" /><span>%s</span></label>', $amenity_term_id, $name, $amenity_term_id, $name );
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
        
}