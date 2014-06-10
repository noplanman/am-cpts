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
   *
   * @since 1.0.0
   */
  protected static $type = 'file';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected $sanitizer = 'intval';

  /**
   * Error message in case the file attachment is not valid.
   *
   * @since 1.1.0
   *
   * @var string
   */
  protected $error = null;

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function output() {
    // Get the file id, 0 represents a new field.
    $file_id = ( intval( $this->value ) > 0 ) ? intval( $this->value ) : 0;

    $file_title = '';
    $class_icon = '';
    $hide_upload_button = $hide_clear_button = ' style="display:none;"';

    // Make sure the selected file is valid.
    if ( $file_id > 0 ) {
      $post = get_post( $file_id );
      if ( isset( $post ) && 'attachment' == $post->post_type ) {
        $file_title = $post->post_title;
        $class_icon = ' checked';
      } else {
        $this->error = __( sprintf( 'Selected File (ID:%1$d) is invalid.', $file_id ), 'am-cpts' );
        $class_icon = ' invalid';
      }
      $hide_clear_button = '';
    } else {
      $hide_upload_button = '';
    }

    // Text used by wp.media frame.
    $wp_media_data = sprintf( ' data-title="%1$s" data-button="%2$s"',
      esc_attr__( 'Choose a File', 'am-cpts' ),
      esc_attr__( 'Use this File', 'am-cpts' )
    );

    return sprintf( '
      <div%9$s>
        <input name="%2$s" type="hidden" class="meta-box-upload-file" value="%1$d"%10$s />
        <span class="meta-box-file-icon%3$s"></span>
        <span class="meta-box-file-title"%5$s>%8$s</span>
        <a href="#" class="meta-box-upload-file-button button"%4$s%11$s>%6$s</a>
        <a href="#" class="meta-box-clear-file-button"%5$s>%7$s</a>
      </div>',
      esc_attr( $file_id ),
      esc_attr( $this->name ),
      $class_icon,
      $hide_upload_button,
      $hide_clear_button,
      esc_html__( 'Choose File', 'am-cpts' ),
      esc_html__( 'Remove File', 'am-cpts'),
      esc_html( ( isset( $this->error ) ) ? $this->error : $file_title ),
      $this->get_classes( 'meta-box-file' ),
      $this->get_data_atts(),
      $wp_media_data
    );
  }
}

?>