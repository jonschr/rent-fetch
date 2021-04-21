jQuery(document).ready(function ($) {

    function toggleTheMap() {
        $('.map-response-wrap').toggleClass('toggle-map-off');
    }

    function waitThenGo() {
        setTimeout(reLoadSlick, 50);
    }

    function reLoadSlick() {

        // $('.property-slider').slick('unslick');
        // $('.property-slider').slick({
        //     dots: true,
        //     infinite: false,
        //     arrows: true,
        //     speed: 500,
        //     fade: true,
        //     cssEase: 'linear',
        //     lazyLoad: 'ondemand',
        // });

        // $('.property-slider').slick('slickGoTo', 2);
        $('.property-slider').slick('reinit');
    }

    $('.toggle').on('click', toggleTheMap);
    $('.toggle').on('click', waitThenGo);


});