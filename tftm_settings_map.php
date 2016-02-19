<?php

// ------------------------------------------------
// Create custom plugin settings menu
// ------------------------------------------------
function tftm_add_plugin_page() {

    add_menu_page('Tweets from the map', 'Tweets from the map', 'administrator', __FILE__, 'tftm_settings_page', 'dashicons-admin-site', 81);

}
add_action('admin_menu', 'tftm_add_plugin_page');

function tftm_settings_page(){
    ?>
    <div class="wrap">
        <h2><?php echo get_admin_page_title() ?></h2>

        <form action="options.php" method="POST">
            <?php settings_fields( 'tweets_maps_group' ); ?>
            <?php do_settings_sections(__FILE__); ?>
            <?php submit_button(); ?>
        </form>

    </div><div id="map"></div>

    <?php
  echo do_shortcode('[tweets_shortcode background-color-header="#CCFFFF" background-color-content="white" width="600px" border-color="black"]Messages from Twitter[/tweets_shortcode]');
}


//// ------------------------------------------------
//// Register settings
//// ------------------------------------------------

function tftm_plugin_settings(){
    register_setting( 'tweets_maps_group', 'tweets_maps_settings', 'tftm_validation_settings' );

    add_settings_section( 'tweets_maps_main', 'Main settings', 'tftm_setting_section_callback_function', __FILE__ );

    add_settings_field('theme', 'Theme', 'tftm_theme_text', __FILE__, 'tweets_maps_main' );
    add_settings_field('latitude', 'Latitude', 'tftm_latitude_text', __FILE__, 'tweets_maps_main' );
    add_settings_field('longitude', 'Longitude', 'tftm_longitude_text', __FILE__, 'tweets_maps_main' );
    add_settings_field('radius', 'Radius, km', 'tftm_radius_text', __FILE__, 'tweets_maps_main' );
}
add_action('admin_init', 'tftm_plugin_settings');

//// ------------------------------------------------
//// Fill settings
//// ------------------------------------------------

function tftm_setting_section_callback_function() {
    echo '<p>Page of settings</p>';
}


function tftm_theme_text(){

    $val = get_option('tweets_maps_settings');

    ?>
    <input type="text" name="tweets_maps_settings[theme]" value="<?php echo esc_attr( $val['theme'] ) ?>" id="map_title"/>
    <?php
}

function tftm_latitude_text(){

    $val = get_option('tweets_maps_settings');

    ?>
    <input type="text" name="tweets_maps_settings[latitude]"  value="<?php echo esc_attr( $val['latitude'] ) ?>" maxlength="10" id="latitude"/>
    <?php
}

function tftm_longitude_text(){

    $val = get_option('tweets_maps_settings');

    ?>
    <input type="text" name="tweets_maps_settings[longitude]" value="<?php echo esc_attr( $val['longitude'] ) ?>" maxlength="10" id="longitude"/>
    <?php
}

function tftm_radius_text(){

    $val = get_option('tweets_maps_settings');

    ?>

    <input type="text" name="tweets_maps_settings[radius]" value="<?php echo esc_attr( $val['radius'] ) ?>" maxlength="5" id="radius"/>
    <div id="slider"></div>

    <?php
}

//// ------------------------------------------------
//// Validation settings
//// ------------------------------------------------

function tftm_validation_settings($tweets_maps_settings){

    $val = get_option('tweets_maps_settings');
    $message = $type = null;

    if(empty($tweets_maps_settings['theme'])){

        $type = 'error';
        $message = 'Field "Theme" can not be empty';

        add_settings_error( 'tftm_setting_error', 'popup_header_text', $message, $type );
        $tweets_maps_settings['theme'] = $val['theme'];

    }else{
        $tweets_maps_settings['theme']= sanitize_text_field($tweets_maps_settings['theme']);
    }

    if(empty($tweets_maps_settings['latitude'])){

        $type = 'error';
        $message = 'Field "Latitude" can not be empty';

        add_settings_error( 'tftm_setting_error', 'latitude', $message, $type );
        $tweets_maps_settings['latitude'] = $val['latitude'];

    } elseif( !is_numeric($tweets_maps_settings['latitude']) ){

        $type = 'error';
        $message = 'field "Latitude" must contain numbers';

        add_settings_error( 'tftm_setting_error', 'latitude', $message, $type );
        $tweets_maps_settings['latitude'] = $val['latitude'];

    } elseif($tweets_maps_settings['latitude']<-90 || $tweets_maps_settings['latitude']>90){

        $type = 'error';
        $message = 'field "Latitude" should be in the range from -90 to 90 degrees';

        add_settings_error( 'tftm_setting_error', 'latitude', $message, $type );
        $tweets_maps_settings['latitude'] = $val['latitude'];
    }

    if(empty($tweets_maps_settings['longitude'])){

        $type = 'error';
        $message = 'Field "Longitude" can not be empty';

        add_settings_error( 'tftm_setting_error', 'longitude', $message, $type );
        $tweets_maps_settings['longitude'] = $val['longitude'];

    } elseif( !is_numeric($tweets_maps_settings['longitude']) ){

        $type = 'error';
        $message = 'field "Longitude" must contain numbers';

        add_settings_error( 'tftm_setting_error', 'longitude', $message, $type );
        $tweets_maps_settings['longitude'] = $val['longitude'];

    } elseif($tweets_maps_settings['longitude']<-180 || $tweets_maps_settings['longitude']>180){

        $type = 'error';
        $message = 'field "Longitude" should be in the range from -180 to 180 degrees';

        add_settings_error( 'tftm_setting_error', 'longitude', $message, $type );
        $tweets_maps_settings['longitude'] = $val['longitude'];
    }

    if(empty($tweets_maps_settings['radius'])){

        $type = 'error';
        $message = 'Field "Radius" can not be empty';

        add_settings_error( 'tftm_setting_error', 'radius', $message, $type );
        $tweets_maps_settings['radius'] = $val['radius'];

    } elseif( !is_numeric($tweets_maps_settings['radius']) ){

        $type = 'error';
        $message = 'field "Radius" must contain numbers';

        add_settings_error( 'tftm_setting_error', 'radius', $message, $type );
        $tweets_maps_settings['radius'] = $val['radius'];

    } elseif($tweets_maps_settings['radius']<1 || $tweets_maps_settings['radius']>40075){

        $type = 'error';
        $message = 'field "Radius" should be in the range from 0 to 40 075 km';

        add_settings_error( 'tftm_setting_error', 'radius', $message, $type );
        $tweets_maps_settings['radius'] = $val['radius'];
    }

    if($message==null){
        $tweets_data=get_tweets($tweets_maps_settings);
        if($tweets_data!=null)
            tftm_settings_bd::tftm_insert($tweets_data);
    }

    return $tweets_maps_settings;
}

function tftm_admin_notices_action() {
    settings_errors( 'tftm_setting_error');
}
add_action( 'admin_notices', 'tftm_admin_notices_action' );
?>

<div id="dialog" title="Error dialog" style="display: none">
    <p></p>
    <p id="error_theme_empty">Field "Theme" can not be empty</p>

    <p id="error_latitude_empty" style="display: none">Field "Latitude" can not be empty</p>
    <p id="erroe_latitude_notnumber" style="display: none">Field "Latitude" must contain numbers</p>
    <p id="error_latitude_range" style="visibility: hidden">Field "Latitude" should be in the range from -90 to 90 degrees.</p>

    <p id="error_longitude_empty" style="display: none">Field "Longitude" can not be empty</p>
    <p id="error_longitude_notnumber" style="display: none">Field "Longitude" must contain numbers</p>
    <p id="error_longitude_range" style="display: none">Field "Longitude" should be in the range from -180 to 180 degrees.</p>

    <p id="error_radius_empty"style="display: none">Field "Radius" can not be empty</p>
    <p id="error_radius_notnumber" style="display: none">Field "Radius" must contain numbers</p>
    <p id="error_radius_range" style="display: none">Field "Radius" should be in the range from 0 to 40 075 km.</p>
</div>