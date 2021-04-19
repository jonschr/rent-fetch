<?php

//* Register the block
add_action('acf/init', 'apartmentsync_floorplangrid_block_register_block');
function apartmentsync_floorplangrid_block_register_block() {

    // Check function exists.
    if( function_exists( 'acf_register_block_type') ) {

        // register a testimonial block.
        acf_register_block_type(array(
            'name'              => 'floorplangrid',
            'title'             => __('Floorplan grid'),
            'description'       => __('A block to show a floorplan grid with simple filters'),
            'render_callback'   => 'apartmentsync_floorplangrid_block_render',
            'enqueue_assets'    => 'apartmentsync_floorplangrid_block_enqueue',
            'category'          => 'formatting',
            'icon'              => 'table-col-before',
            'keywords'          => array( 'floorplan', 'apartment', 'grid', 'availability' ),
            'mode'              => 'preview',
            'align'              => 'normal',
            'supports'          => array(
                'align' => array( 'full', 'wide', 'normal' ),
                'mode' => false,
                'jsx' => true
            ),
        ));
    }
}

//* Enqueues
function apartmentsync_floorplangrid_block_enqueue() {
    wp_enqueue_style( 'floorplangrid-style', APARTMENTSYNC_PATH . 'css/floorplangrid.css', array(), APARTMENTSYNC_VERSION, 'screen' );
    
    // Fancybox
    wp_enqueue_style( 'apartmentsync-fancybox-style', APARTMENTSYNC_PATH . 'vendor/fancybox/jquery.fancybox.min.css', array(), APARTMENTSYNC_VERSION, 'screen' );
    wp_enqueue_script( 'apartmentsync-fancybox-script', APARTMENTSYNC_PATH . 'vendor/fancybox/jquery.fancybox.min.js', array( 'jquery' ), APARTMENTSYNC_VERSION, true );
    
    wp_register_script( 'apartmentsync-filters', APARTMENTSYNC_PATH . 'block/floorplangrid/js/filters.js', array( 'jquery' ), APARTMENTSYNC_VERSION, true );
    
}

function apartmentsync_floorplangrid_block_render( $block, $content = '', $is_preview = false, $post_id = 0 ) {
        
    //* Default class
    $className = 'floorplangrid';
    
    //* Default ID
    $id = 'floorplangrid-' . $block['id'];
    
    //* Get settings and put them in an object so that we can use them elsewhere
    $settings = array(
        'columns' => get_field( 'columns' ),
        'floorplan_filter' => get_field( 'floorplan_filter' ),
        'floorplan_limit' => get_field( 'floorplan_limit' ),
        'limit_number_of_bedrooms' => get_field( 'limit_number_of_bedrooms' ),
        'limit_floorplan_type' => get_field( 'limit_floorplan_type' ),
        'contactavailable_button_enabled' => get_field( 'contactavailable_button_enabled' ),
        'contactavailable_customize_button_text' => get_field( 'contactavailable_customize_button_text' ),
        'contactavailable_button_type' => get_field( 'contactavailable_button_type' ),
        'contactavailable_link' => get_field( 'contactavailable_link' ),
        'contactavailable_link_target' => get_field( 'contactavailable_link_target' ),
        'contactavailable_gravity_form_id' => get_field( 'contactavailable_gravity_form_id' ),
        '0_bedrooms_label' => get_field( '0_bedrooms_label' ),
        '1_bedrooms_label' => get_field( '1_bedrooms_label' ),
        '2_bedrooms_label' => get_field( '2_bedrooms_label' ),
        '3_bedrooms_label' => get_field( '3_bedrooms_label' ),
        '4_bedrooms_label' => get_field( '4_bedrooms_label' ),
        '5_bedrooms_label' => get_field( '5_bedrooms_label' ),
    );

    // Create id attribute allowing for custom "anchor" value.
    if( !empty($block['anchor']) ) 
        $id = $block['anchor'];

    // Create class attribute allowing for custom "className" and "align" values.
    if( !empty($block['className']) )
        $className .= ' ' . $block['className'];

    if( !empty($block['align']) )
        $className .= ' align' . $block['align'];
        
                            
    //* Render
    printf( '<div id="%s" class="%s">', $id, $className );

       apartmentsync_floorplangrid_output_gform( $settings );
        
        // The Query
        $floorplans = apartmentsync_floorplangrid_block_get_posts( $settings );
        
        if ( !$floorplans )
            echo 'No floorplans found using available criteria.';
        
        apartmentsync_floorplangrid_block_show_filter( $settings );
        
        printf( '<div class="floorplangrid-wrap columns-%s">', $settings['columns'] );
            foreach ( $floorplans as $floorplan ) {
                
                // print_r( $floorplan );
                                                    
                apartmentsync_floorplangrid_each( $floorplan->ID, $settings );
                
            }
        echo '</div>';
                
    echo '</div>';    
   
}

//* Output the gravity form if needed
function apartmentsync_floorplangrid_output_gform( $settings ) {
    // Gform lightbox markup (has to just be done once outside the individual markup so that we aren't loading the same gform 50 times)
    if ( $settings['contactavailable_button_enabled'] == 1 && $settings['contactavailable_button_type'] == 'gform') {
        printf( '<div class="apartmentsync-fancybox-container" id="contact-available-button-gform-%s">', $settings['contactavailable_gravity_form_id'] );
            echo do_shortcode( '[gravityform id=' . $settings['contactavailable_gravity_form_id'] . ' title=true description=true ajax=true tabindex=49]' );
        echo '</div>';        
    }
}

//* Add the filters
function apartmentsync_floorplangrid_block_show_filter( $settings ) {
    
    // bail if we aren't filtering
    if ( !$settings['floorplan_filter' ] )
        return;
        
    wp_enqueue_script( 'apartmentsync-filters' );
        
    if ( $settings['floorplan_filter'] == 'bedrooms' )
        apartmentsync_floorplangrid_block_show_filter_bedrooms( $settings );
    
}

// if we're filtering by bedroom...
function apartmentsync_floorplangrid_block_show_filter_bedrooms( $settings ) {
    
    $posts = apartmentsync_floorplangrid_block_get_posts( $settings );
    $meta_values = array();
    foreach( $posts as $post ) {
        $meta_values[] = get_post_meta( $post->ID, 'beds', true );
    }
    
    $bedrooms = array_count_values( $meta_values );
    
    ksort( $bedrooms );
    $bedroomnumbers = array_keys( $bedrooms );
    
    echo '<ul class="filters">';
        printf( '<li><a data-filter="%s" class="active filter-select" href="#">%s</a></li>', 'floorplan', 'All' );
        
        foreach ( $bedroomnumbers as $bedroomnumber ) {
            
            $label = apartmentsync_floorplangrid_number_of_bedrooms_label( $bedroomnumber, $settings );
            printf( '<li><a data-filter="beds-%s" class="filter-select" href="#">%s</a></li>', $bedroomnumber, $label );
        }
    echo '</ul>';
}

//* Output each floorplan
function apartmentsync_floorplangrid_each( $post_id, $settings ) {
    
    //* Grab the data
    $title = get_the_title( $post_id );
    $availability_url = get_field( 'availability_url', $post_id );
    $available_units = get_field( 'available_units', $post_id );
    $numberofbaths = get_field( 'baths', $post_id );
    $numberofbeds = get_field( 'beds', $post_id );
    $has_specials = get_field( 'has_specials', $post_id );
    $floorplan_id = get_field( 'floorplan_id', $post_id );
    $floorplan_image_url = get_field( 'floorplan_image_url', $post_id );
    $floorplan_image_name = get_field( 'floorplan_image_name', $post_id );
    $floorplan_image_alt_text = get_field( 'floorplan_image_alt_text', $post_id );
    $maximum_deposit = get_field( 'maximum_deposit', $post_id );
    $maximum_rent = get_field( 'maximum_rent', $post_id );
    $maximum_sqft = get_field( 'maximum_sqft', $post_id );
    $minimum_deposit = get_field( 'minimum_deposit', $post_id );
    $minimum_rent = get_field( 'minimum_rent', $post_id );
    $minimum_sqft = get_field( 'minimum_sqft', $post_id );
    $property_id = get_field( 'property_id', $post_id );
    $property_show_specials = get_field( 'property_show_specials', $post_id );
    $unit_type_mapping = get_field( 'unit_type_mapping', $post_id );
    $floorplan_source = get_post_meta( $post_id, 'floorplan_source', true );
        
    //* Figure things out
    $beds = apartmentsync_floorplangrid_number_of_bedrooms_label( $numberofbeds, $settings );
    
    // baths
    if ( $numberofbaths === '0' ) $baths = '0 Bath';
    if ( $numberofbaths === '1' ) $baths = '1 Bath';
    if ( $numberofbaths === '2' ) $baths = '2 Bath';
    if ( $numberofbaths === '3' ) $baths = '3 Bath';
    if ( $numberofbaths === '4' ) $baths = '4 Bath';
    if ( $numberofbaths === '5' ) $baths = '5 Bath';
    
    // thumb
    if ( $floorplan_image_url ) {
        $floorplan_image_url = explode( ',', $floorplan_image_url );
        $floorplan_image_url = $floorplan_image_url[0];
    }
    
    if ( !$floorplan_image_url )
        $floorplan_image_url = get_the_post_thumbnail_url( $post_id, 'large' );
    
    // rent
    $rent_range = null;
    if ( $minimum_rent && $maximum_rent ) {
        
        if ( $minimum_rent != $maximum_rent )
            $rent_range = sprintf( '<span class="dollars">$</span><span class="amount">%s</span> - <span class="amount">%s</span>', $minimum_rent, $maximum_rent );
            
        if ( $minimum_rent == $maximum_rent )
            $rent_range = sprintf( '<span class="dollars">$</span><span class="amount">%s</span>', $minimum_rent );
            
    }
    if ( $minimum_rent && !$maximum_rent ) $rent_range = sprintf( '<span class="dollars">$</span><span class="amount">%s</span>', $minimum_rent );
    if ( $maximum_rent && !$minimum_rent ) $rent_range = sprintf( '<span class="dollars">$</span><span class="amount">%s</span>', $maximum_rent );
    
    // sqft
    $sqft_range = null;
    $sqft_label = 'sq. ft.';
    
    if ( $minimum_sqft && $maximum_sqft ) {
        
        if ( $minimum_sqft != $maximum_sqft )
            $sqft_range = sprintf( '%s-%s %s', $minimum_sqft, $maximum_sqft, $sqft_label );
            
        if ( $minimum_sqft == $maximum_sqft )
            $sqft_range = sprintf( '%s %s', $minimum_sqft, $sqft_label );
        
    }
    
    if ( $minimum_sqft && !$maximum_sqft ) $sqft_range = sprintf( '%s %s', $minimum_sqft, $sqft_label );
    if ( !$minimum_sqft && $maximum_sqft ) $sqft_range = sprintf( '%s %s', $maximum_sqft, $sqft_label );
    
    //* Set up the classes
    $floorplanclass = array( 'floorplan', 'floorplan-' . $post_id );
    
    if ( $available_units > 0 ) {
        $floorplanclass[] = 'units-available';
    } else {
        $floorplanclass[] = 'no-units-available';  
    } 
        
    $floorplanclass = implode( $floorplanclass, ' ' );
    
    // echo '<pre>';
    //     print_r( $block['data'] );
    // echo '</pre>';

    //* Do the markup
    printf( '<div class="%s beds-%s baths-%s">', $floorplanclass, $numberofbeds, $numberofbaths );
            
        echo '<div class="floorplangrid__art-wrap">';
        
            if ( $floorplan_image_url ) 
                printf( '<div class="floorplangrid__image-wrap"><a href="%s" data-fancybox="%s" class="floorplangrid__image-link"><img height="200px" width="300px" class="floorplangrid__image" src="%s" title="%s" alt="%s" /></a></div>', $floorplan_image_url, $post_id, $floorplan_image_url, $floorplan_image_name, $floorplan_image_alt_text );
                
        echo '</div>';
            
        echo '<div class="floorplangrid__content">';
        
            if ( $title )
                printf( '<h3 class="floorplangrid__title">%s</h3>', $title );
                
            echo '<p class="floorplangrid__info">';
            
                if ( $beds )
                    printf( '<span class="floorplangrid__beds">%s</span>', $beds );
                    
                if ( $baths )
                    printf( '<span class="floorplangrid__baths">%s</span>', $baths );
                    
                if ( $sqft_range )
                    printf( '<span class="floorplangrid__sqft_range">%s</span>', $sqft_range );
                                    
            echo '</p>';
            
            if ( $rent_range )
                printf( '<p class="floorplangrid__rent_range">%s</p>', $rent_range );
            
            echo '<div class="buttons">';
            
                //* CONTACT BUTTON (FLOORPLAN AVAILABLE)
                // we get the vars above, since these aren't layouts-specific
                
                if ( $settings['contactavailable_button_enabled'] == 1 ) {
                    
                    if ( $settings['contactavailable_button_type'] == 'link' )
                        printf( '<a href="%s" class="floorplangrid__button floorplangrid__contact-available-button" target="%s">%s</a>', $settings['contactavailable_link'], $settings['contactavailable_link_target'], $settings['contactavailable_customize_button_text'] );
                    
                    if ( $settings['contactavailable_button_type'] == 'gform' )
                        printf( '<a href="#" data-fancybox data-src="#contact-available-button-gform-%s" class="floorplangrid__button floorplangrid__contact-available-button">%s</a>', $settings['contactavailable_gravity_form_id'], $settings['contactavailable_customize_button_text'] );
                }
                
                //* AVAILABILITY BUTTON
                
                // if there's an override, use that (or default to the setting)
                $availability_url = get_field( 'availability_button_url_override' ) ?: $availability_url;
            
                // availability button
                if ( $availability_url && get_field( 'availability_button_enabled' ) == 1 ) {
                    
                    // get button info from settings                    
                    $availability_button_text = get_field( 'availability_button_text' ) ?: 'Vew availability';
                    $availability_button_target = get_field( 'availability_button_target' );
                                        
                    printf( '<a href="%s" class="floorplangrid__button floorplangrid__availability-button" target="%s">%s</a>', $availability_url, $availability_button_target, $availability_button_text );
                }
                
            echo '</div>'; // .buttons
            
            edit_post_link( 'Edit', '', '', $post_id );
                            
        echo '</div>';  
        
    echo '</div>';
}

//* We do the query to get the posts then return the results of get_posts
function apartmentsync_floorplangrid_block_get_posts( $settings ) {
        
    // limits
    $limit_number_of_bedrooms = $settings['limit_number_of_bedrooms'];
    $limit_floorplan_type = $settings['limit_floorplan_type'];
        
    $args = array(
        'post_type' => 'floorplans',
        'posts_per_page' => '-1'
    ); 
    
    //* Limit by bedroom
    if ( $settings['floorplan_limit'] == 'bedrooms' ) {
        if ( $limit_number_of_bedrooms ) {    
            
            $arr = array( 
                'meta_query' => array(
                    'relation' => 'OR',
                )
            );
                
            foreach ( $limit_number_of_bedrooms as $limit_number_of_bedroom ) {
                $arr_limit = array(
                    'key'     => 'beds',
                    'value'   => $limit_number_of_bedroom,
                    'compare' => '=',
                );
                
                $arr['meta_query'][] = $arr_limit;
                
            }
            
            // echo '<pre>';
            // print_r( $arr );
            // echo '</pre>';
            
            $args = array_merge( $args, $arr );
            
        }
    }
    
    //* Limit by tax
    if ( $settings['floorplan_limit'] == 'floorplantype' ) {
        if ( $limit_floorplan_type ) { 
                        
            $arr = array(
                'tax_query' => array(
                    'relation' => 'OR',
                    array(
                        'taxonomy' => 'floorplantype',
                        'field'    => 'term_id',
                        'terms'    => array()
                    ),
                )
            );
                
            foreach ( $limit_floorplan_type as $limit_tax ) {
                $arr['tax_query']['0']['terms'][] = $limit_tax;
            }
            
            $args = array_merge( $args, $arr );
            
        }
    }
        
    // echo '<pre>';
    // print_r( $args );
    // echo '</pre>';
    
    $floorplans = get_posts( $args );
        
    return $floorplans;
    
}

//todo probably deprecate this
function apartmentsync_floorplangrid_number_of_bedrooms_label( $numberofbeds, $settings ) {
     
    $string = sprintf( '%s_bedrooms_label', $numberofbeds );
    
    if ( !empty( $settings[$string] ) ) {
        $bedslabel = $settings[$string];
    } else {
        $bedslabel = sprintf( '%s bedroom', $numberofbeds );
    }
            
    return $bedslabel;
} 