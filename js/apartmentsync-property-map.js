jQuery(document).ready(function ($) {

    var map;
    var locationsArray = [];
    var markers = [];

    //* Vars from localization
    // grab the marker image from localization
    var markerImage = options.marker_url;

    // grab the styles from localization and convert the php array to json
    var mapStyle = options.json_style;
    mapsStyle = JSON.stringify(mapStyle);

    function renderMap() {

        var myLatlng = new google.maps.LatLng(39.8484006327939, -104.99522076837074);
        var mapOptions = {
            zoom: 10,
            center: myLatlng,
            styles: mapStyle,
            disableDefaultUI: true, // removes the satellite/map selection (might also remove other stuff)
            // scaleControl: true,
            zoomControl: true,
            zoomControlOptions: {
                position: google.maps.ControlPosition.RIGHT_TOP,
            },
            fullscreenControl: false,
        }
        map = new google.maps.Map(document.getElementById("map"), mapOptions);
    }

    function getLocations() {

        // reset the array
        locationsArray = [];

        // get the positions
        $('#response .type-properties').each(function () {
            lat = $(this).attr('data-latitude');
            long = $(this).attr('data-longitude');
            title = $(this).find('h3').text();
            content = $(this).find('.property-content').html();
            image = $(this).find('.property-images-wrap').attr('data-image-url');
            url = $(this).attr('data-url');
            id = $(this).attr('data-id');
            locationsArray.push([lat, long, title, content, image, url, id]);
        });

    }

    function addMarkers() {

        var bounds = new google.maps.LatLngBounds();

        for (let i = 0; i < locationsArray.length; i++) {

            var latitude = locationsArray[i][0];
            var longitude = locationsArray[i][1];
            var title = locationsArray[i][2];
            var content = locationsArray[i][3];
            var imageurl = locationsArray[i][4];
            var url = locationsArray[i][5];
            var id = locationsArray[i][6];
            var label = title;
            var theposition = new google.maps.LatLng(latitude, longitude);

            if (typeof markerImage !== 'undefined') {

                // if there's a custom marker set, use that
                var marker = new google.maps.Marker({
                    position: theposition,
                    map: map,
                    title: title,
                    icon: markerImage,
                });

            } else {

                // if there's no custom icon, just use the google default
                var marker = new google.maps.Marker({
                    position: theposition,
                    map: map,
                    title: title,
                });

            }

            bounds.extend(theposition);
            map.fitBounds(bounds);

            marker['infowindow'] = new google.maps.InfoWindow({
                content: '<div class="map-property-popup" id="overlay-' + id + '"><a class="overlay" href="' + url + '"></a><a href="' + url + '" class="image"><img src="' + imageurl + '"/></a>' + content + '</div>',
            });

            google.maps.event.addListener(marker, 'click', function () {

                for (let i = 0; i < markers.length; i++) {
                    markers[i]['infowindow'].close(map, this);
                }

                this['infowindow'].open(map, this);

            });

            markers.push(marker);

        }

    }

    function resetMap() {
        renderMap();
        getLocations();
        addMarkers();
    }

    $(document).on('ajaxComplete', resetMap);
    $('.toggle').on('click', resetMap);

});