<?php
/**
 * Plugin Name: Tweets from the map 2
 * Plugin URI:
 * Description: This plugin allows to get tweets, depending on the specified coordinates and themes, and post them on the site.
 * Version:  1.0
 * Author: Stacey
 * Author URI:
*/

if ( ! class_exists( 'Tweets_From_The_Map' ) ) :
    final class Tweets_From_The_Map{

        /**
         * @var null
         */
        protected static $_instance = null;

        /**
         * Settings Map instance.
         *
         * @var TFTM_Settings_Map
         */
        public $settings_map = null;

        /**
         * Settings Api instance.
         *
         * @var TFTM_Settings_Api
         */
        public $settings_api = null;

        /**
         * List Tweets instance.
         *
         * @var TFTM_List_Tweets
         */
        public $list_tweets = null;

        /**
         * TweetsFromTheMap constructor.
         */
        public function __construct() {
            $this->define_constants();
            $this->includes();
            spl_autoload_register( array( $this, 'loader' ) );
            $this->init_hooks();

            do_action( 'tweets_from_the_map_loaded' );
        }

        /**
         * Include required core files used in admin and on the frontend.
         */
        public function includes() {
            include_once(TFTM_ABSPATH . DIRECTORY_SEPARATOR . 'twitteroauth' . DIRECTORY_SEPARATOR . 'autoload.php');
        }

        /**
         * Auto Include required core files used in admin and on the frontend.
         */
        public function loader( $class_name ) {
            if ( false !== strpos( $class_name, 'TFTM' ) ){
                spl_autoload_extensions( '.php' );
                set_include_path( TFTM_ABSPATH . TFTM_CLASS_DIR . DIRECTORY_SEPARATOR );
                $class_file = 'class-'.strtolower(str_replace( '_', '-', $class_name ) ) ;
                spl_autoload ( $class_file );
            }
        }

        /**
         * Installation options TweetsFromTheMap
         */
        public function install() {
            add_option( 'tweets_search','' );
            TFTM_BD_Queries::create_table_tweets();
            wp_schedule_event( time(), 'hourly', 'tftm_create_update' );
            file_put_contents(TFTM_ABSPATH.'error_activation.txt', ob_get_contents());
        }

        /**
         * Load scripts to frontend site
         */
        public function scripts_front() {
            wp_enqueue_script( 'tftm_front', plugin_dir_url(__FILE__) . 'assets' . DIRECTORY_SEPARATOR . TFTM_PLUGIN_BASENAME . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR .'tftm-front.js', array('jquery') );
        }

        /**
         * Load styles to frontend site
         */
        public function styles_front() {
            wp_enqueue_style('tftm_style_front', plugin_dir_url(__FILE__) . 'assets' . DIRECTORY_SEPARATOR . TFTM_PLUGIN_BASENAME . DIRECTORY_SEPARATOR .'css' . DIRECTORY_SEPARATOR . 'tftm-style-front.css');
        }

        /**
         * Load scripts to admin panel site
         */
        public function scripts_admin() {
            $google_api_settings = get_option('google_api_settings');

            wp_enqueue_script( 'tftm_map', plugin_dir_url(__FILE__) . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'tftm-settings-map.js', array( 'jquery', 'jquery-ui-slider', 'jquery-ui-dialog' ) );
            wp_enqueue_script( 'tftm_api', plugin_dir_url(__FILE__) . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'tftm-settings-api.js', array( 'jquery', 'jquery-ui-slider', 'jquery-ui-dialog' ) );
            wp_enqueue_script( 'tftm_google_script', 'https://maps.googleapis.com/maps/api/js?key='.$google_api_settings['api_key'].'&callback=initMap', array( 'tftm_map' ), '1.0', true );
            wp_enqueue_script( 'tftm_table', plugin_dir_url(__FILE__) . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'tftm-tweets-table.js', array( 'jquery', 'jquery-ui-slider', 'jquery-ui-dialog' ) );
            wp_enqueue_script( 'tftm_cookie', plugin_dir_url(__FILE__) . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'jquery.cookie.js', array( 'jquery', 'jquery-ui-slider', 'jquery-ui-dialog') );
        }

        /**
         * Load styles to admin panel site
         */
        public function styles_admin() {
            wp_enqueue_style( 'tftm_style_admin', plugin_dir_url(__FILE__) . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'tftm-style-admin.css' );
            wp_enqueue_style( 'stylesheet', 'https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css' );
        }

        /**
         *  Deactivation plugin
         */
        public function deactivation() {
            wp_clear_scheduled_hook( 'tftm_create_update' );
        }

        /**
         * Uninstall plugin
         */
        public function uninstall() {
            delete_option( 'tweets_maps_settings' );
            delete_option( 'twitter_api_settings' );
            delete_option( 'google_api_settings' );
            TFTM_BD_Queries::delete_table_tweets();
        }

        /**
         * @param $attr
         * @param string $title
         */
        public static function create_shortcode( $attr, $title = '' ) {
            $tweets_array = TFTM_BD_Queries::get_20_tweets();

            echo '<div id="shortcode_content" style="background-color:' . $attr["background-color-header"] . '; 
                                                     width:' . $attr["width"] . '; 
                                                     max-width:' . $attr["max-width"] . '; 
                                                     border-color:' . $attr["border-color"] . '">
            <div id="tweet_img" class="dashicons dashicons-twitter">'.$title.'</div>';
            for($i = 0; $i < count($tweets_array); $i++) {
                echo '<div class="tweet_content" style="background-color:'.$attr["background-color-content"] . '">
                        <div class="tweet_autor">' . $tweets_array[ $i ]['tweet_author_name'] . '</div>
                        <div class="tweet_text">' . $tweets_array[ $i ]['tweet_text'] . '</div>
                      </div>';
            }
            echo '</div>';
        }

        /**
         *  * Main TweetsFromTheMap Instance.
         *
         * @return TweetsFromTheMap - Main instance.
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Hook into actions and filters.
         */
        private function init_hooks() {
            register_activation_hook( __FILE__, array( $this, 'install' ) );
            add_action( 'init', array( $this, 'init' ), 0 );
            add_action( 'init', array($this, 'load_textdomain'), 1);
            add_action( 'wp_enqueue_scripts', array( $this, 'styles_front' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'scripts_front' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'styles_admin' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'scripts_admin' ) );
            add_shortcode( 'tweets_shortcode', array( $this, 'create_shortcode') );
            add_action( 'tftm_create_update', array('TFTM_Cron_Script', 'tweets_cron') );
            register_deactivation_hook( __FILE__, array( $this, 'deactivation') );
            register_uninstall_hook( __FILE__, array( $this, 'uninstall') );
        }

        /**
         * Init "Tweets from the map" when WordPress Initialises. Load class instances.
         */
        public function init() {
            $this->settings_map = new TFTM_Settings_Map(); // Settings Map to create page for set Google map and search tweets settings
            $this->settings_api = new TFTM_Settings_Api(); // Settings Api to create page for set  Google and Twitter api settings
            $this->list_tweets = new TFTM_List_Tweets(); // List Tweets to create page with settings for table tweets with and get tweets from Twitter
        }

        /**
         * Load Localisation files.
         *
         * Note: the first-loaded translation file overrides any following ones if the same translation is present.
         *
         * Locales found in:
         *      - WP_LANG_DIR/tweets-from-the-map/tftm-LOCALE.mo
         *      - WP_LANG_DIR/plugins/tftm-LOCALE.mo
         */
        public function load_textdomain() {
            $locale = apply_filters( 'plugin_locale', get_locale(), 'tftm' );
            $mofile = "tftm"."-" . $locale . ".mo";

            //paths to local (plugin) and global (WP) language files
            $mofile_local  = TFTM_ABSPATH . 'languages/' . $mofile;
            $mofile_global = WP_LANG_DIR . '/' . TFTM_PLUGIN_BASENAME . '/' . $mofile;

            //load global first
            load_textdomain( 'tftm', $mofile_global);

            //load local second
            load_textdomain( 'tftm', $mofile_local );
        }

        /**
         * Define TweetsFromTheMap Constants.
         */
        private function define_constants() {
            $this->define( 'TFTM_ABSPATH', plugin_dir_path( __FILE__ ) );
            $this->define( 'TFTM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
            $this->define( 'TFTM_CLASS_DIR', 'includes' );
        }

        /**
         * Define constant if not already set.
         *
         * @param  string $name
         * @param  string|bool $value
         */
        private function define( $name, $value ) {
            if ( ! defined( $name ) ) {
                define( $name, $value );
            }
        }
    }
endif;
/**
 * @return TweetsFromTheMap
 */
function TFTM() {
    return Tweets_From_The_Map::instance();
}
// Global for backwards compatibility.
$GLOBALS['tweets-from-the-map'] = TFTM();
?>