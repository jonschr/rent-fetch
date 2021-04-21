jQuery(document).ready(function ($) {

    function loadSlick() {
        $('.property-slider').slick({
            dots: true,
            infinite: false,
            arrows: true,
            speed: 500,
            fade: true,
            cssEase: 'linear',
            lazyLoad: 'ondemand',
        });
    }

    $(document).on('ajaxComplete', loadSlick);
    $(window).on('load', loadSlick);

});
