<?php

/**
 * A simple textarea entry field.
 *
 * @since 1.0.0
 */
class AM_MBF_TextArea extends AM_MBF {
  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected static $type = 'textarea';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected $sanitizer = 'textarea';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.2.0
   */
  protected $validator = 'none';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function output() {
    // Get cols and rows sizes.
    $cols = intval( $this->get_setting( 'cols' ) );
    $rows = intval( $this->get_setting( 'rows' ) );

    return sprintf( '<textarea name="%2$s" id="%1$s" cols="%4$d" rows="%5$d"%6$s%7$s>%3$s</textarea>',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      $this->value,
      ( $cols > 0 ) ? $cols : 40,
      ( $rows > 0 ) ? $rows : 4,
      $this->get_classes(),
      $this->get_data_atts()
    );
  }
}

?>