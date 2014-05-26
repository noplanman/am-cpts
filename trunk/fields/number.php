<?php

/**
 * A simple number entry field.
 */
class AM_MBF_Number extends AM_MBF {
  protected static $type = 'number';
  protected $sanitizer = 'intval';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $size = ( intval( $this->size ) > 0 ) ? intval( $this->size ) : 30;
    return '<input type="number" name="' . $this->name . '" id="' . $this->id . '" value="' . intval( $this->value_old ) . '"' . $this->get_classes( 'regular-text' ) . ' size="' . $size . '"' . $this->get_data_atts() . ' />';
  }
}

?>