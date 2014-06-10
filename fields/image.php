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
   * Error message in case the image attachment is not valid.
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
    $image_id = ( intval( $this->value ) > 0 ) ? intval( $this->value ) : 0;

    $image_url = '';
    $image_title = '';
    $class_icon = '';
    $hide_upload_button = $hide_clear_button = ' style="display:none;"';

    // Make sure the selected image is valid.
    if ( $image_id > 0 ) {
      $post = get_post( $image_id );
      if ( isset( $post ) && wp_attachment_is_image( $image_id ) ) {
        $image_url = wp_get_attachment_thumb_url( $image_id );
        $image_title = $post->post_title;
      } else {
        $this->error = __( sprintf( 'Selected Image (ID:%1$d) is invalid.', $image_id ), 'am-cpts' );
        $class_icon = ' invalid';
      }
      $hide_clear_button = '';
    } else {
      $hide_upload_button = '';
    }

    // Text used by wp.media frame.
    $wp_media_data = sprintf( ' data-title="%1$s" data-button="%2$s"',
      esc_attr__( 'Choose an Image', 'am-cpts' ),
      esc_attr__( 'Use this Image', 'am-cpts' )
    );


    return sprintf( '
      <div%11$s>
        <input name="%2$s" type="hidden" class="meta-box-upload-image" value="%1$d"%12$s />
        <img src="%10$s" class="meta-box-preview-image%3$s" title="%7$s" alt="%6$s"%5$s />
        <a href="#" class="meta-box-upload-image-button button"%4$s%13$s>%8$s</a>
        <span class="meta-box-image-title"%5$s>%6$s</span>
        <a href="#" class="meta-box-clear-image-button"%5$s>%9$s</a>
      </div>',
      esc_attr( $image_id ),
      esc_attr( $this->name ),
      $class_icon,
      $hide_upload_button,
      $hide_clear_button,
      ( isset( $this->error ) ) ? esc_html( $this->error ) : $image_title,
      esc_attr__( 'Selected Image', 'am-cpts' ),
      esc_html__( 'Choose Image', 'am-cpts' ),
      esc_html__( 'Remove Image', 'am-cpts'),
      esc_url( $image_url ),
      $this->get_classes( 'meta-box-image' ),
      $this->get_data_atts(),
      $wp_media_data
    );


/*
    return sprintf( '
      <div%11$s>
        <input name="%2$s" type="hidden" class="meta-box-upload-image" value="%1$s"%12$s />
        <img src="%9$s" class="meta-box-preview-image" title="%7$s" alt="%6$s"%5$s />
        <a href="#" class="meta-box-clear-image-button"%5$s>%8$s</a>
        <span class="meta-box-image-title"%5$s>%10$s</span>
        <a href="#" class="meta-box-upload-image-button button" rel="%3$s"%4$s%13$s>%7$s</a>
      </div>',
      esc_attr( $image_id ),
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
    */
  }
}

?>