<?php
/**
 * Plugin Name: Read Me Later
 * Plugin URI:  https://developer.wordpress.org/plugins/read-me-later
 * Description: Save posts / pages to read them later. Visitors can save the articles to read them later even without logged in.
 * Version:     1.0.0
 * Author:      Rizwan Akhtar
 * Author URI:  https://profiles.wordpress.org/rizwanengineer2727/
 * Text Domain: read-me-later
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 */
function activate_read_me_later() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-read-me-later-activator.php';
	Read_Me_Later_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_read_me_later() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-read-me-later-deactivator.php';
	Read_Me_Later_Deactivator::deactivate();
}

//add Activation and De-activation hooks

register_activation_hook( __FILE__, 'activate_read_me_later' );
register_deactivation_hook( __FILE__, 'deactivate_read_me_later' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-read-me-later.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */

function run_read_me_later() {
	$plugin = new Read_Me_Later();
	$plugin->run();
}
run_read_me_later();