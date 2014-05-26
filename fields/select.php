<?php

/**
 * A drop down.
 */
class AM_MBF_Select extends AM_MBF {
  protected static $type = 'select';
  protected $sanitizer = 'text';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $multiple = ( $this->is_multiple ) ? ' multiple="multiple"' : '';
    $ret = '<select name="' . $this->name . '" id="' . $this->id . '"' . $multiple . $this->get_classes() . $this->get_data_atts() . '>';
    if ( ! $this->is_multiple ) {
      $ret .= '<option value=""></option>'; // Select One
    }
    foreach ( $this->options as $opt_value => $opt_label ) {
      $selected = selected( $this->value_old, $opt_value, false );
      $ret .= '<option value="' . $opt_value . '"' . $selected . '>' . $opt_label . '</option>';
    }
    $ret .= '</select>';
    $ret .= '<br class="clear" />' . $this->desc;
    return $ret;
  }
}

/**
 * A 'chosen' drop down.
 */
class AM_MBF_Chosen extends AM_MBF {
  protected static $type = 'chosen';
  protected $sanitizer = 'text';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $multiple = ( $this->is_multiple ) ? ' multiple="multiple"' : '';
    $ret = '<select data-placeholder="' . __( 'Select One', 'textdomain' ) . '" name="' . $this->name . '" id="' . $this->id . '"' . $multiple . $this->get_classes( 'chosen' ) . $this->get_data_atts() . '>';
    if ( ! $this->is_multiple ) {
      $ret .= '<option value=""></option>'; // Select One
    }
    foreach ( $this->options as $opt_value => $opt_label ) {
      $selected = selected( $this->value_old, $opt_value, false );
      $ret .= '<option value="' . $opt_value . '"' . $selected . '>' . $opt_label . '</option>';
    }
    $ret .= '</select>';
    $ret .= '<br class="clear" />' . $this->desc;
    return $ret;
  }
}

?>