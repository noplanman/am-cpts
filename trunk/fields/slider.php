<?php

/**
 * A slider to input numbers.
 */
class AM_MBF_Slider extends AM_MBF {
  protected static $type = 'slider';
  protected $sanitizer = 'floatval';

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
    if ( $this->is_saving ) {
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
   * Check AM_MBF for description.
   */
  public function output() {
    return sprintf( '
      <div id="%1$s-slider"%4$s></div>
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