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
      image_frame.open();
      return;
    }

    // Set up the WP media frame.
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
    image_frame.on( 'select', function() {
      var selection = image_frame.state().get('selection');

      if ( ! selection ) {
        return;
      }

      // loop through the selected files
      selection.each( function( attachment ) {
        var src = attachment.attributes.sizes.full.url;
        var id = attachment.id;
        var title = attachment.attributes.title;
        var alt = attachment.attributes.alt;

        $(img_div).find('.meta-box-preview-image').attr('src', src).show().attr;
        $(img_div).find('.meta-box-upload-image').val(id);
        $(img_div).find('.meta-box-image-title').text(title).show();
      });

      $(img_div).find('.meta-box-upload-image-button').hide();
      $(img_div).find('.meta-box-clear-image-button').show();
    });

    // open the frame
    image_frame.open();
  });

  // the remove image link, removes the image id from the hidden field and replaces the image preview
  $('.meta-box-clear-image-button').live( 'click', function(e) {
    e.preventDefault();

    img_div = $(this).closest('div.meta-box-image');

    $(img_div).find('.meta-box-upload-image').val('');
    $(img_div).find('.meta-box-preview-image').attr('src', '').hide();
    $(img_div).find('.meta-box-image-title').text('').hide();
    $(img_div).find('.meta-box-upload-image-button').show();
    $(this).hide();
  });


  // the file image button, saves the id and outputs the file name
  var file_frame;

  // The div which holds all file fields of the current action.
  var file_div;

  $('.meta-box-upload-file-button, .meta-box-file-icon').live( 'click', function(e) {
    e.preventDefault();

    file_div = $(this).closest('div.meta-box-file');

    // if the frame already exists, open it
    if ( file_frame ) {
      file_frame.open();
      return;
    }

    // Set up the WP media frame.
    file_frame = wp.media({
      title: $(this).data('title'),
      multiple: false,
      button: {
          text: $(this).data('button')
      }
    });

    // set up our select handler
    file_frame.on( 'select', function() {
      var selection = file_frame.state().get('selection');

      if ( ! selection ) {
        return;
      }

      // loop through the selected files
      selection.each( function( attachment ) {
        var src = attachment.attributes.url;
        var id = attachment.id;

        $(file_div).find('.meta-box-upload-file').val(id);
        $(file_div).find('.meta-box-file-name').text(src).show();
        $(file_div).find('.meta-box-file-icon').addClass('checked');
      });

      $(file_div).find('.meta-box-upload-file-button').hide();
      $(file_div).find('.meta-box-clear-file-button').show();
    });

    // open the frame
    file_frame.open();
  });

  // the remove image link, removes the image id from the hidden field and replaces the image preview
  $('.meta-box-clear-file-button').live( 'click', function(e) {
    e.preventDefault();

    file_div = $(this).closest('div.meta-box-file');

    $(file_div).find('.meta-box-upload-file').val('');
    $(file_div).find('.meta-box-file-name').text('').hide();
    $(file_div).find('.meta-box-file-icon').removeClass('checked');
    $(file_div).find('.meta-box-upload-file-button').show();
    $(this).hide();
  });




  // Set up color pickers.
  $('[id^="colorpicker-"]').each(function(index, el) {
    var id = $(el).attr('id').substring(12); // 'colorpicker-' = 12
    $(el).hide().farbtastic('#'+id);
  });

  $('.mbf-type-color').live( 'focus', function(e) {
    var id = $(this).attr('id');
    $(this).siblings('[id^="colorpicker-"]').attr('id', 'colorpicker-'+id);
    $('#colorpicker-'+id).farbtastic('#'+id).show();
  });

  $('.mbf-type-color').live( 'blur', function(e) {
    var id = $(this).attr('id');
    $('#colorpicker-'+id).hide();
  });






  // Set up date pickers.
  $('.mbf-type-date').live('focus', function(e) {
    $(this).datepicker({
      dateFormat: $(this).data('dateformat')
    });
  });



  // Update all ids for repeatable fields in a given tbody.
  function update_repeatable_ids(tbody) {
    var rows = $(tbody).find('tr');
    for(var i = 0;i < rows.length;i++) {
      var row = rows[i];
      $(row).find('input, textarea, select').each(function(index, el) {
        var id = $(el).data('id');
        var subid = $(el).data('subid');
        subid += ('' != subid) ? '-' : '';
        var iid = id + '-' + subid + i;
        var parent = $(el).data('parent');
        $(el).attr('name', parent + '[' + i +'][' + id + ']' + ( ('checkbox' == $(el).attr('type')) ? '[]' : '' ));
        $(el).attr('id', iid);

        // Replace default 'empty' class with the newly assigned id.
        $(el).attr('class', function(i, c){
          return c.replace(/\bmbf-id-\S+-empty\b/g, 'mbf-id-' + iid);
        });

        /* To keep in mind!
        https://gist.github.com/peteboere/1517285
        $(el).alterClass('mbf-id-*-empty', 'mbf-id-' + iid);
        */

        // The repeatable label should show to the first field.
//        $(row).find('label').attr('for', iid);

//        $($(el).attr("tagName").toLowerCase() + ' + label').attr('for', iid);

        $(el).prev('label').attr('for', iid);
        $(el).next('label').attr('for', iid);
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
  });

  $('.meta-box-repeatable-remove').live('click', function(e){
    e.preventDefault();

    var table = $(this).closest('table');

    $(this).closest('tr').remove();

    if ( 0 == $(table).find('tbody tr').length ) {
      add_the_empty_template( table );
    }
  });




  /**
   * Workaround for bug that causes radio inputs to lose settings when meta box is dragged.
   * http://core.trac.wordpress.org/ticket/16972
   */
  $('.meta-box-repeatable .hndle').mousedown(function(){
    // set live event listener for mouse up on the content .wrap and wait a tick to give the dragged div time to settle before firing the reclick function
    $('.meta-box-repeatable .ui-sortable').mouseup(function(){
      store_radio(this);
      setTimeout(function(){ reclick_radio(); },0);
    });
  });

  /**
   * Store object of all radio buttons within element el that are checked.
   */
  function store_radio(el){
      var radioshack = {};
      $('input[type="radio"]', el).each(function(){
          if($(this).is(':checked')){
              radioshack[$(this).attr('name')] = $(this).val();
          }
      });
      $(document).attr('data-radioshack',radioshack);
  }

  /**
  * Restore all radio buttons that were checked.
  */
  function reclick_radio(){
      // get object of checked radio button names and values
      var radios = $(document).data('radioshack');
      //step thru each object element and trigger a click on it's corresponding radio button
      for(key in radios){
          $('input[name="'+key+'"]').filter('[value="'+radios[key]+'"]').trigger('click');
      }
      // unbind the event listener on .wrap  (prevents clicks on inputs from triggering function)
      $('.meta-box-repeatable .ui-sortable').unbind('mouseup');
  }




  $('.meta-box-repeatable tbody').sortable({
    opacity: 0.6,
    revert: 500,
    cursor: 'move',
    handle: '.hndle',
    update: function(event, ui) {
      // Wait a bit to let the radio buttons get re-clicked.
      setTimeout(function(){ update_repeatable_ids(this); }, 10);
    }
  });





  // turn select boxes into something magical
  if (!!$.prototype.chosen)
    $('.chosen').chosen({ allow_single_deselect: true });
});