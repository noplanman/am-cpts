<?php

/**
 * A slider to input numbers.
 */
class AM_MBF_Slider extends AM_MBF {
  protected static $type = 'slider';
  protected $sanitizer = 'floatval';

  /**
   * Prepare values to be sanitized.
   *
   * @since 1.0.0
   */
  public function pre_sanitize() {
    if ( $this->is_saving() ) {
      if ( isset( $this->value_new ) ) {
        $this->value_new = split( ',', $this->value_new );
      }
    } else {
      if ( isset( $this->value_old ) ) {
        $this->value_old = split( ',', $this->value_old );
      }
    }
  }

  /**
   * Put sanitized values back into the correct format.
   *
   * @since 1.0.0
   */
  public function post_sanitize() {
    if ( $this->is_saving() ) {
      if ( is_array( $this->value_new ) ) {
        $this->value_new = join( ',', $this->value_new );
      }
    } else {
      if ( is_array( $this->value_old ) ) {
        $this->value_old = join( ',', $this->value_old );
      }
    }
  }

  /**
   * Return the field output.
   *
   * @return string
   */
  public function output() {
    return '<div id="' . $this->id . '-slider"' . $this->get_classes() . '></div>
      <input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value_old . '"' . $this->get_data_atts() . ' />';
  }
}

?>