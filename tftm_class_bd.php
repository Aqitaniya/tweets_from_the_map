<?php

class tftm_settings_bd{
    static function tftm_install() {
        global $wpdb;

        //$wpdb::set_charset( $wpdb, 'utf8_general_ci' );
        $wpdb->query("CREATE TABLE `".$wpdb->prefix."tweets` (`ID` INT(10) UNSIGNED NULL AUTO_INCREMENT,
                                                              `tweet_theme` VARCHAR(145) DEFAULT 'none',
                                                              `tweet_id` VARCHAR(30) DEFAULT 'none',
                                                              `tweet_text` VARCHAR (145) DEFAULT 'none',
                                                              `tweet_date_created` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
                                                              `tweet_author_name` VARCHAR (50) DEFAULT 'none',
                                                              `tweet_author_screen_name` VARCHAR (50) DEFAULT 'none',
                                                              `tweet_location` VARCHAR (20) DEFAULT 'none',
                                                               PRIMARY KEY (`ID`))");

        $wpdb->query("ALTER TABLE `".$wpdb->prefix."tweets` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    static function tftm_uninstall() {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}tweets");
    }

//    static function tftm_clear(){
//        global $wpdb;
//
//        $wpdb->query("TRUNCATE TABLE `" . $wpdb->prefix . "tweets`");
//    }

    static function tftm_insert($tweets_data){
        global $wpdb;

        $placeholder='';
        $values='';

        foreach ( $tweets_data as $key => $value ) {
            $placeholder .=  "(%s,%s,%s,%s,%s,%s,%s)," ;
            $values .= implode( ",  ", $value).",  ";
        }

        $placeholder = substr($placeholder, 0, -1);
        $values = substr($values, 0, -3);
        $values = explode(",  ", $values);

        $query = $wpdb->prepare("INSERT INTO `" . $wpdb->prefix . "tweets` (`tweet_theme`,
                                                                            `tweet_id`,
                                                                            `tweet_text`,
                                                                            `tweet_date_created`,
                                                                            `tweet_author_name`,
                                                                            `tweet_author_screen_name`,
                                                                            `tweet_location`) VALUES " . $placeholder, $values);
        $wpdb->query($query);
    }
    static function tftm_update($row_content){
        global $wpdb;

        $date = DateTime::createFromFormat('Y-m-d H:i:s', $row_content[3]);
        if ($date)
        {
            $rez = $wpdb->query($wpdb->prepare("UPDATE `wp_tweets` SET `tweet_theme` = %s,
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
                $row_content[1]));

            if ($rez)
                echo "Update table field was successful";
        }

    }
    static function tftm_select_all($orderby, $order) {
        global $wpdb;

        $tweets_array = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."tweets` ORDER BY `{$orderby}` {$order}", ARRAY_A);

        return $tweets_array;
    }

    static function tftm_select_one_page($orderby, $order, $offset,$per_page) {
        global $wpdb;

        $tweets_array = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."tweets` ORDER BY `{$orderby}` {$order} LIMIT {$offset},{$per_page}", ARRAY_A);

        return $tweets_array;
    }

    static function tftm_select_20(){
        global $wpdb;

        $tweets_array = $wpdb->get_results("SELECT `tweet_text`, `tweet_author_name` FROM `".$wpdb->prefix."tweets` ORDER BY `ID` DESC LIMIT 0,20", ARRAY_A);

        return $tweets_array;
    }

    static function  tftm_select_id($tweet_theme){
        global $wpdb;

        $id_array = $wpdb->get_results("SELECT `tweet_id` FROM `".$wpdb->prefix."tweets` WHERE `tweet_theme`='{$tweet_theme}' ORDER BY `tweet_id`", ARRAY_A);

        return $id_array;
    }

    static function tftm_search($str){
        global $wpdb;

        $str= "%" . $str . "%";

        $query=$wpdb->prepare("SELECT * FROM `" . $wpdb->prefix . "tweets` WHERE `tweet_theme` LIKE %s
                                                                              OR `tweet_text` LIKE %s
                                                                              OR `tweet_author_name` LIKE %s
                                                                              OR `tweet_author_screen_name` LIKE %s",$str,$str,$str,$str);
        //$query = ("SELECT * FROM `" . $wpdb->prefix . "tweets` WHERE `tweet_theme` LIKE '{$str}' OR `tweet_text` LIKE '{$str}' OR `tweet_author_name` LIKE '{$str}' OR `tweet_author_screen_name` LIKE '{$str}'");
        $tweets_array = $wpdb->get_results($query,ARRAY_A);
        return $tweets_array;
    }

    static function tftm_search_one_page($str,$orderby, $order, $offset,$per_page) {
        global $wpdb;

        $str= "%" . $str . "%";

        $query=$wpdb->prepare("SELECT * FROM `" . $wpdb->prefix . "tweets` WHERE `tweet_theme` LIKE %s
                                                                              OR `tweet_text` LIKE %s
                                                                              OR `tweet_author_name` LIKE %s
                                                                              OR `tweet_author_screen_name` LIKE %s
                                                                              ORDER BY %s
                                                                              LIMIT %d,%d",$str,$str,$str,$str,$orderby,$offset,$per_page);
        $tweets_array = $wpdb->get_results($query,ARRAY_A);
        return $tweets_array;
    }
    static function tftm_delete($tweet_id){
        global $wpdb;

        if(is_array($tweet_id))
            $tweet_id = "('".implode( "',  '", $tweet_id)."')";
        else
            $tweet_id = "('". $tweet_id."')";
       $query = "DELETE FROM `" . $wpdb->prefix . "tweets` WHERE `tweet_id` IN". $tweet_id;
       $wpdb->query($query);
    }
}
?>
