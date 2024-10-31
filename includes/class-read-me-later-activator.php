<?php
/**
 * Fired during plugin activation.
 */
class Read_Me_Later_Activator {

	public static function activate() {

		// Current User ID
		$current_user = get_current_user_id();

		// Create a New Page
                //Put the shortcode in the content to display read me later saved content.
                
		$page_args = array(
			'post_type' => 'page',
			'post_title' => __( 'Read Me Later', 'read-me-later' ),
			'post_content' => '[read-me-later]',
			'post_status' => 'publish',
			'post_author' => $current_user
		);
		$page_id = wp_insert_post( $page_args );

		// Save our Page ID
		add_option( 'rml_save_for_later_page_id', $page_id );

	}

}