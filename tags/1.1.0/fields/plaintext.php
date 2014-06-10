<?php

/**
 * Output plain text as an h2 header. Use this to split the meta box into seperate sections.
 *
 * @since 1.0.0
 */
class AM_MBF_PlainText extends AM_MBF {
  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected static $type = 'plaintext';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function output() {
    return ( isset( $this->label ) && '' != $this->label )
      ? sprintf( '<div class="meta-box-plaintext"><h2>%1$s</h2><span class="description">%2$s</span></div>',
        $this->label,
        $this->get_desc()
      )
      : '';
  }

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function save( $post_id ) {
    // Nothing to be saved...
  }
}

?>