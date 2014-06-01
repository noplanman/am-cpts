<?php

/**
 * A simple text entry field.
 *
 * @since 1.0.0
 */
class AM_MBF_Text extends AM_MBF {
  /**
   * Check AM_MBF for description.
   */
  protected static $type = 'text';

  /**
   * Check AM_MBF for description.
   */
  protected $sanitizer = 'text_field';

  /**
   * Check AM_MBF for description.
   */
  public function output() {
    return sprintf( '<input type="text" name="%2$s" id="%1$s" value="%3$s" size="%4$s"%5$s%6$s />',
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