<?php

/**
 * A color picker.
 *
 * @todo Use wp-color-picker?
 *
 * @since 1.0.0
 */
class AM_MBF_Color extends AM_MBF {
  protected static $type = 'color';
  protected $sanitizer = 'hexval';

  /**
   * Check AM_MBF for description.
   */
  public function post_sanitize() {
    // Check which value has been sanitized, the old or new one.
    $sanitized_value = ( $this->is_saving ) ? $this->value_new : $this->value_old;

    if ( 3 === strlen( $sanitized_value ) ) {
      // Convert from short to long form (i.e: '0f0' => '00ff00').
      $sanitized_value = sprintf( '%1$d%1$d%2$d%2$d%3$d%3$d',
        $sanitized_value[0],
        $sanitized_value[1],
        $sanitized_value[2]
      );
    } else {
      // Color value.
      $sanitized_value = '#' . substr( str_pad( $sanitized_value, 6, '0', STR_PAD_LEFT ), 0, 6 );
    }

    // Set new sanitized value.
    if ( $this->is_saving ) {
      $this->value_new = $sanitized_value;
    } else {
      $this->value_old = $sanitized_value;
    }
  }

  /**
   * Check AM_MBF for description.
   */
  public function output() {
    return sprintf( '<input type="text" name="%2$s" id="%1$s" value="%3$s" size="%4$s"%5$s%6$s /><div id="colorpicker-%1$s"></div>',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      ( '' != $this->value_old ) ? esc_attr( $this->value_old ) : '#',
      ( intval( $this->size ) > 0 ) ? intval( $this->size ) : 10,
      $this->get_classes(),
      $this->get_data_atts()
    );
  }
}

?>