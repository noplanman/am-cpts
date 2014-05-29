<?php

/**
 * A slider to input numbers.
 *
 * @todo Make repeatable!!
 *
 * @since 1.0.0
 */
class AM_MBF_Slider extends AM_MBF {
  protected static $type = 'slider';
  protected $sanitizer = 'floatval';
  protected $is_repeatable = false;

  /**
   * Constructor to optionally define settings.
   *
   * @since 1.0.0
   *
   * @param null|array $settings Associative array of key-value pairs.
   */
  public function __construct( $settings = null ) {
    $this->add_settings( $settings );
  }

  /**
   * Prepare values to be sanitized.
   *
   * @since 1.0.0
   */
  public function pre_sanitize() {
    if ( $this->is_saving ) {
      if ( isset( $this->value_new ) ) {
        $this->value_new = explode( ',', $this->value_new );
      }
    } else {
      if ( isset( $this->value_old ) ) {
        $this->value_old = explode( ',', $this->value_old );
      }
    }
  }

  /**
   * Put sanitized values back into the correct format.
   *
   * @since 1.0.0
   */
  public function post_sanitize() {
    if ( $this->is_saving ) {
      if ( is_array( $this->value_new ) ) {
        $this->value_new = implode( ',', $this->value_new );
      }
    } else {
      if ( is_array( $this->value_old ) ) {
        $this->value_old = implode( ',', $this->value_old );
      }
    }
  }

  /**
   * Check AM_MBF for description.
   */
  public function output() {

    // Get and assign all settings.
    $min = $this->get_setting( 'min', 0 );
    $max = $this->get_setting( 'max', 100 );
    $step = $this->get_setting( 'step', 1 );
    $range = ( $this->get_setting( 'range', false ) ) ? 'true' : 'false';
    $handles = $this->get_setting( 'handles', 1 );

    $values = $this->get_value_old();

    if ( ! is_array( $values ) ) {
      $values = array_filter( explode( ',', $values ) );
    }

    // Add all handles, even if there aren't enough saved values.
    while ( count( $values ) < intval( $handles ) ) {
      $values[] = $min;
    }
    asort( $values );

    // Make sure there aren't too many values.
    array_splice( $values, intval( $handles ) );

    $values = implode( ',', $values );

    // Add all settings to field as data.
    $this->add_data( array(
      'min' => $min,
      'max' => $max,
      'step' => $step,
      'values' => '[' . $values . ']',
      'range' => $range
    ) );

    return sprintf( '
      <div id="slider-%1$s"%4$s></div>
      <input type="hidden" name="%2$s" id="%1$s" value="%3$s"%5$s />',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      esc_attr( $this->value_old ),
      $this->get_classes(),
      $this->get_data_atts()
    );
  }
}

?>