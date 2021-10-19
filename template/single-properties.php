<?php

//* Add a body class
add_filter( 'body_class', 'apartmentsync_add_properties_body_class' );
function apartmentsync_add_properties_body_class( $classes ) {
    global $post;
    
    if ( isset( $post ) )
        $classes[] = 'single-properties-template';
    
    return $classes;
    
}

////////////
// MARKUP //
////////////

get_header();

wp_enqueue_style( 'rentfetch-single-properties' );

//* Markup
echo '<div class="single-properties-wrap">';

    do_action( 'rentfetch_do_single_properties' );
    
echo '</div>'; // .single-properties-wrap

get_footer();