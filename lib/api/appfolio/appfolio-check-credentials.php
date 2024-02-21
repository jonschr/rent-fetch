<?php

/**
 * Check if the credentials exist
 *
 * @return  bool  true if creds exist, false if not
 */
function rentfetch_check_creds_appfolio() {
	
	$appfolio_integration_creds = get_field( 'appfolio_integration_creds', 'option' );
	$appfolio_database_name = $appfolio_integration_creds['appfolio_database_name'];
	$appfolio_client_id = $appfolio_integration_creds['appfolio_client_id'];
	$appfolio_client_secret = $appfolio_integration_creds['appfolio_client_secret'];
		
	// return false if there's no api key set
	if ( !$appfolio_database_name || !$appfolio_client_id || !$appfolio_client_secret )      
		return false;
	
	// return true if there's an api key
	return true;
	
}
