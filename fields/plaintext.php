<?php

/**
 * Output plain text as an h2 header. Use this to split the meta box into seperate sections.
 */
class AM_MBF_PlainText extends AM_MBF {
  protected static $type = 'plaintext';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $label = ( isset( $this->label ) && '' != $this->label ) ? '<h2>' . $this->label . '</h2>' : '';
    return $label . $this->desc;
  }

  /**
   * Override default save function.
   */
  public function save( $post_id ) {
    // Nothing to be saved...
  }
}

?>