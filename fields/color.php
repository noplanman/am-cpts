<?php

// TODO: use wp-color-picker?
// sanitizer?
/**
 * A color picker.
 */
class AM_MBF_Color extends AM_MBF {
  protected static $type = 'color';
  protected $sanitizer = 'color';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $value = ( $this->value_old ) ? $this->value_old : '#';
    $size = ( intval( $this->size ) > 0 ) ? intval( $this->size ) : 10;
    $ret = '<input type="text" name="' . $this->name . '" id="' . $this->id . '" value="' . $value . '" size="' . $size . '"' . $this->get_classes() . $this->get_data_atts() . ' />
      <div id="colorpicker-' . $this->id . '"></div>
        <script type="text/javascript">
        jQuery(function(jQuery) {
          jQuery("#colorpicker-' . $this->id . '").hide();
          jQuery("#colorpicker-' . $this->id . '").farbtastic("#' . $this->id . '");
          jQuery("#' . $this->id . '").bind("blur", function() { jQuery("#colorpicker-' . $this->id . '").hide(); } );
          jQuery("#' . $this->id . '").bind("focus", function() { jQuery("#colorpicker-' . $this->id . '").show(); } );
        });
        </script>';
    $ret .= '<br class="clear" />' . $this->desc; //???
    return $ret;
  }
}

?>