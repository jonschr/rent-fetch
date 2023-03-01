<?php

add_action( 'add_meta_boxes', 'rf_register_properties_details_metabox' );
function rf_register_properties_details_metabox() {
    
    add_meta_box(
        'rf_properties_identifiers', // ID of the metabox
        'Property Identifiers', // Title of the metabox
        'rf_properties_identifiers_metabox_callback', // Callback function to render the metabox
        'properties', // Post type to add the metabox to
        'normal', // Priority of the metabox
        'default' // Context of the metabox
    );
   
    add_meta_box(
        'rf_properties_contact', // ID of the metabox
        'Property Contact Information', // Title of the metabox
        'rf_properties_contact_metabox_callback', // Callback function to render the metabox
        'properties', // Post type to add the metabox to
        'normal', // Priority of the metabox
        'default' // Context of the metabox
    );
    
    add_meta_box(
        'rf_properties_location', // ID of the metabox
        'Property Location', // Title of the metabox
        'rf_properties_location_metabox_callback', // Callback function to render the metabox
        'properties', // Post type to add the metabox to
        'normal', // Priority of the metabox
        'default' // Context of the metabox
    );
    
    add_meta_box(
        'rf_properties_details', // ID of the metabox
        'Property Display Information', // Title of the metabox
        'rf_properties_display_information_metabox_callback', // Callback function to render the metabox
        'properties', // Post type to add the metabox to
        'normal', // Priority of the metabox
        'default' // Context of the metabox
    );
        
}

function rf_properties_identifiers_metabox_callback( $post ) {
    ?>
    <div class="rf-metabox rf-metabox-properties">
                            
        <?php $property_id = get_post_meta( $post->ID, 'property_id', true ); ?>
        <div class="field">
            <div class="column">
                <label for="property_id">Property ID</label>
            </div>
            <div class="column">                
                <input type="text" id="property_id" name="property_id" value="<?php echo esc_attr( $property_id ); ?>">
                <p class="description">The Property ID should match the Property ID on each associated floorplan, and every property should have a property ID at minimum.</p>
            </div>
        </div>
        
        <?php $property_code = get_post_meta( $post->ID, 'property_code', true ); ?>
        <div class="field">
            <div class="column">
                <label for="property_code">Property Code</label>
            </div>
            <div class="column">                
                <input type="text" id="property_code" name="property_code" value="<?php echo esc_attr( $property_code ); ?>">
                <p class="description">In Yardi, properties also have a property code, so if this property is synced with Yardi, that may show below as well (if this is not a Yardi property, you can probably ignore this).</p>
            </div>
        </div>
        
    </div>
    <?php
}

function rf_properties_location_metabox_callback( $post ) {
    ?>
    <div class="rf-metabox rf-metabox-properties">
        
        <div class="columns columns-4">
        
            <?php $address = get_post_meta( $post->ID, 'address', true ); ?>
            <div class="field">
                <div class="column">
                    <label for="address">Address</label>
                </div>
                <div class="column">                
                    <input type="text" id="address" name="address" value="<?php echo esc_attr( $address ); ?>">
                </div>
            </div>
            
            <?php $city = get_post_meta( $post->ID, 'city', true ); ?>
            <div class="field">
                <div class="column">
                    <label for="city">City</label>
                </div>
                <div class="column">                
                    <input type="text" id="city" name="city" value="<?php echo esc_attr( $city ); ?>">
                </div>
            </div>
            
            <?php $state = get_post_meta( $post->ID, 'state', true ); ?>
            <div class="field">
                <div class="column">
                    <label for="state">State</label>
                </div>
                <div class="column">                
                    <input type="text" id="state" name="state" value="<?php echo esc_attr( $state ); ?>">
                </div>
            </div>
            
            <?php $zipcode = get_post_meta( $post->ID, 'zipcode', true ); ?>
            <div class="field">
                <div class="column">
                    <label for="zipcode">Zipcode</label>
                </div>
                <div class="column">                
                    <input type="text" id="zipcode" name="zipcode" value="<?php echo esc_attr( $zipcode ); ?>">
                </div>
            </div>
        
        </div>
        
        <div class="columns columns-2">
        
            <?php $latitude = get_post_meta( $post->ID, 'latitude', true ); ?>
            <div class="field">
                <div class="column">
                    <label for="latitude">Latitude</label>
                </div>
                <div class="column">                
                    <input type="text" id="latitude" name="latitude" value="<?php echo esc_attr( $latitude ); ?>">
                </div>
            </div>
            
            <?php $longitude = get_post_meta( $post->ID, 'longitude', true ); ?>
            <div class="field">
                <div class="column">
                    <label for="longitude">Longitude</label>
                </div>
                <div class="column">                
                    <input type="text" id="longitude" name="longitude" value="<?php echo esc_attr( $longitude ); ?>">
                </div>
            </div>
            
        </div>
       
    </div>
    <?php
}

function rf_properties_contact_metabox_callback( $post ) {
    ?>
    <div class="rf-metabox rf-metabox-properties">
        
        <div class="columns columns-3">
            
            <?php $email = get_post_meta( $post->ID, 'email', true ); ?>
            <div class="field">
                <div class="column">
                    <label for="email">Email</label>
                </div>
                <div class="column">                
                    <input type="text" id="email" name="email" value="<?php echo esc_attr( $email ); ?>">
                </div>
            </div>
            
            <?php $phone = get_post_meta( $post->ID, 'phone', true ); ?>
            <div class="field">
                <div class="column">
                    <label for="phone">Phone</label>
                </div>
                <div class="column">                
                    <input type="text" id="phone" name="phone" value="<?php echo esc_attr( $phone ); ?>">
                </div>
            </div>
            
            <?php $url = get_post_meta( $post->ID, 'url', true ); ?>
            <div class="field">
                <div class="column">
                    <label for="url">URL</label>
                </div>
                <div class="column">                
                    <input type="text" id="url" name="url" value="<?php echo esc_attr( $url ); ?>">
                </div>
            </div>
            
        </div>
        
    </div>
    <?php
}

function rf_properties_display_information_metabox_callback( $post ) {
    wp_nonce_field( 'rf_properties_metabox_nonce_action', 'rf_properties_metabox_nonce_field' );
    
    ?>
    <div class="rf-metabox rf-metabox-properties">
        
        <script type="text/javascript">
            jQuery(document).ready(function( $ ) {
                                
                // Get the container for the gallery images
                var galleryContainer = $('#gallery-container');
                
                // Get the hidden input field for the gallery IDs
                var galleryIdsField = $('#images');
                
                // Get the "Select Images" button
                var imagesButton = $('#images_button');
                
                // convert gallleryIdsField value to an array
                var selectedImageIds = galleryIdsField.val().split(',');
                
                // Handle the "Select Images" button click event
                imagesButton.click(function(e) {
                    e.preventDefault();
                    
                    var galleryFrame;
                    
                    if (galleryFrame) {
                        galleryFrame.open();
                        return;
                    }
                    
                    galleryFrame = wp.media({
                        title: 'Select Images',
                        button: {
                            text: 'Add to Gallery'
                        },
                        multiple: true,
                        library: {
                            type: 'image'
                        }
                    });
                    
                    galleryFrame.on('select', function() {
                        var selection = galleryFrame.state().get('selection');
                        galleryIdsField = $('#images');
                        
                        selection.map(function(attachment) {
                            attachment = attachment.toJSON();
                            
                            if (attachment.id && selectedImageIds.indexOf(attachment.id) === -1) {
                                selectedImageIds.push(attachment.id);
                                galleryContainer.append('<div class="gallery-image" data-id="' + attachment.id + '"><img src="' + attachment.sizes.thumbnail.url + '"><button class="remove-image">Remove</button></div>');
                            }
                        });
                        
                        galleryIdsField.val(selectedImageIds.join(','));
                    });
                    
                    galleryFrame.open();
                });
                
                // Handle the "Remove" button click event
                galleryContainer.on('click', '.remove-image', function() {
                    var imageDiv = $(this).closest('.gallery-image');
                    var imageId = imageDiv.data('id');
                    
                    selectedImageIds = $( '#images').val().split(',');
                    
                    console.log( selectedImageIds );
                    
                    selectedImageIds.splice(selectedImageIds.indexOf(imageId), 1);
                    galleryIdsField.val(selectedImageIds.join(','));
                    imageDiv.remove();
                });
                
                // Prepopulate the gallery container with existing images
                if (galleryIdsField.val()) {
                    var galleryIds = galleryIdsField.val().split(',');
                    
                    for (var i = 0; i < galleryIds.length; i++) {
                        var imageId = parseInt(galleryIds[i], 10);
                        
                        if (imageId && selectedImageIds.indexOf(imageId) === -1) {
                            selectedImageIds.push(imageId);
                            galleryContainer.append('<div class="gallery-image" data-id="' + imageId + '"><img src="' + wp.media.attachment(imageId).get('sizes')['thumbnail']['url'] + '"/><button class="remove-image">Remove</button></div>');
                        }
                    }
                }
            });
        </script>
      
        <div class="field">
            <div class="column">
                <label for="images">Images</label>
            </div>
            <div class="column"> 
                <p class="description">These are custom images added to the site, and are never synced. Any image here will override any synced images.</p>               
                <?php
                
                $images = get_post_meta( $post->ID, 'images', true );
                $images_ids = get_post_meta( $post->ID, 'images', true );
                $images_ids = explode( ',', $images_ids );
                $image_url = '';
        
                foreach( $images_ids as $image_id ) {
                    $attachment_url = wp_get_attachment_image_src( $image_id, 'thumbnail' );
                    $image_url .= '
                        <div class="gallery-image" data-id="' . $image_id . '">
                            <img src="' . $attachment_url[0] . '">
                            <button class="remove-image">Remove</button>
                        </div>
                    ';
                }

                echo '<input type="text" id="images" name="images" value="' . esc_attr( $images ) . '">';
                echo '<div id="gallery-container">' . $image_url . '</div>';
                echo '<input type="button" id="images_button" class="button" value="Add Images">';
                ?>
                
            </div>
        </div>        
                        
        <?php $description = get_post_meta( $post->ID, 'description', true ); ?>
        <div class="field">
            <div class="column">
                <label for="description">Description</label>
            </div>
            <div class="column">                
                <input type="text" id="description" name="description" value="<?php echo esc_attr( $description ); ?>">
                <p class="description">The description is synced from most APIs, but if yours is not, this is the main place to put general information about this property.</p>
            </div>
        </div>
        
        <?php $matterport = get_post_meta( $post->ID, 'matterport', true ); ?>
        <div class="field">
            <div class="column">
                <label for="matterport">Tour Matterport embed code</label>
            </div>
            <div class="column">                
                <input type="text" id="matterport" name="matterport" value="<?php echo esc_attr( $matterport ); ?>">
            </div>
        </div>
        
        <?php $video = get_post_meta( $post->ID, 'video', true ); ?>
        <div class="field">
            <div class="column">
                <label for="video">Tour video</label>
            </div>
            <div class="column">                
                <input type="text" id="video" name="video" value="<?php echo esc_attr( $video ); ?>">
            </div>
        </div>
                
        <?php $pets = get_post_meta( $post->ID, 'pets', true ); ?>
        <div class="field">
            <div class="column">
                <label for="pets">Pets</label>
            </div>
            <div class="column">                
                <input type="text" id="pets" name="pets" value="<?php echo esc_attr( $pets ); ?>">
            </div>
        </div>
        
        <?php $content_area = get_post_meta( $post->ID, 'content_area', true ); ?>
        <div class="field">
            <div class="column">
                <label for="content_area">Content area</label>
                <p class="description">The content area is always unsynced, so if you have more to say, you can say it here.</p>
            </div>
            <div class="column">                
                <textarea rows="3" id="content_area" name="content_area"><?php echo esc_attr( $content_area ); ?></textarea>
                <p class="description">It's always recommended to start this section with a heading level 2. If this is empty, the content area section of the single-properties template will not be displayed (there won't be a blank space). By default, if there's something to say here, this section will display below the amenities.</p>
            </div>
        </div>
        
    </div>
    
    <?php
}

add_action( 'save_post', 'rf_save_properties_metaboxes' );
function rf_save_properties_metaboxes( $post_id ) {
    // if ( ! isset( $_POST['rf_properties_metabox'] ) ) {
    //     return;
    // }

    // if ( ! wp_verify_nonce( $_POST['rf_properties_metabox'], 'rf_properties_metabox' ) ) {
    //     return;
    // }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( isset( $_POST['images'] ) ) {
        update_post_meta( $post_id, 'images', $_POST['images'] );
    }
}

// add_action( 'admin_enqueue_scripts', 'rf_properties_metabox_scripts' );
// function rf_properties_metabox_scripts() {
//     wp_enqueue_media();
//     wp_register_script( 'custom_metabox', get_template_directory_uri() . '/custom_metabox.js', array( 'jquery' ) );
//     wp_enqueue_script( 'custom_metabox' );
// }

function rf_properties_metabox_scripts() {
    wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'rf_properties_metabox_scripts' );