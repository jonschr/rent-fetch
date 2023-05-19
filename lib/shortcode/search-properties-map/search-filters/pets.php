<?php

function rentfetch_search_properties_map_filters_pets() {
    
    // check whether beds search is enabled
    $map_search_components = get_option( 'options_map_search_components' );
        
    // bail if beds search is not enabled
    if ( !in_array( 'pets_search', $map_search_components ) )
        return;
        
        //* get information about pets from the database
        $pets = rentfetch_get_meta_values( 'pets', 'properties' );
        $pets = array_unique( $pets );
        asort( $pets );
        $pets = array_filter( $pets );
                        
        $pets_choices = [
            1 => 'Cats allowed',
            2 => 'Cats and Dogs allowed',  
            3 => 'Pet-friendly', 
            4 => 'Pets not allowed',
        ];
                                
        //* build the pets search
        if ( !empty( $pets ) ) {
            echo '<div class="input-wrap input-wrap-pets">';
                echo '<div class="dropdown">';
                    echo '<button type="button" class="dropdown-toggle" data-reset="Pet policy">Pet policy</button>';
                    echo '<div class="dropdown-menu dropdown-menu-pets">';
                        echo '<div class="dropdown-menu-items">';
                            foreach( $pets as $pet ) {
                                printf( '<label><input type="radio" data-pets="%s" data-pets-name="%s" name="pets" value="%s" /><span>%s</span></label>', $pet, $pets_choices[$pet], $pet, $pets_choices[$pet] );
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

add_filter( 'rentfetch_search_property_map_properties_query_args', 'rentfetch_search_property_map_properties_args_pets', 10, 1 );
function rentfetch_search_property_map_properties_args_pets( $property_args ) {

    if ( isset( $_POST['pets'] ) ) {
        $property_args['meta_query'][] = array(
            array(
                'key' => 'pets',
                'value' => sanitize_text_field( $_POST['pets'] ),
            )
        );
    }
    
    return $property_args;
}