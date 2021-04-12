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
                               
                $(location).attr('href', '<?php echo $url; ?>?textsearch=' + textsearch + '&beds=' + beds + '&baths=' + baths );
                
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
                            printf( '<label><input type="checkbox" data-beds="%s" name="beds-%s">%s Bedroom</input></label>', $bed, $bed, $bed );
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
                            printf( '<label><input type="checkbox" data-baths="%s" name="baths-%s">%s Bathroom</input></label>', $bath, $bath, $bath );
                        }
                    echo '</div>';
                    echo '<div class="filter-application">';
                        echo '<a class="clear" href="#">Clear</a>';
                        echo '<a class="apply-local" href="#">Apply</a>';
                    echo '</div>';
                echo '</div>';
            echo '</div>'; // .dropdown
        echo '</div>'; // .input-wrap
        
        //* Building type
        echo '<div class="input-wrap input-wrap-building-type incomplete">';
            echo '<div class="dropdown">';
                echo '<button type="button" class="dropdown-toggle" data-reset="Type">Type</button>';
                echo '<div class="dropdown-menu">';
                    echo '<div class="dropdown-menu-items">';
                        // foreach( $baths as $bath ) {
                        //     if ( in_array( $bath, $bathsparam ) ) {
                        //         printf( '<label><input type="checkbox" data-baths="%s" name="baths-%s" checked>%s Bathroom</input></label>', $bath, $bath, $bath );
                        //     } else {
                        //         printf( '<label><input type="checkbox" data-baths="%s" name="baths-%s">%s Bathroom</input></label>', $bath, $bath, $bath );
                        //     }
                        // }
                    echo '</div>';
                    echo '<div class="filter-application">';
                        echo '<a class="clear" href="#">Clear</a>';
                        echo '<a class="apply" href="#">Apply</a>';
                    echo '</div>';
                echo '</div>';
            echo '</div>'; // .dropdown
        echo '</div>'; // .input-wrap
        
        echo '<div class="input-wrap input-wrap-submit">';
            echo '<button onclick="sendMessage()" type="submit">Submit</button>';
        echo '</div>';
        
    echo '</form>';
    
    
    
    return ob_get_clean();
}