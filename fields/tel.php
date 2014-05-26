<?php

/**
 * A simple telephone number entry field.
 */
class AM_MBF_Tel extends AM_MBF {
  protected static $type = 'tel';
  protected $sanitizer = 'text_field';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $size = ( intval( $this->size ) > 0 ) ? intval( $this->size ) : 30;
    return '<input type="tel" name="' . $this->name . '" id="' . $this->id . '" value="' . esc_attr( $this->value_old ) . '"' . $this->get_classes( 'regular-text' ) . ' size="' . $size . '"' . $this->get_data_atts() . ' />';
  }
}

?>