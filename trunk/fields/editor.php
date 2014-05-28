<?php

/**
 * A TinyMCE editor.
 */
class AM_MBF_Editor extends AM_MBF {
  protected static $type = 'editor';
  protected $sanitizer = 'textarea';
  protected $is_repeatable = false;

  /**
   * Constructor to optionally define settings.
   *
   * @since 1.0.0
   *
   * @param null|array $settings Associative array of key-value pairs.
   */
  public function __construct( $settings = null ) {
    $this->add_settings( $settings );
  }

  /**
   * Check AM_MBF for description.
   */
  public function output() {
    $this->add_setting( 'textarea_name', $this->name );
    $this->add_setting( 'editor_class', $this->get_classes( $this->get_setting( 'editor_class' ), false ) );

    ob_start();
    wp_editor( $this->value_old, $this->id, $this->get_settings() );
    return ob_get_clean();
  }
}

?>