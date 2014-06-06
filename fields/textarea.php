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
    return sprintf( '<textarea name="%2$s" id="%1$s" cols="60" rows="4"%4$s%5$s>%3$s</textarea>',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      esc_textarea( $this->value ),
      $this->get_classes(),
      $this->get_data_atts()
    );
  }
}

?>