<?php

add_shortcode( 'propertysearch', 'apartmentsync_propertysearch' );
function apartmentsync_propertysearch( $atts ) {
    
    $a = shortcode_atts( array(
        'url' => '/property-search',
    ), $atts );
    
    $url = $a['url'];
    
    wp_enqueue_style( 'apartmentsync-search-properties-map' );
    wp_enqueue_script( 'apartmentsync-search-filters-general' );
    wp_enqueue_script( 'apartmentsync-searchbar' );
    
    ob_start();
    
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
    
    printf( '<form class="property-search-starter" onsubmit="return false" id="filter">' );
    
        //* Build the text search
        echo '<div class="input-wrap input-wrap-text-search">';
            echo '<input type="text" name="textsearch" placeholder="Search city or property name..." />';
        echo '</div>';
                
        //* Build the beds filter
        $beds = apartentsync_get_meta_values( 'beds', 'floorplans' );
        $beds = array_unique( $beds );
        asort( $beds );
                
        // beds
        echo '<div class="input-wrap input-wrap-beds">';
            echo '<div class="dropdown">';
                echo '<button type="button" class="dropdown-toggle" data-reset="Beds">Beds</button>';
                echo '<div class="dropdown-menu">';
                    echo '<div class="dropdown-menu-items">';
                        foreach( $beds as $bed ) {
                            printf( '<label><input type="checkbox" data-beds="%s" name="beds-%s" /><span>%s Bedroom</span></label>', $bed, $bed, $bed );
                        }
                    echo '</div>';
                    echo '<div class="filter-application">';
                        echo '<a class="clear" href="#">Clear</a>';
                        echo '<a class="apply-local" href="#">Apply</a>';
                    echo '</div>';
                echo '</div>';
            echo '</div>'; // .dropdown
        echo '</div>'; // .input-wrap
        
        //* Build the baths filter
        $baths = apartentsync_get_meta_values( 'baths', 'floorplans' );
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
                    echo '<div class="dropdown-menu">';
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
            
        echo '<div class="input-wrap input-wrap-submit">';
            echo '<button onclick="sendMessage()" type="submit">Submit</button>';
        echo '</div>';
        
    echo '</form>';
    
    
    
    return ob_get_clean();
}