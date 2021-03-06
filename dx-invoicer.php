<?php
/**
 * Plugin Name: DX Invoicer
 * Description: Invoice manager for WordPress, includes user and invoice management, templating and exports
 * Version: 0.1
 * License: GPLv2
 * 
 */

// Defines
// ....


if( !class_exists( 'DX_Invoicer' ) ) {
	class DX_Invoicer {
		/**
		 * Constructor
		 */
		public function __construct() {
			$this->enqueue_scripts_styles();
			$this->include_files();
			$this->register_cpts();
			$this->prepare_hooks();
		}
		
		/**
		 * Include helper files for the plugin
		 */
		public function include_files() {
 			require_once 'inc/invoice.class.php';
 			require_once 'inc/customer.class.php';
 			require_once 'helpers/form-helper.php';
 			require_once 'helpers/form-filters.php';
 			
 			new DX_Invoice_Class();
 			new DX_Customer_Class();
		}
		
		/**
		 * Prepare scripts and styles, yo!
		 */
		public function enqueue_scripts_styles() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_styles' ) );
		}
		
		public function admin_enqueue_styles( $hook ) {
			wp_enqueue_script('jquery');
			
			if( $hook == 'post.php' || $hook == 'post-new.php' ) {
				wp_enqueue_style( 'dx-invoicer-post-screens', plugins_url( '/css/dx-invoicer-post-screens.css', __FILE__ ), array(), '1.0', 'screen' );
				
				// style for datepicker
				global $wp_scripts;
				wp_enqueue_script('jquery-ui-datepicker');
				$ui = $wp_scripts->query('jquery-ui-core');
				$url = "http://ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.css";
				wp_enqueue_style('jquery-ui-smoothness', $url, false, $ui->ver);
				
				wp_enqueue_script( 'dx-invoicer-post-screens', plugins_url( '/js/dx-invoicer-post-screens.js', __FILE__ ), array( 'jquery' ) );
				wp_enqueue_script( 'dx-invoicer-admin', plugins_url( '/js/dx-invoicer-admin.js', __FILE__ ), array( 'jquery' ) );
			} else if( $hook == 'dx-invoicer' ) { // TODO: is this a valid hook?
				wp_enqueue_style( 'dx-invoicer-admin', plugins_url( '/css/dx-invoicer-admin.css', __FILE__ ), array(), '1.0', 'screen' );
				wp_enqueue_script( 'dx-invoicer-admin', plugins_url( '/js/dx-invoicer-admin.js', __FILE__ ), array( 'jquery' ) );
			}
		}

		public function wp_enqueue_styles() {
			wp_enqueue_style( 'dx-invoicer', plugins_url( '/css/dx-invoicer.css',_FILE_), array(), '1.0', 'screen' );
		}
		
		/**
		 * Hook to existing actions and filters 
		 */
		public function prepare_hooks() {
			
		}
		
		public function register_cpts() {
			add_action( 'init', array( DX_Invoice_Class, 'register_invoice_cpt' ), 10 );
			add_action( 'init', array( DX_Customer_Class, 'register_customer_cpt' ), 10 );
			
			add_action( 'add_meta_boxes', array( DX_Invoice_Class, 'register_invoice_custom_meta' ), 12 );
			add_action( 'add_meta_boxes', array( DX_Customer_Class, 'register_customer_custom_meta' ), 12 );
			
			add_action( 'save_post', array( DX_Invoice_Class, 'save_invoice_post' ) );
			add_action( 'save_post', array( DX_Customer_Class, 'save_customer_post' ) );
		}
		
		public static function get_default_table_header_classes( $column_name ) {
			switch( $column_name ) {
				case 'number': return 'dx_invoice_number_field';
				case 'description': return 'dx_invoice_description_field';
				case 'rate': return 'dx_invoice_rate_field';
				case 'quantity': return 'dx_invoice_quantity_field';
				case 'net': return 'dx_invoice_net_field';
				case 'total': return 'dx_invoice_total_field';
				
				default: return apply_filters('dx_invoicer_default_column_class_name', '');
			}
		}
		
	}
	
	new DX_Invoicer();
}