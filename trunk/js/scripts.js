jQuery(document).ready(function($) {

  // the upload image button, saves the id and outputs a preview of the image
  var image_frame;

  // The div which holds all image fields of the current action.
  var img_div;

  $('.meta-box-upload-image-button, .meta-box-preview-image').live('click', function(e) {
    e.preventDefault();

    img_div = $(this).closest('div.meta-box-image');

    // if the frame already exists, open it
    if ( image_frame ) {
      $(image_frame).open();
      return;
    }

    // set our settings
    image_frame = wp.media({
      title: $(this).data('title'),
      multiple: false,
      library: {
        type: 'image'
      },
      button: {
        text: $(this).data('button')
      }
    });

    // set up our select handler
    $(image_frame).on( 'select', function() {
      var selection = $(image_frame).state().get('selection');

      if ( ! selection ) {
        return;
      }

      // loop through the selected files
      $(selection).each( function( attachment ) {
        var src = $(attachment).attributes.sizes.full.url;
        var id = $(attachment).id;

        $(img_div).find('.meta-box-preview-image').attr('src', src).show();
        $(img_div).find('.meta-box-upload-image').val(id);
      });

      $(img_div).find('.meta-box-upload-image-button').hide();
      $(img_div).find('.meta-box-clear-image-button').show();
    });

    // open the frame
    $(image_frame).open();
  });

  // the remove image link, removes the image id from the hidden field and replaces the image preview
  $('.meta-box-clear-image-button').live( 'click', function(e) {
    e.preventDefault();

    img_div = $(this).closest('div.meta-box-image');

    $(img_div).find('.meta-box-upload-image').val('');
    var preview_image = $(img_div).find('.meta-box-preview-image');
    $(preview_image).attr('src', '').hide();
    $(img_div).find('.meta-box-upload-image-button').show();
    $(this).hide();
  });


  // the file image button, saves the id and outputs the file name
  var file_frame;

  // The div which holds all file fields of the current action.
  var file_div;

  $('.meta-box-upload-file-button').live( 'click', function(e) {
    e.preventDefault();

    file_div = $(this).closest('div.meta-box-file');

    // if the frame already exists, open it
    if ( file_frame ) {
      $(file_frame).open();
      return;
    }

    // set our settings
    file_frame = wp.media({
      title: $(this).data('title'),
      multiple: false,
      button: {
          text: $(this).data('button')
      }
    });

    // set up our select handler
    $(file_frame).on( 'select', function() {
      var selection = $(file_frame).state().get('selection');

      if ( ! selection ) {
        return;
      }

      // loop through the selected files
      $(selection).each( function( attachment ) {
        var src = $(attachment).attributes.url;
        var id = $(attachment).id;

        $(file_div).find('.meta-box-upload-file').val(id);
        $(file_div).find('.meta-box-filename').text(src);
        $(file_div).find('.meta-box-file_icon').addClass('checked');
      });

      $(file_div).find('.meta-box-upload-file-button').hide();
      $(file_div).find('.meta-box-clear-file-button').show();
    });

    // open the frame
    $(file_frame).open();
  });

  // the remove image link, removes the image id from the hidden field and replaces the image preview
  $('.meta-box-clear-file-button').live( 'click', function(e) {
    e.preventDefault();

    file_div = $(this).closest('div.meta-box-file');

    $(file_div).find('.meta-box-upload-file').val('');
    $(file_div).find('.meta-box-filename').text('');
    $(file_div).find('.meta-box-file_icon').removeClass('checked');
    $(file_div).find('.meta-box-upload-file-button').show();
    $(this).hide();
  });

  // Update all ids for repeatable fields in a given set of rows.
  function update_repeatable_ids(tbody) {
    var rows = $(tbody).find('tr');
    for(var i = 0;i < rows.length;i++) {
      var row = rows[i];
      $(row).find('input, textarea, select').each(function(index, el) {
        var id = $(el).data('id');
        var parent = $(el).data('parent');
        $(el).attr('name', parent + '[' + i +'][' + id + ']');
        $(el).attr('id', id + '-' + i);
        $(row).find('label').attr('for', id + '-' + i);
      });
    }
  }

  function add_the_empty_template(table) {
    var tbody = $(table).find('tbody');
    $(table).find('.empty-fields-template')
      .clone()
      .appendTo(tbody)
      .removeClass('empty-fields-template')
      .show();
    update_repeatable_ids( tbody );
  }

  $('table.meta-box-repeatable').each(function(index, table) {
    if ( 0 == $(table).find('tbody tr').length ) {
      add_the_empty_template( table );
    }
  });

  $('.meta-box-repeatable-add').live('click', function(e) {
    e.preventDefault();

    var table = $(this).closest('table');
    var tbody = $(table).find('tbody');
    var new_row = $(table).find('.empty-fields-template').clone();

    if ( 'top' == $(this).data('position') ) {
      $(new_row).prependTo(tbody);
    } else if ( 'bottom' == $(this).data('position') ) {
      $(new_row).appendTo(tbody);
    }

    new_row.removeClass('empty-fields-template').show();

    if (!!$.prototype.chosen) {
      $(new_row).find('select.chosen')
        .chosen({allow_single_deselect: true});
    }

    update_repeatable_ids( tbody );

    //check_row_count( tbody );
  });

  $('.meta-box-repeatable-remove').live('click', function(e){
    e.preventDefault();

    var table = $(this).closest('table');

    $(this).closest('tr').remove();

    if ( 0 == $(table).find('tbody tr').length ) {
      add_the_empty_template( table );
    }
    //check_row_count($(this).closest('tbody'));
  });

  $('.meta-box-repeatable tbody').sortable({
    opacity: 0.6,
//    revert: true,
    cursor: 'move',
    handle: '.hndle',
    update: function(event, ui) {
      update_repeatable_ids(this);
    }
  });

  // post_drop_sort
  $('.sort-list').sortable({
    connectWith: '.sort-list',
    opacity: 0.6,
//    revert: true,
    cursor: 'move',
    cancel: '.post-drop-sort-area-name',
    items: 'li:not(.post-drop-sort-area-name)',
    update: function(event, ui) {
      var result = $(this).sortable('toArray');
      var thisID = $(this).attr('id');
      $('.store-' + thisID).val(result);
    }
  });

  $('.sort-list').disableSelection();

  // turn select boxes into something magical
  if (!!$.prototype.chosen)
    $('.chosen').chosen({ allow_single_deselect: true });
});