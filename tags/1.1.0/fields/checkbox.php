<?php

/**
 * A simple checkbox.
 *
 * @since 1.0.0
 */
class AM_MBF_Checkbox extends AM_MBF {
  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected static $type = 'checkbox';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected $sanitizer = 'boolean';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function output() {
    return sprintf( '<label><input type="checkbox" name="%2$s" id="%1$s" value="1"%4$s%5$s%6$s />&nbsp;%3$s</label>',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      $this->get_label(),
      checked( $this->value, true, false ),
      $this->get_classes(),
      $this->get_data_atts()
    );
  }
}

/**
 * A simple checkbox group.
 *
 * @since 1.0.0
 */
class AM_MBF_CheckboxGroup extends AM_MBF {
  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected static $type = 'checkbox_group';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected $sanitizer = 'text_field';

  /**
   * Constructor to optionally define options.
   *
   * @since 1.0.0
   *
   * @param null|array $options Associative array of key-value pairs.
   */
  public function init( $options = null ) {
    $this->add_options( $options );
  }

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function output() {
    $ret = '<ul class="meta-box-items">';

    foreach ( $this->options as $opt_value => $opt_label ) {
      // Add option value before iteration id.
      $new_id = '';
      if ( $this->has_parent() ) {
        $id_parts = explode( '-', $this->id );
        array_splice( $id_parts, -1, 0, $opt_value );
        $new_id = implode( '-', $id_parts );
      } else {
        $new_id = $this->id . '-' . $opt_value;
      }

      $ret .= sprintf( '<li><label><input type="checkbox" value="%4$s" name="%2$s[]" id="%1$s"%5$s%6$s%7$s />&nbsp;%3$s</label></li>',
        esc_attr( $new_id ),
        esc_attr( $this->name ),
        esc_html( $opt_label ),
        esc_attr( $opt_value ),
        checked( is_array( $this->value ) && in_array( $opt_value, $this->value ), true, false ),
        $this->get_classes(),
        $this->get_data_atts()
      );
    }

    $ret .= '</ul>';

    return $ret;
  }
}

?>