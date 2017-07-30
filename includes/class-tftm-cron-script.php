<?php

/**
 * Class TFTM_Cron_Script
 */
class TFTM_Cron_Script {

    /**
     * Contain new tweets from Twitter
     *
     * @var null
     */
    private static $tweets_data = NULL;

    /**
     * Contain api settings for connection with twitter
     *
     * @var null
     */
    private $twitter_api_settings = array();

    /**
     * Contain coordinates and parameters for searching tweets
     *
     * @var array
     */
    private $map_search_settings = array();

    private static $connaction = array();

    /**
     * TFTM_Cron_Script constructor.
     */
    public function __construct() {}

    /**
     * Set parameters for getting tweets from Twitter
     */
    private function set_parameters() {
        $this->twitter_api_settings = get_option( 'twitter_api_settings' );
        $this->map_search_settings = get_option( 'tweets_maps_settings' );
    }

    /**
     * Set connection with Twitter
     */
    private function twitter_connection() {
        $this->connaction = TFTM_List_Tweets::twetter_connection( $this->twitter_api_settings );
    }

    /**
     * Get tweets from the Twitter
     */
    private function get_tweets() {
        $this->tweets_data = TFTM_List_Tweets::get_tweets( $this->map_search_settings, $this->connaction['connection'] );
    }

    /**
     * Save tweets from the Twitter to DB
     */
    private function save_tweeets() {
        TFTM_BD_Queries::add_new_tweets( $this->tweets_data );
    }

    /**
     * Cron script for getting new tweets from Twitter
     */
    public static function tweets_cron() {
       self::set_parameters();
       self::twitter_connection();
       if( self::$connaction['state'] ) {
           self::get_tweets();
           if(self::$tweets_data != NULL)
              self::save_tweeets();
           file_put_contents ( 'cron.txt', 'write' );
       }
    }
}
?>