<?php

    require_once("../../../../wp-load.php");

/**
 * Class TFTM_Download_Tweets
 */
class TFTM_Download_Tweets{

    /**
     * Contain file extension
     *
     * @var
     */
    private $file_type;

    /**
     * Contain ful file name with extension
     *
     * @var
     */
    private $file_name;

    /**
     * Contain all tweets from DB
     *
     * @var
     */
    private $tweets_data;

    /**
     * TFTM_Download_Tweets constructor.
     */
    public function __construct() {
        ob_end_clean();
        if( isset( $_GET['type_file'] ) ) {
            $this->set_file();
            $this->create_headers();
            $this->get_data();
            $this->generate_file();
        }
    }

    /**
     * Get file extension and set file parameters
     */
    private function set_file() {
        $this->file_type = $_GET['type_file'];
        $this->file_name = 'TableOfTweets.' . $this->file_type;
    }

    /**
     * Set page headers
     */
    private function create_headers() {
        header( 'Content-Description: File Transfer' );
        header( 'Content-Type: application/octet-stream' );
        header( 'Content-Disposition: attachment; filename=' . $this->file_name );
        header( 'Expires: 0' );
        header( 'Cache-Control: must-revalidate' );
        header( 'Pragma: public' );
    }

    /**
     * Get tweets from DB
     */
    private function get_data() {
        $this->tweets_data = TFTM_BD_Queries::get_all_tweets( "ID", "DESC" );
    }

    /**
     * Define the function of generating a file, depending on the required file extension
     */
    private function generate_file() {
        switch ( $this->file_type ) {
            case 'csv':
                $this->generate_file_csv();
                break;
            case 'xml':
                $this->generate_file_xml();
                break;
        }
    }

    /**
     * Generate and download csv file
     */
    private function generate_file_csv() {
        $out = fopen( 'php://output', 'w' );
        foreach ( $this->tweets_data as $fields ) {
            fputcsv( $out, $fields );
        }
        fclose( $out );
    }

    /**
     * Generate and download xml file
     */
    private function generate_file_xml() {
        $doc = new DOMDocument( '1.0' );
        $doc->formatOutput = true;
        $TableOfTweets = $doc->createElement( 'TableOfTweets' );
        $TableOfTweets = $doc->appendChild( $TableOfTweets );

        foreach ( $this->tweets_data as $key => $row )
        {
            $current_row = $doc->createElement( 'TweetContent' );
            $TableOfTweets->appendChild( $current_row );

            foreach ( $row as $key => $value ) {
                $em = $doc->createElement( $key );
                $text = $doc->createTextNode( $value );
                $em->appendChild( $text );
                $current_row->appendChild( $em );
            }
        }
        $doc->save( 'php://output' );
    }
}

$create_file = new TFTM_Download_Tweets();
?>