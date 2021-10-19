jQuery(document).ready(function ($) {

    //* Vars from localization
    // grab the marker image from localization
    var markerImage = options.marker_url;

    // grab the styles from localization and convert the php array to json
    var mapStyle = options.json_style;
    mapsStyle = JSON.stringify(mapStyle);

    var latitude = parseFloat(options.latitude);
    var longitude = parseFloat(options.longitude);

    var location = options.location;

    function initMap() {
        const myLatLng = { lat: latitude, lng: longitude };
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 12,
            center: myLatLng,
            styles: mapStyle,
            fullscreenControl: false,
            disableDefaultUI: true,
            zoomControl: true,
            zoomControlOptions: {
                position: google.maps.ControlPosition.RIGHT_TOP,
            },
        });

        const contentString =
            '<div id="map-marker">' +
            location +
            '</div>';

        const infowindow = new google.maps.InfoWindow({
            content: contentString,
        });

        const marker = new google.maps.Marker({
            position: myLatLng,
            map,
            title: "Hello World!",
            icon: markerImage,
        });

        infowindow.open({
            anchor: marker,
            map,
            shouldFocus: false,
        });

        marker.addListener("click", () => {
            infowindow.open({
                anchor: marker,
                map,
                shouldFocus: false,
            });
        });

    }

    initMap();


});