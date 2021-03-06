<?php

/**
 * A simple url entry field.
 *
 * @since 1.0.0
 */
class AM_MBF_Url extends AM_MBF {
  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected static $type = 'url';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected $sanitizer = 'url';

  /**
   * Check AM_MBF for description.
   *
   * 'url' validator removes text if it's not a correct url.
   *
   * @since 1.2.0
   */
  protected $validator = 'text_field';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function output() {
    return sprintf( '<input type="url" name="%2$s" id="%1$s" value="%3$s"%4$s%5$s />',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      esc_attr( $this->value ),
      $this->get_classes( 'regular-text' ),
      $this->get_data_atts()
    );
  }
}

?>