<?php

/**
 * A file selector / uploader.
 */
class AM_MBF_File extends AM_MBF {
  protected static $type = 'file';
  protected $sanitizer = 'intval';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $class_icon = 'meta-box-file-icon';
    $file_url = '';

    $hide_upload_button = $hide_clear_button = ' style="display:none;"';
    if ( isset( $this->value_old ) && $file_url = esc_url( wp_get_attachment_url( intval( $this->value_old ) ) ) ) {
      $class_icon .= ' checked';
      $hide_clear_button = '';
    } else {
      $hide_upload_button = '';
    }

    // Text used by wp.media frame.
    $wp_media_data = '
      data-title="' . esc_attr__( 'Choose a File', 'textdomain' ) . '"
      data-button="' . esc_attr__( 'Use this File', 'textdomain' ) . '"
    ';

    return '
      <div' . $this->get_classes( 'meta-box-file' ) . '>
        <input name="' . $this->name . '" type="hidden" class="meta_box_upload_file" value="' . intval( $this->value ) . '"' . $this->get_data_atts() . ' />
        <span class="' . $class_icon . '"></span>
        <span class="meta-box-filename">' . $file_url . '</span>
        <a href="#" class="meta-box-upload-file-button button" rel="' . get_the_ID() . '"' . $hide_upload_button . $wp_media_data . '>' . __( 'Choose File', 'textdomain' ) . '</a>
        <a href="#" class="meta-box-clear-file-button"' . $hide_clear_button . '>' . __( 'Remove File', 'textdomain') . '</a>
      </div>';
  }
}

?>