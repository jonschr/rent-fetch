<?php

add_shortcode( 'propertysearch', 'rentfetch_propertysearch' );
function rentfetch_propertysearch( $atts ) {
    
    ob_start();
    
    $a = shortcode_atts( array(
        'url' => '/property-search',
    ), $atts );
    
    $url = $a['url'];
    
    wp_enqueue_style( 'rentfetch-search-properties-map' );
    
    // Localize the search filters general script, then enqueue that
    $search_options = array(
        'maximum_bedrooms_to_search' => intval( get_field( 'maximum_bedrooms_to_search', 'option' ) ),
    );
    wp_localize_script( 'rentfetch-search-filters-general', 'searchoptions', $search_options );
    wp_enqueue_script( 'rentfetch-search-filters-general' );
    
    ?>
    <script>
        const sendMessage = () => {
            
            jQuery(document).ready(function( $ ) {
                
                // get the text search from form
                var textsearch = $( '.input-wrap-text-search input ').val();
                
                var beds = [];
                $( '.input-wrap-beds input[type="checkbox"]:checked ').each( function() {
                    bed = $( this ).attr( 'data-beds' );
                    
                    if ( bed != null ) {
                        beds.push( bed );
                    }
                });
                beds = beds.join(',');
                
                var baths = [];
                $( '.input-wrap-baths input[type="checkbox"]:checked ').each( function() {
                    bath = $( this ).attr( 'data-baths' );
                    
                    if ( bath != null ) {
                        baths.push( bath );
                    }
                });
                baths = baths.join(',');
                
                var propertytypes = [];
                $( '.input-wrap-propertytypes input[type="checkbox"]:checked ').each( function() {
                    propertytype = $( this ).attr( 'data-propertytypes' );
                    
                    if ( propertytype != null ) {
                        propertytypes.push( propertytype );
                    }
                });
                propertytypes = propertytypes.join(',');
                               
                $(location).attr('href', '<?php echo $url; ?>?textsearch=' + textsearch + '&beds=' + beds + '&baths=' + baths + '&propertytypes=' + propertytypes );
                
            });
        }
    </script>
    <?php
    
    printf( '<form class="property-search-starter" onsubmit="return false" id="filter" style="opacity:0;">' );
    
        //* check whether the text search is enabled
        $starter_search_components = get_field( 'starter_search_components', 'option' );
        $enable_text_based_search = $starter_search_components['text_based_search'];
        if ( $enable_text_based_search == true ) {
            
            //* Build the text search
            echo '<div class="input-wrap input-wrap-text-search">';
                echo '<input type="text" name="textsearch" placeholder="Search city or zipcode ..." />';
            echo '</div>';
            
        }
        
        //* check whether the beds search is enabled
        $starter_search_components = get_field( 'starter_search_components', 'option' );
        $enable_beds_search = $starter_search_components['beds_search'];
        if ( $enable_beds_search == true ) {
        
            //* Build the beds filter
            $beds = rentfetch_get_meta_values( 'beds', 'floorplans' );
            $beds = array_unique( $beds );
            asort( $beds );
                    
            // beds
            echo '<div class="input-wrap input-wrap-beds">';
                echo '<div class="dropdown">';
                    echo '<button type="button" class="dropdown-toggle" data-reset="Beds">Beds</button>';
                    echo '<div class="dropdown-menu">';
                        echo '<div class="dropdown-menu-items">';
                            foreach( $beds as $bed ) {
                                
                                // skip if the number isn't defined
                                if ( $bed === null )
                                    continue;
                                    
                                $label = apply_filters( 'rentfetch_get_bedroom_number_label', $label, $bed );
                                    
                                printf( '<label><input type="checkbox" data-beds="%s" name="beds-%s" /><span>%s</span></label>', $bed, $bed, $label );
                            }
                        echo '</div>';
                        echo '<div class="filter-application">';
                            echo '<a class="clear" href="#">Clear</a>';
                            echo '<a class="apply-local" href="#">Apply</a>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>'; // .dropdown
            echo '</div>'; // .input-wrap
            
        }
        
        //* check whether the baths search is enabled
        $starter_search_components = get_field( 'starter_search_components', 'option' );
        $enable_baths_search = $starter_search_components['baths_search'];
        if ( $enable_baths_search == true ) {
        
            //* Build the baths filter
            $baths = rentfetch_get_meta_values( 'baths', 'floorplans' );
            $baths = array_unique( $baths );
            asort( $baths );
            
            echo '<div class="input-wrap input-wrap-baths">';
                echo '<div class="dropdown">';
                    echo '<button type="button" class="dropdown-toggle" data-reset="Baths">Baths</button>';
                    echo '<div class="dropdown-menu">';
                        echo '<div class="dropdown-menu-items">';
                            foreach( $baths as $bath ) {
                                printf( '<label><input type="checkbox" data-baths="%s" name="baths-%s" /><span>%s Bathroom</span></label>', $bath, $bath, $bath );
                            }
                        echo '</div>';
                        echo '<div class="filter-application">';
                            echo '<a class="clear" href="#">Clear</a>';
                            echo '<a class="apply-local" href="#">Apply</a>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>'; // .dropdown
            echo '</div>'; // .input-wrap
        
        }
        
        //* check whether type-based search is enabled
        $starter_search_components = get_field( 'starter_search_components', 'option' );
        $enable_type_search = $starter_search_components['type_search'];
        if ( $enable_type_search == true ) {
        
            //* Property types
            $propertytypes = get_terms( 
                array(
                    'taxonomy' => 'propertytypes',
                    'hide_empty' => true,
                ),
            );
            
            if ( !empty( $propertytypes ) ) {
                echo '<div class="input-wrap input-wrap-propertytypes">';
                    echo '<div class="dropdown">';
                        echo '<button type="button" class="dropdown-toggle" data-reset="Type">Type</button>';
                        echo '<div class="dropdown-menu dropdown-menu-propertytypes">';
                            echo '<div class="dropdown-menu-items">';
                                foreach( $propertytypes as $propertytype ) {
                                    $name = $propertytype->name;
                                    $propertytype_term_id = $propertytype->term_id;
                                    printf( '<label><input type="checkbox" data-propertytypes="%s" data-propertytypesname="%s" name="propertytypes-%s" /><span>%s</span></label>', $propertytype_term_id, $name, $propertytype_term_id, $name );
                                }
                            echo '</div>';
                            echo '<div class="filter-application">';
                                echo '<a class="clear" href="#">Clear</a>';
                                echo '<a class="apply-local" href="#">Apply</a>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>'; // .dropdown
                echo '</div>'; // .input-wrap
            }
            
        }
        
        //* build the submit button
        echo '<div class="input-wrap input-wrap-submit">';
            echo '<button onclick="sendMessage()" type="submit">Submit</button>';
        echo '</div>';
        
    echo '</form>';
    
    
    
    return ob_get_clean();
}