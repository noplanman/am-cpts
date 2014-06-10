<?php

/**
 * A simple date entry field.
 *
 * @todo datetime, time.
 * @todo More options. Check jQuery UI for all possibilities.
 *
 * @since 1.0.0
 */
class AM_MBF_Date extends AM_MBF {
  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected static $type = 'date';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected $sanitizer = 'text_field';

  /**
   * Constructor to optionally define settings.
   *
   * @since 1.0.0
   *
   * @param null|array $settings Associative array of key-value pairs.
   */
  public function init( $settings = null ) {
    $this->add_settings( $settings );
  }

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function output() {
    // Set dateformat data.
    $this->add_data( 'dateformat', $this->get_setting( 'dateformat', 'dd.mm.yy' ) );

    return sprintf( '<input type="text" name="%2$s" id="%1$s" value="%3$s" size="%4$s"%5$s%6$s />',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      esc_attr( $this->value ),
      ( intval( $this->size ) > 0 ) ? intval( $this->size ) : 14,
      $this->get_classes(),
      $this->get_data_atts()
    );
  }
}

?>