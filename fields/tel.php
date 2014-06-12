<?php

/**
 * A simple telephone number entry field.
 *
 * @since 1.0.0
 */
class AM_MBF_Tel extends AM_MBF {
  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected static $type = 'tel';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected $sanitizer = 'text_field';

  /**
   * Check AM_MBF for description.
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
    return sprintf( '<input type="tel" name="%2$s" id="%1$s" value="%3$s"%4$s%5$s />',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      esc_attr( $this->value ),
      $this->get_classes( 'regular-text' ),
      $this->get_data_atts()
    );
  }
}

?>