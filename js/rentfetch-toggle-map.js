jQuery(document).ready(function ($) {

    function toggleTheMap() {
        $('.map-response-wrap').toggleClass('toggle-map-off');
    }

    function waitThenGo() {
        setTimeout(reLoadSlick, 100);
    }

    function reLoadSlick() {
        $('.property-slider').slick('refresh');
    }

    $('.toggle').on('click', toggleTheMap);
    $('.toggle').on('click', waitThenGo);


});