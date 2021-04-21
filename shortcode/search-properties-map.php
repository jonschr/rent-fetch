<?php

add_shortcode( 'propertymap', 'apartmentsync_propertymap' );
function apartmentsync_propertymap( $atts ) {
    
    ob_start();
    
    // search scripts and styles
    wp_enqueue_style( 'apartmentsync-search-properties-map' );
    wp_enqueue_script( 'apartmentsync-search-filters-general' );
    wp_enqueue_script( 'apartmentsync-search-properties-ajax' );
    wp_enqueue_script( 'apartmentsync-search-properties-script' );
    wp_enqueue_script( 'apartmentsync-toggle-map' );
    
    // slick
    wp_enqueue_script( 'apartmentsync-slick-main-script' );
    wp_enqueue_style( 'apartmentsync-slick-main-styles' );
    wp_enqueue_style( 'apartmentsync-slick-main-theme' );
    
    // properties in archive styles
    wp_enqueue_style( 'apartmentsync-properties-in-archive' );
    
     // the map itself
    $key = get_field( 'google_maps_api_key', 'option' );
    wp_enqueue_script( 'apartmentsync-google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $key, array(), APARTMENTSYNC_VERSION, true );
    
    // Localize the google maps script
    $maps_options = array(
        'json_style' => json_decode( get_field( 'google_maps_styles', 'option' ) ),
    );
    wp_localize_script( 'apartmentsync-property-map', 'options', $maps_options );
    
    wp_enqueue_script( 'apartmentsync-property-map');
    
    ?>
    <script>
        
        jQuery(document).ready(function ($) {
            
            function loadSlick() {
                $('.property-slider').slick({
                    dots: true,
                    infinite: false,
                    arrows: true,
                    speed: 500,
                    fade: true,
                    cssEase: 'linear',
                    lazyLoad: 'ondemand',
                });
            }
            
            $( document ).on( 'ajaxComplete', loadSlick );
            
        });

    </script>
    
    <?php
    
    //* Get parameters
    
    // search parameter
    if (isset($_GET['textsearch'])) {
        $searchtext = $_GET['textsearch'];
        $searchtext = esc_attr( $searchtext );
        
    } else {
        $searchtext = null;
    }
    
    // beds parameter
    if (isset($_GET['beds'])) {
        $bedsparam = $_GET['beds'];
        $bedsparam = explode( ',', $bedsparam );
        $bedsparam = array_map( 'esc_attr', $bedsparam );
    } else {
        $bedsparam = array();
    }
    
    // baths parameter
    if (isset($_GET['baths'])) {
        $bathsparam = $_GET['baths'];
        $bathsparam = explode( ',', $bathsparam );
        $bathsparam = array_map( 'esc_attr', $bathsparam );
    } else {
        $bathsparam = array();
    }
    
    // propertytypes parameter
    if ( isset( $_GET['propertytypes'])) {
        $propertytypesparam = $_GET['propertytypes'];
        $propertytypesparam = explode( ',', $propertytypesparam );
        $propertytypesparam = array_map( 'esc_attr', $propertytypesparam );
    } else {
        $propertytypesparam = array();
    }    
    
    printf( '<form class="property-search-filters" action="%s/wp-admin/admin-ajax.php" method="POST" id="filter">', site_url() );
    
        //* Build the text search
        echo '<div class="input-wrap input-wrap-text-search">';
            if ( $searchtext ) {
                printf( '<input type="text" name="textsearch" placeholder="Search city or zipcode ..." class="active" value="%s" />', $searchtext );
            } else {
                echo '<input type="text" name="textsearch" placeholder="Search city or zipcode ..." />';
            }
        echo '</div>';
        
        //* Reset
        printf( '<a href="%s" class="reset link-as-button">Reset</a>', get_permalink( get_the_ID() ) );
        
        //* Build the beds filter
        $beds = apartentsync_get_meta_values( 'beds', 'floorplans' );
        $beds = array_unique( $beds );
        asort( $beds );
                
        // beds
        echo '<div class="input-wrap input-wrap-beds">';
            echo '<div class="dropdown">';
                echo '<button type="button" class="dropdown-toggle" data-reset="Beds">Beds</button>';
                echo '<div class="dropdown-menu dropdown-menu-beds">';
                    echo '<div class="dropdown-menu-items">';
                        foreach( $beds as $bed ) {
                            if ( in_array( $bed, $bedsparam ) ) {
                                printf( '<label><input type="checkbox" data-beds="%s" name="beds-%s" checked /><span>%s Bedroom</span></label>', $bed, $bed, $bed );
                            } else {
                                printf( '<label><input type="checkbox" data-beds="%s" name="beds-%s" /><span>%s Bedroom</span></label>', $bed, $bed, $bed );
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
        
        //* Build the baths filter
        $baths = apartentsync_get_meta_values( 'baths', 'floorplans' );
        $baths = array_unique( $baths );
        asort( $baths );
        
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
        
        //* Move-in date
        echo '<div class="input-wrap input-wrap-move-in-date incomplete">';
            echo '<div class="dropdown">';
                echo '<button type="button" class="dropdown-toggle" data-reset="Move-in">Move-in</button>';
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
        
        //* Price range
        echo '<div class="input-wrap input-wrap-price-range incomplete">';
            echo '<div class="dropdown">';
                echo '<button type="button" class="dropdown-toggle" data-reset="Price">Price</button>';
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
                
        //* Amenities
        $number_of_amenities_to_show = get_field( 'number_of_amenities_to_show', 'option' );
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
                
        if ( !empty( $amenities ) ) {
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
        
        //* Build the pets filter
        $pets = apartentsync_get_meta_values( 'pets', 'properties' );
        $pets = array_unique( $pets );
        asort( $pets );
        $pets = array_filter( $pets );
        
        // Get the corresponding acf 'pets' field so that we can use its labels
        $field = get_field_object('field_60766489d1c66');
        $pets_choices = $field['choices'];
                
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
            
        //* Buttons
        // echo '<button type="reset">Reset</button>';
        
        // echo '<button type="submit">Apply filter</button>';
        echo '<input type="hidden" name="action" value="propertysearch">';
    echo '</form>';
    
    //* Our container markup for the results
    echo '<div class="map-response-wrap">';
        echo '<div id="response"></div>';
        echo '<a class="toggle"></a>';
        echo '<div id="map"></div>';
    echo '</div>';

    return ob_get_clean();
}

add_action( 'wp_ajax_propertysearch', 'apartmentsync_filter_properties' ); // wp_ajax_{ACTION HERE} 
add_action( 'wp_ajax_nopriv_propertysearch', 'apartmentsync_filter_properties' );
function apartmentsync_filter_properties(){
            
    //* start with floorplans
	$args = array(
        'post_type' => 'floorplans',
        'posts_per_page' => -1,
		'orderby' => 'date', // we will sort posts by date
		'order'	=> 'ASC', // ASC or DESC
        // 'cache_results' => false,
        // 'update_post_meta_cache' => false,
        // 'update_post_term_cache' => false,
        'no_found_rows' => true,
	);
        
    //* bedrooms
    $beds = apartentsync_get_meta_values( 'beds', 'floorplans' );
    $beds = array_unique( $beds );
    asort( $beds );
    
    // loop through the checkboxes, and for each one that's checked, let's add that value to our meta query array
    foreach ( $beds as $bed ) {
        if ( isset( $_POST['beds-' . $bed ] ) && $_POST['beds-' . $bed ] == 'on' ) {
            $bed = sanitize_text_field( $bed );
            $bedsarray[] = $bed;
        }
    }
    
    // add the meta query array to our $args
    if ( isset( $bedsarray ) ) {
        $args['meta_query'][] = array(
            array(
                'key' => 'beds',
                'value' => $bedsarray,
            )
        );
    }
    
    //* bathrooms
    $baths = apartentsync_get_meta_values( 'baths', 'floorplans' );
    $baths = array_unique( $baths );
    asort( $baths );
    
    // loop through the checkboxes, and for each one that's checked, let's add that value to our meta query array
    foreach ( $baths as $bath ) {
        if ( isset( $_POST['baths-' . $bath ] ) && $_POST['baths-' . $bath ] == 'on' ) {
            $bath = sanitize_text_field( $bath );
            $bathsarray[] = $bath;
        }
    }
    
    // add the meta query array to our $args
    if ( isset( $bathsarray ) ) {
        $args['meta_query'][] = array(
            array(
                'key' => 'baths',
                'value' => $bathsarray,
            )
        );
    }
    
    //* Remove anything with an unrealistically low value for rent
    $args['meta_query'][] = array(
        array(
            'key' => 'minimum_rent',
            'value' => 100,
            'compare' => '>',
        )
    );
    
    $args['meta_query'][] = array(
        array(
            'key' => 'maximum_rent',
            'value' => 100,
            'compare' => '>',
        )
    );
 
    // echo '<pre style="font-size: 14px;">';
    // print_r( $args );
    // echo '</pre>';
    
	$query = new WP_Query( $args );

    // echo '<pre style="font-size: 14px;">';
    // print_r( $query->post );
    // echo '</pre>';
    
    // reset the floorplans array
    $floorplans = array();
     
	if( $query->have_posts() ) :
        
        // printf( '<div class="count"><h2 class="post-count"><span class="number">%s</span> results</h2><p>Note: Right now this is searching floorplans. Long-term, it will need to search the floorplans first, then do a secondary search of the associated properties.</p></div>', $numberofposts );
        
            while( $query->have_posts() ): $query->the_post();
            
                $id = get_the_ID();
                $property_id = get_post_meta( $id, 'property_id', true );
                $beds = get_post_meta( $id, 'beds', true );
                $baths = get_post_meta( $id, 'baths', true );
                $minimum_rent = get_post_meta( $id, 'minimum_rent', true );
                $maximum_rent = get_post_meta( $id, 'maximum_rent', true );
                $minimum_sqft = get_post_meta( $id, 'minimum_sqft', true );
                $maximum_sqft = get_post_meta( $id, 'maximum_sqft', true );
                $available_units = get_post_meta( $id, 'available_units', true );
                
                if ( !isset( $floorplans[$property_id ] ) ) {
                    $floorplans[ $property_id ] = array(
                        'id' => array( $id ),
                        'beds' => array( $beds ),
                        'baths' => array( $baths ),
                        'minimum_rent' => array( $minimum_rent ),
                        'maximum_rent' => array( $maximum_rent ),
                        'minimum_sqft' => array( $minimum_sqft ),
                        'maximum_sqft' => array( $maximum_sqft ),
                        'available_units' => array( $available_units ),
                    );
                } else {
                    $floorplans[ $property_id ]['id'][] = $id;
                    $floorplans[ $property_id ]['beds'][] = $beds;
                    $floorplans[ $property_id ]['baths'][] = $baths;
                    $floorplans[ $property_id ]['minimum_rent'][] = $minimum_rent;
                    $floorplans[ $property_id ]['maximum_rent'][] = $maximum_rent;
                    $floorplans[ $property_id ]['minimum_sqft'][] = $minimum_sqft;
                    $floorplans[ $property_id ]['maximum_sqft'][] = $maximum_sqft;
                    $floorplans[ $property_id ]['available_units'][] = $available_units;
                }
                
            endwhile;
        
		wp_reset_postdata();
        
	endif;
    
    // echo '<pre style="font-size: 14px;">';
    // print_r( $floorplans );
    // echo '</pre>';
    
    foreach ( $floorplans as $key => $floorplan ) {
        $max = max( $floorplan['beds'] );
        $min = min( $floorplan['beds'] );
        
        if ( $max == $min ) {
            $floorplans[$key]['bedsrange'] = $max;
        } else {
            $floorplans[$key]['bedsrange'] = $min . '-' . $max;
        }
        
        $max = max( $floorplan['baths'] );
        $min = min( $floorplan['baths'] );
        
        if ( $max == $min ) {
            $floorplans[$key]['bathsrange'] = $max;
        } else {
            $floorplans[$key]['bathsrange'] = $min . '-' . $max;
        }
        
        $max = max( $floorplan['maximum_rent'] );
        $min = min( $floorplan['minimum_rent'] );
        
        if ( $max == $min ) {
            $floorplans[$key]['rentrange'] = '$' . $max;
        } else {
            $floorplans[$key]['rentrange'] = '$' . $min . '-' . $max;
        }
        
        if ( $min < 100 || $max < 100 )
            $floorplans[$key]['rentrange'] = 'Pricing unavailable';
        
        $max = max( $floorplan['maximum_sqft'] );
        $min = min( $floorplan['minimum_sqft'] );
        
        if ( $max == $min ) {
            $floorplans[$key]['sqftrange'] = $max;
        } else {
            $floorplans[$key]['sqftrange'] = $min . '-' . $max;
        }
        
    }
    
    $property_ids = array_keys( $floorplans );
    if ( empty( $property_ids ) )
    $property_ids = array( '1' ); // if there aren't any properties, we shouldn't find anything – empty array will let us find everything, so let's pass nonsense to make the search find nothing
    
    // echo '<pre style="font-size: 14px;">';
    // print_r( $property_ids );
    // echo '</pre>';
    
    //* The base property query
    $propertyargs = array(
        'post_type' => 'properties',
        'posts_per_page' => -1,
		'orderby' => 'menu_order',
		'order'	=> 'ASC', // ASC or DESC
        'no_found_rows' => true,
	);
    
    //* Add text-based search into the 
    $search = null;
    
    if ( isset( $_POST['textsearch'] ) ) {
        $search = $_POST['textsearch'];
        $search = sanitize_text_field( $search );
    }
        
    if ( $search != null ) {
        $propertyargs['s'] = $search;
        
        // force the site to use relevanssi if it's installed
        if ( function_exists( 'relevanssi_truncate_index_ajax_wrapper' ) )
            $propertyargs['relevanssi'] = true;
    }    
    
    //* Add all of our property IDs into the property search
    $propertyargs['meta_query'] = array(
        array(
            'key' => 'property_id',
            'value' => $property_ids,
        ),
    );
    
    //* Pets (this is a simple one)
    if ( isset( $_POST['pets'] ) ) {
        $propertyargs['meta_query'][] = array(
            array(
                'key' => 'pets',
                'value' => $_POST['pets'],
            )
        );
    }
    
    //* Add the tax queries
    $propertyargs['tax_query'] = array();
    
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
        $propertyargs['tax_query'][] = array(
            array(
                'taxonomy' => 'propertytypes',
                'terms' => $propertytypeids,
            )
        );
    }
    
    //* amenities taxonomy
    $number_of_amenities_to_show = get_field( 'number_of_amenities_to_show', 'option' );
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
            $propertyargs['tax_query'][] = array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'amenities',
                    'terms' => $amenity_term_id,
                )
            );
        }
    } 
    
    // echo '<pre>';
    // print_r( $propertyargs );
    // echo '</pre>';
    
    $propertyquery = new WP_Query( $propertyargs );
    
    // echo '<pre>';
    // print_r( $propertyquery );
    // echo '</pre>';
    
    if( $propertyquery->have_posts() ) :
        
        $numberofposts = $propertyquery->post_count;
        printf( '<div class="count"><h2 class="post-count"><span class="number">%s</span> results</h2></div>', $numberofposts );
        
        echo '<div class="properties-loop">';
            while( $propertyquery->have_posts() ): $propertyquery->the_post();
                $property_id = get_post_meta( get_the_ID(), 'property_id', true );
                $floorplan = $floorplans[$property_id ];
                do_action( 'apartmentsync_do_each_property', $propertyquery->post, $floorplan );
            endwhile;
        echo '</div>';
        
		wp_reset_postdata();
        
	else :
        
		echo 'No properties found matching the current search parameters.';
        
	endif;
 
	die();
}

