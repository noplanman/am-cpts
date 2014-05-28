<?php

/**
 * A simple url entry field.
 */
class AM_MBF_Url extends AM_MBF {
  protected static $type = 'url';
  protected $sanitizer = 'text_field'; // 'url' sanitizer removes text if it's not a correct email address.

  /**
   * Check AM_MBF for description.
   */
  public function output() {
    return sprintf( '<input type="url" name="%2$s" id="%1$s" value="%3$s" size="%4$s"%5$s%6$s />',
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