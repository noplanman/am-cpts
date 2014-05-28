<?php

/**
 * A file selector / uploader.
 */
class AM_MBF_File extends AM_MBF {
  protected static $type = 'file';
  protected $sanitizer = 'intval';

  /**
   * Check AM_MBF for description.
   */
  public function output() {
    $class_icon_checked = '';
    $file_id = isset( $this->value_old ) ? intval( $this->value_old ) : 0;
    $file_url = '';

    $hide_upload_button = $hide_clear_button = ' style="display:none;"';
    if ( $file_url = wp_get_attachment_url( $file_id ) ) {
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
        <a href="#" class="meta-box-upload-file-button button" rel="%3$s"%5$s%11$s>%7$s</a>
        <a href="#" class="meta-box-clear-file-button"%6$s>%8$s</a>
        <span class="meta-box-file-name"%6$s>%9$s</span>
      </div>',
      $file_id,
      esc_attr( $this->name ),
      get_the_ID(),
      $class_icon_checked,
      $hide_upload_button,
      $hide_clear_button,
      esc_html__( 'Choose File', 'am-cpts' ),
      esc_html__( 'Remove File', 'am-cpts'),
      esc_url( $file_url ),
      $this->get_classes( 'meta-box-file' ),
      $this->get_data_atts(),
      $wp_media_data
    );
  }
}

?>