jQuery(document).ready(function ($) {

    //* Submission events
    $('a.apply').click(function (e) {
        e.preventDefault();
        $('#filter').submit();
        $(this).parents('.dropdown-menu').removeClass('show');
    });

    //* submit the form
    function submitTheForm() {
        $('#filter').submit();
    }

    // on load, do these functions
    submitTheForm();

});