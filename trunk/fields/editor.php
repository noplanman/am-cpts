<?php

/**
 * A TinyMCE editor.
 */
class AM_MBF_Editor extends AM_MBF {
  protected static $type = 'editor';
  protected $sanitizer = 'textarea';
  protected $is_repeatable = false;

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $this->add_setting( 'textarea_name', $this->name );
    $this->add_setting( 'editor_class', $this->get_classes( $this->get_setting( 'editor_class' ), false ) );

    ob_start();
    wp_editor( $this->value_old, $this->id, $this->get_settings() );
    return ob_get_clean() .
      '<br class="clear" />' . $this->desc; //???
  }
}

?>