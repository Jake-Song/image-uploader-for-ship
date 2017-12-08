jQuery( document ).ready( function($){

  var addButton = $( '#image-upload-button' );
  var deleteButton = $( '#image-delete-button' );
  var imgBox = $('#metabox_wrapper');
  var hidden = $( '#img-hidden-field' );

  var customUploader = wp.media({
      title: 'Select an Image',
      button: {
          text: 'Use this Image'
      },
      multiple: true
  });

  addButton.on( 'click', function() {
      if ( customUploader ) {
          customUploader.open();
      }
  } );

  customUploader.on( 'select', function() {
      var attachmentImgs = customUploader.state().get('selection').toJSON();
      var img = [];
      var hiddenVal = [];
      for(var i = 0; i < attachmentImgs.length; i++){
        var test = 0;
        img[i] = $('<img class="selected">');
        img[i].attr( 'src', attachmentImgs[i].url );

        hiddenVal[i] = {
          id: attachmentImgs[i].id,
          url: attachmentImgs[i].url,
          alt: attachmentImgs[i].alt
        };

        imgBox.prepend(img[i]);
      }

      var test = 0;

      hidden.attr( 'value', JSON.stringify( hiddenVal ));
      toggleVisibility( 'ADD' );
  } );

  deleteButton.on( 'click', function() {
      var img = $('img.selected');
      img.attr( 'src', '' );
      hidden.attr( 'value', '' );
      toggleVisibility( 'DELETE' );
  } );

  var toggleVisibility = function( action ) {
      var img = $('img.selected');

      if ( 'ADD' === action ) {
          addButton.css( 'display', 'none' );
          deleteButton.css( 'display', 'block' );
          img.css( 'width', '15%' );
      }

      if ( 'DELETE' === action ) {
          addButton.css('display', 'block');
          deleteButton.css( 'display', 'none' );
          img.remove();
      }
  };

  $( window ).on( 'load', function() {
      console.log(customUploads.imageData);
      if ( "" === customUploads.imageData || 0 === customUploads.imageData.length ) {
          toggleVisibility( 'DELETE' );
      } else {
          var images = $('img.selected');

          for ( var i = 0; i < images.length; i++ ){
            images.eq(i).attr( 'src', customUploads.imageData[i].url );
          }

          hidden.attr( 'value', JSON.stringify( customUploads.imageData ) );
          toggleVisibility( 'ADD' );
      }
  } );

});
