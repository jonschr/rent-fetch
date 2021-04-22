jQuery(document).ready(function ($) {

    // onload, hide the dropdowns
    $('.dropdown-menu').removeClass('show');

    // when a dropdown toggle is clicked, toggle the dropdown menu next to it
    $('.dropdown button').click(function () {
        $('.dropdown-menu').removeClass('show');
        $(this).siblings('.dropdown-menu').toggleClass('show')
    });

    // if the target of the click isn't the container nor a descendant of the container
    $(document).mousedown(function (e) {
        var container = $('.dropdown-menu, .flatpickr-calendar');
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.removeClass('show');
        }
    });

    //* Submission events
    $('a.apply-local').click(function (e) {
        e.preventDefault();
        $(this).parents('.dropdown-menu').removeClass('show');
    });

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

    //* text for propertytypes button
    function importPropertyTypes() {

        var inputs = $('.input-wrap-propertytypes input');
        var button = $('.input-wrap-propertytypes button');
        var propertyTypeNames = [];

        inputs.each(function () {
            if (this.checked) {
                propertyTypeName = $(this).attr('data-propertytypesname');
                propertyTypeNames.push(propertyTypeName);
            }
        });

        if (jQuery.isEmptyObject(propertyTypeNames) == false) {
            var text = propertyTypeNames.join(', ');
            button.text(text);
            button.addClass('active');
        } else {
            text = button.attr('data-reset');
            button.text(text);
            button.removeClass('active');
        }
    }

    //* text for propertytypes button
    function importAmenities() {

        var inputs = $('.input-wrap-amenities input');
        var button = $('.input-wrap-amenities button');
        var AmenityNames = [];

        inputs.each(function () {
            if (this.checked) {
                AmenityName = $(this).attr('data-amenities-name');
                AmenityNames.push(AmenityName);
            }
        });

        if (jQuery.isEmptyObject(AmenityNames) == false) {
            var text = AmenityNames.join(', ');
            button.text(text);
            button.addClass('active');
        } else {
            text = button.attr('data-reset');
            button.text(text);
            button.removeClass('active');
        }
    }

    //* text for pets button
    function importPetsToButton() {
        var selectedPetPolicy = $('.input-wrap-pets input:checked').attr('data-pets-name');
        console.log(selectedPetPolicy);

        $('.input-wrap-pets button').addClass('active');
        $('.input-wrap-pets button').text(selectedPetPolicy);
    }

    function textInputActive() {
        var textsearchval = $(this).val();
        if (textsearchval.length > 0) {
            $(this).addClass('active');
        } else {
            $(this).removeClass('active');
        }
    }

    function clearDropdown() {
        $(this).closest('.dropdown-menu').find('input[type="checkbox"]').prop("checked", false);
        $(this).closest('.dropdown-menu').find('input[type="radio"]').prop("checked", false);
        var button = $(this).closest('.input-wrap').find('button');
        var text = button.attr('data-reset');
        button.text(text);
        button.removeClass('active');
    }

    // on load, do these functions
    importBedsToButton();
    importBathsToButton();
    importPropertyTypes();

    // do some of these functions when something happens
    $('.input-wrap-baths input').on('change', importBathsToButton);
    $('.input-wrap-beds input').on('change', importBedsToButton);
    $('.input-wrap-pets input').on('change', importPetsToButton);
    $('.input-wrap-propertytypes input').on('change', importPropertyTypes);
    $('.input-wrap-amenities input').on('change', importAmenities);
    $('.clear').on('click', clearDropdown);
    $('input[type="text"]').on('change', textInputActive);

});