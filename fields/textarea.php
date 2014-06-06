<?php

/**
 * A simple textarea entry field.
 *
 * @todo $size for cols and rows (i.e. $size="<cols>,<rows>").
 *
 * @since 1.0.0
 */
class AM_MBF_TextArea extends AM_MBF {
  /**
   * Check AM_MBF for description.
   */
  protected static $type = 'textarea';

  /**
   * Check AM_MBF for description.
   */
  protected $sanitizer = 'textarea';

  /**
   * Check AM_MBF for description.
   */
  public function output() {
    // Get cols and rows sizes.
    $size = array_map( 'intval', array_pad( explode( ',', $this->size ), 2, 0 ) );

    return sprintf( '<textarea name="%2$s" id="%1$s" cols="%4$d" rows="%5$d"%6$s%7$s>%3$s</textarea>',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      esc_textarea( $this->value ),
      ( $size[0] > 0 ) ? $size[0] : 60,
      ( $size[1] > 0 ) ? $size[1] : 4,
      $this->get_classes(),
      $this->get_data_atts()
    );
  }
}

?>