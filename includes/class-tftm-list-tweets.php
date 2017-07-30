<?php
use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Class TFTM_List_Tweets
 */
class TFTM_List_Tweets{
    /**
     * List_Tweets constructor.
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'register_list_tweets_submenu_page' ) );
        add_filter( 'set-screen-option', array( $this, 'tweets_table_set_option', 10, 3) );
        add_action( 'wp_ajax_clear_search',  array( $this, 'clear_search_javascript' ) );
        add_action( 'wp_ajax_tweets_update',  array( $this, 'tweets_update_javascript' ) );
        add_action( 'admin_notices', array( $this, 'admin_notices_connection_api_twitter' ) );
    }

    /**
     * Register List Tweets sub menu page
     */
    public function register_list_tweets_submenu_page() {
        $hook = add_submenu_page(
                    dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'class-tftm-settings-map.php',   // parent slug
                    __('List tweets', 'tftm'),                              // page title
                    __( 'List tweets', 'tftm' ),                            // menu title
                    'manage_options',                                       // capability
                    'list_tweets',                                          // menu slug
                    array( $this, 'submenu_list_tweets_page_callback' )     // function
                );
        add_action( 'load-' . $hook, array( $this, 'add_options' ) );
    }

    /**
     * Add options on List Tweets sub menu page,
     * which allow to set the number of displayed
     * tweets on one page
     */
    public function add_options() {
        $option = 'per_page';
        $args = array(
            'label' => 'Tweets',
            'default' => 5,
            'option' => 'tweets_per_page'
        );
        add_screen_option( $option, $args );
    }

    /**
     * Set the number of displayed tweets on one page from List Tweets sub menu
     *
     * @param $status
     * @param $option
     * @param $value
     * @return mixed
     */
    public function tweets_table_set_option( $status, $option, $value ) {
        return $value;
    }

    /**
     * Clear search settings
     */
    public function clear_search_javascript() {
        if ( isset( $_POST['clear_search'] ) ) {
            update_option( 'tweets_search', '' );
        }
        wp_die();
    }

    /**
     * Update row from List Tweets Table
     */
    public function tweets_update_javascript() {
        if ( isset( $_POST['row_content'] ) ) {
            echo TFTM_BD_Queries::update_tweet_record( $_POST['row_content'] );
        }
        wp_die();

    }

    /**
     * Create List Tweets Table sub menu page
     */
    public function submenu_list_tweets_page_callback() {
        $tweets_list_table = new TFTM_Tweets_Table();
        $tweets_list_table->prepare_items();
        ?>
        <div class="wrap">
            <h2><?php _e( 'Table of tweets', 'tftm' ); ?></h2>

            <a href='<?php echo plugin_dir_url( __FILE__ ); ?>class-tftm-download-tweets.php?type_file=csv'><?php _e( 'Download file in csv format', 'tftm' ); ?> (TableOfTweets.csv)</a><br>
            <a href='<?php echo plugin_dir_url( __FILE__ ); ?>class-tftm-download-tweets.php?type_file=xml'><?php _e( 'Download file in xml format', 'tftm' ); ?> (TableOfTweets.xml)</a>


            <form  id="table_form" method="post" >
                <input type="hidden" name="page" value="" />
                <?php $tweets_list_table->search_box( __( 'search', 'tftm' ), 'search_tweets' ); ?>
            </form>
            <form id="twwets-filter" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <?php $tweets_list_table->display(); ?>
            </form>

        </div>
        <?php
    }

    /**
     * Show notices about connection by twitter api
     */
    public function admin_notices_connection_api_twitter() {
        settings_errors( 'tftm_get_twitter_data' );
    }

    /**
     * Set connection with twitter
     *
     * @param null $twitter_api_settings
     * @return array
     */
    public static function twetter_connection( $twitter_api_settings = NULL ){
        $message = NULL;
        $type = NULL;
        $rezult = array();

        if (   ! isset( $twitter_api_settings['consumer_key'] )
            || ! isset( $twitter_api_settings['consumer_secret'] )
            || ! isset( $twitter_api_settings['oauth_token'] )
            || ! isset( $twitter_api_settings['oauth_secret']) ) {
            $type = 'error';
            $message = __( 'Error: api twitter settings are not set', TFTM_PLUGIN_BASENAME );
            add_settings_error( 'tftm_get_twitter_data', esc_attr( 'connection_twitter' ), $message, $type );
        }

        define( 'CONSUMER_KEY', $twitter_api_settings['consumer_key'] );
        define( 'CONSUMER_SECRET', $twitter_api_settings['consumer_secret'] );
        define( 'OAUTH_TOKEN', $twitter_api_settings['oauth_token'] );
        define( 'OAUTH_SECRET', $twitter_api_settings['oauth_secret'] );

        $connection = new TwitterOAuth( CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET );
        $user = $connection->get( 'account/verify_credentials' );

        //Twitter: "Could not authenticate you"
        if ( ! isset( $user->id ) ) {
            $type = 'error';
            $message = __( 'Error in  Twitter settings', TFTM_PLUGIN_BASENAME );
            add_settings_error( 'tftm_get_twitter_data', esc_attr( 'connection_twitter' ), $message, $type );
            $rezult['state'] = false;
        } else {
            $rezult['state'] = true;
        }
        $rezult['connection'] = $connection;

        return $rezult;
    }

    /**
     * Get tweets from twitter
     *
     * @param null $map_search_settings
     * @param $connection
     * @return null
     */
    public static function get_tweets( $map_search_settings = NULL, $connection ) {
        $message = NULL;
        $type = NULL;

        if (   ! isset( $map_search_settings['theme'] )
            || ! isset( $map_search_settings['latitude'] )
            || ! isset( $map_search_settings['longitude'] )
            || ! isset( $map_search_settings['radius'] ) ) {
            $type = 'error';
            $message = __( 'Error: search settings are not set', TFTM_PLUGIN_BASENAME );
            add_settings_error( 'tftm_get_twitter_data', esc_attr( 'connection_twitter' ), $message, $type );
        }

        define( 'SEARCH_THEME', $map_search_settings['theme'] );
        define( 'LATITUDE', $map_search_settings['latitude'] );
        define( 'LONGITUDE', $map_search_settings['longitude'] );
        define( 'RADIUS', $map_search_settings['radius'] );
        define( 'COUNT_TWEETS', 200 );

        $list_tweets = $connection->get( 'search/tweets', array( 'q' => SEARCH_THEME, 'geocode' => LATITUDE.','.LONGITUDE.','.RADIUS.'km', "count" => COUNT_TWEETS ) );

        if ( $list_tweets->errors[0]->code ) {
            $type = 'error';
            $message = __( 'Error in getting information from Twitter', TFTM_PLUGIN_BASENAME );
            add_settings_error( 'tftm_get_twitter_data', esc_attr( 'connection_twitter' ), $message, $type );
        }

        //Get tweets in JSON format
        $array_tweets = $list_tweets->statuses;

        if( $array_tweets ) {
            foreach ( $array_tweets as $key => $status ) {
                $tweets_data[$key] = array(
                    'theme'                 => htmlspecialchars( SEARCH_THEME, ENT_QUOTES | ENT_SUBSTITUTE | ENT_IGNORE | ENT_DISALLOWED, 'UTF-8' ),
                    'id'                    => $status->id,
                    'text'                  => wp_encode_emoji( htmlspecialchars( $status->text, ENT_QUOTES | ENT_SUBSTITUTE | ENT_IGNORE | ENT_DISALLOWED, 'UTF-8' ) ),
                    'tweets_data'           => mysql2date( 'Y-m-d H:i:s', $status->created_at ),
                    'author_name'           => htmlspecialchars( $status->user->name, ENT_QUOTES | ENT_SUBSTITUTE | ENT_IGNORE | ENT_DISALLOWED, 'UTF-8' ),
                    'author_screen_name'    => wp_encode_emoji( htmlspecialchars( $status->user->screen_name, ENT_QUOTES | ENT_SUBSTITUTE | ENT_IGNORE | ENT_DISALLOWED, 'UTF-8' ) ),
                    'location'              => htmlspecialchars( $status->user->location, ENT_QUOTES | ENT_SUBSTITUTE | ENT_IGNORE | ENT_DISALLOWED, 'UTF-8' ),
                );
            };

            $tweet_id = TFTM_BD_Queries::get_tweets_id( SEARCH_THEME );
            if ( $tweet_id ) {
                $tweet_id = array_column( $tweet_id, 'tweet_id' );
                foreach ( $tweets_data as $key => $new_tweet_id ) {
                    if ( in_array( $new_tweet_id['id'], $tweet_id ) ) {
                        unset( $tweets_data[$key] );
                    }
                }
            }
            return $tweets_data;
        } else {
            return null;
        }
    }
}

?>