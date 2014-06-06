<?php

/**
 * A file selector / uploader.
 *
 * @todo Multiple file selection.
 * @todo Undo function when removing file.
 *
 * @since 1.0.0
 */
class AM_MBF_File extends AM_MBF {
  /**
   * Check AM_MBF for description.
   */
  protected static $type = 'file';

  /**
   * Check AM_MBF for description.
   */
  protected $sanitizer = 'intval';

  /**
   * Check AM_MBF for description.
   */
  public function output() {
    $class_icon_checked = '';
    $file_id = ( intval( $this->value_old ) > 0 ) ? intval( $this->value_old ) : -1;
    $file_title = '';

    $hide_upload_button = $hide_clear_button = ' style="display:none;"';
    if ( $file_title = get_the_title( $file_id ) ) {
      $class_icon_checked = ' checked';
      $hide_clear_button = '';
    } else {
      $hide_upload_button = '';
    }

    // Text used by wp.media frame.
    $wp_media_data = '
      data-title="' . esc_attr__( 'Choose a File', 'am-cpts' ) . '"
      data-button="' . esc_attr__( 'Use this File', 'am-cpts' ) . '"
    ';

    return sprintf( '
      <div%10$s>
        <input name="%2$s" type="hidden" class="meta-box-upload-file" value="%1$s"%11$s />
        <span class="meta-box-file-icon%4$s"></span>
        <span class="meta-box-file-title"%6$s>%9$s</span>
        <a href="#" class="meta-box-upload-file-button button" rel="%3$s"%5$s%11$s>%7$s</a>
        <a href="#" class="meta-box-clear-file-button"%6$s>%8$s</a>
      </div>',
      $file_id,
      esc_attr( $this->name ),
      get_the_ID(),
      $class_icon_checked,
      $hide_upload_button,
      $hide_clear_button,
      esc_html__( 'Choose File', 'am-cpts' ),
      esc_html__( 'Remove File', 'am-cpts'),
      esc_html( $file_title ),
      $this->get_classes( 'meta-box-file' ),
      $this->get_data_atts(),
      $wp_media_data
    );
  }
}

?>