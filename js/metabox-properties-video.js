jQuery(document).ready(function ($) {
    // Get the URL input field and add an event listener for when the input changes
    $('input#video').on('input', function () {
        // Get the oembed container element
        const oembedContainer = $('#video-container');

        // Remove any existing oembed content
        oembedContainer.empty();

        // Get the video ID from the YouTube URL
        const videoID = this.value.split('v=')[1];

        // Create an oembed URL for the video
        const oembedUrl = `https://www.youtube.com/oembed?url=${this.value}`;

        // Fetch the oembed data from the API
        $.getJSON(oembedUrl)
            .done(function (data) {
                // Create a new HTML element for the oembed content
                const oembedContent = $('<div></div>').html(data.html);

                // Add the oembed content to the container element
                oembedContainer.append(oembedContent);
            })
            .fail(function (error) {
                console.error(error);
            });
    });
});
