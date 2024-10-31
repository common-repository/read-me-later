<?php

/**
 * Define the internationalization functionality
 */

class Read_Me_Later_Lang {
    
	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'read-me-later',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}