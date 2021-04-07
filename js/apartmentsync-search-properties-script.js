jQuery(document).ready(function ($) {

    // onload, hide the dropdowns
    $('.dropdown-menu').removeClass('show');

    // when a dropdown toggle is clicked, toggle the dropdown menu next to it
    $('.dropdown button').click(function () {
        $('.dropdown-menu').removeClass('show');
        $(this).siblings('.dropdown-menu').toggleClass('show')
    });

    // if the target of the click isn't the container nor a descendant of the container
    $(document).mouseup(function (e) {
        var container = $('.dropdown-menu');
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.removeClass('show');
        }
    });

    // when something changes, submit
    $('#filter input').change(function () {
        $('#filter').submit();
    });

    // when the form is reset, trigger a submit
    $('#filter').on('reset', function () {
        $('#filter').submit();
    });

    // submit the form when we load the page
    window.onload = function () {
        $('#filter').submit();
    }

    $('.filter-application .clear').click(function (e) {
        e.preventDefault();
        // $('input[type="checkbox"]').prop("checked", false);
        $(this).closest('.dropdown-menu').children('input[type="checkbox"]').prop("checked", false);
    });

});