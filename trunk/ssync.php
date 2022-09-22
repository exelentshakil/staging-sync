<?php 
/**
 * Plugin Name:       Staging Sync
 * Plugin URI:        https://stagingsync.com/
 * Description:       Custom plugin crafted that allows you to sync 2 sites..
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            jlouis
 * Author URI:        https://jlouis.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ssync
 * Domain Path:       /languages
 */

namespace SSync;

require_once( plugin_dir_path(__FILE__).'includes/admin.php'); 
require_once( plugin_dir_path(__FILE__).'includes/endpoints.php');

// Don't call this file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

 class SSyncPlugin{

	private $plugin_url;
	private $plugin_path;
	private $version;
	
	public $settings;
 

 	public function __construct(){  


		$this->plugin_url  = trailingslashit( plugins_url('', __FILE__ ) );
		$this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
		$this->version     = 1.0;	
		 
		new SSync_Admin([ 'plugin_url'=> $this->plugin_url ]);
		new SSync_Endpoints([ 'plugin_url'=> $this->plugin_url ]);
		$this->actions();
		$this->define_constants();

 	}

 	private function actions(){   

		add_action( 'admin_init', [$this,'redirect_if_activated']);
		add_action('plugins_loaded', [$this, 'load_textdomain']);
		add_action('init', array($this, 'add_cors_http_header'));
        add_action('init', array($this, 'do_output_buffer'));
        add_action('plugins_loaded', array($this, 'do_output_buffer'));
        add_action( 'admin_enqueue_scripts', [$this, 'common_scripts'] );
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );


 	}

     public function common_scripts() {
         wp_enqueue_style( 'main' );
         wp_enqueue_script( 'jquery' );
		 wp_enqueue_script( 'ajax-handler' );
     }

     public function enqueue_scripts() {
         $styles  = $this->get_styles();
         $scripts = $this->get_scripts();

         $this->register_styles( $styles );
         $this->register_scripts( $scripts );
     }

     public function register_styles( $styles ) {

         foreach ( $styles as $handle => $style ) {
             $deps    = isset( $style['deps'] ) ? $style['deps'] : [];
             $version = isset( $style['version'] ) ? $style['version'] : '1.0';
             wp_register_style( $handle, $style['src'], $deps, $version );
         }

     }

     public function register_scripts( $scripts ) {

         foreach ( $scripts as $handle => $script ) {
             $deps    = isset( $script['deps'] ) ? $script['deps'] : [];
             $version = isset( $script['version'] ) ? $script['version'] : '1.0';

             wp_register_script( $handle, $script['src'], $deps, $version, true );
         }

     }

     public function get_styles() {

         return [
             'main'             => [
                 'src'     => SSYNC_ASSETS . '/css/main.css',
                 'version' => filemtime( SSYNC_PATH . '/assets/css/main.css' ),
                 'deps'    => [],
             ],
         ];

     }

     public function get_scripts() {

         return [
             'ajax-handler'              => [
                 'src'     => SSYNC_ASSETS . '/js/ajax-handler.js',
                 'version'     => filemtime( SSYNC_PATH . '/assets/js/ajax-handler.js' ),
                 'deps'    => ['jquery'],
             ],
         ];

     }

    public function define_constants() {
        define( 'SSYNC_VERSION', $this->version );
        define( 'SSYNC_FILE', __FILE__ );
        define( 'SSYNC_PATH', dirname( SSYNC_FILE ) );
        define( 'SSYNC_URL', plugins_url( '', SSYNC_FILE ) );
        define( 'SSYNC_ASSETS', SSYNC_URL . '/assets' );
    }

     public function update_version() {

         $installed = get_option( 'ssync_installed' );

         if ( $installed ) {
             update_option( 'ssync_installed', time() );
         }

         update_option( 'ssync_version', RIS_AUCTION_VERSION );

     }

    public function do_output_buffer() {
         ob_start();
     }
	public function add_cors_http_header(){
		header("Access-Control-Allow-Origin: *");
	}

 	public function activation_flag(){ 

    	add_option('tss_redirect', true);

 	}

	public function redirect_if_activated(){ 

		if (get_option('tss_redirect', false)) {
	        delete_option('tss_redirect');
	        if(!isset($_GET['activate-multi'])) {
	            wp_redirect("admin.php?page=ssync_settings");
	        }
	    }

	} 

	function load_textdomain(){

		$plugin_rel_path = basename( dirname( __FILE__ ) ) . '/languages'; /* Relative to WP_PLUGIN_DIR */
    	load_plugin_textdomain( 'tss', false, $plugin_rel_path );

	}


 }

$ssync = new SSyncPlugin();
register_activation_hook(__FILE__, [$ssync,'activation_flag']);