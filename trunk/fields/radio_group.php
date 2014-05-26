<?php

// TODO: get data atts need to be compatible with options, get_data_atts() on radio elements?
/**
 * A simple radio group.
 */
class AM_MBF_RadioGroup extends AM_MBF {
  protected static $type = 'radio_group';
  protected $sanitizer = 'text';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    // Backup id.
    $id_bkp = $this->id;

    $ret .= '<ul class="meta-box-items">';
    foreach ( $this->options as $opt_value => $opt_label ) {
      $checked = checked( $this->value_old, $opt_value, false );
      $this->id .= $this->id . '-' . $opt_value; //???

      $ret .= '
        <li>
          <input type="radio" name="' . $this->name . '" id="' . $this->id . '" value="' . esc_attr( $opt_value ) . '" ' . $checked . $this->get_classes() . $this->get_data_atts() . ' />
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