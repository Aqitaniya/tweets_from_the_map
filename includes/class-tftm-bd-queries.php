<?php

/**
 * Class TFTM_BD_Queries
 */
class TFTM_BD_Queries{

    /**
     * Create table Tweets
     */
    static function create_table_tweets() {
        global $wpdb;

        $wpdb->query( "CREATE TABLE `" . $wpdb->prefix . "tweets` (`ID` INT(10) UNSIGNED NULL AUTO_INCREMENT,
                                                              `tweet_theme` VARCHAR(145) DEFAULT 'none',
                                                              `tweet_id` VARCHAR(30) DEFAULT 'none',
                                                              `tweet_text` VARCHAR (145) DEFAULT 'none',
                                                              `tweet_date_created` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
                                                              `tweet_author_name` VARCHAR (50) DEFAULT 'none',
                                                              `tweet_author_screen_name` VARCHAR (50) DEFAULT 'none',
                                                              `tweet_location` VARCHAR (20) DEFAULT 'none',
                                                               PRIMARY KEY (`ID`) )" );

        $wpdb->query("ALTER TABLE `" . $wpdb->prefix . "tweets` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci" );
    }

    /**
     * Delete table Tweets
     */
    static function delete_table_tweets() {
        global $wpdb;

        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}tweets" );
    }

    /**
     * Add new records to table Tweets
     *
     * @param $tweets_data
     */
    static function add_new_tweets( $tweets_data ) {
        global $wpdb;

        $placeholder = '';
        $values = array();
        foreach ( $tweets_data as $key => $value ) {
            $placeholder .= "(%s,%s,%s,%s,%s,%s,%s),";
            $values =  array_merge( $values, array_values( $value ) );
        }
        $placeholder = substr($placeholder, 0, -1);
        $query = $wpdb->prepare("INSERT INTO `" . $wpdb->prefix . "tweets` (`tweet_theme`,
                                                                            `tweet_id`,
                                                                            `tweet_text`,
                                                                            `tweet_date_created`,
                                                                            `tweet_author_name`,
                                                                            `tweet_author_screen_name`,
                                                                            `tweet_location`) VALUES " . $placeholder, $values);
        $wpdb->query($query);
    }

    /**
     * Update tweet record in table Tweets
     *
     * @param $row_content
     * @return bool|string
     */
    static function update_tweet_record( $row_content ) {
        global $wpdb;

        $date = DateTime::createFromFormat( 'Y-m-d H:i:s', $row_content[3] );
        if ( $date )
        {
            $rez = $wpdb->query( $wpdb->prepare("UPDATE `".$wpdb->prefix."tweets` SET `tweet_theme` = %s,
                                                       `tweet_text` = %s,
                                                       `tweet_date_created` = %s,
                                                       `tweet_author_name` = %s,
                                                       `tweet_author_screen_name`=%s,
                                                       `tweet_location`=%s
                                                  WHERE `tweet_id` = %s",
                $row_content[0],
                $row_content[2],
                $row_content[3],
                $row_content[4],
                $row_content[5],
                $row_content[6],
                $row_content[1]) );

            if ( $rez )
                return "Update table field was successful";
            else
                return false;
        }

    }

    /**
     * Get all tweets records from table Tweets
     *
     * @param $orderby
     * @param $order
     * @return mixed
     */
    static function get_all_tweets ( $orderby, $order ) {
        global $wpdb;

        $tweets_array = $wpdb->get_results( "SELECT * FROM `".$wpdb->prefix."tweets` ORDER BY `{$orderby}` {$order}", ARRAY_A );

        return $tweets_array;
    }

    /**
     * Get tweets for selected page Tweets Table from table Tweets
     * @param $orderby
     * @param $order
     * @param $offset
     * @param $per_page
     * @return mixed
     */
    static function get_tweets_page ( $orderby, $order, $offset, $per_page ) {
        global $wpdb;

        $tweets_array = $wpdb->get_results( "SELECT * FROM `".$wpdb->prefix."tweets` ORDER BY `{$orderby}` {$order} LIMIT {$offset},{$per_page}", ARRAY_A );

        return $tweets_array;
    }

    /**
     * Get 20 last tweets for shortcode
     *
     * @return mixed
     */
    static function get_20_tweets(){
        global $wpdb;

        $tweets_array = $wpdb->get_results( "SELECT `tweet_text`, `tweet_author_name` FROM `".$wpdb->prefix."tweets` ORDER BY `ID` DESC LIMIT 0,20", ARRAY_A );

        return $tweets_array;
    }

    /**
     * Get list tweets id by tweets theme
     *
     * @param $tweet_theme
     * @return mixed
     */
    static function  get_tweets_id( $tweet_theme ) {
        global $wpdb;

        $id_array = $wpdb->get_results( "SELECT `tweet_id` FROM `".$wpdb->prefix."tweets` WHERE `tweet_theme`='{$tweet_theme}' ORDER BY `tweet_id`", ARRAY_A );

        return $id_array;
    }

    /**
     * Search all tweets in table Tweets by input string
     *
     * @param $str
     * @return mixed
     */
    static function search_all_tweets( $str ) {
        global $wpdb;

        $str= "%" . $str . "%";

        $query=$wpdb->prepare( "SELECT * FROM `" . $wpdb->prefix . "tweets` WHERE `tweet_theme` LIKE %s
                                                                              OR `tweet_text` LIKE %s
                                                                              OR `tweet_author_name` LIKE %s
                                                                              OR `tweet_author_screen_name` LIKE %s",$str ,$str ,$str ,$str );

        $tweets_array = $wpdb->get_results( $query, ARRAY_A );
        return $tweets_array;
    }

    /**
     * Search tweets in table Tweets by input string for selected page Tweets Table
     *
     * @param $str
     * @param $orderby
     * @param $order
     * @param $offset
     * @param $per_page
     * @return mixed
     */
    static function search_tweets_one_page( $str, $orderby, $order, $offset, $per_page ) {
        global $wpdb;

        $str= "%" . $str . "%";

        $query=$wpdb->prepare( "SELECT * FROM `" . $wpdb->prefix . "tweets` WHERE `tweet_theme` LIKE %s
                                                                              OR `tweet_text` LIKE %s
                                                                              OR `tweet_author_name` LIKE %s
                                                                              OR `tweet_author_screen_name` LIKE %s
                                                                              ORDER BY %s %s
                                                                              LIMIT %d, %d", $str, $str, $str, $str, $orderby, $order, $offset, $per_page );
        $tweets_array = $wpdb->get_results( $query, ARRAY_A );
        return $tweets_array;
    }

    /**
     * Delete tweet ftom table Tweets by tweet id
     *
     * @param $tweet_id
     */
    static function delete_tweet( $tweet_id ) {
        global $wpdb;

        if( is_array( $tweet_id ) )
            $tweet_id = "('".implode( "',  '", $tweet_id )."')";
        else
            $tweet_id = "('". $tweet_id."')";
       $query = "DELETE FROM `" . $wpdb->prefix . "tweets` WHERE `tweet_id` IN". $tweet_id;
       $wpdb->query( $query );
    }
}
?>