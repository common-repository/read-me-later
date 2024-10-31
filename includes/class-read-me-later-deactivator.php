<?php

/**
 * Fired when plugin is deactivated
 */

class Read_Me_Later_Deactivator {

	/**
	 * Deactivate this plugin.
         * Delete the page created on activation and delete the options saved in DB
	 */
	public static function deactivate() {

		// Get Page ID
		$page_id = get_option( 'rml_save_for_later_page_id' );

		// Delete Page
		if ( $page_id ) {
			wp_delete_post( $page_id, true );
		}

		// Delete our Page ID
		delete_option( 'rml_save_for_later_page_id' );
        delete_option( 'read-me-later-settings' );
        delete_option( 'read-me-later-display-settings' );

	}

}