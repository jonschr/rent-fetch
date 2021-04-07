jQuery(function ($) {

    // our ajax query to get stuff and put it into the response div
    $('#filter').submit(function () {
        var filter = $('#filter');
        $.ajax({
            url: filter.attr('action'),
            data: filter.serialize(), // form data
            type: filter.attr('method'), // POST
            beforeSend: function (xhr) {
                filter.find('button[type="reset"]').text('Processing...'); // changing the button label
            },
            success: function (data) {
                filter.find('button[type="reset"]').text('Reset'); // changing the button label back
                $('#response').html(data); // insert data
            }
        });
        return false;
    });


});