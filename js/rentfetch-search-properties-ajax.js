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

    // Function to perform AJAX search
    function performAJAXSearch(queryParams) {
        var filter = $('#filter');

        $.ajax({
            url: filter.attr('action'),
            data: filter.serialize(), // form data
            type: filter.attr('method'), // POST
            beforeSend: function (xhr) {
                filter.find('a.reset').text('Searching...'); // changing the button label
            },
            success: function (data) {
                filter.find('a.reset').text('Reset'); // changing the button label back
                $('#response').html(data); // insert data
            },
        });
    }

    // Our ajax query to get stuff and put it into the response div
    $('#filter').submit(function () {
        var queryParams = getQueryParametersFromForm(); // Get parameters from form
        updateURLWithQueryParameters(queryParams);
        performAJAXSearch(queryParams); // Perform AJAX search
        return false;
    });

    // Handle query parameters when the page loads
    var queryParameters = getQueryParametersFromForm();
    updateURLWithQueryParameters(queryParameters);
    performAJAXSearch(queryParameters); // Perform AJAX search
});
