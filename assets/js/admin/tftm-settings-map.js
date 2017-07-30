var $j = jQuery.noConflict();

//-----------------------------------
//-----Slider------------------------
//-----------------------------------
$j( function(){
    $j( "#slider" ).slider( {
        min: 1,
        max: 40075,
        value: $j( "#radius" ).val(),

        slide: function( event, ui ) {
            $j( "#radius" ).val( ui.value );
            circleOptions.center = new google.maps.LatLng( +$j( "#latitude" ).val(), +$j( "#longitude" ).val() );

            new_circle( circleOptions.center, false, ui.value );
            functions_listeners(true, true, true, true);
        }
    });
//-----------------------------------
//-----Map settings validation-------
//-----------------------------------
    $j( "#map_title" ).change( function() {
        if ( ! $j( "#map_title" ).val() ) {
               $j( "#dialogMap #error_theme_empty" ).css( "display", "inline" );
               $j( "#dialogMap" ).dialog();
        }
    });

    $j( "#radius" ).change( function() {
        if ( ! $j( "#radius" ).val() ) {
               $j( "#dialogMap #error_radius_empty" ).css( "display", "inline" );
               $j( "#dialogMap" ).dialog();
        } else if ( ! $j.isNumeric( $j( "#radius" ).val() ) ) {
            $j( "#dialogMap #error_radius_notnumber" ).css( "display", "inline" );
            $j( "#dialogMap" ).dialog();
        } else if ( $j( "#radius" ).val() < 0 || $j( "#radius" ).val() > 40075) {
            $j( "#dialogMap #error_radius_range" ).css( "display", "inline" );
            $j( "#dialogMap" ).dialog();
        } else {
            $j( "#slider" ).slider( "value", $j( "#radius" ).val() );
            circleOptions.center = new google.maps.LatLng( +$j( "#latitude" ).val(), +$j( "#longitude" ).val() );

            new_circle( circleOptions.center, false, +$j( "#radius" ).val() );
            functions_listeners( true, true, true, true );
        }
    });

    $j( "#latitude" ).change( function() {
        if ( ! $j( "#latitude" ).val() ) {
               $j( "#dialogMap #error_latitude_empty" ).css( "display", "inline" );
               $j( "#dialogMap" ).dialog();
        } else if ( ! $j.isNumeric( $j( "#latitude" ).val() ) ) {
            $j( "#dialogMap #error_latitude_notnumber" ).css( "display", "inline" );
            $j( "#dialogMap" ).dialog();
        } else if ( $j( "#latitude" ).val() < -90 || $j( "#latitude" ).val() > 90 ) {
            $j( "#dialogMap #error_latitude_range" ).css( "display", "inline" );
            $j( "#dialogMap" ).dialog();
        } else {
            initMap();
        }
    });

    $j( "#longitude" ).change( function() {
        if ( ! $j( "#longitude" ).val() ) {
              $j( "#dialogMap #error_longitude_empty" ).css( "display", "inline" );
              $j( "#dialogMap" ).dialog();
        } else if ( ! $j.isNumeric( $j( "#longitude" ).val() ) ) {
            $j( "#dialogMap #error_longitude_notnumber" ).css( "display", "inline" );
            $j( "#dialogMap" ).dialog();
        } else if ( $j( "#longitude" ).val() < -180 || $j( "#longitude" ).val() > 180) {
            $j( "#dialogMap #error_longitude_range" ).css( "display", "inline" );
            $j( "#dialogMap" ).dialog();
        } else {
            initMap();
        }
    });

    $j( "#dialogMap" ).on( "dialogclose", function( event, ui ) {
        $j( "#dialogMap p" ).css( "display", "none" );
    });

    $j( "#dialogMap-message" ).dialog({
        modal: true,
        buttons: {
            Ok: function() {
                $j( this ).dialog( "close" );
            }
        }
    });
});

//-------------------
//-----New map-------
//-------------------
var map, circle, marker;

var mapOptions = {
    zoom: 9,
};

var markerOptions = {
    map: map,
    draggable: true,
};

var circleOptions = {
    map: map,
    fillColor: "#00AAFF",
    fillOpacity: 0.5,
    strokeColor: "#CCFFFF",
    strokeOpacity: 0.8,
    strokeWeight: 5,
    editable: true,
    draggable: true,
    clickable: true,
};

function initMap() {
    if ( jQuery( 'div' ).is( "#map" ) ) {
        var latLng = new google.maps.LatLng(+jQuery("#latitude").val(), +jQuery("#longitude").val());

        mapOptions.center = latLng;
        mapOptions.mapTypeId = google.maps.MapTypeId.TERRAIN;
        map = new google.maps.Map(document.getElementById('map'), mapOptions);

        new_marker(latLng, new_map = true);
        new_circle(latLng, new_map = true, +jQuery("#radius").val());
        functions_listeners(true, true, true, true);
    }
};

function showNewCircle() {
    jQuery( "#radius" ).val( circle.getRadius() );
    jQuery( "#slider" ).slider( "value", circle.getRadius() );
};

function newMarkerCircle( event ) {
    new_marker( event.latLng, false );
    new_circle( event.latLng, false, jQuery( "#slider" ).slider( "value" ) );
    functions_listeners( false, true, true, true );

    jQuery( "#latitude" ).val( event.latLng.lat() );
    jQuery( "#longitude" ).val( event.latLng.lng() );
};

function markerMove( event ) {
    new_circle( event.latLng, false, +jQuery( "#radius" ).val() );
    functions_listeners( true, true, false, true );

    jQuery( "#latitude" ).val( event.latLng.lat() );
    jQuery( "#longitude" ).val( event.latLng.lng() );
};

function circleMove() {
    new_marker( circle.center, false );
    functions_listeners( true, true, true, false );

    jQuery( "#latitude" ).val( circle.center.lat() );
    jQuery( "#longitude" ).val( circle.center.lng() );
};

function new_marker( latlng, new_map ) {
    markerOptions.position = latlng;
    if ( ! new_map ) {
        marker.setMap( null );
    }
    marker = new google.maps.Marker( markerOptions );
    marker.setMap( map );
};

function new_circle( latlng, new_map, radius ) {
    circleOptions.radius = radius;
    circleOptions.center = latlng;
    if ( ! new_map ){
        circle.setMap( null );
    }
    circle = new google.maps.Circle( circleOptions );
    circle.setMap( map );
};

function functions_listeners( map_click, radius_changed, marker_dragend, circle_dragend ) {
    if( map_click ) {
        google.maps.event.addListener( map, 'click', newMarkerCircle );
    }
    if( radius_changed ) {
        circle.addListener( 'radius_changed', showNewCircle );
    }
    if( marker_dragend ) {
        marker.addListener( 'dragend', markerMove );
    }
    if( circle_dragend ) {
        circle.addListener( 'dragend', circleMove );
    }
};