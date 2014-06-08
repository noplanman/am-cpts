<?php

/**
 * An image selector / uploader.
 *
 * @todo Multiple image selection.
 * @todo Undo function when removing image.
 *
 * @since 1.0.0
 */
class AM_MBF_Image extends AM_MBF {
  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected static $type = 'image';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected $sanitizer = 'intval';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function output() {
    $image_id = ( intval( $this->value ) > 0 ) ? intval( $this->value ) : -1;
    $image_url = '';

    $hide_upload_button = $hide_clear_button = ' style="display:none;"';
    if ( $image_url = wp_get_attachment_thumb_url( $image_id ) ) {
      $hide_clear_button = '';
    } else {
      $hide_upload_button = '';
    }

    // Text used by wp.media frame.
    $wp_media_data = '
      data-title="' . esc_attr__( 'Choose an Image', 'am-cpts' ) . '"
      data-button="' . esc_attr__( 'Use this Image', 'am-cpts' ) . '"
    ';

    return sprintf( '
      <div%11$s>
        <input name="%2$s" type="hidden" class="meta-box-upload-image" value="%1$s"%12$s />
        <img src="%9$s" class="meta-box-preview-image" title="%7$s" alt="%6$s"%5$s />
        <a href="#" class="meta-box-clear-image-button"%5$s>%8$s</a>
        <span class="meta-box-image-title"%5$s>%10$s</span>
        <a href="#" class="meta-box-upload-image-button button" rel="%3$s"%4$s%13$s>%7$s</a>
      </div>',
      $image_id,
      esc_attr( $this->name ),
      get_the_ID(),
      $hide_upload_button,
      $hide_clear_button,
      esc_attr__( 'Selected Image', 'am-cpts' ),
      esc_html__( 'Choose Image', 'am-cpts' ),
      esc_html__( 'Remove Image', 'am-cpts'),
      esc_url( $image_url ),
      get_the_title( $image_id ),
      $this->get_classes( 'meta-box-image' ),
      $this->get_data_atts(),
      $wp_media_data
    );
  }
}

?>