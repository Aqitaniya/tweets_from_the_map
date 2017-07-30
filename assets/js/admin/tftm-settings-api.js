var $j = jQuery.noConflict();
$j(function() {
//-----------------------------------
//-----Api settings validation-------
//-----------------------------------
    $j( "#twitter_consumer_key" ).change( function() {
        if ( !$j( "#twitter_consumer_key" ).val() ) {
             $j( "#dialogApi #error_twitter_consumer_key_empty" ).css( "display", "inline" );
             $j( "#dialogApi" ).dialog();
        }
    });

    $j( "#twitter_consumer_secret" ).change( function() {
        if ( !$j( "#twitter_consumer_secret" ).val() ) {
             $j( "#dialogApi #error_twitter_consumer_secret_empty" ).css( "display", "inline" );
             $j( "#dialogApi" ).dialog();
        }
    });

    $j( "#twitter_oauth_token" ).change( function() {
        if ( !$j( "#twitter_oauth_token" ).val() ) {
             $j( "#dialogApi #error_twitter_oauth_token_empty" ).css( "display", "inline" );
             $j( "#dialogApi" ).dialog();
        }
    });

    $j( "#twitter_oauth_secret" ).change( function() {
        if ( !$j( "#twitter_oauth_secret" ).val() ) {
             $j( "#dialogApi #error_twitter_oauth_secret_empty" ).css( "display", "inline" );
             $j( "#dialogApi" ).dialog();
        }
    });

    $j( "#google_api_key" ).change( function() {
        if ( !$j( "#google_api_key" ).val() ) {
             $j( "#dialogApi #error_google_api_key_empty" ).css( "display", "inline" );
             $j( "#dialogApi" ).dialog();
        }
    });

    $j( "#dialogApi" ).on( "dialogclose", function( event, ui ) {
        $j("#dialogApi p").css( "display", "none" );
    } );

    $j( "#dialogApi-message" ).dialog( {
        modal: true,
        buttons: {
            Ok: function() {
                $j( this ).dialog( "close" );
            }
        }
    });
//-----------------------------------
//-----Api settings tabs-------
//-----------------------------------
    $j( ".tabs-menu a" ).click( function( event ) {
        event.preventDefault();
        $j( this ).parent().addClass( "current" );
        $j( this ).parent().siblings().removeClass( "current" );
        var tab = $j( this ).attr( "href" );
        $j( ".tab-content" ).not( tab ).css( "display", "none" );
        $j( tab ).fadeIn();
    });
});