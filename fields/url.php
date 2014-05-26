<?php

/**
 * A simple url entry field.
 */
class AM_MBF_Url extends AM_MBF {
  protected static $type = 'url';
  protected $sanitizer = 'text_field'; // 'url' sanitizer removes text if it's not a correct email address.

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $size = ( intval( $this->size ) > 0 ) ? intval( $this->size ) : 30;
    return '<input type="url" name="' . $this->name . '" id="' . $this->id . '" value="' . esc_url_raw( $this->value_old ) . '"' . $this->get_classes( 'regular-text' ) . ' size="' . $size . '"' . $this->get_data_atts() . ' />';
  }
}

?>