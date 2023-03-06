<?php

add_action('admin_menu', 'rent_fetch_options_page');
function rent_fetch_options_page() {
    add_menu_page(
        'Rent Fetch Options',
        'Rent Fetch',
        'manage_options',
        'rent_fetch_options',
        'rent_fetch_options_page_html'
    );
}

function rent_fetch_options_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>Rent Fetch Options</h1>
        <nav class="nav-tab-wrapper">
            <a href="?page=rent_fetch_options" class="nav-tab<?php if (!isset($_GET['tab']) || $_GET['tab'] === 'general') { echo ' nav-tab-active'; } ?>">General</a>
            <a href="?page=rent_fetch_options&tab=google" class="nav-tab<?php if (isset($_GET['tab']) && $_GET['tab'] === 'google') { echo ' nav-tab-active'; } ?>">Google</a>
            <a href="?page=rent_fetch_options&tab=property_search" class="nav-tab<?php if (isset($_GET['tab']) && $_GET['tab'] === 'property_search') { echo ' nav-tab-active'; } ?>">Property Search</a>
            <a href="?page=rent_fetch_options&tab=property_archives" class="nav-tab<?php if (isset($_GET['tab']) && $_GET['tab'] === 'property_archives') { echo ' nav-tab-active'; } ?>">Property Archives</a>
            <a href="?page=rent_fetch_options&tab=single_property_template" class="nav-tab<?php if (isset($_GET['tab']) && $_GET['tab'] === 'single_property_template') { echo ' nav-tab-active'; } ?>">Single Property Template</a>
            <a href="?page=rent_fetch_options&tab=floorplan_archives" class="nav-tab<?php if (isset($_GET['tab']) && $_GET['tab'] === 'floorplan_archives') { echo ' nav-tab-active'; } ?>">Floorplan Archives</a>
        </nav>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) );  ?>">
        
            <input type="hidden" name="action" value="rent_fetch_process_form">
            <?php wp_nonce_field( 'rent_fetch_nonce', 'rent_fetch_form_nonce' ); ?>
            <?php $rent_fetch_options_nonce = wp_create_nonce( 'rent_fetch_options_nonce' );  ?>
            
            <?php
            
            if ( !isset($_GET['tab']) || $_GET['tab'] === 'general') {
                do_action( 'rent_fetch_do_settings_general' );
            } elseif (isset($_GET['tab']) && $_GET['tab'] === 'property_search') {
                do_action( 'rent_fetch_do_settings_property_search' );
            } elseif (isset($_GET['tab']) && $_GET['tab'] === 'property_archives') {
                do_action( 'rent_fetch_do_settings_property_archives' );
            } else {

            }
            
            submit_button(); 
            ?>
            
        </form>
    </div>

    <?php
}

// Define a function to process the form data
add_action( 'admin_post_rent_fetch_process_form', 'rent_fetch_process_form_data' );
function rent_fetch_process_form_data() {
    
    // var_dump( $_POST );

    // Verify the nonce
    if ( ! wp_verify_nonce( $_POST['rent_fetch_form_nonce'], 'rent_fetch_nonce' ) ) {
        die( 'Security check failed' );
    }

    // Get the submitted form data
    if ( isset( $_POST['my_setting_1']) ) {
        $my_setting_1 = sanitize_text_field( $_POST['my_setting_1'] );
        update_option( 'my_setting_1', $my_setting_1 );
    }
    
    if ( isset( $_POST['my_setting_2']) ) {
        $my_setting_2 = sanitize_text_field( $_POST['my_setting_2'] );
        update_option( 'my_setting_2', $my_setting_2 );
    }

    // Redirect back to the form page with a success message
    wp_redirect( add_query_arg( 'rent_fetch_message', 'success', 'admin.php?page=rent_fetch_options' ) );
    exit;

}

add_action( 'rent_fetch_do_settings_general', 'rent_fetch_settings_general' );
function rent_fetch_settings_general() {
    ?>
    <h2>General</h2>
    <p>
        <label for="my_setting_1">Setting 1:</label>
        <input type="text" name="my_setting_1" id="my_setting_1" value="<?php echo esc_attr( get_option( 'my_setting_1' ) ); ?>">
    </p>
    <p>
        <label for="my_setting_2">Setting 2:</label>
        <input type="text" name="my_setting_2" id="my_setting_2" value="<?php echo esc_attr( get_option( 'my_setting_2' ) ); ?>">
    </p>
    <?php
}

add_action( 'rent_fetch_do_settings_property_search', 'rent_fetch_settings_property_search' );
function rent_fetch_settings_property_search() {
    ?>
    <h2>Property Search</h2>
    <p>
        <label for="my_setting_3">Setting 3:</label>
        <input type="text" name="my_setting_3" id="my_setting_3" value="<?php echo esc_attr( get_option( 'my_setting_3' ) ); ?>">
    </p>
    <p>
        <label for="my_setting_4">Setting 4:</label>
        <input type="text" name="my_setting_4" id="my_setting_4" value="<?php echo esc_attr( get_option( 'my_setting_4' ) ); ?>">
    </p>
    <?php
}