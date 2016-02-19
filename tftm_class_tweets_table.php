<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Tftm_List_Table extends WP_List_Table  {

    function __construct(){
        global $status, $page;

        parent::__construct( array(
            'singular'  => 'tweet',
            'plural'    => 'tweets',
            'ajax'      => false
        ) );


    }

    function column_default( $item, $column_name )
    {
        switch ($column_name) {
            case 'tweet_theme':
            case 'tweet_id':
            case 'tweet_text':
            case 'tweet_date_created':
            case 'tweet_author_name':
            case 'tweet_author_screen_name':
            case 'tweet_location':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function column_tweet_theme($item){

        $actions = array(
            'edit'      => sprintf('<a class="edit-row" href="#">Edit</a>'),
            'delete'    => sprintf('<a href="?page=%s&action=%s&tweet=%s">Delete</a>',$_REQUEST['page'],'delete',$item['tweet_id']),
        );

        return sprintf('<div>%1$s</div>%3$s<div class="edit_row_fields"></div>' ,
            $item['tweet_theme'],
            $item['tweet_id'],
            $this->row_actions($actions)
        );
    }
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                 $this->_args['singular'],
                 $item['tweet_id']
        );
    }

    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'tweet_theme'               => __('Theme'),
            'tweet_id'                  => __('Tweet ID'),
            'tweet_text'                => __('Text'),
            'tweet_date_created'        => __('Date created'),
            'tweet_author_name'         => __('Author'),
            'tweet_author_screen_name'  => __('Author screen name'),
            'tweet_location'            => __('Location'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'tweet_theme'     => array('tweet_theme',false),
            'tweet_date_created'  => array('tweet_date_created',false),
        );
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action() {

        if( 'delete'===$this->current_action() ) {
            tftm_settings_bd::tftm_delete($_GET['tweet']);
        }
        if( 'edit'===$this->current_action() ) {

        }

    }

    function prepare_items() {

        $per_page = $this->get_items_per_page('tweets_per_page', 5);

        $current_page = $this->get_pagenum();
        $offset=($current_page-1)*$per_page;
        $orderby = !empty($_GET['orderby']) ? $_GET['orderby'] : 'ID';
        $order = !empty($_GET['order']) ? $_GET['order'] : 'DESC';

        if(!empty($_POST['s'])) {
            update_option('tweets_search', $_POST['s']);
        }

        $_REQUEST['s']=get_option('tweets_search');

        $columns = $this->get_columns();
        $hidden = array();
        $sortable =  $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        //if(empty(get_option('tweets_search'))) {
        if(get_option('tweets_search')=="") {
            $data_all = $this->items = tftm_settings_bd::tftm_select_all($orderby,$order);
            $total_items = count($data_all);
            $data = tftm_settings_bd::tftm_select_one_page($orderby, $order, $offset, $per_page);
            $this->items = $data;
        }else{
            $data_all = $this->items = tftm_settings_bd::tftm_search( get_option('tweets_search')) ;

            $total_items = count($data_all);
            $data = tftm_settings_bd::tftm_search_one_page(get_option('tweets_search'),$orderby, $order, $offset,$per_page);
            $this->items = $data;
        }
        $this->set_pagination_args(
            array(
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil($total_items/$per_page)
            )
        );
    }

}

?>