<?php

/**
 * A simple email entry field.
 */
class AM_MBF_Email extends AM_MBF {
  protected static $type = 'email';
  protected $sanitizer = 'text_field'; // 'email' sanitizer removes text if it's not a correct email address.

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $size = ( intval( $this->size ) > 0 ) ? intval( $this->size ) : 30;
    return '<input type="email" name="' . $this->name . '" id="' . $this->id . '" value="' . esc_attr( $this->value_old ) . '"' . $this->get_classes( 'regular-text' ) . ' size="' . $size . '"' . $this->get_data_atts() . ' />';
  }
}

?>