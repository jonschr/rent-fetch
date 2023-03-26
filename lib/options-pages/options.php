<?php

/**
 * Adds Rent Fetch options page to the admin menu.
 */
add_action( 'admin_menu', 'rent_fetch_options_page' );
function rent_fetch_options_page() {
    // Get the contents of the Rent Fetch dashboard icon.
    $menu_icon = file_get_contents( RENTFETCH_DIR . 'images/rentfetch-dashboard-icon.svg' );
    
    // Add Rent Fetch options page to the admin menu.
    add_menu_page(
        'Rent Fetch Options', // Page title.
        'Rent Fetch', // Menu title.
        'manage_options', // Capability required to access the menu.
        'rent_fetch_options', // Menu slug.
        'rent_fetch_options_page_html', // Callback function to render the page.
        'data:image/svg+xml;base64,' . base64_encode( $menu_icon ), // Menu icon.
        58.99 // Menu position.
    );
    
    // Add Rent Fetch options sub-menu page to the admin menu.
    add_submenu_page(
        'rent_fetch_options', // Parent menu slug.
        'Settings', // Page title.
        'Settings', // Menu title.
        'manage_options', // Capability required to access the menu.
        'rent_fetch_options', // Menu slug.
        'rent_fetch_options_page_html' // Callback function to render the page.
    );
        
    // Add Documentation sub-menu page to the admin menu, linking to a third-party URL.
    add_submenu_page(
        'rent_fetch_options', // Parent menu slug.
        'Documentation', // Page title.
        'Documentation', // Menu title.
        'manage_options', // Capability required to access the menu.
        'rent_fetch_documentation', // Menu slug.
        'rent_fetch_documentation_page_html' // Callback function to render the page.
    );
}

/**
 * If the rent_fetch_message parameter is set to 'success', display a success message.
 */
add_action( 'admin_notices', 'rent_fetch_options_page_notice' );
function rent_fetch_options_page_notice() {
    if ( isset( $_GET['rent_fetch_message'] ) && $_GET['rent_fetch_message'] === 'success' ) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'Rent Fetch settings successfully saved.', 'rent-fetch' ); ?></p>
        </div>
        <?php
    }
}


/**
 * Force the documentation link to go to a third-party URL.
 */
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

/**
 * Save the form data for ALL tabs on the Rent Fetch settings page
 */
add_action( 'admin_post_rent_fetch_process_form', 'rent_fetch_process_form_data' );
function rent_fetch_process_form_data() {
    
    //* Verify the nonce
    if ( ! wp_verify_nonce( $_POST['rent_fetch_form_nonce'], 'rent_fetch_nonce' ) ) {
        die( 'Security check failed' );
    }
    
    //* Save the settings
    do_action( 'rent_fetch_save_settings' );
    
    //* Redirect back to the form page with a success message
    // wp_redirect( add_query_arg( 'rent_fetch_message', 'success', 'admin.php?page=rent_fetch_options' ) );
        
    //* Redirect back to the current page with a success message
    $referrer = $_SERVER['HTTP_REFERER'];
    
    // remove the URL from the referrer
    $referrer = preg_replace('/https?:\/\/[^\/]+/', '', $referrer);
    
    // remove /wp-admin/ from the referrer
    $referrer = preg_replace('/\/wp-admin\//', '', $referrer);
    
    var_dump( $referrer );
    
    wp_redirect( add_query_arg( 'rent_fetch_message', 'success', $referrer ) );
    
    exit;

}

/**
 * Adds the general settings section to the Rent Fetch settings page.
 */
add_action( 'rent_fetch_do_settings_general', 'rent_fetch_settings_general' );
function rent_fetch_settings_general() {
    ?>
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
                <option value="single" <?php selected( get_option( 'options_apartment_site_type' ), 'single' ); ?>>This site is for a single property</option>
                <option value="multiple" <?php selected( get_option( 'options_apartment_site_type' ), 'multiple' ); ?>>This site is for multiple properties</option>
            </select>
        </div>
    </div>
    
    <div class="row">
        <div class="column">
            <label for="options_data_sync">Data Sync</label>
        </div>
        <div class="column">
            <ul class="radio">
                <li>
                    <label>
                        <input type="radio" name="options_data_sync" id="options_data_sync" value="nosync" <?php checked( get_option( 'options_data_sync' ), 'nosync' ); ?>>
                        Do not automatically sync any API data into this site
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" name="options_data_sync" id="options_data_sync" value="updatesync" <?php checked( get_option( 'options_data_sync' ), 'updatesync' ); ?>>
                        Update data on this site with data from the API. Try not to delete posts added manually or data in manually-added custom fields which can supplement the API
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" name="options_data_sync" id="options_data_sync" value="delete" <?php checked( get_option( 'options_data_sync' ), 'delete' ); ?>>
                        Delete all data that's been pulled from a third-party API
                    </label>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="row">
        <div class="column">
            <label for="options_sync_term">Sync Term</label>
        </div>
        <div class="column">
            <select name="options_sync_term" id="options_sync_term" value="<?php echo esc_attr( get_option( 'options_sync_term' ) ); ?>">
                <option value="paused" <?php selected( get_option( 'options_sync_term' ), 'paused' ); ?>>Paused</option>
                <option value="hourly" <?php selected( get_option( 'options_sync_term' ), 'hourly' ); ?>>Hourly</option>
            </select>
        </div>
    </div>
    
    <div class="row">
        <div class="column">
            <label for="options_enabled_integrations">Enabled Integrations</label>
        </div>
        <div class="column">
            <script type="text/javascript">
                jQuery(document).ready(function( $ ) {
	
                    $( '.integration' ).hide();
                    
                    // on load and on change of input[name="options_enabled_integrations[]"], show/hide the integration options
                    $( 'input[name="options_enabled_integrations[]"]' ).on( 'change', function() {
                        
                        // hide all the integration options
                        $( '.integration' ).hide();
                        
                        // show the integration options for the checked integrations
                        $( 'input[name="options_enabled_integrations[]"]:checked' ).each( function() {
                            $( '.integration.' + $( this ).val() ).show();
                        });
                        
                    }).trigger( 'change' );
                    
                });
                
            </script>
            <ul class="checkboxes">
                <li>
                    <label>
                        <input type="checkbox" name="options_enabled_integrations[]" value="yardi" <?php checked( in_array( 'yardi', get_option( 'options_enabled_integrations', array() ) ) ); ?>>
                        Yardi/RentCafe
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="options_enabled_integrations[]" value="entrata" <?php checked( in_array( 'entrata', get_option( 'options_enabled_integrations', array() ) ) ); ?>>
                        Entrata
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="options_enabled_integrations[]" value="realpage" <?php checked( in_array( 'realpage', get_option( 'options_enabled_integrations', array() ) ) ); ?>>
                        RealPage
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="options_enabled_integrations[]" value="appfolio" <?php checked( in_array( 'appfolio', get_option( 'options_enabled_integrations', array() ) ) ); ?>>
                        Appfolio
                    </label>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="row integration yardi">
        <div class="column">
            <label>Yardi/RentCafe</label>
        </div>
        <div class="column">
            <div class="white-box">
                <label for="options_yardi_integration_creds_yardi_api_key">Yardi API Key</label>
                <input type="text" name="options_yardi_integration_creds_yardi_api_key" id="options_yardi_integration_creds_yardi_api_key" value="<?php echo esc_attr( get_option( 'options_yardi_integration_creds_yardi_api_key' ) ); ?>">
            </div>
            <div class="white-box">
                <label for="options_yardi_integration_creds_yardi_property_code">Yardi Property Codes</label>
                <textarea rows="10" style="width: 100%;" name="options_yardi_integration_creds_yardi_property_code" id="options_yardi_integration_creds_yardi_property_code"><?php echo esc_attr( get_option( 'options_yardi_integration_creds_yardi_property_code' ) ); ?></textarea>
                <p class="description">Multiple property codes should be entered separated by commas</p>
            </div>
            <div class="white-box">
                <label for="options_yardi_integration_creds_enable_yardi_api_lead_generation">
                    <input type="checkbox" name="options_yardi_integration_creds_enable_yardi_api_lead_generation" id="options_yardi_integration_creds_enable_yardi_api_lead_generation" <?php checked( get_option( 'options_yardi_integration_creds_enable_yardi_api_lead_generation' ), true ); ?>>
                    Enable Yardi API Lead Generation
                </label>
                <p class="description">Adds a lightbox form on the single properties template which can send leads directly to the Yardi API.</p>
            </div>
            <div class="white-box">
                <label for="options_yardi_integration_creds_yardi_username">Yardi Username</label>
                <input type="text" name="options_yardi_integration_creds_yardi_username" id="options_yardi_integration_creds_yardi_username" value="<?php echo esc_attr( get_option( 'options_yardi_integration_creds_yardi_username' ) ); ?>">
            </div>
            <div class="white-box">
                <label for="options_yardi_integration_creds_yardi_password">Yardi Password</label>
                <input type="text" name="options_yardi_integration_creds_yardi_password" id="options_yardi_integration_creds_yardi_password" value="<?php echo esc_attr( get_option( 'options_yardi_integration_creds_yardi_password' ) ); ?>">
            </div>
        </div>
    </div>
    
    <div class="row integration entrata">
        <div class="column">
            <label>Entrata</label>
        </div>
        <div class="column">
            <div class="white-box">
                <label for="options_entrata_integration_creds_entrata_user">Entrata Username</label>
                <input type="text" name="options_entrata_integration_creds_entrata_user" id="options_entrata_integration_creds_entrata_user" value="<?php echo esc_attr( get_option( 'options_entrata_integration_creds_entrata_user' ) ); ?>">
            </div>
            <div class="white-box">
                <label for="options_entrata_integration_creds_entrata_pass">Entrata Username</label>
                <input type="text" name="options_entrata_integration_creds_entrata_pass" id="options_entrata_integration_creds_entrata_pass" value="<?php echo esc_attr( get_option( 'options_entrata_integration_creds_entrata_pass' ) ); ?>">
            </div>
            <div class="white-box">
                <label for="options_entrata_integration_creds_entrata_property_ids">Entrata Property IDs</label>
                <textarea rows="10" style="width: 100%;" name="options_entrata_integration_creds_entrata_property_ids" id="options_entrata_integration_creds_entrata_property_ids"><?php echo esc_attr( get_option( 'options_entrata_integration_creds_entrata_property_ids' ) ); ?></textarea>
                <p class="description">If there are multiple properties to be pulled in, enter those separated by commas</p>
            </div>
        </div>
    </div>
    
    <div class="row integration realpage">
        <div class="column">
            <label>RealPage</label>
        </div>
        <div class="column">
            <div class="white-box">
                <label for="options_realpage_integration_creds_realpage_user">RealPage Username</label>
                <input type="text" name="options_realpage_integration_creds_realpage_user" id="options_realpage_integration_creds_realpage_user" value="<?php echo esc_attr( get_option( 'options_realpage_integration_creds_realpage_user' ) ); ?>">
            </div>
            <div class="white-box">
                <label for="options_realpage_integration_creds_realpage_pass">RealPage Password</label>
                <input type="text" name="options_realpage_integration_creds_realpage_pass" id="options_realpage_integration_creds_realpage_pass" value="<?php echo esc_attr( get_option( 'options_realpage_integration_creds_realpage_pass' ) ); ?>">
            </div>
            <div class="white-box">
                <label for="options_realpage_integration_creds_realpage_pmc_id">RealPage PMC ID</label>
                <input type="text" name="options_realpage_integration_creds_realpage_pmc_id" id="options_realpage_integration_creds_realpage_pmc_id" value="<?php echo esc_attr( get_option( 'options_realpage_integration_creds_realpage_pmc_id' ) ); ?>">
            </div>
            <div class="white-box">
                <label for="options_realpage_integration_creds_realpage_site_ids">RealPage Site IDs</label>
                <textarea rows="10" style="width: 100%;" name="options_realpage_integration_creds_realpage_site_ids" id="options_realpage_integration_creds_realpage_site_ids"><?php echo esc_attr( get_option( 'options_realpage_integration_creds_realpage_site_ids' ) ); ?></textarea>
                <p class="description">If there are multiple properties to be pulled in, enter those separated by commas</p>
            </div>
        </div>
    </div>
    
    <div class="row integration appfolio">
        <div class="column">
            <label>AppFolio</label>
        </div>
        <div class="column">
            <div class="white-box">
                <label for="options_appfolio_integration_creds_appfolio_database_name">Appfolio Database Name</label>
                <input type="text" name="options_appfolio_integration_creds_appfolio_database_name" id="options_appfolio_integration_creds_appfolio_database_name" value="<?php echo esc_attr( get_option( 'options_appfolio_integration_creds_appfolio_database_name' ) ); ?>">
                <p class="description">Typically this is xxxxxxxxxxx.appfolio.com</p>
            </div>
            <div class="white-box">
                <label for="options_appfolio_integration_creds_appfolio_client_id">Appfolio Client ID</label>
                <input type="text" name="options_appfolio_integration_creds_appfolio_client_id" id="options_appfolio_integration_creds_appfolio_client_id" value="<?php echo esc_attr( get_option( 'options_appfolio_integration_creds_appfolio_client_id' ) ); ?>">
            </div>
            <div class="white-box">
                <label for="options_appfolio_integration_creds_appfolio_client_secret">Appfolio Client Secret</label>
                <input type="text" name="options_appfolio_integration_creds_appfolio_client_secret" id="options_appfolio_integration_creds_appfolio_client_secret" value="<?php echo esc_attr( get_option( 'options_appfolio_integration_creds_appfolio_client_secret' ) ); ?>">
            </div>
            <div class="white-box">
                <label for="options_appfolio_integration_creds_appfolio_property_ids">Appfolio Property IDs</label>
                <textarea rows="10" style="width: 100%;" name="options_appfolio_integration_creds_appfolio_property_ids" id="options_appfolio_integration_creds_appfolio_property_ids"><?php echo esc_attr( get_option( 'options_appfolio_integration_creds_appfolio_property_ids' ) ); ?></textarea>
                <p class="description">For AppFolio, this is an optional field. If left blank, Rent Fetch will simply fetch all of the properties in the account, which may or not be your preference. Please note that if property IDs are present here, all *other* synced properties through AppFolio will be deleted when the site next syncs.</p>
            </div>
        </div>
    </div>
         
   
         
    <?php
}

/**
 * Save the general settings
 */
add_action( 'rent_fetch_save_settings', 'rent_fetch_save_settings_general' );
function rent_fetch_save_settings_general() {
    
    // Text field
    if ( isset( $_POST['options_rent_fetch_api_key']) ) {
        $options_rent_fetch_api_key = sanitize_text_field( $_POST['options_rent_fetch_api_key'] );
        update_option( 'options_rent_fetch_api_key', $options_rent_fetch_api_key );
    }
    
    // Select field
    if ( isset( $_POST['options_apartment_site_type']) ) {
        $options_apartment_site_type = sanitize_text_field( $_POST['options_apartment_site_type'] );
        update_option( 'options_apartment_site_type', $options_apartment_site_type );
    }
    
    // Radio field
    if ( isset( $_POST['options_data_sync'] ) ) {
        $options_data_sync = sanitize_text_field( $_POST['options_data_sync'] );
        update_option( 'options_data_sync', $options_data_sync );
    }
    
    // Select field
    if ( isset( $_POST['options_sync_term'] ) ) {
        $options_sync_term = sanitize_text_field( $_POST['options_sync_term'] );
        update_option( 'options_sync_term', $options_sync_term );
    }
    
    // Checkboxes field
    if (isset($_POST['options_enabled_integrations'])) {
        $enabled_integrations = array_map('sanitize_text_field', $_POST['options_enabled_integrations']);
    } else {
        $enabled_integrations = array();
    }
    update_option('options_enabled_integrations', $enabled_integrations);
    
    // Text field
    if ( isset( $_POST['options_yardi_integration_creds_yardi_api_key'] ) ) {
        $options_yardi_integration_creds_yardi_api_key = sanitize_text_field( $_POST['options_yardi_integration_creds_yardi_api_key'] );
        update_option( 'options_yardi_integration_creds_yardi_api_key', $options_yardi_integration_creds_yardi_api_key );
    }
    
    // Textarea field
    if ( isset( $_POST['options_yardi_integration_creds_yardi_property_code'] ) ) {
        $options_yardi_integration_creds_yardi_property_code = sanitize_text_field( $_POST['options_yardi_integration_creds_yardi_property_code'] );
        
        // Remove all whitespace
        $options_yardi_integration_creds_yardi_property_code = preg_replace('/\s+/', '', $options_yardi_integration_creds_yardi_property_code);
        
        // Add a space after each comma
        $options_yardi_integration_creds_yardi_property_code = preg_replace('/,/', ', ', $options_yardi_integration_creds_yardi_property_code);
        
        update_option( 'options_yardi_integration_creds_yardi_property_code', $options_yardi_integration_creds_yardi_property_code );
    }
    
    // Single checkbox field
    if ( isset( $_POST['options_yardi_integration_creds_enable_yardi_api_lead_generation'] ) ) {
        $options_yardi_integration_creds_enable_yardi_api_lead_generation = true;
    } else {
        $options_yardi_integration_creds_enable_yardi_api_lead_generation = false;
    }
    update_option( 'options_yardi_integration_creds_enable_yardi_api_lead_generation', $options_yardi_integration_creds_enable_yardi_api_lead_generation );

    
    // Text field
    if ( isset( $_POST['options_yardi_integration_creds_yardi_username'] ) ) {
        $options_yardi_integration_creds_yardi_username = sanitize_text_field( $_POST['options_yardi_integration_creds_yardi_username'] );
        update_option( 'options_yardi_integration_creds_yardi_username', $options_yardi_integration_creds_yardi_username );
    }
    
    // Text field
    if ( isset( $_POST['options_yardi_integration_creds_yardi_password'] ) ) {
        $options_yardi_integration_creds_yardi_password = sanitize_text_field( $_POST['options_yardi_integration_creds_yardi_password'] );
        update_option( 'options_yardi_integration_creds_yardi_password', $options_yardi_integration_creds_yardi_password );
    }
    
    // Text field
    if ( isset( $_POST['options_entrata_integration_creds_entrata_user'] ) ) {
        $options_entrata_integration_creds_entrata_user = sanitize_text_field( $_POST['options_entrata_integration_creds_entrata_user'] );
        update_option( 'options_entrata_integration_creds_entrata_user', $options_entrata_integration_creds_entrata_user );
    }
    
    // Text field
    if ( isset( $_POST['options_entrata_integration_creds_entrata_pass'] ) ) {
        $options_entrata_integration_creds_entrata_pass = sanitize_text_field( $_POST['options_entrata_integration_creds_entrata_pass'] );
        update_option( 'options_entrata_integration_creds_entrata_pass', $options_entrata_integration_creds_entrata_pass );
    }
    
    // Textarea field
    if ( isset( $_POST['options_entrata_integration_creds_entrata_property_ids'] ) ) {
        $options_entrata_integration_creds_entrata_property_ids = sanitize_text_field( $_POST['options_entrata_integration_creds_entrata_property_ids'] );
        
        // Remove all whitespace
        $options_entrata_integration_creds_entrata_property_ids = preg_replace('/\s+/', '', $options_entrata_integration_creds_entrata_property_ids);
        
        // Add a space after each comma
        $options_entrata_integration_creds_entrata_property_ids = preg_replace('/,/', ', ', $options_entrata_integration_creds_entrata_property_ids);
        
        update_option( 'options_entrata_integration_creds_entrata_property_ids', $options_entrata_integration_creds_entrata_property_ids );
    }
    
    // Text field
    if ( isset( $_POST['options_realpage_integration_creds_realpage_user'] ) ) {
        $options_realpage_integration_creds_realpage_user = sanitize_text_field( $_POST['options_realpage_integration_creds_realpage_user'] );
        update_option( 'options_realpage_integration_creds_realpage_user', $options_realpage_integration_creds_realpage_user );
    }
    
    // Text field
    if ( isset( $_POST['options_realpage_integration_creds_realpage_pass'] ) ) {
        $options_realpage_integration_creds_realpage_pass = sanitize_text_field( $_POST['options_realpage_integration_creds_realpage_pass'] );
        update_option( 'options_realpage_integration_creds_realpage_pass', $options_realpage_integration_creds_realpage_pass );
    }
    
    // Text field
    if ( isset( $_POST['options_realpage_integration_creds_realpage_pmc_id'] ) ) {
        $options_realpage_integration_creds_realpage_pmc_id = sanitize_text_field( $_POST['options_realpage_integration_creds_realpage_pmc_id'] );
        update_option( 'options_realpage_integration_creds_realpage_pmc_id', $options_realpage_integration_creds_realpage_pmc_id );
    }
    
    // Textarea field
    if ( isset( $_POST['options_realpage_integration_creds_realpage_site_ids'] ) ) {
        $options_realpage_integration_creds_realpage_site_ids = sanitize_text_field( $_POST['options_realpage_integration_creds_realpage_site_ids'] );
        
        // Remove all whitespace
        $options_realpage_integration_creds_realpage_site_ids = preg_replace('/\s+/', '', $options_realpage_integration_creds_realpage_site_ids);
        
        // Add a space after each comma
        $options_realpage_integration_creds_realpage_site_ids = preg_replace('/,/', ', ', $options_realpage_integration_creds_realpage_site_ids);
        
        update_option( 'options_realpage_integration_creds_realpage_site_ids', $options_realpage_integration_creds_realpage_site_ids );
    }
    
    // Text field
    if ( isset( $_POST['options_appfolio_integration_creds_appfolio_database_name'] ) ) {
        $options_appfolio_integration_creds_appfolio_database_name = sanitize_text_field( $_POST['options_appfolio_integration_creds_appfolio_database_name'] );
        
        // Remove .appfolio.com from the end of the database name
        $options_appfolio_integration_creds_appfolio_database_name = preg_replace('/.appfolio.com/', '', $options_appfolio_integration_creds_appfolio_database_name);
        
        update_option( 'options_appfolio_integration_creds_appfolio_database_name', $options_appfolio_integration_creds_appfolio_database_name );
    }
    
    // Text field
    if ( isset( $_POST['options_appfolio_integration_creds_appfolio_client_id'] ) ) {
        $options_appfolio_integration_creds_appfolio_client_id = sanitize_text_field( $_POST['options_appfolio_integration_creds_appfolio_client_id'] );
        update_option( 'options_appfolio_integration_creds_appfolio_client_id', $options_appfolio_integration_creds_appfolio_client_id );
    }
    
    // Text field
    if ( isset( $_POST['options_appfolio_integration_creds_appfolio_client_secret'] ) ) {
        $options_appfolio_integration_creds_appfolio_client_secret = sanitize_text_field( $_POST['options_appfolio_integration_creds_appfolio_client_secret'] );
        update_option( 'options_appfolio_integration_creds_appfolio_client_secret', $options_appfolio_integration_creds_appfolio_client_secret );
    }
    
    // Textarea field
    if ( isset( $_POST['options_appfolio_integration_creds_appfolio_property_ids'] ) ) {
        $options_appfolio_integration_creds_appfolio_property_ids = sanitize_text_field( $_POST['options_appfolio_integration_creds_appfolio_property_ids'] );
        
        // Remove all whitespace
        $options_appfolio_integration_creds_appfolio_property_ids = preg_replace('/\s+/', '', $options_appfolio_integration_creds_appfolio_property_ids);
        
        // Add a space after each comma
        $options_appfolio_integration_creds_appfolio_property_ids = preg_replace('/,/', ', ', $options_appfolio_integration_creds_appfolio_property_ids);
        
        update_option( 'options_appfolio_integration_creds_appfolio_property_ids', $options_appfolio_integration_creds_appfolio_property_ids );
    }
    
}

/**
 * Adds the Google settings section to the Rent Fetch settings page
 */
add_action( 'rent_fetch_do_settings_google', 'rent_fetch_settings_google' );
function rent_fetch_settings_google() {
    ?>
        
    <div class="row">
        <div class="column">
            <label>Google reCAPTCHA v2</label>
        </div>
        <div class="column">
            <div class="white-box">
                <label for="options_google_recaptcha_google_recaptcha_v2_site_key">reCAPTCHA key</label>
                <input type="text" name="options_google_recaptcha_google_recaptcha_v2_site_key" id="options_google_recaptcha_google_recaptcha_v2_site_key" value="<?php echo esc_attr( get_option( 'options_google_recaptcha_google_recaptcha_v2_site_key' ) ); ?>">
            </div>
            <div class="white-box">
                <label for="options_google_recaptcha_google_recaptcha_v2_secret">reCAPTCHA key</label>
                <input type="text" name="options_google_recaptcha_google_recaptcha_v2_secret" id="options_google_recaptcha_google_recaptcha_v2_secret" value="<?php echo esc_attr( get_option( 'options_google_recaptcha_google_recaptcha_v2_secret' ) ); ?>">
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="column">
            <label for="options_google_maps_api_key">Google Maps API Key</label>
        </div>
        <div class="column">
            <input type="text" name="options_google_maps_api_key" id="options_google_maps_api_key" value="<?php echo esc_attr( get_option( 'options_google_maps_api_key' ) ); ?>">
            <p class="description">Required for Google Maps.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="column">
            <label for="options_google_map_marker">Google Maps Marker</label>
        </div>
        <div class="column">
            <input type="text" name="options_google_map_marker" id="options_google_map_marker" value="<?php echo esc_attr( get_option( 'options_google_map_marker' ) ); ?>">
            <p class="description">URL to a custom marker image. Leave blank to use the default marker.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="column">
            <label for="options_google_maps_styles">Google Maps Styles</label>
        </div>
        <div class="column">
            <textarea name="options_google_maps_styles" id="options_google_maps_styles" rows="10" style="width: 100%;"><?php echo esc_attr( get_option( 'options_google_maps_styles' ) ); ?></textarea>
            <p class="description">JSON array of Google Maps styles. See <a href="https://snazzymaps.com/" target="_blank">Snazzy Maps</a> for examples.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="column">
            <label>Google Maps default location</label>
            <p class="description">This serves as the map center in the event of a search with no results.</p>
        </div>
        <div class="column">
            <div class="white-box">
                <label for="options_google_maps_default_latitude">Latitude</label>
                <input type="text" name="options_google_maps_default_latitude" id="options_google_maps_default_latitude" value="<?php echo esc_attr( get_option( 'options_google_maps_default_latitude' ) ); ?>">
            </div>
            <div class="white-box">
                <label for="options_google_maps_default_longitude">Longitude</label>
                <input type="text" name="options_google_maps_default_longitude" id="options_google_maps_default_longitude" value="<?php echo esc_attr( get_option( 'options_google_maps_default_longitude' ) ); ?>">
            </div>
        </div>
    </div>
           
    <?php
}

/**
 * Save the Google settings
 */
add_action( 'rent_fetch_save_settings', 'rent_fetch_save_settings_google' );
function rent_fetch_save_settings_google() {
    
    // Text field
    if ( isset( $_POST['options_google_recaptcha_google_recaptcha_v2_site_key'] ) ) {
        $options_google_recaptcha_google_recaptcha_v2_site_key = sanitize_text_field( $_POST['options_google_recaptcha_google_recaptcha_v2_site_key'] );
        update_option( 'options_google_recaptcha_google_recaptcha_v2_site_key', $options_google_recaptcha_google_recaptcha_v2_site_key );
    }
    
    // Text field
    if ( isset( $_POST['options_google_recaptcha_google_recaptcha_v2_secret'] ) ) {
        $options_google_recaptcha_google_recaptcha_v2_secret = sanitize_text_field( $_POST['options_google_recaptcha_google_recaptcha_v2_secret'] );
        update_option( 'options_google_recaptcha_google_recaptcha_v2_secret', $options_google_recaptcha_google_recaptcha_v2_secret );
    }
    
    // Text field
    if ( isset( $_POST['options_google_maps_api_key'] ) ) {
        $options_google_maps_api_key = sanitize_text_field( $_POST['options_google_maps_api_key'] );
        update_option( 'options_google_maps_api_key', $options_google_maps_api_key );
    }
    
    // Text field
    if ( isset( $_POST['options_google_geocoding_api_key'] ) ) {
        $options_google_geocoding_api_key = sanitize_text_field( $_POST['options_google_geocoding_api_key'] );
        update_option( 'options_google_geocoding_api_key', $options_google_geocoding_api_key );
    }
    
    // Text field
    if ( isset( $_POST['options_google_map_marker'] ) ) {
        $options_google_map_marker = sanitize_text_field( $_POST['options_google_map_marker'] );
        update_option( 'options_google_map_marker', $options_google_map_marker );
    }
    
    // Textarea field
    if ( isset( $_POST['options_google_maps_styles'] ) ) {
        $options_google_maps_styles = sanitize_text_field( $_POST['options_google_maps_styles'] );
        update_option( 'options_google_maps_styles', $options_google_maps_styles );
    }
    
    // Text field
    if ( isset( $_POST['options_google_maps_default_latitude'] ) ) {
        $options_google_maps_default_latitude = sanitize_text_field( $_POST['options_google_maps_default_latitude'] );
        update_option( 'options_google_maps_default_latitude', $options_google_maps_default_latitude );
    }
    
    // Text field
    if ( isset( $_POST['options_google_maps_default_longitude'] ) ) {
        $options_google_maps_default_longitude = sanitize_text_field( $_POST['options_google_maps_default_longitude'] );
        update_option( 'options_google_maps_default_longitude', $options_google_maps_default_longitude );
    }
    
}

/**
 * Adds the properties settings section to the Rent Fetch settings page
 */
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

/**
 * Adds the property search settings subsection to the Rent Fetch settings page
 */
add_action( 'rent_fetch_do_settings_properties_property_search', 'rent_fetch_settings_properties_property_search' );
function rent_fetch_settings_properties_property_search() {
    ?>
    
     <div class="row">
        <div class="column">
            <label for="options_maximum_number_of_properties_to_show">Maximum number of properties to show</label>
        </div>
        <div class="column">
            <input type="text" name="options_maximum_number_of_properties_to_show" id="options_maximum_number_of_properties_to_show" value="<?php echo esc_attr( get_option( 'options_maximum_number_of_properties_to_show' ) ); ?>">
        </div>
    </div>
    
    <div class="row">
        <div class="column">
            <label for="options_property_availability_display">Property availability display</label>
        </div>
        <div class="column">
            <select name="options_property_availability_display" id="options_property_availability_display" value="<?php echo esc_attr( get_option( 'options_property_availability_display' ) ); ?>">
                <option value="available" <?php selected( get_option( 'options_property_availability_display' ), 'available' ); ?>>Availability</option>
                <option value="all" <?php selected( get_option( 'options_property_availability_display' ), 'all' ); ?>>All properties ignoring availability</option>
            </select>
        </div>
    </div>
    
    <div class="row">
        <div class="column">
            <label for="options_starter_search_components">Starter search components</label>
            <p class="description">Several default components can be optionally shown in the starter search box. Select the ones you'd like right here.</p>
        </div>
        <div class="column">
            <?php
            
            // Get saved options
            $options_starter_search_components = get_option('options_starter_search_components');
            
            // Define default values
            $default_options = array(
                'text_based_search',
                'beds_search',
                'type_search',
            );
            
            // Make it an array just in case it isn't (for example, if it's a new install)
            if (!is_array($options_starter_search_components)) {
                $options_starter_search_components = $default_options;
            }
                        
            ?>
            <ul class="checkboxes">
                <li>
                    <label>
                        <input type="checkbox" name="options_starter_search_components[]" value="text_based_search" <?php checked( in_array( 'text_based_search', $options_starter_search_components ) ); ?>>
                        Text-based search
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="options_starter_search_components[]" value="beds_search" <?php checked( in_array( 'beds_search', $options_starter_search_components ) ); ?>>
                        Beds search
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="options_starter_search_components[]" value="baths_search" <?php checked( in_array( 'baths_search', $options_starter_search_components ) ); ?>>
                        Baths search
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="options_starter_search_components[]" value="type_search" <?php checked( in_array( 'type_search', $options_starter_search_components ) ); ?>>
                        Type search
                    </label>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="row">
        <div class="column">
            <label for="options_map_search_components">Map search components</label>
            <p class="description">Several default components can be optionally shown in the starter search box. Select the ones you'd like right here. Please note that if no values are available (e.g. if there's no data for Amenities), then that section of the search still won't show despite being selected.</p>
        </div>
        <div class="column">
            <?php
            
            // Get saved options
            $options_map_search_components = get_option('options_map_search_components');
            
            // Define default values
            $default_options = array(
                'text_based_search',
                'beds_search',
                'type_search',
                'date_search',
                'price_search',
                'amenities_search',
                'pets_search',
            );
            
            // Make it an array just in case it isn't (for example, if it's a new install)
            if (!is_array($options_map_search_components)) {
                $options_map_search_components = $default_options;
            }
            
            ?>
            <ul class="checkboxes">
                <li>
                    <label>
                        <input type="checkbox" name="options_map_search_components[]" value="text_based_search" <?php checked( in_array( 'text_based_search', $options_map_search_components ) ); ?>>
                        Text-based search
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="options_map_search_components[]" value="beds_search" <?php checked( in_array( 'beds_search', $options_map_search_components ) ); ?>>
                        Beds search
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="options_map_search_components[]" value="baths_search" <?php checked( in_array( 'baths_search', $options_map_search_components ) ); ?>>
                        Baths search
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="options_map_search_components[]" value="type_search" <?php checked( in_array( 'type_search', $options_map_search_components ) ); ?>>
                        Type search
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="options_map_search_components[]" value="date_search" <?php checked( in_array( 'date_search', $options_map_search_components ) ); ?>>
                        Date search
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="options_map_search_components[]" value="price_search" <?php checked( in_array( 'price_search', $options_map_search_components ) ); ?>>
                        Price search
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="options_map_search_components[]" value="amenities_search" <?php checked( in_array( 'amenities_search', $options_map_search_components ) ); ?>>
                        Amenities search
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="options_map_search_components[]" value="pets_search" <?php checked( in_array( 'pets_search', $options_map_search_components ) ); ?>>
                        Pets search
                    </label>
                </li>                
            </ul>
        </div>
    </div>
    
    <div class="row">
        <div class="column">
            <label for="options_maximum_bedrooms_to_search">Maximum bedrooms to search</label>
        </div>
        <div class="column">
            <input type="text" name="options_maximum_bedrooms_to_search" id="options_maximum_bedrooms_to_search" value="<?php echo esc_attr( get_option( 'options_maximum_bedrooms_to_search' ) ); ?>">
        </div>
    </div>
    
    <div class="row">
        <div class="column">
            <label for="options_price_filter_minimum">Price filter</label>
        </div>
        <div class="column">
            <div class="white-box">
                <label for="options_price_filter_minimum">Price filter minimum</label>
                <input type="text" name="options_price_filter_minimum" id="options_price_filter_minimum" value="<?php echo esc_attr( get_option( 'options_price_filter_minimum' ) ); ?>">
            </div>
            <div class="white-box">
                <label for="options_price_filter_maximum">Price filter maximum</label>
                <input type="text" name="options_price_filter_maximum" id="options_price_filter_maximum" value="<?php echo esc_attr( get_option( 'options_price_filter_maximum' ) ); ?>">
            </div>
            <div class="white-box">
                <label for="options_price_filter_step">Price filter step</label>
                <input type="text" name="options_price_filter_step" id="options_price_filter_step" value="<?php echo esc_attr( get_option( 'options_price_filter_step' ) ); ?>">
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="column">
            <label for="options_number_of_amenities_to_show">Number of amenities to show</label>
        </div>
        <div class="column">
            <input type="text" name="options_number_of_amenities_to_show" id="options_number_of_amenities_to_show" value="<?php echo esc_attr( get_option( 'options_number_of_amenities_to_show' ), 10 ); ?>">
        </div>
    </div>        
    <?php
}

/**
 * Save the property search settings
 */
add_action( 'rent_fetch_save_settings', 'rent_fetch_save_settings_property_search' );
function rent_fetch_save_settings_property_search() {
    
    // Number field
    if ( isset( $_POST['options_maximum_number_of_properties_to_show'] ) ) {
        $max_properties = intval( $_POST['options_maximum_number_of_properties_to_show'] );
        update_option( 'options_maximum_number_of_properties_to_show', $max_properties );
    }
    
    // Select field
    if ( isset( $_POST['options_property_availability_display'] ) ) {
        $property_display = sanitize_text_field( $_POST['options_property_availability_display'] );
        update_option( 'options_property_availability_display', $property_display );
    }
    
    // // Checkbox field
    // if ( isset( $_POST['options_starter_search_components'] ) ) {
    //     $starter_search_components = $_POST['options_starter_search_components'];
    //     $starter_search_components = array_map( 'sanitize_text_field', $starter_search_components );
    //     update_option( 'options_starter_search_components', $starter_search_components );
    // }
    
    // Checkboxes field
    if (isset($_POST['options_starter_search_components'])) {
        $options_starter_search_components = array_map('sanitize_text_field', $_POST['options_starter_search_components']);
    } else {
        $options_starter_search_components = array();
    }
    update_option('options_starter_search_components', $options_starter_search_components);
    
    // Checkboxes field
    if (isset($_POST['options_map_search_components'])) {
        $options_map_search_components = array_map('sanitize_text_field', $_POST['options_map_search_components']);
    } else {
        $options_map_search_components = array();
    }
    update_option('options_map_search_components', $options_map_search_components);
    
    // Number field
    if ( isset( $_POST['options_maximum_bedrooms_to_search'] ) ) {
        $max_bedrooms = intval( $_POST['options_maximum_bedrooms_to_search'] );
        update_option( 'options_maximum_bedrooms_to_search', $max_bedrooms );
    }
    
    // Number field
    if ( isset( $_POST['options_price_filter_minimum'] ) ) {
        $price_filter_minimum = intval( $_POST['options_price_filter_minimum'] );
        update_option( 'options_price_filter_minimum', $price_filter_minimum );
    }
    
    // Number field
    if ( isset( $_POST['options_price_filter_maximum'] ) ) {
        $price_filter_maximum = intval( $_POST['options_price_filter_maximum'] );
        update_option( 'options_price_filter_maximum', $price_filter_maximum );
    }
    
    // Number field
    if ( isset( $_POST['options_price_filter_step'] ) ) {
        $price_filter_step = intval( $_POST['options_price_filter_step'] );
        update_option( 'options_price_filter_step', $price_filter_step );
    }
    
    // Number field
    if ( isset( $_POST['options_number_of_amenities_to_show'] ) ) {
        $number_of_amenities_to_show = intval( $_POST['options_number_of_amenities_to_show'] );
        update_option( 'options_number_of_amenities_to_show', $number_of_amenities_to_show );
    }
    
}

/**
 * Adds the properties archives settings subsection to the Rent Fetch settings page
 */
add_action( 'rent_fetch_do_settings_properties_property_archives', 'rent_fetch_settings_properties_property_archives' );
function rent_fetch_settings_properties_property_archives() {
    ?>
    
    <?php
}

/**
 * Adds the single property template settings section to the Rent Fetch settings page
 */
add_action( 'rent_fetch_do_settings_single_property_template', 'rent_fetch_settings_single_property_template' );
function rent_fetch_settings_single_property_template() {
    ?>
    
    <?php
}

/**
 * Adds the floorplans settings section to the Rent Fetch settings page
 */
add_action( 'rent_fetch_do_settings_floorplan_archives', 'rent_fetch_settings_floorplan_archives' );
function rent_fetch_settings_floorplan_archives() {
    ?>
    
    <?php
}
