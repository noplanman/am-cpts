<?php

/**
 * A drop down.
 *
 * @since 1.0.0
 */
class AM_MBF_Select extends AM_MBF {
  /**
   * Check AM_MBF for description.
   */
  protected static $type = 'select';

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
    $ret = sprintf( '<select name="%2$s" id="%1$s"%3$s%4$s%5$s>',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      ( $this->is_multiple ) ? ' multiple="multiple"' : '',
      $this->get_classes(),
      $this->get_data_atts()
    );
    if ( ! $this->is_multiple ) {
      $ret .= '<option value=""></option>'; // Select One
    }
    foreach ( $this->options as $opt_value => $opt_label ) {
      $ret .= sprintf( '<option value="%1$s"%3$s>%2$s</option>',
        esc_attr( $opt_value ),
        $opt_label,
        selected( $this->value_old, $opt_value, false )
      );
    }
    $ret .= '</select>';

    return $ret;
  }
}

/**
 * A 'chosen' drop down.
 *
 * @since 1.0.0
 */
class AM_MBF_Chosen extends AM_MBF {
  /**
   * Check AM_MBF for description.
   */
  protected static $type = 'chosen';

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
    // Add placeholder value used by chosen.
    $this->add_data( 'placeholder', __( 'Select One', 'am-cpts' ) );

    $ret = sprintf( '<select name="%2$s" id="%1$s"%3$s%4$s%5$s>',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      ( $this->is_multiple ) ? ' multiple="multiple"' : '',
      $this->get_classes( 'chosen' ),
      $this->get_data_atts()
    );

    if ( ! $this->is_multiple ) {
      $ret .= '<option value=""></option>'; // Select One
    }
    foreach ( $this->options as $opt_value => $opt_label ) {
      $ret .= sprintf( '<option value="%1$s"%3$s>%2$s</option>',
        esc_attr( $opt_value ),
        $opt_label,
        selected( $this->value_old, $opt_value, false )
      );
    }
    $ret .= '</select>';

    return $ret;
  }
}

?>