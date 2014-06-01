<?php

/**
 * A simple radio group.
 *
 * @since 1.0.0
 */
class AM_MBF_RadioGroup extends AM_MBF {
  /**
   * Check AM_MBF for description.
   */
  protected static $type = 'radio_group';

  /**
   * Check AM_MBF for description.
   */
  protected $sanitizer = 'text_field';

  /**
   * Constructor to optionally define options.
   *
   * @since 1.0.0
   *
   * @param null|array $options Associative array of key-value pairs.
   */
  public function __construct( $options = null ) {
    $this->add_options( $options );
  }

  /**
   * Check AM_MBF for description.
   */
  public function output() {
    $ret = '<ul class="meta-box-items">';

    foreach ( $this->options as $opt_value => $opt_label ) {
      // Add option value before iteration id.
      $new_id = '';
      if ( $this->is_being_repeated ) {
        $id_parts = explode( '-', $this->id );
        array_splice( $id_parts, -1, 0, $opt_value );
        $new_id = implode( '-', $id_parts );
      } else {
        $new_id = $this->id . '-' . $opt_value;
      }

      // Add option value to data as subid, cause it will be used by jQuery to correctly rewrite ids and names.
      $this->add_data( 'subid', $opt_value );

      $ret .= sprintf( '<li><label><input type="radio" value="%4$s" name="%2$s" id="%1$s"%5$s%6$s%7$s />&nbsp;%3$s</label></li>',
        esc_attr( $new_id ),
        esc_attr( $this->name ),
        $opt_label,
        esc_attr( $opt_value ),
        checked( $this->value_old, $opt_value, false ),
        $this->get_classes(),
        $this->get_data_atts()
      );
    }

    $ret .= '</ul>';

    return $ret;
  }
}

?>