<?php

/**
 * The core plugin class.
 */
class Read_Me_Later {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the front side of the site.
     */
    public function __construct() {

        $this->plugin_name = 'read-me-later';
        $this->version = '1.0.0';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Read_Me_Later_Loader. Loads the hooks of the plugin.
     * - Read_Me_Later_i18n. Defines internationalization functionality.
     * - Read-Me_Later_Admin. Defines all hooks for the admin area.
     * - Read-Me_Later_Public. Defines all hooks for the public side of the site
     */
    private function load_dependencies() {

        /**
         * The class responsible for including the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-read-me-later-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-read-me-later-lang.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-read-me-later-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-read-me-later-public.php';

        $this->loader = new Read_Me_Later_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the RS_Save_For_Later_i18n class in order to set the domain and to register the hook
     * with WordPress.
     */
    private function set_locale() {

        $plugin_i18n = new Read_Me_Later_Lang();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     */
    private function define_admin_hooks() {

        $plugin_admin = new Read_Me_Later_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_menu', $plugin_admin, 'register_admin_page');
        $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     */
    private function define_public_hooks() {

        $plugin_public = new RML_Save_For_Later_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_shortcode('Readmelater', $plugin_public, 'save_for_later_shortcode');
        $this->loader->add_shortcode('read-me-later', $plugin_public, 'saved_for_later_shortcode');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action('the_content', $plugin_public, 'override_content', 45);
        $this->loader->add_action('wp_footer', $plugin_public, 'add_saved_items_to_footer', 45);

        $this->loader->add_action('wp_ajax_nopriv_save_unsave_for_later', $plugin_public, 'save_unsave_for_later');
        $this->loader->add_action('wp_ajax_save_unsave_for_later', $plugin_public, 'save_unsave_for_later');

        $this->loader->add_action('wp_ajax_nopriv_save_for_later_remove_all', $plugin_public, 'save_for_later_remove_all');
        $this->loader->add_action('wp_ajax_save_for_later_remove_all', $plugin_public, 'save_for_later_remove_all');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
