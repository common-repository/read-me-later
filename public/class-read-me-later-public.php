<?php

/**
 * The public-facing functionality of the plugin.
 
 */
class RML_Save_For_Later_Public {
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Get Toggled Logged in Setting
	 */
	public function get_toggle_logged_in() {

		$options = get_option( $this->plugin_name . '-settings' );
		if ( ! empty( $options['toggle-logged-in'] ) ) {
			$toggle_logged_in = $options['toggle-logged-in'];
		} else {
			$toggle_logged_in = 0;
		}

		return $toggle_logged_in;

	}

	/**
	 * Get cookie value
         * we  will convert saved cookie 
	 */
	public function get_cookie() {

		if ( array_key_exists( 'rml_save_for_later', $_COOKIE ) ) {
			if ( isset( $_COOKIE[ 'rml_save_for_later' ] ) ) {
				return json_decode( base64_decode( stripslashes( $_COOKIE[ 'rml_save_for_later' ] ) ), true );
			}
			return array();
		}

		return array();

	}

	/**
	 * Set cookie value
         * endcode the cookie and save as json
	 */
	public function set_cookie( $value ) {
		return base64_encode( json_encode( stripslashes_deep( $value ) ) );

	}

	/**
	 * Get number of saved items
	 */
	public function get_number_of_saved_items() {

		if ( is_user_logged_in() ) {
			$items = get_user_meta( get_current_user_id(), 'rml_saved_for_later', true );
			if ( empty( $items ) ) {
				$items = array();
			}
			$count = count( $items );
		} else {
			if ( $this->get_toggle_logged_in() == 1 ) {
				$count = 0;
			} else {
				$cookie_values = $this->get_cookie();

				$count = count( $cookie_values );
			}
		}

		return $count;

	}

	/**
	 * Register the stylesheets for front end display.
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 */
		$options = get_option( $this->plugin_name . '-settings' );

		
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/read-me-later-public.css', array(), $this->version, 'all' );
		

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 */

		$page_id = get_option( 'rml_save_for_later_page_id' );
		$page_link = get_permalink( $page_id );

		$options = get_option( $this->plugin_name . '-settings' );
		$save = __( 'Read Me Later', 'read-me-later' );
		$unsave = __( 'Remove', 'read-me-later' );
		$saved = __( 'Read Saved', 'read-me-later' );
		$number = __( 'Saved: ', 'read-me-later' );

		if ( ! empty( $options['save-text'] ) ) {
			$save = $options['save-text'];
		}
		if ( ! empty( $options['unsave-text'] ) ) {
			$unsave = $options['unsave-text'];
		}
		if ( ! empty( $options['saved-text'] ) ) {
			$saved = $options['saved-text'];
		}
		if ( ! empty( $options['number-text'] ) ) {
			$number = $options['number-text'];
		}

		// Saved objects
		if ( is_user_logged_in() ) {
			$is_user_logged_in = true;
		} else {
			$is_user_logged_in = false;
		}

		$toggle_logged_in = $this->get_toggle_logged_in();

		wp_enqueue_script( $this->plugin_name . 'js-cookie', plugin_dir_url( __FILE__ ) . 'js/js.cookie.js', array( 'jquery' ), '2.1.4', false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/read-me-later-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name,
			'rml_save_for_later_ajax',
			array(
				'ajax_url'          => admin_url( 'admin-ajax.php', 'relative' ),
				'save_txt'          => $save,
				'unsave_txt'        => $unsave,
				'saved_txt'         => $saved,
				'number_txt'        => $number,
				'saved_page_url'    => esc_url( $page_link ),
				'is_user_logged_in' => $is_user_logged_in,
				'toggle_logged_in'  => $toggle_logged_in
			)
		);

	}

	/**
	 * Set nocache constants and headers.
	 */
	private static function nocache() {
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( "DONOTCACHEPAGE", true );
		}
		if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
			define( "DONOTCACHEOBJECT", true );
		}
		if ( ! defined( 'DONOTCACHEDB' ) ) {
			define( "DONOTCACHEDB", true );
		}
		nocache_headers();
	}

	/**
	 * Add HTML to the end of the page.
	 */
	public function add_saved_items_to_footer() {

		$options = get_option( $this->plugin_name . '-settings' );

		$page_id = get_option( 'rml_save_for_later_page_id' );
		$page_link = get_permalink( $page_id );

		$saved_txt = __( 'Read Saved', 'read-me-later' );

		if ( ! empty( $options['saved-text'] ) ) {
			$saved_txt = $options['saved-text'];
		}

		// Saved objects
		$count = $this->get_number_of_saved_items();
		$toggle_logged_in = $this->get_toggle_logged_in();
                $display_option = get_option($this->plugin_name. '-display-settings');
                if(!empty($display_option['read-me-later-icon'])){
                   if($display_option['read-me-later-icon'] <= 5){
                       $ext = '.svg';
                       
                   }else{
                       $ext = '.png';
                   }
                $icon_src =  plugins_url('read-me-later') . '/admin/img/'.$display_option['read-me-later-icon'].$ext;
                }

		if ( $toggle_logged_in == 1 && is_user_logged_in() || $toggle_logged_in == 0 ) {

			if($display_option['read-me-later-icon'] == 5){
				echo '<a href="' . esc_url( $page_link ) . '" class="rml-saved-trigger button empty" data-toggle="tooltip" data-placement="top" data-title="' . esc_attr( $saved_txt ) . '">'
                                . '<img src="'.$icon_src.'" style="width:170px; left:0px"><span class="rml-count">' . esc_html( $count ) . '</span></a>';
			}else{

			echo '<a href="' . esc_url( $page_link ) . '" class="rml-saved-trigger  empty" data-toggle="tooltip" data-placement="top" data-title="' . esc_attr( $saved_txt ) . '">'
								. '<img src="'.$icon_src.'" style="max-width:100px;"><span class="rml-count">' . esc_html( $count ) . '</span></a>';
			}
		}

	}

	/**
	 * Override 'the_content'.
	 */
	public function override_content( $content ) {

		$options = get_option( $this->plugin_name . '-settings' );
		$post_types = array();
		$toggle = 0;

		if ( ! empty( $options['post-type'] ) ) {
			$post_types = $options['post-type'];
		}
		if ( ! empty( $options['toggle-content-override'] ) ) {
			$toggle = $options['toggle-content-override'];
		}
                if ( ! empty( $options['toggle-title-override'] ) ) {
			$toggle2 = $options['toggle-title-override'];
		}else{
                    $toggle2 =0;
                }

		$toggle_logged_in = $this->get_toggle_logged_in();

		$page_id = get_option( 'rml_save_for_later_page_id' );

		if ( $toggle == 1 && ! empty( $post_types ) && is_singular() && ! is_page( $page_id ) ) {
			$post_id = get_queried_object_id();
			foreach ( $post_types as $post_type ) {
				$current_post_type = get_post_type( $post_id );
				if ( $current_post_type == $post_type ) {
					$custom_content = '';
					ob_start();
					echo $this->get_save_for_later_button_display();
					$custom_content .= ob_get_contents();
					ob_end_clean();
					$content = $content . $custom_content;
				}
			}
		}
                if ( $toggle2 == 1 && ! empty( $post_types ) && is_singular() && ! is_page( $page_id ) ) {
			$post_id = get_queried_object_id();
			foreach ( $post_types as $post_type ) {
				$current_post_type = get_post_type( $post_id );
				if ( $current_post_type == $post_type ) {
					$custom_content = '';
					ob_start();
					echo $this->get_save_for_later_button_display();
					$custom_content .= ob_get_contents();
					ob_end_clean();
					$content = $custom_content. $content;
				}
			}
		}

		return $content;

	}
        
        

	/**
	 * Save/Unsave for Later
	 */
	public function save_unsave_for_later() {

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'rml_object_save_for_later' ) ) {
			die;
		}

		$count = $this->get_number_of_saved_items();

		$no_content = __( 'You don’t have any saved content.', 'read-me-later' );
		if ( ! empty( $options['no-content-text'] ) ) {
			$no_content = $options['no-content-text'];
		}

		$toggle_logged_in = $this->get_toggle_logged_in();

		// Object ID
		$object_id = isset( $_REQUEST['object_id'] ) ? intval( $_REQUEST['object_id'] ) : 0;

		// Check cookie if object is saved
		$saved = false;

		// Cookies
		$cookie_values = $this->get_cookie();

		if ( is_user_logged_in() ) {
			$matches = get_user_meta( get_current_user_id(), 'rml_saved_for_later', true );
			if ( empty( $matches ) ) {
				$matches = array();
			}
			if ( in_array( $object_id, $matches ) ) {
				$saved = true;
				unset( $matches[array_search( $object_id, $matches )] );
			} else {
				$saved = false;
				array_push( $matches, $object_id );
			}
			update_user_meta( get_current_user_id(), 'rml_saved_for_later', $matches );
		} else {
			if ( in_array( $object_id, $cookie_values ) ) {
				$saved = true;
				unset( $cookie_values[array_search( $object_id, $cookie_values )] );
				$cookie_values_js = $this->set_cookie( $cookie_values );
			} else {
				$saved = false;
				array_push( $cookie_values, $object_id );
				$cookie_values_js = $this->set_cookie( $cookie_values );
			}
		}

		if ( $saved == true ) {
			$count = $count - 1;
		} else {
			$count = $count + 1;
		}

		$return = array(
			'status'  => is_user_logged_in(),
			'update'  => $saved,
			'message' => esc_attr( $no_content ),
			'count'   => esc_attr( $count )
		);

		if ( ! is_user_logged_in() ) {
			$return['cookie'] = $cookie_values_js;
		}

		return wp_send_json( $return );

	}

	/**
	 * Save for Later button.
	 */
	public function get_save_for_later_button_display() {

		self::nocache();

		// Object ID
		$object_id = get_queried_object_id();

		$page_id = get_option( 'rml_save_for_later_page_id' );
		$page_link = get_permalink( $page_id );

		$toggle_logged_in = $this->get_toggle_logged_in();

		// Check cookie if object is saved
		$saved = false;

		if ( is_user_logged_in() ) {
			$matches = get_user_meta( get_current_user_id(), 'rml_saved_for_later', true );
			if ( empty( $matches ) ) {
				$matches = array();
			}
			if ( in_array( $object_id, $matches ) ) {
				$saved = true;
			} else {
				$saved = false;
			}
		} else {
			$cookie_values = $this->get_cookie();
			if ( in_array( $object_id, $cookie_values ) ) {
				$saved = true;
			} else {
				$saved = false;
			}
		}

		// Get tooltip text
		$options = get_option( $this->plugin_name . '-settings' );
		$save = __( 'Read Me Later', 'read-me-later' );
		$unsave = __( 'Remove', 'read-me-later' );
		$saved_txt = __( 'Read Saved', 'read-me-later' );
		$number = __( 'Saved: ', 'read-me-later' );

		if ( ! empty( $options['save-text'] ) ) {
			$save = $options['save-text'];
		}
		if ( ! empty( $options['unsave-text'] ) ) {
			$unsave = $options['unsave-text'];
		}
		if ( ! empty( $options['saved-text'] ) ) {
			$saved_txt = $options['saved-text'];
		}
		if ( ! empty( $options['number-text'] ) ) {
			$number = $options['number-text'];
		}
                
                $display_option = get_option($this->plugin_name. '-display-settings');
                if(!empty($display_option['read-me-later-icon'])){
                   if($display_option['read-me-later-icon'] <= 5){
                       $ext = '.svg';
                       
                   }else{
                       $ext = '.png';
                   }
                $icon_src =  plugins_url('read-me-later') . '/admin/img/'.$display_option['read-me-later-icon'].$ext;
                }
                
		// Saved objects
		$count = $this->get_number_of_saved_items();

		if ( $toggle_logged_in == 1 && is_user_logged_in() || $toggle_logged_in == 0 ) {
			if ( $saved == true ) {
				if($display_option['read-me-later-icon'] == 5){
					return '<a href="#" class="rml-save-for-later-button saved" data-toggle="tooltip" data-placement="top" data-title="' . esc_attr( $unsave ) . '" data-nonce="' . wp_create_nonce( 'rml_object_save_for_later' ) . '" data-object-id="' . esc_attr( $object_id ) . '"><img src="'. $icon_src.'" style="width:130px; height:50px"/></a><a href="' . esc_url( $page_link ) . '" class="rml-see-saved" data-toggle="tooltip" data-placement="top" data-title="' . esc_attr( $number ) . ' ' . esc_attr( $count ) . '">' . esc_html( $saved_txt ) . '</a>';
				}else{
				return '<a href="#" class="rml-save-for-later-button saved" data-toggle="tooltip" data-placement="top" data-title="' . esc_attr( $unsave ) . '" data-nonce="' . wp_create_nonce( 'rml_object_save_for_later' ) . '" data-object-id="' . esc_attr( $object_id ) . '"><img src="'. $icon_src.'" style="max-width:100px; max-height:100px"/></a><a href="' . esc_url( $page_link ) . '" class="rml-see-saved" data-toggle="tooltip" data-placement="top" data-title="' . esc_attr( $number ) . ' ' . esc_attr( $count ) . '">' . esc_html( $saved_txt ) . '</a>';
				}
			} else {
				if($display_option['read-me-later-icon'] == 5){
					return '<a href="#" class="rml-save-for-later-button" data-toggle="tooltip" data-placement="top" data-title="' . esc_attr( $save ) . '" data-nonce="' . wp_create_nonce( 'rml_object_save_for_later' ) . '" data-object-id="' . esc_attr( $object_id ) . '"><img src="'.$icon_src.'" style="width:130px; height:50px"/></a>';
				}else{
				return '<a href="#" class="rml-save-for-later-button" data-toggle="tooltip" data-placement="top" data-title="' . esc_attr( $save ) . '" data-nonce="' . wp_create_nonce( 'rml_object_save_for_later' ) . '" data-object-id="' . esc_attr( $object_id ) . '"><img src="'.$icon_src.'" style="max-width:100px; height:100px"/></a>';
				}
			}
		} elseif ( $toggle_logged_in == 1 && ! is_user_logged_in() ) {
			$login_url = wp_login_url( get_permalink() );
			$register_url = wp_registration_url();
			$return = sprintf( __( '%1$sLog in%2$s or %3$sRegister%4$s to save this content for later.', 'read-me-later' ), '<a href="' . esc_url( $login_url ) . '">', '</a>', '<a href="' . esc_url( $register_url ) . '">', '</a>' );
			return apply_filters( 'rml_save_for_later_message', $return );
		}

	}

	/**
	 * Create Save for Later shortcode.
	 */
	public function save_for_later_shortcode() {    
		return $this->get_save_for_later_button_display();
	}
	/**
	 * Create Saved shortcode.
	 */
	public function saved_for_later_shortcode() {

		// Get tooltip text
		$options = get_option( $this->plugin_name . '-settings' );
		$save = __( 'Read Me Later', 'read-me-later' );
		$unsave = __( 'Remove', 'read-me-later' );
		$remove_all = __( 'Remove All', 'read-me-later' );
		$no_content = __( 'You don’t have any saved content.', 'read-me-later' );

		if ( ! empty( $options['save-text'] ) ) {
			$save = $options['save-text'];
		}
		if ( ! empty( $options['unsave-text'] ) ) {
			$unsave = $options['unsave-text'];
		}
		if ( ! empty( $options['remove-all-text'] ) ) {
			$remove_all = $options['remove-all-text'];
		}
		if ( ! empty( $options['no-content-text'] ) ) {
			$no_content = $options['no-content-text'];
		}

		// Saved objects
		$matches = array();
		if ( is_user_logged_in() ) {
			$matches = get_user_meta( get_current_user_id(), 'rml_saved_for_later', true );
		} else {
			$cookie_values = $this->get_cookie();
			foreach ( $cookie_values as $key => $value ) {
				$matches[] = $value;
			}
		}

		// Post types
		$options = get_option( $this->plugin_name . '-settings' );
		$post_types = array();

		if ( ! empty( $options['post-type'] ) ) {
			$post_types = $options['post-type'];
		}

		if ( ! empty( $matches ) ) {
			$saved_args = array(
				'post_type'      => $post_types,
				'posts_per_page' => -1,
				'post__in'       => $matches
			);
			$saved_loop = new WP_Query( $saved_args );

			if ( $saved_loop->have_posts() ) {
				echo '<ul class="rml-saved-for-later">';
				while ( $saved_loop->have_posts() ) : $saved_loop->the_post();
					echo '<li class="rml-item-saved-for-later" id="rml-item-' . esc_attr( get_the_ID() ) . '">';
						echo '<div class="rml-item-content">';
							echo '<a href="' . esc_url( get_the_permalink() ) . '">';
								$html = '';
								if ( has_post_thumbnail() ) {
									$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail' );
									$html .= '<div class="rml-item-image" style="background-image: url(' . $thumbnail[0] . ');"></div>';
								}
								$html .= '<div class="rml-item-float">';
									$html .= '<div class="rml-item-title">' . get_the_title() . '</div>';
									$html .= '<div class="rml-item-date">' . get_the_date() . '</div>';
								$html .= '</div>';
								echo apply_filters( 'rml_saved_for_later_item_html', $html );
							echo '</a>';
							echo '<div class="rml-item-nav">';
             
								echo '<a href="#" class="rml-save-for-later-button saved saved-in-list" data-toggle="tooltip" data-placement="top" data-title="' . esc_attr( $unsave ) . '" data-nonce="' . wp_create_nonce( 'rml_object_save_for_later' ) . '" data-object-id="' . esc_attr( get_the_ID() ) . '"><img class="cross" src="'.plugins_url('read-me-later') . '/admin/img/cross.svg'.'"></a>';
							echo '</div>';
						echo '</div>';
					echo '</li>';
				endwhile; wp_reset_postdata();
				echo '</ul>';
				echo '<button class="rml-save-for-later-remove-all" data-nonce="' . wp_create_nonce( 'rml_save_for_later_remove_all' ) . '">' . esc_html( $remove_all ) . '</button>';
			}
		} else {
			echo '<p class="nothing-saved">' . esc_html( $no_content ) . '</p>';
		}

	}

	/**
	 * Remove All
	 */
	public function save_for_later_remove_all() {

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'rml_save_for_later_remove_all' ) ) {
			die;
		}

		$no_content = __( 'You don’t have any saved content.', 'read-me-later' );
		if ( ! empty( $options['no-content-text'] ) ) {
			$no_content = $options['no-content-text'];
		}

		if ( is_user_logged_in() ) {
			$saved_items = get_user_meta( get_current_user_id(), 'rml_saved_for_later', true );
			if ( ! empty( $saved_items ) ) {
				delete_user_meta( get_current_user_id(), 'rml_saved_for_later' );
			}
			$return = array(
				'user_type' => 'logged_in',
				'message'   => esc_attr( $no_content )
			);
		} else {
			$cookie = $this->set_cookie( array() );
			$return = array(
				'user_type' => 'not_logged_in',
				'cookie'    => $cookie,
				'message'   => esc_attr( $no_content )
			);
		}

		return wp_send_json( $return );

	}
        

}

