<?php

// TODO: datetime, time
/**
 * A simple date entry field.
 */
class AM_MBF_Date extends AM_MBF {
  protected static $type = 'date';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $size = ( intval( $this->size ) > 0 ) ? intval( $this->size ) : 14;
    return '<input type="text"' . $this->get_classes( 'datepicker' ) . ' name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value_old . '" size="' . $size . '"' . $this->get_data_atts() . ' />';
  }
}

?>