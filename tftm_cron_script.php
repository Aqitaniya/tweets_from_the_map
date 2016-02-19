<?php
function tftm_update_table_tweets(){

    $tweets_data=get_tweets(get_option('tweets_maps_settings'));
    if($tweets_data!=null)
        tftm_settings_bd::tftm_insert($tweets_data);
}
?>