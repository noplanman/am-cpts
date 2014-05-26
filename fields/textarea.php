<?php

// TODO: $size for cols and rows
/**
 * A simple textarea entry field.
 */
class AM_MBF_TextArea extends AM_MBF {
  protected static $type = 'textarea';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    return '<textarea name="' . $this->name . '" id="' . $this->id . '" cols="60" rows="4"' . $this->get_classes() . $this->get_data_atts() . '>' . esc_textarea( $this->value_old ) . '</textarea>';
  }
}

?>