<?php
add_action('admin_menu', 'register_my_custom_submenu_page');

function register_my_custom_submenu_page() {
    $hook=add_submenu_page( dirname(__FILE__) . '/tftm_settings_map.php', 'List tweets', 'List tweets', 'manage_options', 'list_tweets','my_custom_submenu_page_callback');
    add_action("load-$hook",'add_options');
}

function add_options() {
    $option = 'per_page';
    $args = array(
        'label' => 'Tweets',
        'default' => 5,
        'option' => 'tweets_per_page'
    );
    add_screen_option( $option, $args );

}

add_filter('set-screen-option', 'tweets_table_set_option', 10, 3);
function tweets_table_set_option($status, $option, $value) {
    return $value;
}

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    if(isset($_POST['clear_search'])) {
        update_option('tweets_search', '');
    }
    if(isset($_POST['row_content'])) {
        tftm_settings_bd::tftm_update($_POST['row_content']);
    }
}

function my_custom_submenu_page_callback() {
    $tweets_list_table = new Tftm_List_Table();
    $tweets_list_table->prepare_items();
    ?>
    <div class="wrap">
        <h2>Table of tweets</h2>

        <a href='<?php echo plugin_dir_url(__FILE__); ?>tftm_download.php?type_file=csv'>Download file in csv formst (TableOfTweets.csv)</a><br>
        <a href='<?php echo plugin_dir_url(__FILE__); ?>tftm_download.php?type_file=xml'>Download file in xml formst (TableOfTweets.xml)</a>


        <form  id="table_form" method="post">
            <input type="hidden" name="page" value="" />
            <?php $tweets_list_table->search_box('search', 'search_tweets'); ?>
        </form>
        <form id="twwets-filter" method="get">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <?php $tweets_list_table->display() ?>
        </form>

    </div>
    <?php
}

require "twitteroauth/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;
//echo 'hhhhhhhhh';
//$tweets_data=get_tweets(get_option('tweets_maps_settings'));
//var_dump($tweets_data);
function get_tweets($maps_settings){

    define("CONSUMER_KEY", "e7zWUJ5zVnGY9ZQQEfEYKj3f0");
    define("CONSUMER_SECRET", "IftcYgdQ23qFwh7YRlpQGUzdSrdzItXev7UtBP4WbeX0JCMmPC");
    define("OAUTH_TOKEN", "4257362595-fi4x38wogRRHj62Fj0EKHqb5ZUH56xdhjj3bhA0");
    define("OAUTH_SECRET", "6ONZsPi7WzZMbd4PVSAguVMKmGLWA3BanF3zPfrxTPxQp");

    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);

    $list_tweets = $connection->get("search/tweets",array('q' => $maps_settings['theme'], "geocode" => $maps_settings['latitude'].','.$maps_settings['longitude'].','.$maps_settings['radius'].'km',"count" => '200'));

    //Get tweeta in JSON format
    $array_tweets=$list_tweets->statuses;

    if($array_tweets) {
        foreach ($array_tweets as $key => $status) {
            $tweets_data[$key] = array(
                'theme' => $maps_settings['theme'],
                'id' => $status->id,
                'text' => wp_encode_emoji(htmlspecialchars($status->text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')),
                'tweets_data' => mysql2date('Y-m-d H:i:s', $status->created_at),
                'author_name' => $status->user->name,
                'author_screen_name' => wp_encode_emoji(htmlspecialchars($status->user->screen_name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')),
                'location' => $status->user->location,
            );
        };

        $tweet_id = tftm_settings_bd::tftm_select_id($maps_settings['theme']);

        if($tweet_id){
            foreach($tweet_id as $key => $id){
                foreach($tweets_data as $key2 => $new_tweet_id){
                  if($id['tweet_id']==$new_tweet_id['id']){
                      unset($tweets_data[$key2]);
                      unset($tweet_id[$key]);
                      break(1);
                  }
                }
            }

        }

    return $tweets_data;
    }
    else
        return null;
    };
?>