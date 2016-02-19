//------------------
//-----Slider-------
//------------------
jQuery(function() {
    jQuery( "#slider" ).slider({
        min:1,
        max:40075,
        value: jQuery( "#radius" ).val(),

        slide: function( event, ui ) {
            jQuery( "#radius" ).val(ui.value);
            circleOptions.center = new google.maps.LatLng(+jQuery("#latitude").val(),  +jQuery("#longitude").val());

            new_circle(circleOptions.center, false, ui.value);
            functions_listeners(true, true, true, true);
        }
    });
//------------------
//-----Map settings-------
//------------------
    jQuery( "#map_title" ).change(function() {
        if(!jQuery("#map_title").val()) {
            jQuery("#dialog #error_theme_empty").css("display", "inline");
            jQuery("#dialog").dialog();
        }
    });

    jQuery( "#radius" ).change(function() {
        if(!jQuery("#radius").val()) {
            jQuery("#dialog #error_radius_empty").css("display", "inline");
            jQuery("#dialog").dialog();
        }
        else if(!jQuery.isNumeric(jQuery("#radius").val())){
            jQuery("#dialog #error_radius_notnumber").css("display", "inline");
            jQuery("#dialog").dialog();
        }
        else if(jQuery("#radius").val()<0 || jQuery("#radius").val()>40075){
            jQuery("#dialog #error_radius_range").css("display", "inline");
            jQuery( "#dialog" ).dialog();
        }
        else {
            jQuery("#slider").slider("value", jQuery("#radius").val());
            circleOptions.center = new google.maps.LatLng(+jQuery("#latitude").val(), +jQuery("#longitude").val());

            new_circle(circleOptions.center, false, +jQuery("#radius").val());
            functions_listeners(true, true, true, true);
        }
    });

    jQuery( "#latitude" ).change(function() {
        if(!jQuery("#latitude").val()) {
            jQuery("#dialog #error_latitude_empty").css("display", "inline");
            jQuery("#dialog").dialog();
        }
        else if(!jQuery.isNumeric(jQuery("#latitude").val())){
            jQuery("#dialog #error_latitude_notnumber").css("display", "inline");
            jQuery("#dialog").dialog();
        }
        else if(jQuery("#latitude").val()<-90 || jQuery("#latitude").val()>90){
            jQuery("#dialog #error_latitude_range").css("display", "inline");
            jQuery( "#dialog" ).dialog();
        }
        else {
            initMap();
        }
    });

    jQuery( "#longitude" ).change(function() {
        if(!jQuery("#longitude").val()) {
            jQuery("#dialog #error_longitude_empty").css("display", "inline");
            jQuery("#dialog").dialog();
        }
        else if(!jQuery.isNumeric(jQuery("#longitude").val())){
            jQuery("#dialog #error_longitude_notnumber").css("display", "inline");
            jQuery("#dialog").dialog();
        }
        else if(jQuery("#longitude").val()<-180 || jQuery("#longitude").val()>180){
            jQuery("#dialog #error_longitude_range").css("display", "inline");
            jQuery( "#dialog" ).dialog();
        }
        else {
            initMap();
        }
    });

    jQuery( "#dialog" ).on( "dialogclose", function( event, ui ) {
        jQuery("#dialog p").css("display", "none");
    } );

    jQuery( "#dialog-message" ).dialog({
        modal: true,
        buttons: {
            Ok: function() {
                $( this ).dialog( "close" );
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

    var latLng = new google.maps.LatLng(+jQuery("#latitude").val(), +jQuery("#longitude").val());

    mapOptions.center = latLng;
    mapOptions.mapTypeId = google.maps.MapTypeId.TERRAIN;
    map = new google.maps.Map(document.getElementById('map'), mapOptions);

    new_marker(latLng, new_map=true);
    new_circle(latLng, new_map=true, +jQuery("#radius").val());
    functions_listeners(true, true, true, true);

};

function showNewCircle(){

    jQuery( "#radius" ).val(circle.getRadius());
    jQuery( "#slider" ).slider( "value", circle.getRadius() );

};

function newMarkerCircle(event) {

    new_marker(event.latLng, false);
    new_circle(event.latLng, false, jQuery("#slider").slider("value"));
    functions_listeners(false, true, true, true);

    jQuery("#latitude").val(event.latLng.lat());
    jQuery("#longitude").val(event.latLng.lng());


};

function markerMove(event) {

    new_circle(event.latLng, false, +jQuery("#radius").val());
    functions_listeners(true, true, false, true);

    jQuery("#latitude").val(event.latLng.lat());
    jQuery("#longitude").val(event.latLng.lng());

};

function circleMove() {

    new_marker(circle.center, false);
    functions_listeners(true, true, true, false);

    jQuery( "#latitude" ).val(circle.center.lat());
    jQuery( "#longitude" ).val(circle.center.lng());

};

function new_marker(latlng, new_map) {

    markerOptions.position = latlng;
    if (!new_map)
        marker.setMap(null);
    marker = new google.maps.Marker(markerOptions);
    marker.setMap(map);

};

function new_circle(latlng, new_map, radius) {

    circleOptions.radius = radius;
    circleOptions.center = latlng;
    if (!new_map)
        circle.setMap(null);
    circle = new google.maps.Circle(circleOptions);
    circle.setMap(map);

};

function functions_listeners(map_click, radius_changed, marker_dragend, circle_dragend){
    if(map_click)
        google.maps.event.addListener(map, 'click', newMarkerCircle);
    if(radius_changed)
        circle.addListener('radius_changed', showNewCircle);
    if(marker_dragend)
        marker.addListener('dragend', markerMove);
    if(circle_dragend)
        circle.addListener('dragend', circleMove);
};

//------------------
//-----Load map-----
//------------------
function loadScript() {
    var script = document.createElement("script");
    script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=initMap";
    document.body.appendChild(script);
};
window.onload = loadScript;



//console.log(marker.getPosition().lng());
//debugger;


