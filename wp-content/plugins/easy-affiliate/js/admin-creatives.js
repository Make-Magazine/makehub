jQuery(document).ready(function($) {

    // Instantiates the variable that holds the media library frame.
    var meta_image_frame;

    // Remove the image, toggle the anchors
    $( '#remove-banner-link' ).on( 'click', function( evt ) {

        // Stop the anchor's default behavior
        evt.preventDefault();

        // First, we'll hide the image
        $( '#featured-footer-image-container' )
            .children( 'img, #ea-link-image-width-height' )
            .hide();

        // Then display the previous container
        $( '#featured-footer-image-container' ) .prev()
            .show();

        // Finally, we add the 'hidden' class back to this anchor's parent
        $( '#featured-footer-image-container' )
            .next()
            .hide()
            .addClass( 'hidden' );

        // Clear out the hidden input field values
        $('#_wafp_creative_image').val('');
        $('#_wafp_creative_image_alt').val('');
        $('#_wafp_creative_image_width').val('');
        $('#_wafp_creative_image_height').val('');
        $('#_wafp_creative_image_title').val('');
    });

    $('#set-banner-link').click(function(evt){
        evt.preventDefault();

        // If the frame already exists, re-open it.
        if ( meta_image_frame ) {
            meta_image_frame.open();
            return;
        }

        // Sets up the media library frame
        // Other options: frame, state, multiple
        // EsafCreatives is the object from the call to localized_script in WP
        meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
            title: EsafCreatives.title,
            button: { text:  EsafCreatives.button },
            library: { type: 'image' }
        });

        // Runs when an image is selected.
        meta_image_frame.on('select', function() {

            // Grabs the attachment selection and creates a JSON representation of the model.
            var json = meta_image_frame.state().get('selection').first().toJSON();

            // First, make sure that we have the URL of an image to display
            if ( 0 > $.trim( json.url.length ) ) {
                return;
            }

            // Sends the attachment URL to our custom image input field.
            $('#_wafp_creative_image').val(json.url);
            $('#_wafp_creative_image_alt').val(json.alt);
            $('#_wafp_creative_image_title').val(json.title);
            $('#_wafp_creative_image_width').val(json.width);
            $('#_wafp_creative_image_height').val(json.height);

            // After that, set the properties of the image and display it
            $( '#featured-footer-image-container' )
                .children( 'img' )
                    .attr( 'src', json.url )
                    .attr( 'alt', json.caption )
                    .attr( 'width', json.caption )
                    .attr( 'height', json.caption )
                    .attr( 'title', json.title )
                        .show()
                .parent()
                .children( '#ea-link-image-width-height' )
                    .text( '(' + json.width + ' x ' + json.height + ')' )
                        .show()
                .parent()
                    .removeClass( 'hidden' );

            // Next, hide the anchor responsible for allowing the user to select an image
            $( '#featured-footer-image-container' )
                .prev()
                .hide();

            // Display the anchor for the removing the featured image
            $( '#featured-footer-image-container' )
                .next()
                .show();

        });

        meta_image_frame.on('close', function () {
          // console.log('event close');
        });

        // Opens the media library frame.
        meta_image_frame.open();
    });

  // Remove the word 'Publish' from the submit button
  $('#publishing-action input#publish').val(EsafCreatives.submit_button_text);

  // This utilizes the jquery validator plugin to enable us to at least validate via javascript
  $.validate();
});
