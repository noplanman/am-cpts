<?php

/**
 * A simple email entry field.
 *
 * @since 1.0.0
 */
class AM_MBF_Email extends AM_MBF {
  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected static $type = 'email';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected $sanitizer = 'email';

  /**
   * Check AM_MBF for description.
   *
   * 'email' validator removes text if it's not a correct email address.
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
    return sprintf( '<input type="email" name="%2$s" id="%1$s" value="%3$s"%4$s%5$s />',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      esc_attr( $this->value ),
      $this->get_classes( 'regular-text' ),
      $this->get_data_atts()
    );
  }
}

?>