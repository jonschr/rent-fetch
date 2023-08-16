<?php

function rentfetch_search_properties_map_filters_property_types() {
    
    // get query parameters about types
    if ( isset( $_GET['propertytypes'])) {
        $propertytypesparam = $_GET['propertytypes'];
        $propertytypesparam = explode( ',', $propertytypesparam );
        $propertytypesparam = array_map( 'esc_attr', $propertytypesparam );
    } else {
        $propertytypesparam = array();
    } 
    
    // check whether beds search is enabled
    $map_search_components = get_option( 'options_map_search_components' );
    
    // this needs to be set to an array even if the option isn't set
    if ( !is_array( $map_search_components ) )
        $map_search_components = array();
        
    // bail if beds search is not enabled
    if ( !in_array( 'type_search', $map_search_components ) )
        return;
        
    // bail if propertytypes taxonomy does not exist
    if ( !taxonomy_exists('propertytypes') )
        return;
    
    // get information about types from the database
    $propertytypes = get_terms( 
        array(
            'taxonomy' => 'propertytypes',
            'hide_empty' => true,
        ),
    );
                    
    // build types search
    if ( !empty( $propertytypes && taxonomy_exists( 'propertytypes' ) ) ) {
        echo '<div class="input-wrap input-wrap-propertytypes">';
            echo '<div class="dropdown">';
                echo '<button type="button" class="dropdown-toggle" data-reset="Type">Type</button>';
                echo '<div class="dropdown-menu dropdown-menu-propertytypes">';
                    echo '<div class="dropdown-menu-items">';
                        foreach( $propertytypes as $propertytype ) {
                            $name = $propertytype->name;
                            $propertytype_term_id = $propertytype->term_id;
                            if ( in_array( $propertytype_term_id, $propertytypesparam ) ) {
                                    printf( '<label><input type="checkbox" data-propertytypes="%s" data-propertytypesname="%s" name="propertytypes-%s" checked /><span>%s</span></label>', $propertytype_term_id, $name, $propertytype_term_id, $name );
                            } else {
                                printf( '<label><input type="checkbox" data-propertytypes="%s" data-propertytypesname="%s" name="propertytypes-%s" /><span>%s</span></label>', $propertytype_term_id, $name, $propertytype_term_id, $name );
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
        
}

add_filter( 'rentfetch_search_property_map_properties_query_args', 'rentfetch_search_property_map_properties_args_types', 10, 1 );
function rentfetch_search_property_map_properties_args_types( $property_args ) {
    
    // bail if there's no propertypes taxonomy
    if ( !taxonomy_exists( 'propertytypes' ) )
        return;
        
    //* Add the tax queries
    $property_args['tax_query'] = array();
    
    //* propertytype taxonomy
    $propertytypes = get_terms( 
        array(
            'taxonomy' => 'propertytypes',
            'hide_empty' => true,
        ),
    );
    
    // loop through the checkboxes, and for each one that's checked, let's add that value to our tax query array
    foreach ( $propertytypes as $propertytype ) {
        $name = $propertytype->name;
        $propertytype_term_id = $propertytype->term_id;
        
        if ( isset( $_POST['propertytypes-' . $propertytype_term_id ] ) && $_POST['propertytypes-' . $propertytype_term_id ] == 'on' ) {
            $propertytype_term_id = sanitize_text_field( $propertytype_term_id );
            $propertytypeids[] = $propertytype_term_id;
        }
    }
        
    // add the meta query array to our $args
    if ( isset( $propertytypeids ) ) {
        $property_args['tax_query'][] = array(
            array(
                'taxonomy' => 'propertytypes',
                'terms' => $propertytypeids,
            )
        );
    } 
    
    return $property_args;
}