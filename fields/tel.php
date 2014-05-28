<?php

/**
 * A simple telephone number entry field.
 */
class AM_MBF_Tel extends AM_MBF {
  protected static $type = 'tel';
  protected $sanitizer = 'text_field';

  /**
   * Check AM_MBF for description.
   */
  public function output() {
    return sprintf( '<input type="tel" name="%2$s" id="%1$s" value="%3$s" size="%4$s"%5$s%6$s />',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      esc_attr( $this->value_old ),
      ( intval( $this->size ) > 0 ) ? intval( $this->size ) : 30,
      $this->get_classes( 'regular-text' ),
      $this->get_data_atts()
    );
  }
}

?>