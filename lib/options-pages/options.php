<?php

add_action('admin_menu', 'rent_fetch_options_page');
function rent_fetch_options_page() {
    
    $menu_icon = file_get_contents( RENTFETCH_DIR . 'images/rentfetch-dashboard-icon.svg' );
    
    add_menu_page(
        'Rent Fetch Options',
        'Rent Fetch',
        'manage_options',
        'rent_fetch_options',
        'rent_fetch_options_page_html',
        'data:image/svg+xml;base64,' . base64_encode( $menu_icon ),
        58.99,
    );
    
    add_submenu_page(
        'rent_fetch_options',
        'Settings',
        'Settings',
        'manage_options',
        'rent_fetch_options',
        'rent_fetch_options_page_html',
    );
        
    // add a submenu page which links to a third-party url
    add_submenu_page(
        'rent_fetch_options',
        'Documentation',
        'Documentation',
        'manage_options',
        'rent_fetch_documentation',
        'rent_fetch_documentation_page_html',
    );
}


//* Force the documentation link to go offsite
add_action( 'admin_footer', 'rentfetch_documentation_submenu_open_new_tab' );    
function rentfetch_documentation_submenu_open_new_tab() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('a[href="admin.php?page=rent_fetch_documentation"').each(function () {
            if ($(this).text() == 'Documentation') {
                $(this).css('color', 'yellow');
                $(this).attr('href', 'https://github.com/jonschr/rent-fetch');
                $(this).attr('target','_blank');
            }
        });
    });
    </script>
    <?php
}

function rent_fetch_options_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <div class="header-wrap">
            <h1>Rent Fetch Options</h1>
            <?php submit_button(); ?>
        </div>
        <nav class="nav-tab-wrapper">
            <a href="?page=rent_fetch_options" class="nav-tab<?php if (!isset($_GET['tab']) || $_GET['tab'] === 'general') { echo ' nav-tab-active'; } ?>">General</a>
            <a href="?page=rent_fetch_options&tab=google" class="nav-tab<?php if (isset($_GET['tab']) && $_GET['tab'] === 'google') { echo ' nav-tab-active'; } ?>">Google</a>
            <a href="?page=rent_fetch_options&tab=properties" class="nav-tab<?php if (isset($_GET['tab']) && $_GET['tab'] === 'properties') { echo ' nav-tab-active'; } ?>">Properties</a>
            <a href="?page=rent_fetch_options&tab=floorplan_archives" class="nav-tab<?php if (isset($_GET['tab']) && $_GET['tab'] === 'floorplan_archives') { echo ' nav-tab-active'; } ?>">Floorplan Archives</a>
        </nav>
        <form method="post" class="rent-fetch-options" action="<?php echo esc_url( admin_url( 'admin-post.php' ) );  ?>">
        
            <input type="hidden" name="action" value="rent_fetch_process_form">
            <?php wp_nonce_field( 'rent_fetch_nonce', 'rent_fetch_form_nonce' ); ?>
            <?php $rent_fetch_options_nonce = wp_create_nonce( 'rent_fetch_options_nonce' );  ?>
            
            <?php
            
            if ( !isset($_GET['tab']) || $_GET['tab'] === 'general') {
                do_action( 'rent_fetch_do_settings_general' );
            } elseif (isset($_GET['tab']) && $_GET['tab'] === 'google') {
                do_action( 'rent_fetch_do_settings_google' );
            } elseif (isset($_GET['tab']) && $_GET['tab'] === 'properties') {
                do_action( 'rent_fetch_do_settings_properties' );
            } elseif (isset($_GET['tab']) && $_GET['tab'] === 'property_search') {
                do_action( 'rent_fetch_do_settings_property_search' );
            } elseif (isset($_GET['tab']) && $_GET['tab'] === 'property_archives') {
                do_action( 'rent_fetch_do_settings_property_archives' );
            } elseif (isset($_GET['tab']) && $_GET['tab'] === 'single_property_template') {
                do_action( 'rent_fetch_do_settings_single_property_template' );
            } elseif (isset($_GET['tab']) && $_GET['tab'] === 'floorplan_archives') {
                do_action( 'rent_fetch_do_settings_floorplan_archives' );
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
    if ( isset( $_POST['options_rent_fetch_api_key']) ) {
        $options_rent_fetch_api_key = sanitize_text_field( $_POST['options_rent_fetch_api_key'] );
        update_option( 'options_rent_fetch_api_key', $options_rent_fetch_api_key );
    }
    
    if ( isset( $_POST['options_apartment_site_type']) ) {
        $options_apartment_site_type = sanitize_text_field( $_POST['options_apartment_site_type'] );
        update_option( 'options_apartment_site_type', $options_apartment_site_type );
    }

    // Redirect back to the form page with a success message
    wp_redirect( add_query_arg( 'rent_fetch_message', 'success', 'admin.php?page=rent_fetch_options' ) );
    exit;

}

add_action( 'rent_fetch_do_settings_general', 'rent_fetch_settings_general' );
function rent_fetch_settings_general() {
    ?>
    <h2>General</h2>
    enabled_integrations<br/>
    yardi_integration_creds<br/>
    entrata_integration_creds<br/>
    realpage_integration_creds<br/>
    appfolio_integration_creds<br/>
    enable_rentfetch_logging<br/>
    <div class="row">
        <div class="column">
            <label for="options_rent_fetch_api_key">Rent Fetch API Key</label>
        </div>
        <div class="column">
            <input type="text" name="options_rent_fetch_api_key" id="options_rent_fetch_api_key" value="<?php echo esc_attr( get_option( 'options_rent_fetch_api_key' ) ); ?>">
            <p class="description">Required for syncing any data down from an API.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="column">
            <label for="options_apartment_site_type">Site type</label>
        </div>
        <div class="column">
            <select name="options_apartment_site_type" id="options_apartment_site_type" value="<?php echo esc_attr( get_option( 'options_apartment_site_type' ) ); ?>">
                <option value="single">This site is for a single property</option>
                <option value="multiple">This site is for multiple properties</option>
            </select>
        </div>
    </div>
    
    <div class="row">
        <div class="column">
            <label for="options_data_sync">Data Sync</label>
        </div>
        <div class="column">
            <input type="text" name="options_data_sync" id="options_data_sync" value="<?php echo esc_attr( get_option( 'options_data_sync' ) ); ?>">
        </div>
    </div>
    
    <div class="row">
        <div class="column">
            <label for="options_sync_term">Sync Term</label>
        </div>
        <div class="column">
            <input type="text" name="options_sync_term" id="options_sync_term" value="<?php echo esc_attr( get_option( 'options_sync_term' ) ); ?>">
        </div>
    </div>
    
    <div class="row">
        <div class="column">
            <label for="options_enabled_integrations">Enabled Integrations</label>
        </div>
        <div class="column">
            <input type="text" name="options_enabled_integrations" id="options_enabled_integrations" value="<?php echo esc_attr( get_option( 'options_enabled_integrations' ) ); ?>">
            
            
        </div>
    </div>
         
    <div class="row">
        <div class="column">
            <label for="options_enabled_integrations">Enabled Integrations</label>
        </div>
        <div class="column">
            <input type="text" name="options_enabled_integrations" id="options_enabled_integrations" value="<?php echo esc_attr( get_option( 'options_enabled_integrations' ) ); ?>">
        </div>
    </div>
         
    <div class="row">
        <div class="column">
            <label for="options_enabled_integrations">Enabled Integrations</label>
        </div>
        <div class="column">
            <input type="text" name="options_enabled_integrations" id="options_enabled_integrations" value="<?php echo esc_attr( get_option( 'options_enabled_integrations' ) ); ?>">
        </div>
    </div>
         
    <?php
}

add_action( 'rent_fetch_do_settings_google', 'rent_fetch_settings_google' );
function rent_fetch_settings_google() {
    ?>
    <h2>Google</h2>
    google_recaptcha<br/>
    google_maps_api_key<br/>
    google_geocoding_api_key<br/>
    google_map_marker<br/>
    google_maps_styles<br/>
    google_maps_default_latitude<br/>
    google_maps_default_longitude<br/>            
    <?php
}

add_action( 'rent_fetch_do_settings_properties', 'rent_fetch_settings_properties' );
function rent_fetch_settings_properties() {    
    ?>
    <ul class="rent-fetch-options-submenu">
        <li><a href="?page=rent_fetch_options&tab=properties&section=property_search" class="tab<?php if (!isset($_GET['section']) || $_GET['section'] === 'property_search') { echo ' tab-active'; } ?>">Property Search</a></li>
        <li><a href="?page=rent_fetch_options&tab=properties&section=property_archives" class="tab<?php if ( isset( $_GET['section']) && $_GET['section'] === 'property_archives') { echo ' tab-active'; } ?>">Property Archives</a></li>
    </ul>    
    <?php
    if ( !isset($_GET['section']) || $_GET['section'] === 'property_search') {
        do_action( 'rent_fetch_do_settings_properties_property_search' );
    } elseif (isset($_GET['section']) && $_GET['section'] === 'property_archives') {
        do_action( 'rent_fetch_do_settings_properties_property_archives' );
    }
}

add_action( 'rent_fetch_do_settings_properties_property_search', 'rent_fetch_settings_properties_property_search' );
function rent_fetch_settings_properties_property_search() {
    ?>
    <h2>Property Search</h2>
    
    <?php
}

add_action( 'rent_fetch_do_settings_properties_property_archives', 'rent_fetch_settings_properties_property_archives' );
function rent_fetch_settings_properties_property_archives() {
    ?>
    <h2>Property Archives</h2>
    
    <?php
}

add_action( 'rent_fetch_do_settings_single_property_template', 'rent_fetch_settings_single_property_template' );
function rent_fetch_settings_single_property_template() {
    ?>
    <h2>Single Property Template</h2>
    
    <?php
}

add_action( 'rent_fetch_do_settings_floorplan_archives', 'rent_fetch_settings_floorplan_archives' );
function rent_fetch_settings_floorplan_archives() {
    ?>
    <h2>Floorplan Archives</h2>
    
    <?php
}
