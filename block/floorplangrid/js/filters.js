jQuery(document).ready(function ($) {

    //* Get the floorplans parameter in case we need it
    var urlParams = new URLSearchParams(window.location.search);
    var currentOnLoad = urlParams.get('filter');

    if (currentOnLoad) {
        updatePlans(currentOnLoad);
        updateButton(currentOnLoad);
    }

    $('.filter-select').click(function (e) {
        e.preventDefault();
        var current = $(this).data('filter');
        updatePlans(current);
        updateButton(current);
    });

    function updatePlans(current) {

        console.log(current);

        $('.floorplangrid .floorplans').hide();
        $('.floorplangrid .' + current).show();

        if ('URLSearchParams' in window) {
            var searchParams = new URLSearchParams(window.location.search);
            searchParams.set("filter", current);
            window.history.pushState("", "", "?filter=" + current);
        }

    }

    //* Update the active class on the button
    function updateButton(current) {
        $('.filter-select').removeClass('active');
        $('[data-filter=' + current + ']').addClass('active');
    }

    //* Update the URL
    function UpdateQueryString(key, value, url) {
        if (!url) url = window.location.href;
        var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
            hash;

        if (re.test(url)) {
            if (typeof value !== 'undefined' && value !== null) {
                return url.replace(re, '$1' + key + "=" + value + '$2$3');
            }
            else {
                hash = url.split('#');
                url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
                if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
                    url += '#' + hash[1];
                }
                return url;
            }
        }
        else {
            if (typeof value !== 'undefined' && value !== null) {
                var separator = url.indexOf('?') !== -1 ? '&' : '?';
                hash = url.split('#');
                url = hash[0] + separator + key + '=' + value;
                if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
                    url += '#' + hash[1];
                }
                return url;
            }
            else {
                return url;
            }
        }
    }

});

