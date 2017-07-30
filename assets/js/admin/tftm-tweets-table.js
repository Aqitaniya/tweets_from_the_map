var $j = jQuery.noConflict();
$j( function() {
    $j( "#search_tweets-search-input" ).change( function() {
        if( $j( this ).val() == '') {
            var data = {
                'action': 'clear_search',
                clear_search:'none'
            };

            $j.post( ajaxurl, data, function() {
                window.location.reload( true );
            });
        }
    });

    $j( ".edit a" ).on( 'click', function() {
        var current_this = $j( this );
        var row_content = Array();
        var colls = $j( this ).closest( 'tr' ).find( 'td' ).length;
        if( $j( this ).html() == 'Edit' ) {
            $j( this ).html( 'Save' );
            row_content[0]= $j( this ).closest( 'tr' ).find( 'td:nth-child(' + 2 + ') div:nth-child(1)').text();
            $j( this ).closest( 'tr' ).find( 'td:nth-child(' + 2 + ') div:nth-child(1)' ).empty().append( '<input type="text" id="'+0+'" value="'+row_content[0]+'"/>');

            for( var i=1; i < colls; i++ ) {
                row_content[i] = $j( this ).closest( 'tr' ).find( 'td:nth-child(' + (i+2) + ')' ).html();
                $j( this ).closest( 'tr' ).find( 'td:nth-child(' + (i+2) + ')' ).empty().append( '<input type="text" id="' + i + '" value=""/>' );
                $j( this ).closest( 'tr' ).find( 'td:nth-child(' + (i+2) + ')' ).find( '#'+i+'' ).val( row_content[i] );
            }
            $j( this ).closest( 'tr' ).find( 'td:nth-child(3) input' ).attr( 'disabled','disabled' );

            $j.cookie( 'row_content_old', row_content.join(',') );
        }
        else if( $j( this ).html() == 'Save') {
            for ( var i = 0; i < colls; i++ )
                row_content[i] = $j( this ).closest( 'tr' ).find( 'td:nth-child(' + (i+2) + ')' ).find( '#'+i+'' ).val();
            var data = {
                'action': 'tweets_update',
                'row_content': row_content
            };
            $j.post( ajaxurl, data, function( data ) {
                var str=data.indexOf( 'Update table field was successful' );
                if ( str != -1 ) {
                    current_this.closest( 'tr' ).find( 'td:nth-child(2) div:nth-child(1)' ).empty().append( row_content[0] );
                    for ( var i = 1; i < colls; i++ ) {
                        current_this.closest( 'tr' ).find( 'td:nth-child(' + (i+2) + ')' ).empty().append( row_content[i] );
                    }
                    current_this.closest( 'td' ).find( '.row-actions .edit a' ).html( 'Edit' );
                } else {
                    row_content = $j.cookie( 'row_content_old' ).split( /,/ );
                    current_this.closest( 'tr' ).find( 'td:nth-child(2) div:nth-child(1)' ).empty().append( row_content[0] );
                    for( var i = 1; i < colls; i++ ) {
                        current_this.closest( 'tr' ).find( 'td:nth-child(' + (i + 2) + ')' ).empty().append( row_content[i] )
                    }
                    current_this.closest( 'td' ).find( '.row-actions .edit a' ).html( 'Edit' );
                }
            });
        }
    });

    $j( ".edit_row_fields" ).on( 'click', '.close-edit-row', function() {
        $j( this ).closest( 'td' ).find( '.row-actions .edit a' ).html( 'Edit' );
        $j( this ).closest( 'div' ).empty();
    });
});