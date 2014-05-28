<?php

/**
 * Output plain text as an h2 header. Use this to split the meta box into seperate sections.
 */
class AM_MBF_PlainText extends AM_MBF {
  protected static $type = 'plaintext';

  /**
   * Check AM_MBF for description.
   */
  public function output() {
    return ( isset( $this->label ) && '' != $this->label ) ? sprintf( '<h2>%1$s</h2>', esc_html( $this->label ) ) : '';
  }

  /**
   * Override default save function.
   */
  public function save( $post_id ) {
    // Nothing to be saved...
  }
}

?>