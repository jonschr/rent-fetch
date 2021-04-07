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

    //* Dropdown clearing
    // $('a.clear').click(function (e) {
    //     e.preventDefault();
    //     $(this).closest('.dropdown-menu').find('input[type="checkbox"]').prop("checked", false);
    // });

    //* Submission events

    $('a.apply').click(function (e) {
        e.preventDefault();
        $('#filter').submit();
        $(this).parents('.dropdown-menu').removeClass('show');
    });

    // when something changes, submit
    // $('#filter input').change(function () {
    //     $('#filter').submit();
    // });

    // when the form is reset, trigger a submit
    $('#filter').on('reset', function () {
        $('#filter').submit();
    });

    // // submit the form when we load the page
    // window.onload = function () {
    //     $('#filter').submit();
    // }

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

        console.log(bedsArray);

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


    submitTheForm();
    $('.input-wrap-baths input').on('change', importBathsToButton);
    $('.input-wrap-beds input').on('change', importBedsToButton);
    $('.clear').on('click', clearDropdown);
    $('button[type="reset"]').on('click', clearAllDropdowns);


});