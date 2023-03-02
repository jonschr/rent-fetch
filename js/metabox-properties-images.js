jQuery(document).ready(function ($) {
    // Get the container for the gallery images
    var galleryContainer = $('#gallery-container');

    // Get the hidden input field for the gallery IDs
    var galleryIdsField = $('#images');

    // Get the "Select Images" button
    var imagesButton = $('#images_button');

    // convert gallleryIdsField value to an array
    var selectedImageIds = galleryIdsField.val().split(',');

    var selection;

    var galleryFrame;

    //* Handle the "Select Images" button click event
    imagesButton.click(function (e) {
        e.preventDefault();

        if (galleryFrame) {
            galleryFrame.open();
            return;
        }

        // // var selection = galleryFrame.state().get('selection');
        // var selectedImageIds = $('#images').val();

        // if (selectedImageIds.length > 0) {
        //     var ids = selectedImageIds.split(',');

        //     ids.forEach(function (id) {
        //         attachment = wp.media.attachment(id);
        //         attachment.fetch();
        //         selection.add(attachment ? [attachment] : []);
        //     });
        // }

        galleryFrame = wp.media({
            title: 'Select Images',
            button: {
                text: 'Add to Gallery',
            },
            multiple: true,
            library: {
                type: 'image',
            },
        });

        // console_log(galleryFrame);

        //* Handle the selection when confirmed
        galleryFrame.on('select', function () {
            var selection = galleryFrame.state().get('selection');
            galleryIdsField = $('#images');

            selection.map(function (attachment) {
                attachment = attachment.toJSON();

                if (
                    attachment.id &&
                    selectedImageIds.indexOf(attachment.id) === -1
                ) {
                    selectedImageIds.push(attachment.id);
                    galleryContainer.append(
                        '<div class="gallery-image" data-id="' +
                            attachment.id +
                            '"><img src="' +
                            attachment.sizes.thumbnail.url +
                            '"><button class="remove-image">Remove</button></div>'
                    );
                }
            });

            galleryIdsField.val(selectedImageIds.join(','));
        });

        galleryFrame.open();
    });

    //* When the gallery frame opens, get the IDs of the images that are already selected
    // galleryFrame.on('open', function () {
    //     console.log('hello world');
    //     var selection = galleryFrame.state().get('selection');
    //     var ids_value = jQuery('#images').val();

    //     if (ids_value.length > 0) {
    //         var ids = ids_value.split(',');

    //         ids.forEach(function (id) {
    //             attachment = wp.media.attachment(id);
    //             attachment.fetch();
    //             selection.add(attachment ? [attachment] : []);
    //         });
    //     }
    // });

    //* Handle the "Remove" button click event
    galleryContainer.on('click', '.remove-image', function () {
        var imageDiv = $(this).closest('.gallery-image');
        var imageId = imageDiv.data('id');

        selectedImageIds = $('#images').val().split(',');

        // console.log(selectedImageIds);

        selectedImageIds.splice(selectedImageIds.indexOf(imageId), 1);
        galleryIdsField.val(selectedImageIds.join(','));
        imageDiv.remove();
    });
});
