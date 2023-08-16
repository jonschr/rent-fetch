<?php

function rentfetch_search_properties_map_filters_amenities() {
    
    // check whether beds search is enabled
    $map_search_components = get_option( 'options_map_search_components' );
    
    // this needs to be set to an array even if the option isn't set
    if ( !is_array( $map_search_components ) )
        $map_search_components = array();
        
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

add_filter( 'rentfetch_search_property_map_properties_query_args', 'rentfetch_search_property_map_properties_args_amenities', 10, 1 );
function rentfetch_search_property_map_properties_args_amenities( $property_args ) {
    
    //* amenities taxonomy
    $number_of_amenities_to_show = get_option( 'options_number_of_amenities_to_show' );
    if ( empty( $number_of_amenities_to_show ) )
        $number_of_amenities_to_show = 10;
    
    $amenities = get_terms( 
        array(
            'taxonomy'      => 'amenities',
            'hide_empty'    => true,
            'number'        => $number_of_amenities_to_show,
            'orderby'       => 'count',
            'order'         => 'DESC',
        ),
    );
    
    // loop through the checkboxes, and for each one that's checked, let's add that value to our tax query array
    foreach ( $amenities as $amenity ) {
        $name = $amenity->name;
        $amenity_term_id = $amenity->term_id;
        
        if ( isset( $_POST['amenities-' . $amenity_term_id ] ) && $_POST['amenities-' . $amenity_term_id ] == 'on' ) {
            $amenity_term_id = sanitize_text_field( $amenity_term_id );

            // this is an "AND" query, unlike property types, because here we only want things showing up where ALL of the conditions are met
            $property_args['tax_query'][] = array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'amenities',
                    'terms' => $amenity_term_id,
                )
            );
        }
    } 
    
    return $property_args;
}