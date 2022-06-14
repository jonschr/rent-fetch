<?php

add_action( 'rentfetch_do_single_properties', 'rentfetch_single_property_title', 10 );
add_action( 'rentfetch_do_single_properties', 'rentfetch_single_property_images', 20 );
add_action( 'rentfetch_do_single_properties', 'rentfetch_single_property_basic_info', 30 );
add_action( 'rentfetch_do_single_properties', 'rentfetch_single_property_description', 40 );
add_action( 'rentfetch_do_single_properties', 'rentfetch_single_property_floorplans', 50 );
add_action( 'rentfetch_do_single_properties', 'rentfetch_single_property_amenities', 60 );
add_action( 'rentfetch_do_single_properties', 'rentfetch_single_property_lease_details', 70 );
add_action( 'rentfetch_do_single_properties', 'rentfetch_single_property_map', 80 );
add_action( 'rentfetch_do_single_properties', 'rentfetch_single_property_neighborhood', 90 );
add_action( 'rentfetch_do_single_properties', 'rentfetch_single_property_nearby_properties', 100 );

function rentfetch_single_property_title() {
        
    global $post;
    
    $id = get_the_ID();
    $title = get_the_title();
    $city = get_post_meta( $id, 'city', true );
    $state = get_post_meta( $id, 'state', true );
    $voyager_property_code = get_post_meta( $id, 'voyager_property_code', true );
    $property_id = get_post_meta( $id, 'property_id', true );
    $location = null;
    
    // prepare the location
    if ( $city && $state )
        $location = sprintf( '<span class="city-state">in %s, %s</span>', $city, $state );
        
    if ( $city && !$state )
        $location = sprintf( '<span class="city-state">in %s</span>', $city );
        
    if ( !$city && $state )
        $location = sprintf( '<span class="city-state">in %s</span>', $state );
    
    echo '<div class="wrap-single-properties-entry-header"><div class="single-properties-entry-header">';

        if ( $title && $location )
            printf( '<h1>%s %s</h1>', $title, $location );
            
        if ( $title && !$location )
            printf( '<h1>%s</h1>', $title );
            
        if ( current_user_can( 'edit_posts' ) ) {
            echo '<p class="admin-data">';
            
                if ( $voyager_property_code )
                    printf( '<span><strong>Voyager Property Code:</strong> %s <a target="_blank" href="/wp-admin/tools.php?page=action-scheduler&status=pending&s=%s&action=-1&paged=1&action2=-1">View scheduled actions...</a></span>', $voyager_property_code, $voyager_property_code );
                    
                if ( $property_id )
                    printf( '<span><strong>Property ID:</strong> %s</span>', $property_id );
                    
                if ( !$property_id )
                    echo '<span><strong>Property ID:</strong> (not found)</span>';
            
            echo '</p>';
        }

    echo '</div></div>';
}

function rentfetch_single_property_images() {
    
    echo '<div class="wrap-images"><div class="images">';
    
        global $post;
        $id = get_the_ID();
        
        // manually-added images
        $property_images_manual = get_post_meta( $id, 'images', true );
                
        // these are images pulled from an API and stored as a JSON array
        $property_images_yardi = get_post_meta( $id, 'property_images', true );
        $property_images_yardi = json_decode( $property_images_yardi );
                       
        if ( $property_images_manual ) {
            do_action( 'rentfetch_do_single_property_images_manual' );
        } elseif ( $property_images_yardi ) {
            do_action( 'rentfetch_do_single_property_images_yardi' );
        }
        
    echo '</div></div>';
    
}

function rentfetch_single_property_basic_info() {
    
    global $post;
    $id = get_the_ID();
    
    $address = get_post_meta( $id, 'address', true );
    $city = get_post_meta( $id, 'city', true );
    $state = get_post_meta( $id, 'state', true );
    $zipcode = get_post_meta( $id, 'zipcode', true );
    $url = get_post_meta( $id, 'url', true );
    $phone = get_post_meta( $id, 'phone', true );

    echo '<div class="wrap-basic-info"><div class="basic-info">';
    
        if ( $address || $city || $state || $zipcode ) {
            echo '<div class="location">';
                echo '<p class="the-location">';
                
                    if ( $address )
                        printf( '<span class="address">%s</span>', $address );
                        
                    if ( $city )
                        printf( '<span class="city">%s</span>', $city );
                        
                    if ( $state )
                        printf( '<span class="state">%s</span>', $state );
                        
                    if ( $zipcode )
                        printf( '<span class="zipcode">%s</span>', $zipcode );
                    
                echo '</p>';
            echo '</div>';
        }
        
        if ( $phone ) {
            echo '<div class="call">';
                echo '<p class="the-call">';
            
                    printf( '<span class="calltoday">Call today</span>' );
                    printf( '<span class="phone">%s</span>', $phone );
                    
                echo '</p>';
            echo '</div>';
        }
        
        if ( $url ) {
            
            // prepare the property url
            $url = apply_filters( 'rentfetch_filter_property_url', $url );
            
            echo '<div class="property-website">';
            
                printf( '<a class="button property-link" target="_blank" href="%s">Visit property website</a>', $url );
            
            echo '</div>';
        }
        
    echo '</div></div>';
}

function rentfetch_single_property_description() {
    
    global $post;
    $id = get_the_ID();
    
    $title = get_the_title();
    $city = get_post_meta( $id, 'city', true );
    $description = apply_filters( 'the_content', get_post_meta( $id, 'description', true ) );

    if ( $description || $city ) {
        
        echo '<div class="wrap-description"><div class="description-wrap">';
        
            if ( $city )
                printf( '<h4 class="city">%s</h4>', $city );
                
            if ( $title )
                printf( '<h2 id="description">Welcome home to %s</h2>', $title );
                
            printf( '<div class="description">%s</div>', $description );
            
            do_action( 'rentfetch_single_property_do_lead_generation' );
        
        echo '</div></div>';
        
    }
}

add_action( 'rentfetch_single_property_do_lead_generation', 'rentfetch_single_property_yardi_lead_generation' );
function rentfetch_single_property_yardi_lead_generation() {
    
    //* bail if this property is not pulled automatically from Yardi
    $property_source = get_post_meta( get_the_ID(), 'property_source', true );
    if ( $property_source != 'yardi' )
        return;
        
    //* bail if there's no username or password set for Yardi
    $yardi_integration_creds = get_field( 'yardi_integration_creds', 'option' );
    
    // bail if the feature is disabled
    $enable_yardi_api_lead_generation = $yardi_integration_creds['enable_yardi_api_lead_generation'];
    if ( $enable_yardi_api_lead_generation != true )
        return;
        
    // bail if there's no username
    $yardi_username = $yardi_integration_creds['yardi_username'];
    if ( !$yardi_username )
        return;
        
    // bail if there's no password
    $yardi_password = $yardi_integration_creds['yardi_password'];
    if ( !$yardi_password )
        return;
        
    //* Output the button
    echo '<a class="button" data-fancybox href="#yardi-api-form-wrap">Contact us today</a>';
    
    $wp_load_path = ABSPATH . 'wp-load.php';
    
    ?>
    <script>
        function recaptchaCallback() {
            var response = grecaptcha.getResponse();
            if ( response ) {
                document.getElementById('yardi-api-submit').removeAttribute('disabled');
            }
        };
            
        jQuery(document).ready(function( $ ) {
                        
            $("#yardi-api-form").submit(function(e) {

                // Stop form from submitting normally
                e.preventDefault();
                                                                
                $.ajax({
                    url: '<?php echo RENTFETCH_PATH . 'template/formproxy/yardi-form-proxy.php'; ?>',
                    type: 'POST',
                    data: {
                        FirstName: $( this ).find( "input[name='FirstName']" ).val(),
                        LastName: $( this ).find( "input[name='LastName']" ).val(),
                        Email: $( this ).find( "input[name='Email']" ).val(),
                        Phone: $( this ).find( "input[name='Phone']" ).val(),
                        Message: $( this ).find( "textarea[name='Message']" ).val(),
                        PropertyCode: $( this ).find( "input[name='PropertyCode']" ).val(),
                        Source: '<?php echo home_url(); ?>',
                        path: '<?php echo $wp_load_path; ?>',
                    },
                    success: function(response) {
                        
                        //* Hide the form
                        $( '#yardi-api-form' ).hide();                        
                        
                        //* Log the success or error code response
                        console.log( 'Yardi response: ' + response );
                        
                        //* Output some text
                        if ( response == 'Success' ) {
                            $( '#yardi-api-response' ).html( '<p>Thanks for reaching out. Our team will be in touch soon.</p>' );
                        } else {
                            $( '#yardi-api-response' ).html( '<p>Sorry, your message was not sent. Please try again later or reach out directly.</p>' );
                        }
                    }
                });
            });
        });
            
    </script>
    
    <?php
        echo '<div id="yardi-api-form-wrap" style="display:none;">';
            echo '<form id="yardi-api-form" class="rentfetch-api-form">';
            echo '<ul class="form-wrap">';
                echo '<li class="group columns-2">';
                    echo '<div class="column">';
                        echo '<label for="FirstName">First Name <span class="required">Required</span></label>';
                        echo '<input required type="text" id="FirstName" name="FirstName" />';
                    echo '</div>';
                    echo '<div class="column">';
                        echo '<label for="LastName">Last Name <span class="required">Required</span></label>';
                        echo '<input required type="text" id="LastName" name="LastName" />';
                    echo '</div>';
                echo '</li>';
                echo '<li class="group columns-2">';
                    echo '<div class="column">';
                        echo '<label for="Email">Email <span class="required">Required</span></label>';
                        echo '<input required type="email" id="Email" name="Email" />';
                    echo '</div>';
                    echo '<div class="column">';
                        echo '<label for="Phone">Phone <span class="required">Required</span></label>';
                        echo '<input required type="tel" id="Phone" name="Phone" />';
                    echo '</div>';
                echo '</li>';
                echo '<li>';
                    echo '<label for="Message">Message</label>';
                    echo '<textarea id="Message" name="Message"></textarea>';
                echo '</li>';
                
                //* Google reCAPTCHA
                $google_recaptcha = get_field( 'google_recaptcha', 'option' );
                $google_recaptcha_v2_site_key = $google_recaptcha['google_recaptcha_v2_site_key'];
                $google_recaptcha_v2_secret = $google_recaptcha['google_recaptcha_v2_secret'];
                
                if ( $google_recaptcha_v2_site_key && $google_recaptcha_v2_secret ) {
                    
                    wp_enqueue_script( 'rentfetch-google-recaptcha' );
                    
                    ?>
                    
                    <script>
                        jQuery(document).ready(function( $ ) {
                            $('#yardi-api-submit').attr( 'disabled', 'disabled' );                            
                        });
                    </script>
                    
                    <?php
                    echo '<li>';
                        printf( '<div class="g-recaptcha" data-callback="recaptchaCallback" data-sitekey="%s"></div>', $google_recaptcha_v2_site_key );
                    echo '</li>';
                }
                    
                echo '<li class="form-footer">';
                
                    //* Hidden inputs
                    printf( '<input type="hidden" id="PropertyCode" name="PropertyCode" value="%s" />', get_post_meta( get_the_ID(), 'property_code', true ) );
                    printf( '<input type="hidden" id="Source" name="Source" value="%s" />', home_url() );
                    
                    echo '<input type="submit" name="yardi-api-submit" class="button" id="yardi-api-submit" value="Get in touch" />';
                echo '</li>';
            echo '</ul>';
        echo '</form>';
        echo '<div id="yardi-api-response"></div>';
    echo '</div>'; // #yardi-api-form-wrap
}

function rentfetch_single_property_floorplans() {
    
    global $post;
    $id = get_the_ID();
    $property_id = get_post_meta( $id, 'property_id', true );
    
    // grab the gravity forms lightbox, if enabled on this page
    do_action( 'rentfetch_do_gform_lightbox' );
    
    // get the possible values for the beds
    $beds = rentfetch_get_meta_values( 'beds', 'floorplans' );
    $beds = array_unique( $beds );
    asort( $beds );
    
    echo '<div class="wrap-floorplans"><div class="floorplans-wrap" id="floorplans">';
    
    // loop through each of the possible values, so that we can do markup around that
    foreach( $beds as $bed ) {
        
        $args = array(
            'post_type' => 'floorplans',
            'posts_per_page' => -1,
            'orderby' => 'meta_value_num',
            'meta_key' => 'beds',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key'   => 'property_id',
                    'value' => $property_id,
                ),
                array(
                    'key' => 'beds',
                    'value' => $bed,
                ),
            ),
        );
        
        $floorplans_query = new WP_Query( $args );
            
        if ( $floorplans_query->have_posts() ) {
            echo '<details open>';
                echo '<summary><h3>';                    
                    echo apply_filters( 'rentfetch_get_bedroom_number_label', $label, $bed );
                echo '</h3></summary>';
                echo '<div class="floorplan-in-archive">';
                    while ( $floorplans_query->have_posts() ) : $floorplans_query->the_post(); 
                        do_action( 'rentfetch_do_floorplan_in_archive', $post->ID );                    
                    endwhile;
                echo '</div>'; // .floorplans
            echo '</details>';
            
        }
        
        wp_reset_postdata();
    }
    
    echo '</div></div>';
    
}

function rentfetch_single_property_amenities() {
    
    global $post;
    
    $terms = get_the_terms( get_the_ID(), 'amenities' );
    if ( $terms ) {
        echo '<div class="wrap-amenities"><div class="amenities-wrap">';
            echo '<h2 id="amenities">Amenities</h2>';
            echo '<ul class="amenities">';
                foreach( $terms as $term ) {                
                    printf( '<li>%s</li>', $term->name );
                }
            echo '</ul>';
        echo '</div></div>';
    }
}

function rentfetch_single_property_lease_details() {
    
    global $post;
    
    $content_area = get_post_meta( get_the_ID(), 'content_area', true );
    if ( !empty( $content_area ) ) {
        $content_area = apply_filters( 'the_content', $content_area );
        
        echo '<div class="wrap-content-area"><div class="content-area-wrap">';
            echo $content_area;
        echo '</div></div>';
    }
}

function rentfetch_single_property_neighborhood() {
    
    // bail if we dont have metabox's relationships plugin installed
    if ( !class_exists( 'MB_Relationships_API' ) )
        return;
    
    global $post;
        
    $neighborhoods = MB_Relationships_API::get_connected( [
        'id'   => 'properties_to_neighborhoods',
        'to' => get_the_ID(),
    ] );
        
    if ( !empty( $neighborhoods ) ) {
        
        $neighborhood = $neighborhoods[0];   
        $neighborhood_id = $neighborhood->ID;
        $permalink = get_the_permalink( $neighborhood_id );
        $thumb = get_the_post_thumbnail_url( $neighborhood_id, 'large' );
        $title = get_the_title( $neighborhood_id );
        $excerpt = apply_filters( 'the_content', get_the_excerpt( $neighborhood_id ) );
        
        echo '<div class="wrap-neighborhoods"><div class="neighborhoods-wrap">';
        
            printf( '<div class="neighborhood-photo-wrap"><a href="%s" class="neighborhood-photo" style="background-image:url(%s);"></a></div>', $permalink, $thumb );
            
            echo '<div class="neighborhood-content">';
            
                echo '<h4>The neighborhood</h4>';
                printf( '<h2>Life in %s</h2>', $title );
                
                if ( $excerpt )
                    printf( '<div class="excerpt">%s</div>', $excerpt );
                    
                printf( '<a href="%s" class="button">Explore the neighborhood</a>', $permalink );
                
            echo '</div>';
        echo '</div></div>';
    }
}

function rentfetch_single_property_nearby_properties() {
    
    global $post;
    
    do_action( 'rentfetch_single_properties_nearby_properties' );
    
}


function rentfetch_single_property_map() {
    
    global $post;
    $id = get_the_ID();

    $latitude = floatval( get_post_meta( $id, 'latitude', true ) );
    $longitude = floatval( get_post_meta( $id, 'longitude', true ) );
    
    //* bail if there's not a lat or longitude
    if ( empty( $latitude ) || empty( $longitude) )
        return;
        
    $title = get_the_title( $id );
    $address = get_post_meta( $id, 'address', true );
    $city = get_post_meta( $id, 'city', true );
    $state = get_post_meta( $id, 'state', true );
    $zipcode = get_post_meta( $id, 'zipcode', true );
    $phone = get_post_meta( $id, 'phone', true );
    
    $location = sprintf( '<p class="single-property-map-title">%s</p><p class="single-property-map-address"><span class="address">%s<br/>%s, %s %s</span><span class="phone">%s</span></p>', $title, $address, $city, $state, $zipcode, $phone );

    // the map itself
    $key = get_field( 'google_maps_api_key', 'option' );
    wp_enqueue_script( 'rentfetch-google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $key, array(), null, true );

    // Localize the google maps script, then enqueue that
    $maps_options = array(
        'json_style' => json_decode( get_field( 'google_maps_styles', 'option' ) ),
        'marker_url' => get_field( 'google_map_marker', 'option' ),
        'latitude' => $latitude,
        'longitude' => $longitude,
        'location' => $location,
    );
    wp_localize_script( 'rentfetch-single-property-map', 'options', $maps_options );
    wp_enqueue_script( 'rentfetch-single-property-map');
    
    echo '<div class="map" id="map"></div>';
        
}