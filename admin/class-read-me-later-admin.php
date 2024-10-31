<?php

/**
 * The admin-specific functionality of the plugin.
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.

 */
class Read_Me_Later_Admin {

    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
            add_action( 'wp_ajax_rml_save_ratings', array( $this, 'rml_save_ratings' ) );
    }

    /**
     * Register the stylesheets for the admin area.
     */
    public function enqueue_styles($hook) {
        

        /**
         * This function is provided for demonstration purposes only.
         */
        if($hook == 'toplevel_page_read-me-later'){
        wp_enqueue_style($this->plugin_name . '-font-roboto', 'https://fonts.googleapis.com/css?family=Roboto:400,500,500italic,300&subset=latin,latin-ext', array(), '', 'all');
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/read-me-later-admin.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-materialize-min', plugin_dir_url(__FILE__) . 'css/materialize.min.css', array(), $this->version, 'all');
    }
    }

    /**
     * Register the JavaScript for the admin area.
     */
    public function enqueue_scripts($hook) {
      
        /**
         * This function is provided for demonstration purposes only.
         */
        if($hook == 'toplevel_page_read-me-later'){
        wp_enqueue_script($this->plugin_name . 'materialize-js', plugin_dir_url(__FILE__) . 'js/materialize.min.js', array(), $this->version, true);
        wp_enqueue_script($this->plugin_name . 'custom-js', plugin_dir_url(__FILE__) . 'js/custom.js', array($this->plugin_name . 'material-js'), $this->version, TRUE);
    }
    }

    /**
     * Register the page for the admin area.
     */
    public function register_admin_page() {

        add_menu_page('Read Me Later', 'Read Me Later', 'manage_options', 'read-me-later', array($this, 'display_admin_page'), 'dashicons-tag');
    }

    /**
     * Display the page for the admin area
     */
    public function display_admin_page() {

        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/read-me-later-admin-display.php';
    }

    /**
     * Settings callbacks.
     */
    public function rml_setting_callback($input) {

        $new_input = array();

        if (isset($input)) {
            foreach ($input as $key => $value) {
                if ($key == 'post-type') {
                    $new_input[$key] = $value;
                } else {
                    $new_input[$key] = sanitize_text_field($value);
                }
            }
        }

        return $new_input;
    }

    public function rml_display_settings_section_callback() {
        $new_input = array();


        if (isset($input)) {
            foreach ($input as $key => $value) {
                if ($key == 'post-type') {
                    $new_input[$key] = $value;
                } else {
                    $new_input[$key] = sanitize_text_field($value);
                }
            }
        }

        return $new_input;
    }

    /**
     * Register settings.
     */
    public function register_settings() {

        // Settings
        register_setting(
                $this->plugin_name . '-settings', $this->plugin_name . '-settings', array($this, 'rml_setting_callback')
        );
        register_setting(
                $this->plugin_name . '-display-settings', $this->plugin_name . '-display-settings', null
        );


        add_settings_section(
                $this->plugin_name . '-settings-section', __('', 'read-me-later'), array($this, 'rml_settings_section_callback'), $this->plugin_name . '-settings'
        );

        add_settings_section(
                $this->plugin_name . '-display-settings-section', __('', 'read-me-later'), array($this, 'rml_display_settings_section_callback'), $this->plugin_name . '-display-settings'
        );
        //Fields

        add_settings_field(
                'read-me-later-icon', __('Read Me Later Icon', 'read-me-later'), array($this, 'rml_icon_override_callback'), $this->plugin_name . '-display-settings', $this->plugin_name . '-display-settings-section', array(
            'label_for' => $this->plugin_name . '-display-settings[read-me-later-icon]'
                )
        );

        add_settings_field(
                'toggle-title-override', __('Add Read Me Later button after the post title', 'read-me-later'), array($this, 'rml_toggle_title_override_callback'), $this->plugin_name . '-settings', $this->plugin_name . '-settings-section', array(
            'label_for' => $this->plugin_name . '-settings[toggle-title-override]'
                )
        );

        add_settings_field(
                'toggle-content-override', __('Add Read Me Later button after post content', 'read-me-later'), array($this, 'rml_toggle_content_override_callback'), $this->plugin_name . '-settings', $this->plugin_name . '-settings-section', array(
            'label_for' => $this->plugin_name . '-settings[toggle-content-override]'
                )
        );

        add_settings_field(
                'post-type', __('Add Read Me Later button to these post types', 'read-me-later'), array($this, 'rml_post_type_callback'), $this->plugin_name . '-settings', $this->plugin_name . '-settings-section'
        );

//        add_settings_field(
//                'toggle-css', __('Use Default tyles', 'read-me-later'), array($this, 'rml_toggle_css_callback'), $this->plugin_name . '-settings', $this->plugin_name . '-settings-section'
//        );

        add_settings_field(
                'toggle-logged-in', __('Enable Read Me Later ONLY for Logged IN users', 'read-me-later'), array($this, 'rml_toggle_logged_in_callback'), $this->plugin_name . '-settings', $this->plugin_name . '-settings-section'
        );

        add_settings_field(
                'save-text', __('Your "Read Me Later" text.', 'read-me-later'), array($this, 'rml_save_text_callback'), $this->plugin_name . '-settings', $this->plugin_name . '-settings-section'
        );

        add_settings_field(
                'unsave-text', __('Your "Remove from Saved for Later" text.', 'read-me-later'), array($this, 'rml_unsave_text_callback'), $this->plugin_name . '-settings', $this->plugin_name . '-settings-section'
        );

        add_settings_field(
                'saved-text', __('Your "Read Saved" text.', 'read-me-later'), array($this, 'rml_saved_text_callback'), $this->plugin_name . '-settings', $this->plugin_name . '-settings-section'
        );

        add_settings_field(
                'number-text', __('Your "Number of Saved Items" text. ', 'read-me-later'), array($this, 'rml_number_text_callback'), $this->plugin_name . '-settings', $this->plugin_name . '-settings-section'
        );

        add_settings_field(
                'remove-all-text', __('Your "Remove All" text.', 'read-me-later'), array($this, 'rml_remove_all_text_callback'), $this->plugin_name . '-settings', $this->plugin_name . '-settings-section'
        );

        add_settings_field(
                'no-content-text', __('Your "No saved content" text.', 'read-me-later'), array($this, 'rml_no_content_text_callback'), $this->plugin_name . '-settings', $this->plugin_name . '-settings-section'
        );
    }

    /**
     * Section callbacks.
     */
    public function rml_settings_section_callback() {

        return;
    }

    /**
     * Field callbacks.
     */
    public function rml_toggle_content_override_callback() {

        $options = get_option($this->plugin_name . '-settings');
        $option = 0;

        if (!empty($options['toggle-content-override'])) {
            $option = $options['toggle-content-override'];
        }
        ?>

        <p>
            <label>
                <input type="checkbox" name="<?php echo $this->plugin_name . '-settings'; ?>[toggle-content-override]" id="<?php echo $this->plugin_name . '-settings'; ?>[toggle-content-override]" <?php checked($option, 1, true); ?> value="1" />
                <span></span>
            </label>
        </p>
        <?php
    }

    public function rml_toggle_title_override_callback() {

        $options = get_option($this->plugin_name . '-settings');

        $option = 0;

        if (!empty($options['toggle-title-override'])) {
            $option = $options['toggle-title-override'];
        }
        ?>
        <p>
            <label>
                <input type="checkbox" name="<?php echo $this->plugin_name . '-settings'; ?>[toggle-title-override]" id="<?php echo $this->plugin_name . '-settings'; ?>[toggle-title-override]" <?php checked($option, 1, true); ?> value="1" />
                <span></span>
            </label>
        </p>
        <?php
    }

    public function rml_post_type_callback() {

        $options = get_option($this->plugin_name . '-settings');
        $option = array();

        if (!empty($options['post-type'])) {
            $option = $options['post-type'];
        }

        $args = array(
            'public' => true
        );
        $post_types = get_post_types($args, 'names');

        foreach ($post_types as $post_type) {
            if ($post_type != 'attachment') {
                $checked = in_array($post_type, $option) ? 'checked="checked"' : '';
                ?>
                <p>
                    <label class="post-types">
                        <input type="checkbox" id="<?php echo $this->plugin_name; ?>-settings[post-type]" name="<?php echo $this->plugin_name; ?>-settings[post-type][]" value="<?php echo esc_attr($post_type); ?>" <?php echo $checked; ?> />
                        <span><?php echo $post_type; ?></span>
                    </label>
                </p>



                <?php
            }
        }
    }

    public function rml_toggle_css_callback() {

        $options = get_option($this->plugin_name . '-settings');
        $option = 0;

        if (!empty($options['toggle-css'])) {
            $option = $options['toggle-css'];
        }
        ?>
        <p>
            <label>
                <input type="checkbox" name="<?php echo $this->plugin_name . '-settings'; ?>[toggle-css]" id="<?php echo $this->plugin_name . '-settings'; ?>[toggle-css]" <?php checked($option, 1, true); ?> value="1" />
                <span></span>
            </label>
        </p>
        <?php
    }

    public function rml_toggle_logged_in_callback() {

        $options = get_option($this->plugin_name . '-settings');
        $option = 0;

        if (!empty($options['toggle-logged-in'])) {
            $option = $options['toggle-logged-in'];
        }
        ?>
        <p>
            <label>
                <input type="checkbox" name="<?php echo $this->plugin_name . '-settings'; ?>[toggle-logged-in]" id="<?php echo $this->plugin_name . '-settings'; ?>[toggle-logged-in]" <?php checked($option, 1, true); ?> value="1" />
                <span></span>
            </label>
        </p>
        <?php
    }

    public function rml_save_text_callback() {

        $options = get_option($this->plugin_name . '-settings');
        $option = __('Save for Later', 'read-me-later');

        if (!empty($options['save-text'])) {
            $option = $options['save-text'];
        }
        ?>
        <p>
            <label>
                <input type="text" id="<?php echo $this->plugin_name; ?>-settings[save-text]" name="<?php echo $this->plugin_name; ?>-settings[save-text]" value="<?php echo esc_attr($option); ?>">
                <span></span>
            </label>
        </p>
        <?php
    }

    public function rml_unsave_text_callback() {

        $options = get_option($this->plugin_name . '-settings');
        $option = __('Remove', 'read-me-later');

        if (!empty($options['unsave-text'])) {
            $option = $options['unsave-text'];
        }
        ?>
        <p>
            <label>

                <input type="text" id="<?php echo $this->plugin_name; ?>-settings[unsave-text]" name="<?php echo $this->plugin_name; ?>-settings[unsave-text]" value="<?php echo esc_attr($option); ?>">
                <span></span>
            </label>
        </p>
        <?php
    }

    public function rml_saved_text_callback() {

        $options = get_option($this->plugin_name . '-settings');
        $option = __('See Saved', 'read-me-later');

        if (!empty($options['saved-text'])) {
            $option = $options['saved-text'];
        }
        ?>
        <p>
            <label>
                <input type="text" id="<?php echo $this->plugin_name; ?>-settings[saved-text]" name="<?php echo $this->plugin_name; ?>-settings[saved-text]" value="<?php echo esc_attr($option); ?>">
                <span></span>
            </label>
        </p>
        <?php
    }

    public function rml_number_text_callback() {

        $options = get_option($this->plugin_name . '-settings');
        $option = __('Saved: ', 'read-me-later');

        if (!empty($options['number-text'])) {
            $option = $options['number-text'];
        }
        ?>
        <p>
            <label>
                <input type="text" id="<?php echo $this->plugin_name; ?>-settings[number-text]" name="<?php echo $this->plugin_name; ?>-settings[number-text]" value="<?php echo esc_attr($option); ?>">
                <span></span>
            </label>
        </p>
        <?php
    }

    public function rml_remove_all_text_callback() {

        $options = get_option($this->plugin_name . '-settings');
        $option = __('Remove All', 'read-me-later');

        if (!empty($options['remove-all-text'])) {
            $option = $options['remove-all-text'];
        }
        ?>
        <p>
            <label>
                <input type="text" id="<?php echo $this->plugin_name; ?>-settings[remove-all-text]" name="<?php echo $this->plugin_name; ?>-settings[remove-all-text]" value="<?php echo esc_attr($option); ?>">
                <span></span>
            </label>
        </p>
        <?php
    }

    public function rml_no_content_text_callback() {

        $options = get_option($this->plugin_name . '-settings');
        $option = __('You don’t have any saved content.', 'read-me-later');

        if (!empty($options['no-content-text'])) {
            $option = $options['no-content-text'];
        }
        ?>
        <p>
            <label>
                <input type="text" id="<?php echo $this->plugin_name; ?>-settings[no-content-text]" name="<?php echo $this->plugin_name; ?>-settings[no-content-text]" value="<?php echo esc_attr($option); ?>">
                <span></span>
            </label>
        </p>
        <?php
    }

    public function demo_radio_display() {
        ?>
        <input type="radio" name="demo-radio" value="1" <?php checked(1, get_option('demo-radio'), true); ?>>1
        <input type="radio" name="demo-radio" value="2" <?php checked(2, get_option('demo-radio'), true); ?>>2
        <?php
    }

         //Display Section callbacks
    public function rml_icon_override_callback() {
        $options = get_option($this->plugin_name . '-display-settings');

        $option = 0;


        if (!empty($options['read-me-later-icon'])) {
            $option = $options['read-me-later-icon'];
        }
        ?>
        <ul class="read-me-later-icon">
            <li>

                <label>
                    <input type="radio" name="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" id="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]"value="1" <?php checked(1, $option, true); ?>>


                    <span><img src="<?php echo plugin_dir_url(__FILE__) . 'img/1.svg' ?>"/></span>
                </label>

            </li>
            <li>

                <label>

                    <input type="radio" name="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" id="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" value="2" <?php checked(2, $option, true); ?>>

                    <span><img src="<?php echo plugin_dir_url(__FILE__) . 'img/2.svg' ?>"/></span>
                </label>
            </li>
            <li>

                <label>

                    <input type="radio" name="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" id="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" value="3" <?php checked(3, $option, true); ?>>

                    <span><img src="<?php echo plugin_dir_url(__FILE__) . 'img/3.svg' ?>"/></span>
                </label>
            </li>
            <li>

                <label>

                    <input type="radio" name="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" id="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" value="4" <?php checked(4, $option, true); ?>>

                    <span><img src="<?php echo plugin_dir_url(__FILE__) . 'img/4.svg' ?>"/></span>
                </label>
            </li>
            <li>

                <label>

                    <input type="radio" name="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" id="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" value="5" <?php checked(5, $option, true); ?>>

                    <span><img src="<?php echo plugin_dir_url(__FILE__) . 'img/5.svg' ?>" style="width:120px"/></span>
                </label>
            </li>
            <li>

                <label>

                    <input type="radio" name="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" id="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" value="6" <?php checked(6, $option, true); ?>>

                    <span><img src="<?php echo plugin_dir_url(__FILE__) . 'img/6.png' ?>"/></span>
                </label>
            </li>
            <li>

                <label>

                    <input type="radio" name="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" id="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" value="7" <?php checked(7, $option, true); ?>>

                    <span><img src="<?php echo plugin_dir_url(__FILE__) . 'img/7.png' ?>"/></span>
                </label>
            </li>
            <li>

                <label>

                    <input type="radio" name="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" id="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" value="8" <?php checked(8, $option, true); ?>>

                    <span><img src="<?php echo plugin_dir_url(__FILE__) . 'img/8.png' ?>"/></span>
                </label>
            </li>
            <li>

                <label>

                    <input type="radio" name="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" id="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" value="9" <?php checked(9, $option, true); ?>>

                    <span><img src="<?php echo plugin_dir_url(__FILE__) . 'img/9.png' ?>"/></span>
                </label>
            </li>
            <li>

                <label>

                    <input type="radio" name="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" id="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" value="10" <?php checked(10, $option, true); ?>>

                    <span><img src="<?php echo plugin_dir_url(__FILE__) . 'img/10.png' ?>"/></span>
                </label>
            </li>
           <li>

                <label>

                    <input type="radio" name="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" id="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" value="11" <?php checked(11, $option, true); ?>>

                    <span><img src="<?php echo plugin_dir_url(__FILE__) . 'img/11.png' ?>"/></span>
                </label>
            </li>
            <li>

                <label>

                    <input type="radio" name="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" id="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" value="12" <?php checked(12, $option, true); ?>>

                    <span><img src="<?php echo plugin_dir_url(__FILE__) . 'img/12.png' ?>"/></span>
                </label>
            </li>
            <li>

                <label>

                    <input type="radio" name="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" id="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" value="13" <?php checked(13, $option, true); ?>>

                    <span><img src="<?php echo plugin_dir_url(__FILE__) . 'img/13.png' ?>"/></span>
                </label>
            </li>
            <li>

                <label>

                    <input type="radio" name="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" id="<?php echo $this->plugin_name . '-display-settings'; ?>[read-me-later-icon]" value="14" <?php checked(14, $option, true); ?>>

                    <span><img src="<?php echo plugin_dir_url(__FILE__) . 'img/14.png' ?>"/></span>
                </label>
            </li>
            

        </ul>

        <?php
    }


    /**
     * Display a thank you nag when the plugin has been installed/upgraded.
     */
    public  static function rml_admin_notice() { 
        if ( !current_user_can('install_plugins') ) return;


    $install_date = get_option( 'rml_installdate' );
    $display_date = date( 'Y-m-d h:i:s' );

    $datetime1 = new DateTime( $install_date );
    $datetime2 = new DateTime( $display_date );
    $diff_intrval = round( ($datetime2->format( 'U' ) - $datetime1->format( 'U' )) / (60 * 60 * 24) );
        if ( $diff_intrval >= 7 && get_option( 'rml_supported' ) != "yes"  ) {
                
        
            $html = sprintf(
                        '<div class="row">
                        <div class="col s12 update-nag rml_msg ">
                        <p>%s<b>%s</b>%s 
                        %s<b>%s</b>%s
                        %s
                        %s</p>
                       ~Rizwan Akhtar (@rizwanakhtar2727)<br><br>
                       <div class="rml_support_btns">
                    <a href="https://wordpress.org/support/plugin/read-me-later/reviews/?filter=5#new-post" class="rml_review button button-primary" target="_blank">
                        %s  
                    </a>
                    <a href="javascript:void(0);" class="rml_HideRating button" >
                    %s  
                    </a>
                    <br><br>
                    <a href="javascript:void(0);" class="rml_HideRating" >
                    %s  
                    </a>
                        </div>
                        </div> </div>',
                        __( 'Awesome, you have been using ', 'read-me-later' ),
                        __( 'Read Me Later ', 'read-me-later' ),
                        __( 'for more than 1 week.', 'read-me-later' ),
                         __( 'May I ask you to give it a ', 'read-me-later' ),
                         __( '5-star ', 'read-me-later' ), 
                         __( 'rating on Wordpress? ', 'read-me-later' ), 
                         __( 'This will help to spread its popularity and to make this plugin a better one.', 'read-me-later' ),
                        __( 'Your help is much appreciated. Thank you very much. ', 'read-me-later' ),
                        __( 'I Like Read Me Later - It increased engagement on my site', 'read-me-later' ),
                         __( 'I already rated it', 'read-me-later' ),
                        __( 'No, not good enough, I do not like to rate it', 'read-me-later' )
                  
                    );
            $script = ' <script>
                jQuery( document ).ready(function( $ ) {

                jQuery(\'.rml_review\').click(function(){
                    
                   var data={\'action\':\'rml_save_ratings\'}
                         jQuery.ajax({
                    
                    url: "' . admin_url( 'admin-ajax.php' ) . '",
                    type: "post",
                    data: data,
                    dataType: "json",
                    async: !0,
                    success: function(e ) {
                        
                        if (e=="success") {
                            jQuery(\'.rml_msg\').slideUp(\'fast\');
                           
                        }
                    }
                     });
                    });

                    jQuery(\'.rml_HideRating\').click(function(){
                        jQuery(\'.rml_msg\').slideUp(\'fast\');
                        });
                
                });
    </script>';
        echo $html . $script;   
        }   
        
    }


        //add opiton on making a reiew

        function rml_save_ratings(){
            if(get_option('rml_supported') !='yes'){
             add_option('rml_supported','yes');
         }
            
            }
}

