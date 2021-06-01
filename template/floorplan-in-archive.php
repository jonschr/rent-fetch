<?php

// add_action( 'apartmentsync_do_floorplan_in_floorplans_block', 'apartmentsync_floorplan_in_archive', 10, 1 );
add_action( 'apartmentsync_do_floorplan_in_archive', 'apartmentsync_floorplan_in_archive', 10, 1 );
function apartmentsync_floorplan_in_archive( $post_id ) {
    
    // styles for the layout
    wp_enqueue_style( 'apartmentsync-floorplan-in-archive' );
    
    // slick
    wp_enqueue_script( 'apartmentsync-slick-main-script' );
    wp_enqueue_script( 'apartmentsync-floorplan-images-slider-init' );
    wp_enqueue_style( 'apartmentsync-slick-main-styles' );
    wp_enqueue_style( 'apartmentsync-slick-main-theme' );
    
    // $post_id = $post->ID;
        
    //* Grab the data
    $title = get_the_title( $post_id );
    $available_units = get_field( 'available_units', $post_id );    
        
    //* Set up the classes
    $floorplanclass = get_post_class( $post_id );
    
    if ( $available_units > 0 ) {
        $floorplanclass[] = 'units-available';
    } else {
        $floorplanclass[] = 'no-units-available';  
    } 
        
    $floorplanclass = implode( ' ', $floorplanclass );

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
    $baths = floatval( $baths );
    
    // allow for hooking in
    $baths = apply_filters( 'apartmentsync_customize_baths_text', $baths );
    
    if ( $baths )
        printf( '<span class="floorplan-baths">%s</span>', $baths );
    
}

add_action( 'apartmentsync_do_each_floorplan_beds', 'apartmentsync_each_floorplan_beds' );
function apartmentsync_each_floorplan_beds() {
    
    $post_id = get_the_ID();
        
    $beds = get_field( 'beds', $post_id );
    $beds = floatval( $beds );
    
    // allow for hooking in
    $beds = apply_filters( 'apartmentsync_customize_beds_text', $beds );
        
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

add_action( 'apartmentsync_do_gform_lightbox', 'apartmentsync_gform_lightbox' );
function apartmentsync_gform_lightbox() {
    
    // fancybox
    wp_enqueue_style( 'apartmentsync-fancybox-style' );
    wp_enqueue_script( 'apartmentsync-fancybox-script' );
    
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
    
    printf( '<div id="gform-%s" class="apartmentsync-gform">', $gravity_form_id );
        echo do_shortcode( '[gravityform id="' . $gravity_form_id . '" title=false description=false ajax=true tabindex=49]');
    echo '</div>';
}

add_action( 'apartmentsync_do_each_floorplan_buttons', 'apartmentsync_each_floorplan_buttons' );
function apartmentsync_each_floorplan_buttons() {
    
    // fancybox
    wp_enqueue_style( 'apartmentsync-fancybox-style' );
    wp_enqueue_script( 'apartmentsync-fancybox-script' );
    
    $contact_button = get_field( 'contact_button', 'options' );
    $availability_button = get_field( 'availability_button', 'options' );
    
    echo '<div class="floorplan-buttons">';
    
        // each of the buttons is hooked onto this action, which allows for simpler reordering if needed
        do_action( 'apartmentsync_do_floorplan_each_button' );
            
    echo '</div>'; // .buttons
}

add_action( 'apartmentsync_do_floorplan_each_button', 'apartmentsync_default_tour_button', 5 );
function apartmentsync_default_tour_button() {
    
    // get the options
    $tour_button = get_field( 'tour_button', 'options' ); 
                
    if ( isset( $tour_button['enabled'] ) )
        $enabled = $tour_button['enabled'];
        
    //* bail if this button isn't enabled
    if ( $enabled !== true )
        return;
        
    $post_id = get_the_ID();
    $floorplan_video_or_tour = get_post_meta( $post_id, 'floorplan_video_or_tour', true );
                
    if ( isset( $tour_button['link_target'] ) )
        $link_target = $tour_button['link_target'];
        
    if ( isset( $tour_button['button_label'] ) )
        $button_label = $tour_button['button_label'];

    if ( $floorplan_video_or_tour && $link_target === 'lightbox' ) {
        
        if ( strpos( $floorplan_video_or_tour, 'youtube' ) !== false || strpos( $floorplan_video_or_tour, 'vimeo' ) !== false ) {
            // if this is a youtube/vimeo link
            printf( '<a href="#" data-fancybox data-src="%s" class="button tour-button">%s</a>', $floorplan_video_or_tour, $button_label );
        } else {
            // if it's anything else
            printf( '<a href="%s" data-fancybox data-type="iframe" class="button tour-button">%s</a>', $floorplan_video_or_tour, $button_label );
        }
        
    }
        
    if ( $floorplan_video_or_tour && $link_target === 'newtab' )
        printf( '<a href="%s" target="blank" class="button tour-button">%s</a>', $floorplan_video_or_tour, $button_label );
        
}

add_action( 'apartmentsync_do_floorplan_each_button', 'apartmentsync_default_availability_button', 10 );
function apartmentsync_default_availability_button() {
    
    $post_id = get_the_ID();
    
    // get the options
    $availability_button = get_field( 'availability_button', 'options' ); 
    
    // var_dump( $availability_button );
        
    if ( isset( $availability_button['enabled'] ) )
        $enabled = $availability_button['enabled'];
        
    //* bail if this button isn't enabled
    if ( $enabled !== true )
        return;
        
    if ( isset( $availability_button['button_label'] ) )
        $button_label = $availability_button['button_label'];
        
    if ( isset( $availability_button['button_behavior'] ) )
        $button_behavior = $availability_button['button_behavior'];
        
    if ( isset( $availability_button['link'] ) )
        $link = $availability_button['link'];
        
    $availability_url = get_post_meta( $post_id, 'availability_url', true );    
    $available_units = get_post_meta( $post_id, 'available_units', true );
        
    // if there's a specific link, use that instead
    if ( $availability_url )
        $link = $availability_url;
        
        
    // bail if there's no link to output
    if ( !$link )
        return;
        
    if ( $available_units > 0 || $button_behavior === 'fallback' )
        printf( '<a href="%s" class="button availability-button" target="_blank">%s</a>', $link, $button_label );
        
}

add_action( 'apartmentsync_do_floorplan_each_button', 'apartmentsync_default_contact_button', 15 );
function apartmentsync_default_contact_button() {
    
    // ob_start();
        
    // get the options
    $contact_button = get_field( 'contact_button', 'options' ); 
        
    if ( isset( $contact_button['enabled'] ) )
        $enabled = $contact_button['enabled'];
        
    //* bail if this button isn't enabled
    if ( $enabled !== true )
        return;
    
    if ( isset( $contact_button['button_type'] ) )
        $button_type = $contact_button['button_type'];
        
    if ( isset( $contact_button['link'] ) )
        $link = $contact_button['link'];
    
    if ( isset( $contact_button['link_target'] ) )
        $link_target = $contact_button['link_target'];
    
    if ( isset( $contact_button['gravity_form_id'] ) )
        $gravity_form_id = $contact_button['gravity_form_id'];
    
    if ( isset( $contact_button['button_label'] ) )
        $button_label = $contact_button['button_label'];
    
    if ( $button_type === 'link' && !empty( $link ) && !empty( $link_target ) && !empty( $button_label ) )
        printf( '<a href="%s" target="%s" class="button contact-button">%s</a>', $link, $link_target, $button_label );
            
    if ( $button_type === 'gform' && !empty( $gravity_form_id ) )
        printf( '<a href="#" data-fancybox data-src="#gform_wrapper_%s" class="button contact-button">%s</a>', $gravity_form_id, $button_label );
        
    // return ob_get_clean();
        
}

add_action( 'apartmentsync_do_floorplan_each_button', 'apartmentsync_default_single_button', 20 );
function apartmentsync_default_single_button() {
    
    // get the options
    $single_button = get_field( 'single_button', 'options' ); 
        
    if ( isset( $single_button['enabled'] ) )
        $enabled = $single_button['enabled'];
        
    //* bail if this button isn't enabled
    if ( $enabled !== true )
        return;
        
    $post_id = get_the_ID();
    $permalink = get_the_permalink( $post_id );
            
    if ( isset( $single_button['button_label'] ) )
        $button_label = $single_button['button_label'];
    
    if ( $permalink )
        printf( '<a href="%s" class="button single-button">%s</a>', $permalink, $button_label );
    
        
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
    
    $sqft_range = apply_filters( 'apartmentsync_customize_sqft_text', $sqft_range );
    
    if ( $sqft_range )
        printf( '<span class="floorplan-sqft_range">%s</span>', $sqft_range );
}

add_filter( 'apartmentsync_customize_sqft_text', 'apartmentsync_default_beds_text', 10, 1  );
function apartmentsync_default_beds_text( $sqft_range ) {
    
    // bail if there isn't a value
    if ( $sqft_range == null )
        return;
        
    return $sqft_range . ' sqft';
}

add_action( 'apartmentsync_do_each_floorplan_images', 'apartmentsync_each_floorplan_images' );
function apartmentsync_each_floorplan_images() {
    
    // fancybox
    wp_enqueue_style( 'apartmentsync-fancybox-style' );
    wp_enqueue_script( 'apartmentsync-fancybox-script' );
    
    $post_id = get_the_ID();
    
    $page_title = get_the_title();
    
    //* get the images from whatever source we have
    $floorplan_image_urls = null;
    $floorplan_images = apply_filters( 'floorplan_image_urls', $floorplan_image_urls );
                
    // if there aren't any images, then output a fallback
    if ( !$floorplan_images ) {
        
        $floorplan_image = APARTMENTSYNC_PATH . 'images/fallback-property.svg';
        
        echo '<div class="floorplan-images-wrap">';
        
            do_action( 'apartmentsync_floorplan_in_archive_do_show_specials' );
            
            echo '<div class="floorplan-slider">';
                    echo '<div class="floorplan-slide">';
                        printf( '<img loading=lazy src="%s" alt="%s" title="%s" />', $floorplan_image, $page_title, $page_title );
                    echo '</div>';
            echo '</div>';
        echo '</div>';
        
    } else {
        
        echo '<div class="floorplan-images-wrap">';
        
            do_action( 'apartmentsync_floorplan_in_archive_do_show_specials' );
            
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
}

$floorplan_image_urls = null;
add_filter( 'floorplan_image_urls', 'floorplan_get_image_urls', $floorplan_image_urls );
function floorplan_get_image_urls() {
    
    // get the ID of the post we're on
    $post_id = get_the_ID();
    
    // set the value of floorplan_image_urls to nothing
    $floorplan_image_urls = array();
    
    //* Try for manual images first
    $manual_images = get_field( 'manual_images', $post_id );
            
    if ( $manual_images !== null && $manual_images !== false ) {
        
        foreach ( $manual_images as $manual_image ) {
            $floorplan_image_urls[] = $manual_image['sizes']['large'];
        }
        
        return $floorplan_image_urls;
    }
      
    //* Try for Yardi images next
    $yardi_image_urls = get_field( 'floorplan_image_url', $post_id );
        
    if ( $yardi_image_urls ) {
        
        $floorplan_image_urls = explode( ',', $yardi_image_urls );
        
        return $floorplan_image_urls;    
    }
        
    //* if we didn't find any images, just return nothing
    return null;
    
}


add_action( 'apartmentsync_floorplan_in_archive_do_show_specials', 'apartmentsync_floorplan_in_archive_show_specials' );
function apartmentsync_floorplan_in_archive_show_specials() {
    $post_id = get_the_ID();
    
    $has_specials = get_post_meta( $post_id, 'has_specials', true );
    
    // bail if there are no specials
    if ( $has_specials != true )
        return;
        
    $text = null; // set $text to null, since we're not passing anything in
    $specials_text = apply_filters( 'apartmentsync_has_specials_text', $text );    
    printf( '<div class="has-specials-floorplan">%s</div>', $specials_text );
}

add_filter( 'apartmentsync_has_specials_text', 'apartmentsync_default_specials_text', 10, 1 );
function apartmentsync_default_specials_text( $specials_text ) {
    $specials_text = 'Specials available';
    return $specials_text;
} 