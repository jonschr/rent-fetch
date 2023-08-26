jQuery(function ($) {
    // Function to update URL with query parameters
    function updateURLWithQueryParameters(params) {
        var baseUrl = window.location.href.split('?')[0];
        var queryString = $.param(params); // Serialize the parameters
        var newUrl = baseUrl + (queryString ? '?' + queryString : '');
        history.pushState(null, '', newUrl);
    }

    // Function to get query parameters from POST request
    function getQueryParametersFromForm() {
        var queryParams = {};

        // Loop through all form inputs
        $('#filter')
            .find('input, select')
            .each(function () {
                var inputName = $(this).attr('name');
                if (inputName) {
                    var inputValue = $(this).val();

                    // Handle checkboxes and multiple values
                    if ($(this).is(':checkbox')) {
                        if (!queryParams[inputName]) {
                            queryParams[inputName] = [];
                        }
                        if ($(this).is(':checked')) {
                            queryParams[inputName].push(inputValue);
                        }
                    } else {
                        queryParams[inputName] = inputValue;
                    }
                }
            });

        // Remove empty and unwanted parameters
        $.each(queryParams, function (key, value) {
            if (
                value === '' || // Exclude empty values
                key === 'textsearch' || // Exclude specific parameters
                key === 'dates' || // Exclude specific parameters
                key === 'action' // Exclude specific parameters
            ) {
                delete queryParams[key];
            }
        });

        return queryParams;
    }

    var isFirstRequest = true; // Flag variable to track the first request

    // Function to perform AJAX search
    function performAJAXSearch(queryParams) {
        var filter = $('#filter');

        $.ajax({
            url: filter.attr('action'),
            data: filter.serialize(), // form data
            type: filter.attr('method'), // POST
            beforeSend: function (xhr) {
                filter.find('#reset').text('Searching...'); // changing the button label
                $('#reset').text('Searching...'); // changing the button label
            },
            success: function (data) {
                $('#reset').text('Clear All'); // changing the button label
                $('#response').html(data); // insert data

                if ($('#map').length) {
                    var mapOffset = $('#map').offset().top;
                    var viewportTop = $(window).scrollTop();
                    if (mapOffset - viewportTop > 200) {
                        $('html, body').animate(
                            {
                                scrollTop: mapOffset,
                            },
                            1000
                        );
                    }
                }

                isFirstRequest = false;

                // look in data for .properties-loop, and count the number of children
                var count = $('.properties-loop').children().length;
                // update #properties-found with the count
                $('#properties-found').text(count);
            },
        });
    }

    // Our ajax query to get stuff and put it into the response div
    function submitForm() {
        var queryParams = getQueryParametersFromForm(); // Get parameters from form

        updateURLWithQueryParameters(queryParams);
        performAJAXSearch(queryParams); // Perform AJAX search

        return false;
    }

    // submit on page load
    submitForm();

    // // Handle query parameters when the page loads
    // var queryParameters = getQueryParametersFromForm();
    // updateURLWithQueryParameters(queryParameters);
    // performAJAXSearch(queryParameters); // Perform AJAX search

    // on page load, submit the form
    // $('#filter').submit();

    //! WHEN CHANGES ARE MADE, SUBMIT THE FORM

    var submitTimer; // Timer identifier

    // Function to submit the form after 1 second of inactivity
    function submitFormAfterInactivity() {
        var self = this; // Capture the current context
        clearTimeout(submitTimer); // Clear any previous timer
        submitTimer = setTimeout(function () {
            submitForm(); // Submit the form after 1 second of inactivity
        }, 1000);
    }

    // Call the function on input
    // $('#filter').on('change', submitFormAfterInactivity);

    //! RESET THE FORMS

    // Function to clear all values from fields in #filter when #reset is clicked
    function clearFilterValues() {
        // Reset all non-hidden inputs to null value
        $('#filter, #featured-filters, #filter-toggles')
            .find('input:not([type="hidden"],[type="checkbox"],[type="radio"])')
            .val('');
        // .trigger('change'); // Trigger the change event

        // Reset checkboxes to unchecked
        $('#filter, #featured-filters, #filter-toggles')
            .find('[type="checkbox"]:checked') // Select only checked checkboxes
            .prop('checked', false);
        // .trigger('change'); // Trigger the change event

        // Get default values for input#pricesmall and input#pricebig
        var defaultValSmall = $('#pricesmall').data('default-value');
        var defaultValBig = $('#pricebig').data('default-value');

        // Set default values for input#pricesmall and input#pricebig
        $('#pricesmall, #featured-pricesmall').val(defaultValSmall);
        // .trigger('change'); // Trigger the change event
        $('#pricebig, #featured-pricebig').val(defaultValBig);
        // .trigger('change'); // Trigger the change event
    }

    // Call the function when #reset is clicked
    $('#reset, #featured-reset').click(function () {
        clearFilterValues();
        submitForm();
    });

    //! SYNC THE FORMS

    // Select all input, select, and textarea elements
    var $inputs = $('input, select, textarea');

    var programmaticChange = false; // Flag to check if the change was programmatic

    $inputs.on('change input', function () {
        if (programmaticChange) {
            // If the change was programmatic, return early
            return;
        }

        var elementName = $(this).attr('name');
        var newValue = $(this).val();

        // Update identically named elements with the new value
        $inputs
            .filter('[name="' + elementName + '"]')
            .not(this)
            .each(function () {
                var elementType = $(this).prop('tagName').toLowerCase();

                if (
                    elementType === 'input' &&
                    $(this).attr('type') === 'checkbox'
                ) {
                    // For checkboxes, update the checked status
                    $(this).prop(
                        'checked',
                        $(this).is(':checked') || $(this).val() === newValue
                    );
                } else {
                    // For other elements, update the value
                    if ($(this).val() !== newValue) {
                        programmaticChange = true; // Set the flag to true before making the change
                        $(this).val(newValue);
                        $(this).trigger('change');
                        programmaticChange = false; // Reset the flag after making the change
                    }
                }
            });

        submitFormAfterInactivity();
    });

    function changeInputHandler() {
        var elementName = $(this).attr('name');
        var newValue = $(this).val();

        // Update identically named elements with the new value
        $inputs
            .filter('[name="' + elementName + '"]')
            .not(this)
            .each(function () {
                var elementType = $(this).prop('tagName').toLowerCase();

                if (
                    elementType === 'input' &&
                    $(this).attr('type') === 'checkbox'
                ) {
                    // For checkboxes, update the checked status
                    $(this).prop(
                        'checked',
                        $(this).is(':checked') || $(this).val() === newValue
                    );
                } else {
                    // For other elements, update the value
                    if ($(this).val() !== newValue) {
                        $(this).off('change input'); // Temporarily remove the event handler
                        $(this).val(newValue);
                        $(this).trigger('change');
                        $(this).on('change input', changeInputHandler); // Reattach the event handler
                    }
                }
            });

        submitFormAfterInactivity();
    }

    // Event listener for changes in the input elements
    $inputs.on('change input', function () {
        var elementName = $(this).attr('name');
        var newValue = $(this).val();

        // Update identically named elements with the new value
        $inputs
            .filter('[name="' + elementName + '"]')
            .not(this)
            .each(function () {
                var elementType = $(this).prop('tagName').toLowerCase();

                if (
                    elementType === 'input' &&
                    $(this).attr('type') === 'checkbox'
                ) {
                    // For checkboxes, update the checked status
                    $(this).prop(
                        'checked',
                        $(this).is(':checked') || $(this).val() === newValue
                    );
                } else {
                    // For other elements, update the value
                    if ($(this).val() !== newValue) {
                        $(this).off('change input'); // Temporarily remove the event handler
                        $(this).val(newValue);
                        $(this).trigger('change');
                        $(this).on('change input', changeInputHandler); // Reattach the event handler
                    }
                }
            });

        submitFormAfterInactivity();

        // Dynamically resize text inputs in #filter-toggles
        $('#filter-toggles input[type="text"]').on(
            'change input load',
            function () {
                this.setAttribute('size', this.value.length);
                console.log('this.value.length:', this.value.length);
            }
        );

        // Dynamically resize number inputs in #filter-toggles
        $('#filter-toggles input[type="number"]').on(
            'input change load',
            function () {
                var len = $(this).val().length;
                $(this).css('width', len + 'ch');
            }
        );
    });

    //! REMOVE UNWANTED ELEMENTS FROM THE TOGGLES

    // Remove unwanted elements from the toggles
    $('#filter-toggles')
        .find('fieldset, legend, button.toggle, div')
        .each(function () {
            var $this = $(this);
            var $children = $this.children();

            // Keep the elements that are being wrapped
            if ($children.length > 0) {
                $children.unwrap();
            } else {
                $this.remove();
            }
        });
});
