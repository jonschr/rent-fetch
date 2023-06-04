<?php

/**
 * Do the property images, selecting how to output those
 */
add_action( 'rentfetch_do_property_images', 'rentfetch_property_images' );
function rentfetch_property_images() {
    
    // get the single image
    rentfetch_property_single_image();
    
    // get the slider
    //! TODO: add a slider capability and an option to toggle between these
    
}

/**
 * Single image for each property
 */
function rentfetch_property_single_image() {
    $images = rentfetch_get_property_images();            

    ?>
    <div style="display: block; height: 200px; width: 100%;">
        <img src="<?php echo $images[0]['url']; ?>" loading="lazy" style="width: 100%; height: 100%;">
    </div>
    <?php
}
