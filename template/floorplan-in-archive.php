<?php

add_action( 'apartmentsync_do_floorplan_in_archive', 'apartmentsync_floorplan_in_archive', 10, 1 );
function apartmentsync_floorplan_in_archive( $post ) {
    
    // styles for the layout
    wp_enqueue_style( 'apartmentsync-floorplan-in-archive' );
    
    // fancybox
    wp_enqueue_style( 'apartmentsync-fancybox-style' );
    wp_enqueue_script( 'apartmentsync-fancybox-script' );
    
    // slick
    wp_enqueue_script( 'apartmentsync-slick-main-script' );
    wp_enqueue_script( 'apartmentsync-floorplan-images-slider-init' );
    wp_enqueue_style( 'apartmentsync-slick-main-styles' );
    wp_enqueue_style( 'apartmentsync-slick-main-theme' );
    
    $post_id = $post->ID;
        
    //* Grab the data
    $title = get_the_title( $post_id );
    $available_units = get_field( 'available_units', $post_id );    
        
    //* Set up the classes
    $floorplanclass = get_post_class();
    
    if ( $available_units > 0 ) {
        $floorplanclass[] = 'units-available';
    } else {
        $floorplanclass[] = 'no-units-available';  
    } 
        
    $floorplanclass = implode( $floorplanclass, ' ' );

    //* Do the markup
    printf( '<div class="%s">', $floorplanclass );
        echo '<div class="floorplan-inner">';
        
            do_action( 'apartmentsync_do_each_floorplan_images' );
                
            echo '<div class="floorplan-content">';
            
                if ( $title )
                    printf( '<h3 class="floorplan-title">%s</h3>', $title );
                    
                do_action( 'apartmentsync_do_each_floorplan_availability' );
                    
                echo '<p class="info">';
                
                    do_action( 'apartmentsync_do_each_floorplan_beds' );
                    do_action( 'apartmentsync_do_each_floorplan_baths' ); 
                    do_action( 'apartmentsync_do_each_floorplan_squarefoot_range' );
                                                                
                echo '</p>';
                
                edit_post_link( 'Edit', '', '', $post_id );
                
            echo '</div>';  
            
            echo '<div class="floorplan-rent-range">';
            
                do_action( 'apartmentsync_do_each_floorplan_rent_range' );
                do_action( 'apartmentsync_do_each_floorplan_buttons' );
            
            echo '</div>'; // .floorplan-rent-range
            
          echo '</div>'; // .floorplan-inner  
        
    echo '</div>';
    
}

add_action( 'apartmentsync_do_each_floorplan_availability', 'apartmentsync_each_floorplan_availability' );
function apartmentsync_each_floorplan_availability() {
    
    $post_id = get_the_ID();
    
    $availability_date = get_post_meta( $post_id, 'availability_date', true );
    $availability_date = date('m/d/y', strtotime($availability_date));
    
    if ( $availability_date )
        printf( '<p class="availability-date"><span class="availability">Availability:</span> %s</p>', $availability_date );
}

add_action( 'apartmentsync_do_each_floorplan_baths', 'apartmentsync_each_floorplan_baths' );
function apartmentsync_each_floorplan_baths() {
    
    $post_id = get_the_ID();
        
    $baths = get_field( 'baths', $post_id );
    $baths = intval( $baths );
    
    if ( $baths )
        printf( '<span class="floorplan-baths">%s</span>', $baths );
    
}

add_action( 'apartmentsync_do_each_floorplan_beds', 'apartmentsync_each_floorplan_beds' );
function apartmentsync_each_floorplan_beds() {
    
    $post_id = get_the_ID();
        
    $beds = get_field( 'beds', $post_id );
    $beds = intval( $beds );
    
    if ( $beds === 0 )
        $beds = 'Studio';
        
    if ( $beds )
        printf( '<span class="floorplan-beds">%s</span>', $beds );
}


add_action( 'apartmentsync_do_each_floorplan_rent_range', 'apartmentsync_each_floorplan_rent_range' );
function apartmentsync_each_floorplan_rent_range() {
    
    $post_id = get_the_ID();
    $minimum_rent = get_field( 'minimum_rent', $post_id );
    $maximum_rent = get_field( 'maximum_rent', $post_id );
    
    $rent_range = null;
    if ( $minimum_rent && $maximum_rent ) {
        
        if ( $minimum_rent != $maximum_rent )
            $rent_range = sprintf( '<span class="dollars">$</span><span class="amount">%s</span>-<span class="amount">%s</span>', $minimum_rent, $maximum_rent );
            
        if ( $minimum_rent == $maximum_rent )
            $rent_range = sprintf( '<span class="dollars">$</span><span class="amount">%s</span>', $minimum_rent );
                    
    }
    
    if ( $minimum_rent < 100 || $maximum_rent < 100 )
        $rent_range = 'Pricing unavailable';
            
            
    if ( $minimum_rent && !$maximum_rent ) $rent_range = sprintf( '<span class="dollars">$</span><span class="amount">%s</span>', $minimum_rent );
    if ( $maximum_rent && !$minimum_rent ) $rent_range = sprintf( '<span class="dollars">$</span><span class="amount">%s</span>', $maximum_rent );
    
    if ( $rent_range )
        printf( '<p class="rent_range">%s</p>', $rent_range );
    
}

//TODO add the button settings
add_action( 'apartmentsync_do_each_floorplan_buttons', 'apartmentsync_each_floorplan_buttons' );
function apartmentsync_each_floorplan_buttons() {
    
    $post_id = get_the_ID();
    
    echo '<div class="floorplan-buttons">';
                
        echo '<a href="#" class="button">Sample button</a>';
        echo '<a href="#" class="button">Sample button</a>';
        
            //* CONTACT BUTTON (FLOORPLAN AVAILABLE)
            // we get the vars above, since these aren't layouts-specific
            
            if ( $settings['contactavailable_button_enabled'] == 1 ) {
                
                if ( $settings['contactavailable_button_type'] == 'link' )
                    printf( '<a href="%s" class="button contact-available-button" target="%s">%s</a>', $settings['contactavailable_link'], $settings['contactavailable_link_target'], $settings['contactavailable_customize_button_text'] );
                
                if ( $settings['contactavailable_button_type'] == 'gform' )
                    printf( '<a href="#" data-fancybox data-src="#contact-available-button-gform-%s" class="button contact-available-button">%s</a>', $settings['contactavailable_gravity_form_id'], $settings['contactavailable_customize_button_text'] );
            }
            
            //* AVAILABILITY BUTTON
            
            // if there's an override, use that (or default to the setting)
            $availability_url = get_field( 'availability_button_url_override' ) ?: $availability_url;
        
            // availability button
            if ( $availability_url && get_field( 'availability_button_enabled' ) == 1 ) {
                
                // get button info from settings                    
                $availability_button_text = get_field( 'availability_button_text' ) ?: 'Vew availability';
                $availability_button_target = get_field( 'availability_button_target' );
                                    
                printf( '<a href="%s" class="button availability-button" target="%s">%s</a>', $availability_url, $availability_button_target, $availability_button_text );
            }
            
        echo '</div>'; // .buttons
}

add_action( 'apartmentsync_do_each_floorplan_squarefoot_range', 'apartmentsync_each_floorplan_squarefoot_range' );
function apartmentsync_each_floorplan_squarefoot_range() {
    
    $post_id = get_the_ID();
    
    $maximum_sqft = get_field( 'maximum_sqft', $post_id );
    $minimum_sqft = get_field( 'minimum_sqft', $post_id );
    
    $sqft_range = null;
    
    if ( $minimum_sqft && $maximum_sqft ) {
        
        if ( $minimum_sqft != $maximum_sqft )
            $sqft_range = sprintf( '%s-%s', $minimum_sqft, $maximum_sqft );
            
        if ( $minimum_sqft == $maximum_sqft )
            $sqft_range = sprintf( '%s', $minimum_sqft );
        
    }
    
    if ( $minimum_sqft && !$maximum_sqft ) $sqft_range = sprintf( '%s', $minimum_sqft );
    if ( !$minimum_sqft && $maximum_sqft ) $sqft_range = sprintf( '%s', $maximum_sqft );
    
    if ( $sqft_range )
        printf( '<span class="floorplan-sqft_range">%s sqft</span>', $sqft_range );
}

add_action( 'apartmentsync_do_each_floorplan_images', 'apartmentsync_each_floorplan_images' );
function apartmentsync_each_floorplan_images() {
    
    $post_id = get_the_ID();
    
    $page_title = get_the_title();
    
    //* pull from the field (source is Yardi)
    $floorplan_image_urls = get_field( 'floorplan_image_url', $post_id );    
    
    if ( $floorplan_image_urls )
        $floorplan_image_urls = explode( ',', $floorplan_image_urls );
        
    //TODO add capabilities for manually adding multiple images. For now, we're making the selection always the Yardi images and automatic
    $floorplan_images = $floorplan_image_urls;
        
    // bail if ther aren't any images
    if ( !$floorplan_images )
        return;
    
    echo '<div class="floorplan-images-wrap">';
        echo '<div class="floorplan-slider">';
                    
            foreach( $floorplan_images as $floorplan_image ) {
                                                                                                                          
                // detect if there are special characters
                $regex = preg_match('[@_!#$%^&*()<>?/|}{~:]', $floorplan_image);
                                                        
                // bail on this slide if there are special characters in the image url
                if ( $regex )
                    break;
                                    
                echo '<div class="floorplan-slide">';
                    printf( '<a data-fancybox="gallery-%s" href="%s" ><img loading=lazy src="%s" alt="%s" title="%s" /></a>', $post_id, $floorplan_image, $floorplan_image, $page_title, $page_title );
                echo '</div>';
                
                // $count++;
                
            }
        
        echo '</div>';
                
    echo '</div>';
    
}