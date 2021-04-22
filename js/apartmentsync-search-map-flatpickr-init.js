jQuery(document).ready(function ($) {


    // flatpickr("#datepicker", {
    //     altInput: true,
    //     altFormat: "F j, Y",
    //     dateFormat: "Ymd",
    // });


    $("#datepicker").flatpickr(
        {
            altInput: true,
            altFormat: "F j",
            dateFormat: "Ymd",
            mode: "range",
            minDate: "today",
            inline: true,
            // disable: [
            //     function (date) {
            //         // disable every multiple of 8
            //         return !(date.getDate() % 8);
            //     }
            // ]
        }
    );

});
