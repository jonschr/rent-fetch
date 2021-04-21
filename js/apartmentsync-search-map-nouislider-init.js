jQuery(document).ready(function ($) {

    var valSmall = 500;
    var valLarge = 15000;
    var valStep = 50;

    var slider = document.getElementById('price-slider');

    noUiSlider.create(slider, {
        start: [valSmall, valLarge],
        connect: true,
        margin: 100,
        step: valStep,
        range: {
            'min': valSmall,
            'max': valLarge
        },
    });

    // Give the slider dimensions
    // range.style.height = '400px';
    // range.style.margin = '0 auto 30px';

    var valuesDivs = [
        document.getElementById('pricesmall-display'),
        document.getElementById('pricebig-display'),
    ];

    var valuesInputs = [
        document.getElementById('pricesmall'),
        document.getElementById('pricebig'),
    ];

    var diffDivs = [
        document.getElementById('range-diff-1'),
        document.getElementById('range-diff-2'),
        document.getElementById('range-diff-3')
    ];

    // When the slider value changes, update the input and span
    slider.noUiSlider.on('update', function (values, handle) {
        var values = slider.noUiSlider.get();
        values[handle] = parseInt(values[handle]);
        valuesDivs[handle].innerHTML = values[handle];
        valuesInputs[handle].value = values[handle];
        // diffDivs[0].innerHTML = values[1] - values[0];
        // diffDivs[1].innerHTML = values[2] - values[1];
        // diffDivs[2].innerHTML = values[3] - values[2];
    });

});