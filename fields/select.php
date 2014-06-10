<?php

/**
 * A normal or 'chosen' drop down.
 *
 * Valid settings:
 * chosen    Display this drop down as a 'chosen' drop down.
 * force     Force a selection.
 * multiple  Multiple selection possible.
 *
 * @since 1.0.0
 */
class AM_MBF_Select extends AM_MBF {
  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected static $type = 'select';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected $sanitizer = 'text_field';

  /**
   * Constructor to optionally define options and settings.
   *
   * @since 1.0.0
   *
   * @param null|array $options Associative array of key-value pairs.
   * @param null|array $settings Associative array of key-value pairs.
   */
  public function init( $options = null, $settings = null ) {
    $this->add_options( $options );
    $this->add_settings( $settings );
  }

  /**
   * Check AM_MBF for description.
   *
   * @since 1.1.0
   */
  public function get_sub_type() {
    if ( $this->get_setting( 'chosen' ) ) {
      return 'chosen';
    }
  }

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function output() {
    // Check if this is a 'chosen' drop down.
    $chosen = $this->get_setting( 'chosen', false );

    // Add placeholder value used by chosen.
    if ( $chosen ) {
      $this->add_data( 'placeholder', __( 'Select One', 'am-cpts' ) );
    }

    $ret = sprintf( '<select name="%2$s[]" id="%1$s"%3$s%4$s%5$s>',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      ( $this->get_setting( 'multiple' ) ) ? ' multiple="multiple"' : '',
      $this->get_classes( ( $chosen ) ? 'chosen' : '' ),
      $this->get_data_atts()
    );
    if ( ! $this->get_setting( 'multiple' ) && ! $this->get_setting( 'force', false ) ) {
      $ret .= '<option value=""></option>'; // Select One
    }
    foreach ( $this->options as $opt_value => $opt_label ) {
      $ret .= sprintf( '<option value="%1$s"%3$s>%2$s</option>',
        esc_attr( $opt_value ),
        $opt_label,
        selected( is_array( $this->value ) && in_array( $opt_value, $this->value ), true, false )
      );
    }
    $ret .= '</select>';

    return $ret;
  }
}

?>