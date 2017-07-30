<?php
/**
 * Class TFTM_Api_Settings
 */
class TFTM_Settings_Api {
    /**
     * Twitter_Options constructor.
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'register_api_settings_submenu_page' ) );
        add_action( 'admin_init', array( $this, 'twitter_api_settings' ) );
        add_action( 'admin_init', array( $this, 'google_api_settings' ) );
        add_action( 'admin_notices', array( $this, 'admin_notices_api_twitter_action' ) );
        add_action( 'admin_notices', array( $this, 'admin_notices_api_google_action' ) );
    }

    /**
     * Register custom settings API sub menu page
     */
    public function register_api_settings_submenu_page() {
        add_submenu_page(
            dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'class-tftm-settings-map.php',   // parent slug
            __( 'API Settings', 'tftm' ),                           // page title
            __( 'API Settings', 'tftm' ),                           // menu title
            'manage_options',                                       // capability
            'api_settings',                                         // menu slug
            array( $this, 'submenu_settings_api_page_callback' )    // callable function
        );
    }

    /**
     * Create settings API sub menu page
     */
    public function submenu_settings_api_page_callback() {
        ?>
        <div class="wrap">
            <h2><?php echo get_admin_page_title(); ?></h2>
            <div id="tabs-container" class="api-settings">
                <ul class="tabs-menu">
                    <li class="current"><a href="#tab-1"><?php _e( 'Twitter', 'tftm' ); ?></a></li>
                    <li><a href="#tab-2"><?php _e( 'Google', 'tftm' ); ?></a></li>
                </ul>
                <div class="tab">
                    <div id="tab-1" class="tab-content">
                        <form action="options.php" method="POST">
                            <?php settings_fields( 'api_group' ); ?>
                            <?php do_settings_sections( 'twitter_api_settings' ); ?>
                            <?php submit_button( 'Save Settings' ); ?>
                        </form>
                    </div>
                    <div id="tab-2" class="tab-content">
                        <form action="options.php" method="POST">
                            <?php settings_fields( 'api_group1' ); ?>
                            <?php do_settings_sections( 'google_api_settings' ); ?>
                            <?php submit_button( 'Save Settings' ); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="dialogApi" title="Error dialog" style="display: none">
            <p></p>
            <p id="error_twitter_consumer_key_empty" style="display: none">Field "Consumer Key" can not be empty</p>
            <p id="error_twitter_consumer_secret_empty" style="display: none">Field "Consumer Secret" can not be empty</p>
            <p id="error_twitter_oauth_token_empty" style="display: none">Field "Oauth Token" can not be empty</p>
            <p id="error_twitter_oauth_secret_empty" style="display: none">Field "Oauth Secret" can not be empty</p>
            <p id="error_google_api_key_empty" style="display: none">Field "API Key" can not be empty</p>
        </div>
        <?php
    }

    /**
     * Add twitter api settings to sub menu page
     */
    public function twitter_api_settings() {
        add_settings_section(
            'tweets_api',                                           // id
            __( 'Twitter api settings', 'tftm' ),                   // title
            array( $this, 'print_twitter_settings_section_info' ),  // callback
            'twitter_api_settings'                                  // page
        );
        add_settings_field(
            'consumer_key',                             // id
            __( 'Consumer Key', 'tftm' ),               // setting title
            array( $this, 'consumer_key_callback' ),    // settings page
            'twitter_api_settings', 'tweets_api'        // settings section
        );
        add_settings_field(
            'consumer_secret',                          // id
            __( 'Consumer Secret', 'tftm' ),            // setting title
            array( $this, 'consumer_secret_callback' ), // display callback
            'twitter_api_settings',                     // settings page
            'tweets_api'                                // settings section
        );
        add_settings_field(
            'oauth_token',                              // id
            __( 'Oauth Token', 'tftm' ),                // setting title
            array( $this, 'oauth_token_callback' ),     // display callback
            'twitter_api_settings',                     // settings page
            'tweets_api'                                // settings section
        );
        add_settings_field(
            'oauth_secret',                             // id
            __( 'Oauth Secret', 'tftm' ),               // setting title
            array( $this, 'oauth_secret_callback' ),    // display callback
            'twitter_api_settings',                     // settings page
            'tweets_api'                                // settings section
        );
        register_setting(
            'api_group',                                        // option group
            'twitter_api_settings',                             // option name
            array( $this, 'twitter_api_validation_settings' )   // sanitize callback
        );
    }

    /**
     * Add google api settings to sub menu page
     */
    public function google_api_settings() {
        add_settings_section(
            'google_api',                                           // id
            __('Google api settings', 'tftm'),                      // title
            array( $this, 'print_google_settings_section_info' ),   // callback
            'google_api_settings'                                   // page
        );
        add_settings_field(
            'api_key',                              // id
            __( 'API Key', 'tftm' ),                // setting title
            array( $this, 'api_key_callback' ),     // display callback
            'google_api_settings',                  // settings page
            'google_api'                            // settings section
        );
        register_setting(
            'api_group1',                                       // option group
            'google_api_settings',                              // option name
            array( $this, 'google_api_validation_settings' )    // sanitize callback
        );
    }

    /**
     * Print title in twitter settings section
     */
    public function print_twitter_settings_section_info() {
        echo '<p>'.__( 'Please enter the settings for the connection with Twitter api', 'tftm' ).'</p>';
    }

    /**
     * Print title in google settings section
     */
    public function print_google_settings_section_info() {
        echo '<p>'.__( 'Please enter the settings for the connection with Google api', 'tftm' ).'</p>';
    }

    /**
     * Twitter settings section - add field "consumer key"
     */
    public function consumer_key_callback() {
        $val = get_option( 'twitter_api_settings' );
        ?>
        <input type="text" name="twitter_api_settings[consumer_key]" value="<?php echo esc_attr( $val['consumer_key'] ) ?>" placeholder="e7zWUJ5zVnGY9ZQQEfEYKj3f0" id="twitter_consumer_key" />
        <?php
    }

    /**
     * Twitter settings section - add field "consumer secret"
     */
    public function consumer_secret_callback() {
        $val = get_option( 'twitter_api_settings' );
        ?>
        <input type="text" name="twitter_api_settings[consumer_secret]" value="<?php echo esc_attr( $val['consumer_secret'] ) ?>" placeholder="IftcYgdQ23qFwh7YRlpQGUzdSrdzItXev7UtBP4WbeX0JCMmPC" id="twitter_consumer_secret"/>
        <?php
    }

    /**
     * Twitter settings section - add field "oauth token"
     */
    public function oauth_token_callback() {
        $val = get_option( 'twitter_api_settings' );
        ?>
        <input type="text" name="twitter_api_settings[oauth_token]" value="<?php echo esc_attr( $val['oauth_token'] ) ?>" placeholder="4257362595-fi4x38wogRRHj62Fj0EKHqb5ZUH56xdhjj3bhA0" id="twitter_oauth_token"/>
        <?php
    }

    /**
     * Twitter settings section - add field "oauth secret"
     */
    public function oauth_secret_callback() {
        $val = get_option( 'twitter_api_settings' );
        ?>
        <input type="text" name="twitter_api_settings[oauth_secret]" value="<?php echo esc_attr( $val['oauth_secret'] ) ?>" placeholder="6ONZsPi7WzZMbd4PVSAguVMKmGLWA3BanF3zPfrxTPxQp" id="twitter_oauth_secret"/>
        <?php
    }

    /**
     * Goofle settings section - add field "api key"
     */
    public function api_key_callback() {
        $val = get_option( 'google_api_settings' );
        ?>
        <input type="text" name="google_api_settings[api_key]" value="<?php echo esc_attr( $val['api_key'] ) ?>" placeholder="AIzaSyCQZy2967nSXCkZ04keH79kVYkKBxCXCNQ" id="google_api_key"/>
        <?php
    }

    /**
     * Validation fields from Twitter settings section
     *
     * @param $twitter_api_input_settings
     * @return mixed
     */
    public function twitter_api_validation_settings ( $twitter_api_input_settings ) {
        $val = get_option( 'twitter_api_settings' );
        $message = NULL;
        $type = NULL;

        if ( empty( $twitter_api_input_settings['consumer_key'] ) ) {
            $type = 'error';
            $message = __( 'Field "Consumer Key" can not be empty', 'tftm' );

            add_settings_error( 'tftm_setting_api_twitter_error', 'popup_header_text', $message, $type );
            $twitter_api_input_settings['consumer_key'] = $val['consumer_key'];
        } else {
            $twitter_api_input_settings['consumer_key'] = sanitize_text_field( $twitter_api_input_settings['consumer_key'] );
        }

        if ( empty( $twitter_api_input_settings['consumer_secret'] ) ) {
            $type = 'error';
            $message = __( 'Field "Consumer Secret" can not be empty', 'tftm' );

            add_settings_error( 'tftm_setting_api_twitter_error', 'popup_header_text', $message, $type );
            $twitter_api_input_settings['consumer_secret'] = $val['consumer_secret'];
        } else {
            $twitter_api_input_settings['consumer_secret'] = sanitize_text_field( $twitter_api_input_settings['consumer_secret'] );
        }

        if ( empty( $twitter_api_input_settings['oauth_token'] ) ) {
            $type = 'error';
            $message = __( 'Field "Oauth Token" can not be empty', 'tftm' );

            add_settings_error( 'tftm_setting_api_twitter_error', 'popup_header_text', $message, $type );
            $twitter_api_input_settings['oauth_token'] = $val['oauth_token'];
        } else {
            $twitter_api_input_settings['oauth_token'] = sanitize_text_field( $twitter_api_input_settings['oauth_token'] );
        }

        if ( empty( $twitter_api_input_settings['oauth_secret'] ) ) {
            $type = 'error';
            $message = __( 'Field "Oauth Secret" can not be empty', 'tftm' );

            add_settings_error( 'tftm_setting_api_twitter_error', 'popup_header_text', $message, $type );
            $twitter_api_input_settings['oauth_secret'] = $val['oauth_secret'];
        } else {
            $twitter_api_input_settings['oauth_secret'] = sanitize_text_field( $twitter_api_input_settings['oauth_secret'] );
        }

        $result = TFTM_List_Tweets::twetter_connection( $twitter_api_input_settings );
        if ( ! $result['state'] ) {
            $type = 'error';
            $message = __( 'You entered incorrect api data', 'tftm' );

            add_settings_error( 'tftm_setting_api_twitter_error', 'popup_header_text', $message, $type );
        }

        return $twitter_api_input_settings;
    }

    /**
     * Validation fields from Google settings section
     *
     * @param $google_api_input_settings
     * @return mixed
     */
    public function google_api_validation_settings( $google_api_input_settings ) {
        $val = get_option( 'google_api_settings' );
        $message = NULL;
        $type = NULL;

        if ( empty( $google_api_input_settings['api_key'] ) ) {
            $type = 'error';
            $message = 'Field "API Key" can not be empty';

            add_settings_error( 'tftm_setting_api_google_error', 'popup_header_text', $message, $type );
            $google_api_input_settings['api_key'] = $val['api_key'];
        }
        else {
            $google_api_input_settings['api_key'] = sanitize_text_field( $google_api_input_settings['api_key'] );
        }
        return $google_api_input_settings;
    }

    /**
     * Show notices for Twitter settings section if submitted incorrect data
     */
    public function admin_notices_api_twitter_action() {
        settings_errors( 'tftm_setting_api_twitter_error' );
    }

    /**
     * Show notices for Google settings section if submitted incorrect data
     */
    public function admin_notices_api_google_action() {
        settings_errors( 'tftm_setting_api_google_error' );
    }
}
?>
