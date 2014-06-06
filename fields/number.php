<?php

/**
 * A simple number entry field.
 *
 * @since 1.0.0
 */
class AM_MBF_Number extends AM_MBF {
  /**
   * Check AM_MBF for description.
   */
  protected static $type = 'number';

  /**
   * Check AM_MBF for description.
   */
  protected $sanitizer = 'intval';

  /**
   * Check AM_MBF for description.
   */
  public function output() {
    return sprintf( '<input type="number" name="%2$s" id="%1$s" value="%3$s" size="%4$s"%5$s%6$s />',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      ( $this->value ) ? intval( $this->value ) : 0,
      ( intval( $this->size ) > 0 ) ? intval( $this->size ) : 30,
      $this->get_classes( 'regular-text' ),
      $this->get_data_atts()
    );
  }
}

?>