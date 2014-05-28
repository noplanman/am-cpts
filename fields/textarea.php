<?php

/**
 * A simple textarea entry field.
 * @todo $size for cols and rows.
 */
class AM_MBF_TextArea extends AM_MBF {
  protected static $type = 'textarea';
  protected $sanitizer = 'textarea';

  /**
   * Check AM_MBF for description.
   */
  public function output() {
    return sprintf( '<textarea name="%2$s" id="%1$s" cols="60" rows="4"%4$s%5$s>%3$s</textarea>',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      esc_textarea( $this->value_old ),
      $this->get_classes(),
      $this->get_data_atts()
    );
  }
}

?>