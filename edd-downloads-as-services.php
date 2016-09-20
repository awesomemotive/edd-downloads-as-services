<?php
/*
Plugin Name: Easy Digital Downloads - Downloads As Services
Plugin URI: 
Description: Define downloads as "services". Services will not show "no downloadable files found" on the purchase confirmation page, nor will they show a dash in the purchase receipt email
Version: 1.0.4
Author: Andrew Munro, Sumobi
Author URI: http://sumobi.com/
License: GPL-2.0+
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Downloads_As_Services' ) ) {

	class EDD_Downloads_As_Services {

		private static $instance;

		/**
		 * Main Instance
		 *
		 * Ensures that only one instance exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 *
		 */
		public static function instance() {
			if ( ! isset ( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Start your engines
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		public function __construct() {
			$this->setup_globals();
			$this->setup_actions();
			$this->load_textdomain();
		}

		/**
		 * Globals
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function setup_globals() {
			// paths
			$this->file         = __FILE__;
			$this->basename     = apply_filters( 'edd_das_plugin_basenname', plugin_basename( $this->file ) );
			$this->plugin_dir   = apply_filters( 'edd_das_plugin_dir_path',  plugin_dir_path( $this->file ) );
			$this->plugin_url   = apply_filters( 'edd_das_plugin_dir_url',   plugin_dir_url ( $this->file ) );
		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function setup_actions() {
			global $edd_options;

			// metabox
			add_action( 'edd_meta_box_settings_fields', array( $this, 'add_metabox' ) );
			add_action( 'edd_metabox_fields_save', array( $this, 'save_metabox' ) );
			
			// settings
			add_filter( 'edd_settings_extensions', array( $this, 'settings' ) );

			// filter each download
			add_filter( 'edd_receipt_show_download_files', array( $this, 'receipt' ), 10, 2 );
			add_filter( 'edd_email_receipt_download_title', array( $this, 'email_receipt' ), 10, 3 );

			do_action( 'edd_das_setup_actions' );
		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.0
		 * @return void
		 */
		public function load_textdomain() {
			// Set filter for plugin's languages directory
			$lang_dir = dirname( plugin_basename( $this->file ) ) . '/languages/';
			$lang_dir = apply_filters( 'edd_das_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale        = apply_filters( 'plugin_locale',  get_locale(), 'edd-das' );
			$mofile        = sprintf( '%1$s-%2$s.mo', 'edd-das', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/edd-downloads-as-services/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				load_textdomain( 'edd-das', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				load_textdomain( 'edd-das', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'edd-das', false, $lang_dir );
			}
		}

		/**
		 * Add Metabox if per download email attachments are enabled
		 *
		 * @since 1.0
		*/
		public function add_metabox( $post_id ) {
			$checked = (boolean) get_post_meta( $post_id, '_edd_das_enabled', true );
		?>
			<p><strong><?php apply_filters( 'edd_das_header', printf( __( '%s As Service:', 'edd-das' ), edd_get_label_singular() ) ); ?></strong></p>
			<p>
				<label for="edd_download_as_service">
					<input type="checkbox" name="_edd_das_enabled" id="edd_download_as_service" value="1" <?php checked( true, $checked ); ?> />
					<?php apply_filters( 'edd_das_header_label', printf( __( 'This %s is a service', 'edd-das' ), strtolower( edd_get_label_singular() ) ) ); ?>
				</label>
			</p>
		<?php
		}

		/**
		 * Add to save function
		 * @param  $fields Array of fields
		 * @since 1.0
		 * @return array
		*/
		public function save_metabox( $fields ) {
			$fields[] = '_edd_das_enabled';

			return $fields;
		}

		/**
		 * Prevent receipt from listing download files
		 * @param $enabled default true
		 * @param int  $item_id ID of download
		 * @since 1.0
		 * @return boolean
		*/
		public function receipt( $enabled, $item_id ) {
			if ( $this->is_service( $item_id ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Modify email template to remove dash if the item is a service
		 *
		 * @since 1.0
		*/
		public function email_receipt( $title, $item_id, $price_id ) {
			if ( $this->is_service( $item_id ) ) {
				$title = get_the_title( $item_id );

				if( $price_id !== false ) {
					$title .= "&nbsp;" . edd_get_price_option_name( $item_id, $price_id );
				}
			}
			
			return $title;
		}

		/**
		 * Is service
		 * @param  int  $item_id ID of download
		 * @return boolean true if service, false otherwise
		 * @return boolean
		 */
		public function is_service( $item_id ) {
			global $edd_receipt_args, $edd_options;

			// get array of service categories
			$service_categories = isset( $edd_options['edd_das_service_categories'] ) ? $edd_options['edd_das_service_categories'] : '';
			
			$term_ids = array();

			if ( $service_categories ) {
				foreach ( $service_categories as $term_id => $term_name ) {
					$term_ids[] = $term_id;
				}
			}
			
			$is_service = get_post_meta( $item_id, '_edd_das_enabled', true );

			// get payment
			$payment   = get_post( $edd_receipt_args['id'] );
			$meta      = isset( $payment ) ? edd_get_payment_meta( $payment->ID ) : '';
			$cart      = isset( $payment ) ? edd_get_payment_meta_cart_details( $payment->ID, true ) : '';

			if ( $cart ) {
				foreach ( $cart as $key => $item ) {
					$price_id = edd_get_cart_item_price_id( $item );

					$download_files = edd_get_download_files( $item_id, $price_id );

					// if the service has a file attached, we still want to show it
					if ( $download_files )
						return;
				}
			} 

			// check if download has meta key or has a service term assigned to it
			if ( $is_service || has_term( $term_ids, 'download_category', $item_id ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Get terms
		 * @return array
		 * @since  1.0
		 */
		public function get_terms() {
			$args = array(
			  'hide_empty'     => false,
			  'hierarchical'	=> false
			);

			$terms = get_terms( 'download_category', apply_filters( 'edd_das_get_terms', $args ) );
			
			$terms_array = array();

			foreach ( $terms as $term ) {
				$term_id = $term->term_id;
				$term_name = $term->name;

				$terms_array[$term_id] = $term_name;
			}

			if ( $terms )
				return $terms_array;

			return false;
		}

		/**
		 * Settings
		 *
		 * @since 1.0
		*/
		public function settings( $settings ) {
		  $new_settings = array(
				array(
					'id' => 'edd_das_header',
					'name' => '<strong>' . __( 'Downloads As Services', 'edd-das' ) . '</strong>',
					'type' => 'header'
				),
				array(
					'id' => 'edd_das_service_categories',
					'name' => __( 'Select Service Categories', 'edd-das' ),
					'desc' => __( 'Select the categories that contain "services"', 'edd-das' ),
					'type' => 'multicheck',
					'options' => $this->get_terms()
				),
			);

			return array_merge( $settings, $new_settings );
		}
	}
}

/**
 * Get everything running
 *
 * @since 1.0
 *
 * @access private
 * @return void
 */
function edd_downloads_as_services() {
	$edd_downloads_as_services = new EDD_Downloads_As_Services();
}
add_action( 'plugins_loaded', 'edd_downloads_as_services' );