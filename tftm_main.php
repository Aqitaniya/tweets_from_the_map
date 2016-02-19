<?php
/*
Plugin Name: Tweets from the map
Description: Create tweets from the map
Version:  1.0
Author: Stacey
*/


// ------------------------------------------------
// Enqueue styles and scripts
// ------------------------------------------------
///
function tftm_add_wp_enqueue_styles_scripts()
{
    wp_enqueue_style('tftm_style_front', plugin_dir_url('') . '/tweets_from_the_map/css/tftm_style_front.css');

    wp_enqueue_script('tftm_front', plugin_dir_url('') . '/tweets_from_the_map/js/front/tftm_front.js', array('jquery', 'jquery-ui-draggable', 'backbone','underscore'));

}
add_action('wp_enqueue_scripts', 'tftm_add_wp_enqueue_styles_scripts');


function tftm_add_admin_enqueue_styles_scripts()
{

    wp_enqueue_style('tftm_style_admin', plugin_dir_url('') . 'tweets_from_the_map/css/tftm_style_admin.css');
    wp_enqueue_style('stylesheet', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css');

    wp_enqueue_script('tftm_map', plugin_dir_url('') . '/tweets_from_the_map/js/admin/tftm_map.js', array('jquery','jquery-ui-slider','jquery-ui-dialog'));
    wp_enqueue_script('tftm_table', plugin_dir_url('') . '/tweets_from_the_map/js/admin/tftm_table.js', array('jquery','jquery-ui-slider','jquery-ui-dialog'));
    wp_enqueue_script('tftm_cookie', plugin_dir_url('') . '/tweets_from_the_map/js/admin/jquery-cookie-master/src/jquery.cookie.js', array('jquery','jquery-ui-slider','jquery-ui-dialog'));

}
add_action('admin_enqueue_scripts', 'tftm_add_admin_enqueue_styles_scripts');
// ------------------------------------------------
// Add files
// ------------------------------------------------
require_once ( dirname(__FILE__) . '/tftm_class_bd.php' );
require_once ( dirname( __FILE__ ) . '/tftm_settings_map.php' );
require_once ( dirname( __FILE__ ) . '/tftm_list_tweets.php' );
require_once ( dirname( __FILE__ ) . '/tftm_cron_script.php' );
require_once ( dirname( __FILE__ ) . '/tftm_class_tweets_table.php' );

// ------------------------------------------------
// Plugin activation
// ------------------------------------------------
function tftm_activation(){
    $default=array('theme' => 'apple',
        'latitude' => '50.45127',
        'longitude' => '30.523368',
        'radius' => '5000',
    );
    add_option( 'tweets_maps_settings',$default);
    add_option( 'tweets_search','');
    tftm_settings_bd::tftm_install();

    wp_schedule_event( time(), 'hourly', 'tftm_create_update' );
}
register_activation_hook(__FILE__, 'tftm_activation');
add_action( 'tftm_create_update', 'tftm_update_table_tweets' );
// ------------------------------------------------
// Plugin deactivation
// ------------------------------------------------
function tftm_deactivation(){
    wp_clear_scheduled_hook('tftm_create_update');
}
register_deactivation_hook(__FILE__, 'tftm_deactivation');
// ------------------------------------------------
// Uniinstall deactivation
// ------------------------------------------------
function tftm_uninstall(){
    delete_option('tweets_maps_settings');
    tftm_settings_bd::tftm_uninstall();
}
register_uninstall_hook(__FILE__,'tftm_uninstall');
// ------------------------------------------------
// Shortcode
// ------------------------------------------------

function tftm_shortcode($attr, $title='')
{
    $tweets_array=tftm_settings_bd::tftm_select_20();

    echo '<div id="shortcode_content" style="background-color:'.$attr["background-color-header"].'; width:'.$attr["width"].'; border-color:'.$attr["border-color"].'">
            <div id="tweet_img" class="dashicons dashicons-twitter">'.$title.'</div>';
            for($i=0; $i<count($tweets_array);$i++) {
                echo '<div id="tweet_content" style="background-color:'.$attr["background-color-content"].'">';
                    echo '<div id="tweet_autor">'.$tweets_array[$i]['tweet_author_name'].'</div>
                    <div id="tweet_text">'.$tweets_array[$i]['tweet_text'].'</div>
                </div>';
            }
         echo '</div>';
}

add_shortcode('tweets_shortcode', 'tftm_shortcode');

?>

