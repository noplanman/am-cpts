<?php

/**
 * A simple checkbox.
 */
class AM_MBF_Checkbox extends AM_MBF {
  protected static $type = 'checkbox';
  protected $sanitizer = 'boolean';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $checked = checked( $this->value_old, true, false );
    return '<input type="checkbox"' . $this->get_classes() . ' name="' . $this->name . '" id="' . $this->id . '"' . $checked . ' value="1"' . $this->get_data_atts() . ' />
      <label for="' . $this->id . '">' . $this->get_label() . '</label>';
  }
}

/**
 * A simple checkbox group.
 */
class AM_MBF_CheckboxGroup extends AM_MBF {
  protected static $type = 'checkbox_group';
  protected $sanitizer = 'boolean';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    // Backup id.
    $id_bkp = $this->id;

    $ret = '<ul class="meta-box-items">';
    foreach ( $this->options as $opt_value => $opt_label ) {
      $checked = checked( is_array( $this->value_old ) && in_array( $opt_value, $this->value_old ), true, false );
      $this->id = $this->id . '-' . $opt_value; //???
      $ret .= '
        <li>
          <input type="checkbox" value="' . $opt_value . '" name="' . $this->name . '[]" id="' . $this->id . '"' . $checked . $this->get_classes() . $this->get_data_atts() . ' />
          <label for="' . $this->id . '">' . $opt_label . '</label>
        </li>
      ';
    }
    $ret .= '</ul>';
    $ret .= '<br class="clear" />' . $this->desc; //???

    // Revert id.
    $this->id = $id_bkp;

    return $ret;
  }
}

?>