jQuery( document ).ready( function( $ ) {

  // WP Media window to upload / choose image.
  var image_frame;

  // The div which holds all image fields of the current action.
  var $image_div;

  /**
   * Choose image button, saves the id and outputs a preview of the image.
   */
  $( '.meta-box' ).on( 'click', '.meta-box-upload-image-button, .meta-box-preview-image', function( event ) {
    event.preventDefault();

    $image_div = $( this ).closest( 'div.meta-box-image' );

    // If the frame already exists, just open it.
    if ( image_frame ) {
      image_frame.open();
      return;
    }

    // Set up the WP media frame.
    image_frame = wp.media({
      title: $( '.meta-box-upload-image-button', $image_div ).data( 'title' ),
      multiple: false,
      library: {
        type: 'image'
      },
      button: {
        text: $( '.meta-box-upload-image-button', $image_div ).data( 'button' )
      }
    });

    // Set up our select handler.
    image_frame.on( 'select', function() {
      var selection = image_frame.state().get( 'selection' );

      if ( ! selection ) {
        return;
      }

      // Loop through the selected files.
      selection.each( function( attachment ) {
        var id = attachment.id;
        var title = attachment.attributes.title;
        var src = attachment.attributes.sizes.thumbnail.url;

        $( '.meta-box-upload-image', $image_div ).val( id );
        $( '.meta-box-image-title', $image_div ).text( title ).show();
        $( '.meta-box-preview-image', $image_div ).removeClass( 'invalid' ).attr( 'src', src ).show();
      });

      $( '.meta-box-upload-image-button', $image_div ).hide();
      $( '.meta-box-clear-image-button', $image_div ).show();
    });

    image_frame.open();
  });

  /**
   * Remove image link, removes the image id from the hidden field.
   */
  $( '.meta-box' ).on( 'click', '.meta-box-clear-image-button', function( event ) {
    event.preventDefault();

    $image_div = $( this ).closest( 'div.meta-box-image' );

    $( '.meta-box-upload-image', $image_div ).val( '' );
    $( '.meta-box-preview-image', $image_div ).attr( 'src', '' ).hide();
    $( '.meta-box-image-title', $image_div ).text( '' ).hide();
    $( '.meta-box-upload-image-button', $image_div ).show();
    $( this ).hide();
  });


  // WP Media window to upload / choose file.
  var file_frame;

  // The div which holds all file fields of the current action.
  var $file_div;

  /**
   * Choose file button, saves the id and outputs the file name.
   */
  $( '.meta-box' ).on( 'click', '.meta-box-upload-file-button, .meta-box-file-icon', function( event ) {
    event.preventDefault();

    // Required to attach uploaded image to the current post.
    $( this ).attr( 'rel', $( '#post_ID' ).val() );

    $file_div = $( this ).closest( 'div.meta-box-file' );

    // If the frame already exists, just open it.
    if ( file_frame ) {
      file_frame.open();
      return;
    }

    // Set up the WP media frame.
    file_frame = wp.media({
      title: $( '.meta-box-upload-file-button', $file_div ).data( 'title' ),
      multiple: false,
      button: {
        text: $( '.meta-box-upload-file-button', $file_div ).data( 'button' )
      }
    });

    // Set up our select handler.
    file_frame.on( 'select', function() {
      var selection = file_frame.state().get( 'selection' );

      if ( ! selection ) {
        return;
      }

      // Loop through the selected files.
      selection.each( function( attachment ) {
        var id = attachment.id;
        var title = attachment.attributes.title;

        $( '.meta-box-upload-file', $file_div ).val( id );
        $( '.meta-box-file-title', $file_div ).text( title ).show();
        $( '.meta-box-file-icon', $file_div ).removeClass( 'invalid' ).addClass( 'checked' );
      });

      $( '.meta-box-upload-file-button', $file_div ).hide();
      $( '.meta-box-clear-file-button', $file_div ).show();
    });

    file_frame.open();
  });

  /**
   * Remove file link, removes the file id from the hidden field.
   */
  $( '.meta-box' ).on( 'click', '.meta-box-clear-file-button', function( event ) {
    event.preventDefault();

    $file_div = $( this ).closest( 'div.meta-box-file' );

    $( '.meta-box-upload-file', $file_div ).val( '' );
    $( '.meta-box-file-title', $file_div ).text( '' ).hide();
    $( '.meta-box-file-icon', $file_div ).removeClass( 'invalid' ).removeClass( 'checked' );
    $( '.meta-box-upload-file-button', $file_div ).show();
    $( this ).hide();
  });










  /**
   * Set up all sliders within parent.
   *
   * @param {object} parent Parent container.
   */
  function set_up_sliders( parent ) {
    $( '.mbf-type-slider', parent ).each(function( index, el ) {
      var $el = $( el );

      $el.slider({

        min:    $el.data( 'min' ),
        max:    $el.data( 'max' ),
        step:   $el.data( 'step' ),
        values: $el.data( 'values' ),
        range:  $el.data( 'range' ),

        create: function( event, ui ) {
          // Create all labels and add them to their respective handle.
          var $handles = $( '.ui-slider-handle', this );
          for ( var i = 0; i < $handles.length; i++ ) {
            $( '<span></span>' )
              .html( $( this ).slider( 'values', i ) )
              .appendTo( $handles[ i ] )
              .position({
                my: 'center top',
                at: 'center bottom+1',
                of: $handles[ i ],
                collision: 'none'
              });
          }
        },
        slide: function( event, ui ) {
          $( 'span', ui.handle )
            .html( ui.value )
            .position({
              my: 'center top',
              at: 'center bottom+1',
              of: ui.handle,
              collision: 'none'
            });
          $( '#' + $el.data( 'storage' ) ).val( ui.values );
        }
      });
    });
  }

  /**
   * Set up all color pickers within parent.
   *
   * @param {object} parent Parent container.
   */
  function set_up_color_pickers( parent ) {
    $( '.mbf-type-color', parent ).each(function( index, el ) {
      $( el ).wpColorPicker();
    });
  }

  /**
   * Set up all date pickers within parent.
   *
   * @param {object} parent Parent container.
   */
  function set_up_date_pickers( parent ) {
    $( '.mbf-type-date', parent ).each(function( index, el ) {
      $( el ).datepicker({
        dateFormat: $( this ).attr( 'data-dateformat' )
      });
    });
  }

  // Show or hide repeatable empty message.
  function check_repeatable_empty( mbr_table ) {
    if ( 0 == $( 'tbody tr', mbr_table ).length ) {
      // Show 'empty' message.
      $( '.repeatable-empty-message', mbr_table ).show();
    } else {
      // Hide 'empty' message.
      $( '.repeatable-empty-message', mbr_table ).hide();
    }
  }

  /**
   * Add a new empty template row to the tbody of the repeatable table.
   */
  function add_the_empty_template( mbr_table, pos ) {

    var $mbr_table = $( mbr_table );

    // Disable addition of rows.
    $( '.meta-box-repeatable-add', $mbr_table ).addClass( 'loading' );

    var $mbr_tbody = $( 'tbody', $mbr_table );
    var iterator = parseInt( $mbr_table.attr( 'data-iid' ) );

    // Data for AJAX call.
    var data = {
      'action': 'output_repeatable_fields_' + $mbr_table.data( 'id' ),
      'iterator': iterator
    };

    // Since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php.
    $.post( ajaxurl, data, function( new_row ) {
      if ( '' != new_row ) {
        var $new_row = $( $.parseHTML( new_row ) );

        if ( 'top' == pos ) {
          $new_row.hide().prependTo( $mbr_tbody ).fadeIn();
        } else if ( 'bottom' == pos ) {
          $new_row.hide().appendTo( $mbr_tbody ).fadeIn();
        }

        // Set up all fields for new row.
        set_up_all_fields( $new_row );

        // Remove 'empty' message.
        check_repeatable_empty( $mbr_table );

        // Increase iterator id.
        $mbr_table.attr( 'data-iid', iterator + 1);
      }
    })
    .always(function() {
      // Reenable addition of rows.
      $( '.meta-box-repeatable-add', $mbr_table ).removeClass( 'loading' );
    });
  }

  /**
   * Set up all field types within parent.
   *
   * @param {object} parent Parent container.
   */
  function set_up_all_fields( parent ) {
    set_up_sliders( parent );
    set_up_chosen( parent );
    set_up_color_pickers( parent );
    set_up_date_pickers( parent );
  }

  /**
   * Handle all new 'chosen' select boxes within parent.
   *
   * @param {object} parent Parent container.
   */
  function set_up_chosen( parent ) {
    if ( ! ! $.prototype.chosen ) {
      $( 'select.chosen', parent ).chosen();
    }
  }





  $( '.meta-box' ).each(function( index, meta_box ) {
    set_up_all_fields( meta_box );
  });

  /**
   * If there are no repeatable fields saved, add an empty template.
   */
  $( '.meta-box-repeatable' ).each(function( index, table ) {
    check_repeatable_empty( table );
  });


  /**
   * Add another repeatable field row.
   */
  $( '.meta-box-repeatable' ).on( 'click', '.meta-box-repeatable-add', function( event ) {
    event.preventDefault();

    // Add the new repeatable field row.
    add_the_empty_template(
      $( this ).closest( '.meta-box-repeatable' ),
      $( this ).attr( 'data-position' )
    );
  });


  /**
   * Remove a repeatable field row.
   */
  $( '.meta-box-repeatable' ).on( 'click', '.meta-box-repeatable-remove', function( event ) {
    event.preventDefault();

    var $mbr_table   = $( this ).closest( '.meta-box-repeatable' );

    // Remove the current row.
    $( this ).closest( 'tr' ).fadeOut('fast', function() {
      $( this ).remove();

      check_repeatable_empty( $mbr_table );
    });
  });


  /**
   * Workaround for bug that causes radio inputs to lose settings when meta box is dragged.
   * http://core.trac.wordpress.org/ticket/16972
   */
  $( '.meta-box-repeatable .sort' ).mousedown(function() {
    // Set live event listener for mouse up on the container and wait a tick to give the dragged div time to settle before firing the reclick function.
    $( '.meta-box-repeatable .ui-sortable' ).mouseup(function() {
      store_radio( this );
      setTimeout(function() { reclick_radio(); }, 10 );
    });
  });

  /**
   * Store object of all radio buttons within 'parent' that are checked.
   */
  function store_radio( parent ) {
    var radioshack = {};
    $( 'input[type="radio"]', parent ).each(function() {
      if ( $( this ).is( ':checked' ) ) {
        radioshack[ $( this ).attr( 'name' ) ] = $( this ).val();
      }
    });
    $( document ).attr( 'data-radioshack', radioshack );
  }

  /**
  * Restore all radio buttons that were checked.
  */
  function reclick_radio() {
    // Get object of checked radio button names and values.
    var radios = $( document ).attr( 'data-radioshack' );
    // Trigger a click on each corresponding radio button.
    for ( key in radios ) {
      $( 'input[name="' + key + '"]' ).filter( '[value="' + radios[ key ] + '"]' ).trigger( 'click' );
    }
    // Unbind the event listener, cause it should only be called when .sort handle has been clicked.
    $( '.meta-box-repeatable .ui-sortable' ).unbind( 'mouseup' );
  }


  // Make the repeatable table sortable.
  $( '.meta-box-repeatable tbody' ).sortable({
    opacity: 0.6,
    cursor: 'move',
    handle: '.sort',
    placeholder: 'ui-state-placeholder',
    update: function( event, ui ) {
      // Wait a bit to let the radio buttons get re-clicked.
      //setTimeout(function() { update_repeatable_ids( this ); }, 20 );
    }
  });



  /**
   * Fix for moving around meta boxes with TinyMCE editors in them without breaking them.
   * see https://core.trac.wordpress.org/ticket/19173
   *
   * @param  {object} postbox         The postbox being moved around / reordered.
   * @param  {boolean} creatingEditor If the editor is being created or destroyed.
   */
  function _triggerAllEditors( postbox, creatingEditor ) {
    $( 'textarea.wp-editor-area', postbox ).each(function( index, el ) {
      var editor = tinyMCE.EditorManager.get( el.id );
      var is_active = $( this ).parents( '.tmce-active' ).length;

      if ( creatingEditor ) {
        if ( ! editor && is_active ) {
          tinyMCE.execCommand( 'mceAddControl', true, el.id );
        }
      } else {
        if ( editor && is_active ) {
          editor.save();
          tinyMCE.execCommand( 'mceRemoveControl', true, el.id );
        }
      }
    });
  }
  // Part of fix from above.
  $( '#poststuff' )
    .on( 'sortstart', function( event ) { _triggerAllEditors( event.target, false ); })
    .on( 'sortstop',  function( event ) { _triggerAllEditors( event.target, true ); });

});