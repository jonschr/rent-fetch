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
        
    // Fancybox
    wp_enqueue_style( 'apartmentsync-fancybox-style', APARTMENTSYNC_PATH . 'vendor/fancybox/jquery.fancybox.min.css', array(), APARTMENTSYNC_VERSION, 'screen' );
    wp_enqueue_script( 'apartmentsync-fancybox-script', APARTMENTSYNC_PATH . 'vendor/fancybox/jquery.fancybox.min.js', array( 'jquery' ), APARTMENTSYNC_VERSION, true );
    
    // Specific scripts and styles
    wp_enqueue_style( 'floorplangrid-style', APARTMENTSYNC_PATH . 'css/floorplangrid.css', array(), APARTMENTSYNC_VERSION, 'screen' );
    wp_register_script( 'apartmentsync-filters', APARTMENTSYNC_PATH . 'block/floorplangrid/js/filters.js', array( 'jquery' ), APARTMENTSYNC_VERSION, true );
    
    // Slick
    wp_enqueue_script( 'apartmentsync-slick-main-script' );
    wp_enqueue_script( 'apartmentsync-floorplan-images-slider-init' );
    wp_enqueue_style( 'apartmentsync-slick-main-styles' );
    wp_enqueue_style( 'apartmentsync-slick-main-theme' );
        
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
        'maximum_number_of_floorplans_to_show' => get_field( 'maximum_number_of_floorplans_to_show' ),
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
       apartmentsync_floorplangrid_block_show_filter( $settings );
        
        // The Query
        $floorplans_query = apartmentsync_floorplangrid_block_get_posts( $settings );
        
        // var_dump( $floorplans_query );
        
        if ( $floorplans_query->have_posts() ) {
            
            printf( '<div class="floorplangrid-wrap columns-%s">', $settings['columns'] );
            
            while ( $floorplans_query->have_posts() ) {
                $floorplans_query->the_post();
                
                    do_action( 'apartmentsync_do_floorplan_in_floorplans_block', get_the_ID() );
                
            }
            
            echo '</div>'; // .floorplangrid-wrap
        } else {
            echo 'No floorplans found using available criteria.';
        }
                
    echo '</div>';    
   
}

/**
 * Render each floorplan
 *
 * @param   string  $post_id 
 *
 * @return  none
 */
add_action( 'apartmentsync_do_floorplan_in_floorplans_block', 'apartmentsync_floorplangrid_render_each_floorplan', 10, 1 );
function apartmentsync_floorplangrid_render_each_floorplan( $post_id ) {
    
    //* Grab the data
    $title = get_the_title( $post_id );
    $available_units = get_field( 'available_units', $post_id );
    $number_of_baths = get_field( 'baths', $post_id );
    $number_of_beds = get_field( 'beds', $post_id ); 
        
    //* Set up the classes
    $floorplanclass = get_post_class( $post_id );
    
    if ( $available_units > 0 ) {
        $floorplanclass[] = 'units-available';
    } else {
        $floorplanclass[] = 'no-units-available';  
    } 
    
    if ( $number_of_beds !== null )
        $floorplanclass[] = sprintf( 'beds-%s', $number_of_beds );
        
    if ( $number_of_baths !== null )
        $floorplanclass[] = sprintf( 'baths-%s', $number_of_baths );
        
    $floorplanclass = implode( ' ', $floorplanclass );

    //* Do the markup
    printf( '<div class="%s">', $floorplanclass );
        echo '<div class="floorplan-inner">';
        
            do_action( 'apartmentsync_do_each_floorplan_images' );
                
            echo '<div class="floorplangrid__content">';
            
                if ( $title )
                    printf( '<h3 class="floorplangrid__title">%s</h3>', $title );
                    
                do_action( 'apartmentsync_do_each_floorplan_availability' );
                    
                echo '<p class="floorplangrid__info">';
                
                    add_filter( 'apartmentsync_customize_beds_text', 'apartmentsync_floorplansgrid_customize_beds_text', 10, 1 );
                    do_action( 'apartmentsync_do_each_floorplan_beds' );
                    
                    add_filter( 'apartmentsync_customize_baths_text', 'apartmentsync_floorplansgrid_customize_baths_text', 10, 1 );
                    do_action( 'apartmentsync_do_each_floorplan_baths' ); 
                    
                    do_action( 'apartmentsync_do_each_floorplan_squarefoot_range' );
                                                                
                echo '</p>';
                
                echo '<div class="floorplangrid__rent_range">';
            
                    do_action( 'apartmentsync_do_each_floorplan_rent_range' );
                    
                echo '</div>'; // .floorplan-rent-range
                echo '<div class="buttons">';
                    
                    do_action( 'apartmentsync_do_each_floorplan_buttons' );
                    
                echo '</div>';
                
                edit_post_link( 'Edit', '', '', $post_id );
                
            echo '</div>';  
            
          echo '</div>'; // .floorplan-inner  
        
    echo '</div>';
    
}

function apartmentsync_floorplansgrid_customize_beds_text( $beds ) {
    if ( $beds == 0 )
        return 'Studio';
        
    if ( $beds == 1 )
        return '1 Bedroom';
        
    return $beds . ' Bedrooms';
}

function apartmentsync_floorplansgrid_customize_baths_text( $baths ) {
    return $baths . ' Baths';
}

//* Output the gravity form if needed
function apartmentsync_floorplangrid_output_gform() {    
    
    // get the options
    $contact_button = get_field( 'contact_button', 'options' ); 
            
    if ( isset( $contact_button['enabled'] ) )
        $enabled = $contact_button['enabled'];
        
    if ( isset( $contact_button['gravity_form_id'] ) )
        $gravity_form_id = $contact_button['gravity_form_id'];
        
    //* bail if this button isn't enabled
    if ( $enabled !== true )
        return;
    
    //* bail if there's no gravity form ID specified
    if ( !$gravity_form_id )
        return;
    
    printf( '<div style="display:none;" id="gform-%s" class="apartmentsync-gform">', $gravity_form_id );
        echo do_shortcode( '[gravityform id="' . $gravity_form_id . '" title=false description=false ajax=true tabindex=49]');
    echo '</div>';
    
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
    
    $floorplans_filter_query = apartmentsync_floorplangrid_block_get_posts( $settings );
    $meta_values = array();
    
    if ( $floorplans_filter_query->have_posts() ) {
        while ( $floorplans_filter_query->have_posts() ) {
            $floorplans_filter_query->the_post();
            
            $meta_values[] = get_post_meta( get_the_ID(), 'beds', true );
        }
    }
    
    wp_reset_query();
    
    $bedrooms = array_count_values( $meta_values );
    
    ksort( $bedrooms );
    $bedroomnumbers = array_keys( $bedrooms );
        
    echo '<ul class="filters">';
        printf( '<li><a data-filter="%s" class="active filter-select" href="#">%s</a></li>', 'floorplans', 'All' );
        
        foreach ( $bedroomnumbers as $bedroomnumber ) {
            
            $label = apartmentsync_floorplangrid_number_of_bedrooms_label( $bedroomnumber, $settings );
            printf( '<li><a data-filter="beds-%s" class="filter-select" href="#">%s</a></li>', $bedroomnumber, $label );
        }
    echo '</ul>';
}

//* We do the query to get the posts then return the results
function apartmentsync_floorplangrid_block_get_posts( $settings ) {
        
    // limits
    $limit_number_of_bedrooms = $settings['limit_number_of_bedrooms'];
    $limit_floorplan_type = $settings['limit_floorplan_type'];
    $maximum_number_of_floorplans_to_show = $settings['maximum_number_of_floorplans_to_show'];
        
    $args = array(
        'post_type' => 'floorplans',
        'posts_per_page' => $maximum_number_of_floorplans_to_show,
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
    
    // $floorplans = get_posts( $args );
    
    $floorplans = new WP_Query( $args );
        
    return $floorplans;
    
}

//todo probably deprecate this
function apartmentsync_floorplangrid_number_of_bedrooms_label( $numberofbeds, $settings ) {
     
    $string = sprintf( '%s_bedrooms_label', $numberofbeds );
    
    if ( !empty( $settings[$string] ) ) {
        $bedslabel = $settings[$string];
    } elseif ( !$numberofbeds ){
        $bedslabel = 'Studio';
    } else {
        $bedslabel = sprintf( '%s Bedroom', $numberofbeds );
    }    
            
    return $bedslabel;
} 