<?php
/**
 * Class TFTM_Setting_Map
 */
class TFTM_Settings_Map{
    /**
     * Map_Settings constructor.
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'register_map_settings_menu_page') );
        add_action( 'admin_init', array( $this, 'map_settings') );
        add_action( 'admin_notices', array( $this, 'admin_notices_map_action') );
    }

    /**
     * Register settings map menu page
     */
    public function register_map_settings_menu_page() {
        add_menu_page(
            __( 'Tweets from the map', 'tftm' ),    // page title
            __( 'Tweets from the map', 'tftm' ),    // menu title
            'administrator',                        // capability
            __FILE__,                               // menu slug
            array( $this, 'map_settings_page' ),    // function
            'dashicons-admin-site',                 // icon url
            81                                      // position
        );
    }

    /**
     * Create settings map menu page
     */
    public function map_settings_page() {
        ?>
        <div class="wrap">
            <h2><?php echo get_admin_page_title(); ?></h2>
            <form action="options.php" method="POST">
                <?php settings_fields( 'tweets_maps_group' ); ?>
                <?php do_settings_sections( __FILE__ ); ?>
                <?php submit_button(); ?>
            </form>
        </div><div id="map"></div>
        <?php
            echo do_shortcode( '[tweets_shortcode 
                                 background-color-header="#CCFFFF" 
                                 background-color-content="white" 
                                 width="100%" 
                                 max-width="1000px" 
                                 border-color="black"]
                                 Messages from Twitter[/tweets_shortcode]'
                              );
        ?>

        <div id="dialogMap" title="Error dialog" style="display: none">
            <p></p>
            <p id="error_theme_empty" style="display: none">Field "Theme" can not be empty</p>

            <p id="error_latitude_empty" style="display: none">Field "Latitude" can not be empty</p>
            <p id="erroe_latitude_notnumber" style="display: none">Field "Latitude" must contain numbers</p>
            <p id="error_latitude_range" style="visibility: hidden">Field "Latitude" should be in the range from -90 to 90 degrees.</p>

            <p id="error_longitude_empty" style="display: none">Field "Longitude" can not be empty</p>
            <p id="error_longitude_notnumber" style="display: none">Field "Longitude" must contain numbers</p>
            <p id="error_longitude_range" style="display: none">Field "Longitude" should be in the range from -180 to 180 degrees.</p>

            <p id="error_radius_empty" style="display: none">Field "Radius" can not be empty</p>
            <p id="error_radius_notnumber" style="display: none">Field "Radius" must contain numbers</p>
            <p id="error_radius_range" style="display: none">Field "Radius" should be in the range from 0 to 40 075 km.</p>
        </div>
        <?php
    }

    /**
     * Add map settings to menu page
     */
    public function map_settings() {
        add_settings_section(
            'tweets_maps_main',                                 // id
            __( 'Main settings', 'tftm' ),                      // title
            array( $this, 'print_map_settings_section_info' ),  // callback
            __FILE__                                            // page
        );
        add_settings_field(
            'theme',                                // id
            __( 'Theme', 'tftm' ),                  // setting title
            array( $this, 'theme_callback' ),       // display callback
            __FILE__,                               // settings page
            'tweets_maps_main'                      // settings section
        );
        add_settings_field(
            'latitude',                             // id
            __( 'Latitude' , 'tftm' ),              // setting title
            array( $this, 'latitude_callback' ),    // display callback
            __FILE__,                               // settings page
            'tweets_maps_main'                      // settings section
        );
        add_settings_field(
            'longitude',                            // id
            __( 'Longitude', 'tftm' ),              // setting title
            array( $this, 'longitude_callback' ),   // display callback
            __FILE__,                               // settings page
            'tweets_maps_main'                      // settings section
        );
        add_settings_field(
            'radius',                               // id
            __( 'Radius, km', 'tftm' ),             // setting title
            array( $this, 'radius_callback' ),      // display callback
            __FILE__,                               // settings page
            'tweets_maps_main'                      // settings section
        );
        register_setting(
            'tweets_maps_group',                        // option group
            'tweets_maps_settings',                     // option name
            array( $this, 'map_validation_settings' )   // sanitize callback
        );

    }

    /**
     * Print title in map settings section
     */
    public function print_map_settings_section_info() {
        echo '<p>'.__( 'Page of settings google map', 'tftm' ).'</p>';
    }

    /**
     * Google map settings section - add field "theme"
     */
    public function theme_callback() {
        $val = get_option( 'tweets_maps_settings' );
        ?>
        <input type="text" name="tweets_maps_settings[theme]" value="<?php echo esc_attr( $val['theme'] ) ?>" placeholder="apple" id="map_title"/>
        <?php
    }

    /**
     * Google map settings section - add field "latitude"
     */
    public function latitude_callback() {
        $val = get_option( 'tweets_maps_settings' );
        ?>
        <input type="text" name="tweets_maps_settings[latitude]"  value="<?php echo esc_attr( $val['latitude'] ) ?>" maxlength="10" placeholder="50.45127" id="latitude"/>
        <?php
    }

    /**
     * Google map settings section - add field "longitude"
     */
    public function longitude_callback() {
        $val = get_option( 'tweets_maps_settings' );
        ?>
        <input type="text" name="tweets_maps_settings[longitude]" value="<?php echo esc_attr( $val['longitude'] ) ?>" maxlength="10" placeholder="30.523368" id="longitude"/>
        <?php
    }

    /**
     * Google map settings section - add field "radius"
     */
    public function radius_callback() {
        $val = get_option('tweets_maps_settings');
        ?>
        <input type="text" name="tweets_maps_settings[radius]" value="<?php echo esc_attr( $val['radius'] ) ?>" maxlength="5" placeholder="5000" id="radius"/>
        <div id="slider"></div>
        <?php
    }

    /**
     * Validation fields from Map settings section
     *
     * @param $tweets_maps_settings
     * @return mixed
     */
    public function map_validation_settings( $tweets_maps_settings ) {
        $val = get_option( 'tweets_maps_settings' );
        $message = NULL;
        $type = NULL;

        if ( empty( $tweets_maps_settings['theme'] ) ) {
            $type = 'error';
            $message = __( 'Field "Theme" can not be empty', 'tftm' );

            add_settings_error( 'tftm_setting_map_error', 'popup_header_text', $message, $type );
            $tweets_maps_settings['theme'] = $val['theme'];
        } else {
            $tweets_maps_settings['theme'] = sanitize_text_field( $tweets_maps_settings['theme'] );
        }

        if ( empty( $tweets_maps_settings['latitude'] ) ) {
            $type = 'error';
            $message = __('Field "Latitude" can not be empty', 'tftm');

            add_settings_error( 'tftm_setting_map_error', 'latitude', $message, $type );
            $tweets_maps_settings['latitude'] = $val['latitude'];
        } elseif ( ! is_numeric( $tweets_maps_settings['latitude'] ) ) {
            $type = 'error';
            $message = __( 'Field "Latitude" must contain numbers', 'tftm' );

            add_settings_error( 'tftm_setting_map_error', 'latitude', $message, $type );
            $tweets_maps_settings['latitude'] = $val['latitude'];

        } elseif ( $tweets_maps_settings['latitude'] < -90 || $tweets_maps_settings['latitude'] > 90 ) {
            $type = 'error';
            $message = __( 'Field "Latitude" should be in the range from -90 to 90 degrees', 'tftm' );

            add_settings_error( 'tftm_setting_map_error', 'latitude', $message, $type );
            $tweets_maps_settings['latitude'] = $val['latitude'];
        }

        if ( empty( $tweets_maps_settings['longitude'] ) ) {
            $type = 'error';
            $message = __( 'Field "Longitude" can not be empty', 'tftm' );

            add_settings_error( 'tftm_setting_map_error', 'longitude', $message, $type );
            $tweets_maps_settings['longitude'] = $val['longitude'];
        } elseif ( ! is_numeric( $tweets_maps_settings['longitude'] ) ) {
            $type = 'error';
            $message = __( 'Field "Longitude" must contain numbers', 'tftm' );

            add_settings_error( 'tftm_setting_map_error', 'longitude', $message, $type );
            $tweets_maps_settings['longitude'] = $val['longitude'];
        } elseif ( $tweets_maps_settings['longitude'] < -180 || $tweets_maps_settings['longitude'] > 180 ) {
            $type = 'error';
            $message = __( 'Field "Longitude" should be in the range from -180 to 180 degrees', 'tftm' );

            add_settings_error( 'tftm_setting_map_error', 'longitude', $message, $type );
            $tweets_maps_settings['longitude'] = $val['longitude'];
        }

        if ( empty( $tweets_maps_settings['radius'] ) ) {
            $type = 'error';
            $message = __( 'Field "Radius" can not be empty', 'tftm' );

            add_settings_error( 'tftm_setting_map_error', 'radius', $message, $type );
            $tweets_maps_settings['radius'] = $val['radius'];
        } elseif ( ! is_numeric($tweets_maps_settings['radius'] ) ) {
            $type = 'error';
            $message = __( 'Field "Radius" must contain numbers', 'tftm' );

            add_settings_error( 'tftm_setting_map_error', 'radius', $message, $type );
            $tweets_maps_settings['radius'] = $val['radius'];
        } elseif ( $tweets_maps_settings['radius'] < 1 || $tweets_maps_settings['radius'] > 40075 ) {
            $type = 'error';
            $message = __( 'Field "Radius" should be in the range from 0 to 40 075 km', 'tftm' );

            add_settings_error( 'tftm_setting_map_error', 'radius', $message, $type );
            $tweets_maps_settings['radius'] = $val['radius'];
        }

        if ( $message == NULL ) {
            $tweets_data = NULL;
            $twitter_api_settings = get_option( 'twitter_api_settings' );

            $rezult = TFTM_List_Tweets::twetter_connection( $twitter_api_settings );
            if ( $rezult['state'] ) {
                $tweets_data = TFTM_List_Tweets::get_tweets( $tweets_maps_settings, $rezult['connection'] );
            }
            if ( $tweets_data != NULL ) {
                TFTM_BD_Queries::add_new_tweets( $tweets_data );
            }
        }
        return $tweets_maps_settings;
    }

    /**
     * Show notices for Map settings section if submitted incorrect data
     */
    public function admin_notices_map_action() {
        settings_errors( 'tftm_setting_map_error' );
    }
}

?>
