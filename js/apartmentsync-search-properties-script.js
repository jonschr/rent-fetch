jQuery(document).ready(function ($) {

    //* Dropdown showing/hiding

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

    //* Submission events
    $('a.apply').click(function (e) {
        e.preventDefault();
        $('#filter').submit();
        $(this).parents('.dropdown-menu').removeClass('show');
    });

    // when the form is reset, trigger a submit
    $('#filter').on('reset', function () {
        $('#filter').submit();
    });

    //* submit the form
    function submitTheForm() {
        $('#filter').submit();
    }

    //* text for baths button
    function importBathsToButton() {

        var inputs = $('.input-wrap-baths input');
        var button = $('.input-wrap-baths button');
        var bathsArray = [];

        inputs.each(function () {
            if (this.checked) {
                bathNumber = $(this).attr('data-baths');
                bathsArray.push(bathNumber);
            }
        });

        if (jQuery.isEmptyObject(bathsArray) == false) {
            var text = bathsArray.join(', ');
            text = text + ' Bathroom';
            button.text(text);
            button.addClass('active');
        } else {
            text = button.attr('data-reset');
            button.text(text);
            button.removeClass('active');
        }
    }

    //* text for beds button
    function importBedsToButton() {

        var inputs = $('.input-wrap-beds input');
        var button = $('.input-wrap-beds button');
        var bedsArray = [];

        inputs.each(function () {
            if (this.checked) {
                bathNumber = $(this).attr('data-beds');
                bedsArray.push(bathNumber);
            }
        });

        if (jQuery.isEmptyObject(bedsArray) == false) {
            var text = bedsArray.join(', ');
            text = text + ' Bedroom';
            button.text(text);
            button.addClass('active');
        } else {
            text = button.attr('data-reset');
            button.text(text);
            button.removeClass('active');
        }
    }

    function clearDropdown() {
        event.preventDefault();
        $(this).closest('.dropdown-menu').find('input[type="checkbox"]').prop("checked", false);
        var button = $(this).closest('.input-wrap').find('button');
        var text = button.attr('data-reset');
        button.text(text);
        button.removeClass('active');
    }

    function clearAllDropdowns() {
        $('button.dropdown-toggle').each(function () {
            var button = $(this);
            var text = button.attr('data-reset');
            button.text(text);
            button.removeClass('active');
        });

    }

    function textInputActive() {
        var textsearchval = $(this).val();
        if (textsearchval.length > 0) {
            $(this).addClass('active');
        } else {
            $(this).removeClass('active');
        }

        submitTheForm();
    }

    // on load, do these functions
    importBedsToButton();
    importBathsToButton();
    submitTheForm();

    // do some of these functions when something happens
    $('.input-wrap-baths input').on('change', importBathsToButton);
    $('.input-wrap-beds input').on('change', importBedsToButton);
    $('.clear').on('click', clearDropdown);
    $('button[type="reset"]').on('click', clearAllDropdowns);
    $('input[type="text"]').on('change', textInputActive);

});