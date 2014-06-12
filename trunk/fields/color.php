<?php

/**
 * A color picker.
 *
 * @todo Save 'transparent'?
 *
 * @since 1.0.0
 */
class AM_MBF_Color extends AM_MBF {
  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected static $type = 'color';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected $sanitizer = 'hexval';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.2.0
   */
  protected $validator = 'hexval';

  /**
   * Convert hex value to color with format #rrggbb.
   *
   * @since 1.2.0
   *
   * @param  string $hex_value The hex string to convert.
   * @return string            The color as a string with format #rrggbb.
   */
  private function _convert_to_color( $hex_value ) {
    if ( ! is_string( $hex_value ) || '' == $hex_value ) {
      $hex_value = '';
    } elseif ( 3 === strlen( $hex_value ) ) {
      // Convert from short to long form (i.e.: '0f0' => '00ff00').
      $hex_value = sprintf( '#%1$d%1$d%2$d%2$d%3$d%3$d',
        $hex_value[0],
        $hex_value[1],
        $hex_value[2]
      );
    } else {
      // Color value. Pad incomplete colors with 0 (i.e.: 'f46d' => '00f46d').
      $hex_value = '#' . substr( str_pad( $hex_value, 6, '0', STR_PAD_LEFT ), 0, 6 );
    }

    return $hex_value;
  }

  /**
   * Convert the validated string into a color. Check AM_MBF for description.
   *
   * @since 1.2.0
   */
  public function post_validate() {
    // Check which value has been validated, the old or new one.
    if ( $this->is_saving ) {
      $this->value_new = $this->_convert_to_color( $this->value_new );
    } else {
      $this->value = $this->_convert_to_color( $this->value );
    }
  }

  /**
   * Convert the sanitized string into a color. Check AM_MBF for description.
   *
   * @since 1.2.0
   */
  public function post_sanitize() {
    // Check which value has been sanitized, the old or new one.
    if ( $this->is_saving ) {
      $this->value_new = $this->_convert_to_color( $this->value_new );
    } else {
      $this->value = $this->_convert_to_color( $this->value );
    }
  }

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function output() {
    return sprintf( '<input type="text" name="%2$s" id="%1$s" value="%3$s" %4$s%5$s />',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      ( '' != $this->value ) ? esc_attr( $this->value ) : '#',
      $this->get_classes( 'wp-color-picker' ),
      $this->get_data_atts()
    );
  }
}

?>