<?php

/**
 * An image selector / uploader.
 */
class AM_MBF_Image extends AM_MBF {
  protected static $type = 'image';
  protected $sanitizer = 'intval';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $image_url = '';
    $hide_upload_button = $hide_clear_button = ' style="display:none;"';

    if ( ! empty( $this->value_old ) ) {
      if ( $image = wp_get_attachment_image_src( $this->value_old, 'medium' ) ) {
        $image_url = esc_url( $image[0] );
      }
      $hide_clear_button = '';
    } else {
      $hide_upload_button = '';
    }

    // Text used by wp.media frame.
    $wp_media_data = '
      data-title="' . esc_attr__( 'Choose an Image', 'textdomain' ) . '"
      data-button="' . esc_attr__( 'Use this Image', 'textdomain' ) . '"
    ';

    return  '
      <div' . $this->get_classes( 'meta-box-image' ) . '>
        <input name="' . $this->name . '" type="hidden" class="meta-box-upload-image" value="' . esc_attr( $this->value_old ) . '"' . $this->get_data_atts() . ' />
        <img src="' . $image_url . '" class="meta-box-preview-image" alt="' . __( 'Selected image', 'text-domain' ) . '"' . $hide_clear_button . ' />
        <a href="#" class="meta-box-upload-image-button button" rel="' . get_the_ID() . '"' . $hide_upload_button . $wp_media_data .  '>' . __( 'Choose Image', 'textdomain' ) . '</a>
        <a href="#" class="meta-box-clear-image-button"' . $hide_clear_button . '>' . __( 'Remove Image', 'textdomain' ) . '</a>
      </div>';
  }
}

?>