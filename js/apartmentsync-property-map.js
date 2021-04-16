jQuery(document).ready(function ($) {


    var map;
    var bounds = new google.maps.LatLngBounds();

    function initMap() {

        // grab the styles from localization and convert the php array to json
        var mapStyle = options.json_style;
        mapsStyle = JSON.stringify(mapStyle);

        var myLatlng = new google.maps.LatLng(39.8484006327939, -104.99522076837074);
        var mapOptions = {
            zoom: 10,
            center: myLatlng,
            styles: mapStyle,
            disableDefaultUI: true, // removes the satellite/map selection (might also remove other stuff)
            // scaleControl: true,
            zoomControl: true,
            zoomControlOptions: {
                position: google.maps.ControlPosition.LEFT_TOP,
            },
            fullscreenControl: false,
        }
        map = new google.maps.Map(document.getElementById("map"), mapOptions);
    }

    function getLocations() {

        // empty array for global
        var global = [];

        $('.type-properties').each(function () {
            lat = $(this).attr('data-latitude');
            long = $(this).attr('data-longitude');
            global.push([lat, long]);
        });

        markerArray = [];

        for (var i = 0; i < global.length; i++) {
            console.log(global[i]);
            markerArray[i] = addMarker(global[i][2], global[i][0], global[i][1], map);
        }

        // for (var i = 0; i < markerArray.length; i++) {
        //     bounds.extend(markerArray[i]);
        // }


    }

    function addMarker(title, x, y, map) {

        position = new google.maps.LatLng(x, y);

        var marker = new google.maps.Marker({
            position: position,
            map: map,
            title: title
        });

        infowindow = new google.maps.InfoWindow({
            content: 'Hello world!',
        });

        marker.addListener("click", () => {
            infowindow.open(map, marker);
        });

        bounds.extend(position);
        map.fitBounds(bounds);

        return marker;
    }

    $(window).on('load', initMap);
    $(window).on('ajaxComplete', getLocations);


});