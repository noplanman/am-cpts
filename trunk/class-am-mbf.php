<?php

/**
 * Require all field type classes.
 */
foreach ( glob( dirname( __FILE__ ) . '/fields/*.php' ) as $filename ) {
  require_once $filename;
}

/**
 * Abstract meta box field class which must be inherited by each meta box field.
 * Contains all functions required to manage fields.
 *
 * Abstract functions that MUST be overridden by each individual field:
 * output
 *
 * Some functions CAN be overridden by each individual field.
 *
 * @since 1.0.0
 */
abstract class AM_MBF {

  /**
   * The meta box object this field is assigned to.
   *
   * @since 1.0.0
   * @var AM_MB
   */
  protected $meta_box = null;

  /**
   * The field type.
   *
   * @since 1.0.0
   * @var string
   */
  protected static $type = ''; // Must be assigned by each field class individually!

  /**
   * The field's raw unique id, which is only set on construction.
   *
   * @since 1.0.0
   * @var string
   */
  protected $_id = '';

  /**
   * The field's unique id.
   *
   * @since 1.0.0
   * @var string
   */
  protected $id = '';

  /**
   * The label of the field.
   *
   * @since 1.0.0
   * @var string
   */
  protected $label = '';

  /**
   * The description of the field.
   *
   * @since 1.0.0
   * @var string
   */
  protected $desc = '';

  /**
   * The sanitizer to use for this field.
   *
   * @since 1.0.0
   * @var string
   */
  protected $sanitizer = 'text_field';

  /**
   * Check if this field is being saved or loaded (required for sanitization).
   *
   * @since 1.0.0
   * @var boolean
   */
  protected $is_saving = false;

  /**
   * The size of this field's input.
   *
   * @since 1.0.0
   * @var string
   */
  protected $size = null;

  /**
   * Additional data to be assigned to this field. Key-Value pair of 'data-' HTML tags.
   *
   * @since 1.0.0
   * @var array
   */
  protected $data = array();

  /**
   * Check / set if this field allows multiple selection.
   *
   * @since 1.0.0
   * @var boolean
   */
  protected $is_multiple = false;

  /**
   * Define if this field type is repeatable.
   *
   * @since 1.0.0
   * @var bool
   */
  protected $is_repeatable = true;

  /**
   * Define if this field is being repeated.
   *
   * @since 1.0.0
   * @var bool
   */
  protected $is_being_repeated = false;

  /**
   * The post type this field handles. (For all post fields.)
   *
   * @since 1.0.0
   * @var string
   */
  protected $post_type = '';

  /**
   * This field's options (Key-Value), used for checkbox- and radio groups.
   *
   * @since 1.0.0
   * @var array
   */
  protected $options = array();

  /**
   * Specific settings related to this field.
   *
   * @since 1.0.0
   * @var array
   */
  protected $settings = array();

  /**
   * The currently set meta value.
   *
   * @since 1.0.0
   * @var string|array
   */
  protected $value = null;

  /**
   * The new meta value to be set.
   *
   * @since 1.0.0
   * @var string|array
   */
  protected $value_new = null;

  /**
   * Initialize the field with core data.
   *
   * @since 1.0.0
   *
   * @param  string $id    Field id to set.
   * @param  string $label Field label to display.
   * @param  string $desc  Field description to display.
   */
  final public function __construct( $id, $label, $desc ) {
    $this->set_id( $id );
    // Set raw id.
    $this->_id = $this->id;
    $this->set_name( $id ); // Set name to be the same as id.
    $this->set_label( $label );
    $this->set_desc( $desc );
  }

  /**
   * Create a new field object and return it after it has been initialized.
   * This method is a dynamic field factory that searches all field classes and creates the appropriate field object.
   *
   * @since 1.0.0
   *
   * @param string $type      The type of field to create.
   * @param string $id        The field's unique id.
   * @param string $label     The label of the field.
   * @param string $desc      The description of the field.
   * @param object $param_arr A variable amount of parameters that will be passed to the field's constructor.
   *                           (Note: the variable $param_arr is just a placeholder for explanitory reasons, it is not used in any way!)
   * @return AM_MBF            The specific meta box field object.
   */
  final public static function create( $type, $id, $label, $desc = '', $param_arr = null ) {
    $field = null;

    /**
     * Find all declared AM_MBF classes and create the respective field type object if found.
     */
    foreach ( get_declared_classes() as $mbf_class ) {
      if ( 0 === strpos( $mbf_class, 'AM_MBF_' ) && is_subclass_of( $mbf_class, 'AM_MBF' ) ) {
        if ( method_exists( $mbf_class, 'get_type' ) && $type == call_user_func( array( $mbf_class, 'get_type' ) ) ) {

          $field = new ReflectionClass( $mbf_class );
          $field = $field->newInstanceArgs( array( $id, $label, $desc ) );

          // Make sure our field is legitimate and initialize it.
          if ( $field instanceof AM_MBF ) {
            if ( method_exists( $field, 'init' ) ) {
              /**
               *  Call init method with the passed parameters.
               *  These parameters are variable, so the first elements must be sliced off.
               */
              ( new ReflectionMethod( $field, 'init' ) )->invokeArgs( $field, array_slice( func_get_args(), 4 ) );
            }
          } else {
            $field = null;
          }

          // Our field has already been found, jump out of foreach loop.
          break;
        }
      }
    }

    return $field;
  }

  /**
   * Create multiple field objects at once by passing multiple arrays of field data.
   *
   * @since 1.0.0
   *
   * @param  array $fields Variable list of arrays that each contain field data.
   * @return array An array of AM_MBF objects.
   */
  final public static function create_batch( $fields ) {
    // Array of newly created fields to return.
    $new_fields = array();

    // Get dynamic arrays of field data.
    $fields = func_get_args();

    // Create each new field.
    foreach ( $fields as $field ) {
      if ( is_array( $field ) ) {
        $new_fields[] = call_user_func_array( array( 'AM_MBF', 'create'), array_values( $field ) );
      }
    }

    // Remove empty / null values.
    return array_filter( $new_fields );
  }

  /**
   * Get the type of this field. This is used by the create function to dynamically fetch the type.
   *
   * @since 1.0.0
   *
   * @return string
   */
  final public static function get_type() {
    return static::$type;
  }


  /**
   * Modify the new values before it gets sanitized. Could be used to bring the data into the right format.
   */
  protected function pre_sanitize() {
    // This can be overridden if necessary.
  }

  /**
   * Modify the new values after they have been  it gets sanitized. Could be used to bring the data into the right format.
   */
  protected function post_sanitize() {
    // This can be overridden if necessary.
  }

  /**
   * Sanitize the new value of this field and all repeatable fields.
   */
  public function sanitize() {
    // Pre-Sanitize new value data.
    $this->pre_sanitize();

    // Check which values have to be sanitized, the old or new ones.
    $values_to_sanitize = ( $this->is_saving ) ? $this->value_new : $this->value;

    if ( isset( $values_to_sanitize ) ) {

      // If the value to sanitize is an array, sanitize each individual element of the array.
      // Remember if the value was an array to begin with, because an array is created anyways to loop through the entries.
      $was_array = true;
      if ( ! is_array( $values_to_sanitize ) ) {
        $values_to_sanitize = array ( $values_to_sanitize );
        $was_array = false;
      }

      $values_sanitized = array();
      foreach ( $values_to_sanitize as $key => $value ) {
        switch ( $this->sanitizer ) {
          case 'absint':
            $values_sanitized[ $key ] = absint( $value ); break;
          case 'intval':
            $values_sanitized[ $key ] = intval( $value ); break;
          case 'floatval':
            $values_sanitized[ $key ] = floatval( $value ); break;
          case 'hexval';
            $values_sanitized[ $key ] = preg_replace('/[^a-f0-9]+/', '', strtolower( $value ) ); break;
          case 'textarea':
            $values_sanitized[ $key ] = esc_textarea( $value ); break;
          case 'kses_post':
            $values_sanitized[ $key ] = wp_kses_post( $value ); break;
          case 'kses_data':
            $values_sanitized[ $key ] = wp_kses_data( $value ); break;
          case 'url':
            $values_sanitized[ $key ] = esc_url_raw( $value ); break;
          case 'email':
            $values_sanitized[ $key ] = sanitize_email( $value ); break;
          case 'title':
            $values_sanitized[ $key ] = sanitize_title( $value ); break;
          case 'boolean':
            $values_sanitized[ $key ] = ( isset( $value ) && ( intval( $value ) === 1 || true === $value || 'true' === trim( $value ) ) ); break;
          case 'text_field':
          default:
            $values_sanitized[ $key ] = sanitize_text_field( $value );
        }
      }


      // If the value was an array to start with, just take the first entry of the newly sanitized array.
      if ( ! $was_array ) {
        $values_sanitized = reset( $values_sanitized );
      }

      if ( $this->is_saving ) {
        $this->value_new = $values_sanitized;
      } else {
        $this->value = $values_sanitized;
      }
    }

    // Post-Sanitize new value data.
    $this->post_sanitize();
  }


  /**
   * Set the meta box this field is assigned to.
   *
   * @since 1.0.0
   *
   * @param AM_MB $meta_box
   */
  final public function set_meta_box( $meta_box ) {
    // Make sure the  right type is passed.
    if ( $meta_box instanceof AM_MB ) {
      $this->meta_box = $meta_box;
    }
  }

  /**
   * Get the meta box this field is assigned to.
   *
   * @since 1.0.0
   *
   * @return AM_MB
   */
  final public function get_meta_box() {
    return $this->meta_box;
  }

  /**
   * Set the field's id.
   *
   * @since 1.0.0
   *
   * @param string $id
   */
  final public function set_id( $id ) {
    $this->id = sanitize_title( $id );
  }

  /**
   * Get the field's id.
   *
   * @since 1.0.0
   *
   * @return string
   */
  final public function get_id( $raw = false) {
    return ( $raw ) ? $this->_id : $this->id;
  }

  /**
   * Set the field's name.
   *
   * @since 1.0.0
   *
   * @param string $name
   */
  final public function set_name( $name ) {
    $this->name = $name;
  }

  /**
   * Get the field's name.
   *
   * @since 1.0.0
   *
   * @return string
   */
  final public function get_name() {
    return $this->name;
  }

  /**
   * Set the field's label.
   *
   * @since 1.0.0
   *
   * @param string $label
   */
  final public function set_label( $label ) {
    $this->label = $label;
  }

  /**
   * Get the field's label.
   *
   * @since 1.0.0
   *
   * @return string
   */
  final public function get_label() {
    return $this->label;
  }

  /**
   * Set the field's description.
   *
   * @since 1.0.0
   *
   * @param string $desc
   */
  final public function set_desc( $desc ) {
    $this->desc = $desc;
  }

  /**
   * Get the field's description.
   *
   * @since 1.0.0
   *
   * @return string
   */
  final public function get_desc() {
    return $this->desc;
  }

  /**
   * Set the field's sanitizer.
   *
   * @since 1.0.0
   *
   * @param string $sanitizer
   */
  final public function set_sanitizer( $sanitizer ) {
    $this->sanitizer = $sanitizer;
  }

  /**
   * Get this field's sanitizer.
   *
   * @since 1.0.0
   *
   * @return string
   */
  final public function get_sanitizer() {
    return $this->sanitizer;
  }

  /**
   * Set this field's input size.
   *
   * @since 1.0.0
   *
   * @param string $size
   */
  final public function set_size( $size ) {
    $this->size = $size;
  }

  /**
   * Get this field's input size.
   *
   * @since 1.0.0
   *
   * @return string
   */
  final public function get_size() {
    return $this->size;
  }

  /**
   * Generate some default classes related to this field.
   *
   * @since 1.0.0
   *
   * @param  string  $more_classes Additional classes to append to the generated list.
   * @param  boolean $make_att     If a 'class=""' structure should be returned or just an array of classes.
   * @return string|array          Either a 'class=""' string or an array of classes.
   */
  final public function get_classes( $more_classes = '', $make_att = true ) {
    if ( ! is_array( $more_classes ) ) {
      // Split passed class string by space and comma.
      $more_classes = preg_split( '/[\s,]+/', $more_classes, null, PREG_SPLIT_NO_EMPTY );
    }

    $classes = array( 'mbf-id-' . $this->id, 'mbf-type-' . static::$type );
    $classes = array_unique( array_merge( $classes, $more_classes ) );
    $classes = implode( ' ', $classes );

    return ( $make_att ) ? ' class="' . $classes . '"' : $classes;
  }

  /**
   * Add data attributes to this field.
   *
   * @since 1.0.0
   *
   * @param string|array $key   Key string or entire array of Key-Value pairs.
   * @param object|null $value  Value string or null (if $key is array).
   */
  final public function add_data( $key, $value = null ) {
    if ( is_array( $key ) && ! isset( $value ) ) {

      // Trim all keys and values and merge with data array.
      $keys   = array_map( 'trim', array_keys( $key ) );
      $values = array_map( 'trim', array_values( $key ) );

      $this->data = array_merge( $this->data, array_combine ( $keys , $values ) );
    } elseif ( isset( $key ) && isset( $value ) ) {
      $this->data[ trim( $key ) ] = trim( $value );
    }
  }

  /**
   * Remove data attributes from this field.
   *
   * @since 1.0.0
   *
   * @param string|array $keys Attributes to remove.
   */
  final public function remove_data( $keys ) {
    if ( ! isset( $keys ) ) {
      return;
    }

    // Make sure we have an array to work with. If we have comma seperated values, make them into an array.
    if ( ! is_array( $keys ) ) {
      $keys = explode( ',', $keys );
    }

    // Trim all entries.
    $keys = array_map( 'trim', $keys );

    // Remove all data attributes.
    $this->data = array_diff_key( $this->data, array_flip( $keys ) );
  }

  /**
   * Get specific data value(s).
   *
   * @since 1.0.0
   *
   * @param  string $key Either an individual data entry or, if null, all data values.
   * @return object|array
   */
  final public function get_data( $key = null, $default = null ) {
    if ( isset( $key ) ) {
      if ( array_key_exists( $key, $this->options ) ) {
        return $this->options[ $key ];
      } else {
        return $default;
      }
    } else {
      return $this->data;
    }
  }

  /**
   * Get concatenated string of 'data-$key="$value"' attributes.
   *
   * @since 1.0.0
   *
   * @param array $new_data Data to be added before getting the formatted string.
   * @return string
   */
  final public function get_data_atts( $new_data = null ) {
    // If new data should be added too, do that first.
    $this->add_data( $new_data );

    // Now make a string of all data attributes.
    $data_atts = '';
    foreach ( $this->data as $key => $value ) {
      $data_atts .= ' data-' . $key . '="' . esc_attr( $value ) . '"';
    }
    return $data_atts;
  }

  /**
   * Get or set the saving flag. Defines if the field is currently being saved or loaded.
   *
   * @since 1.0.0
   *
   * @param  bool|null $is_saving If bool, set the passed value, else return the set value.
   * @return boolean
   */
  final public function is_saving( $is_saving = null ) {
    if ( is_bool( $is_saving ) ) {
      $this->is_saving = $is_saving;
    }
    return $this->is_saving;
  }

  /**
   * Get or set the multiple flag. Defines if multiple entries can be selected for a select field.
   *
   * @since 1.0.0
   *
   * @param  bool|null $is_multiple If bool, set the passed value, else return the set value.
   * @return boolean
   */
  final public function is_multiple( $is_multiple = null ) {
    if ( is_bool( $is_multiple ) ) {
      $this->is_multiple = $is_multiple;
    }
    return $this->is_multiple;
  }

  /**
   * Find out if this field type is repeatable.
   *
   * @since 1.0.0
   *
   * @return bool
   */
  final public function is_repeatable() {
    return $this->is_repeatable;
  }

  /**
   * Get or set the being repeated flag. Defines if this field is being repeated within a repeatable field.
   *
   * @since 1.0.0
   *
   * @param  bool|null $is_being_repeated If bool, set the passed value, else return the set value.
   * @return boolean
   */
  final public function is_being_repeated( $is_being_repeated = null ) {
    if ( is_bool( $is_being_repeated ) ) {
      $this->is_being_repeated = $is_being_repeated;
    }
    return $this->is_being_repeated;
  }

  /**
   * Set the post type for this field, if it relies on post type information.
   *
   * @since 1.0.0
   *
   * @param string $post_type
   */
  final public function set_post_type( $post_type ) {
    $this->post_type = $post_type;
  }

  /**
   * Get post type of this field.
   *
   * @since 1.0.0
   *
   * @return string
   */
  final public function get_post_type() {
    return $this->post_type;
  }

  /**
   * Add new options to this field.
   *
   * @since 1.0.0
   *
   * @param string|array $key   Key string or entire array of Key-Value pairs.
   * @param object|null  $value Option value or null (if $key is array).
   */
  final public function add_option( $key, $value = null ) {
    if ( is_array( $key ) && ! isset( $value ) ) {

      // Trim all keys and values and merge with data array.
      $keys   = array_map( 'trim', array_keys( $key ) );
      $values = array_map( 'trim', array_values( $key ) );

      $this->options = array_merge( $this->options, array_combine ( $keys , $values ) );
    } elseif ( isset( $key ) && isset( $value ) ) {
      $this->options[ trim( $key ) ] = trim( $value );
    }
  }

  /**
   * Add new options to this field.
   * (Facade function to add multiple options as an array.)
   *
   * @since 1.0.0
   *
   * @param array $options Array of Key-Value pairs.
   */
  final public function add_options( $options ) {
    $this->add_option( $options );
  }

  /**
   * Remove options from this field.
   *
   * @since 1.0.0
   *
   * @param string|array $keys Options to remove. (Single key, comma seperated keys, array of keys.)
   */
  final public function remove_option( $keys ) {
    if ( ! isset( $keys ) ) {
      return;
    }

    // Make sure we have an array to work with. If we have comma seperated values, make them into an array.
    if ( ! is_array( $keys ) ) {
      $keys = explode( ',', $keys );
    }

    // Trim all entries.
    $keys = array_map( 'trim', $keys );

    // Remove all options.
    $this->options = array_diff_key( $this->options, array_flip( $keys ) );
  }

  /**
   * Remove options from this field.
   * (Facade function to remove multiple options as an array.)
   *
   * @since 1.0.0
   *
   * @param array $options.
   */
  final public function remove_options( $options ) {
    $this->remove_option( $options );
  }

  /**
   * Get a specific option.
   *
   * @since 1.0.0
   *
   * @param  string $key
   * @param  string|array $default Default value if option doesn't exist.
   * @return object|array
   */
  final public function get_option( $key, $default = null ) {
    return ( isset( $key ) && array_key_exists( $key, $this->options ) ) ? $this->options[ $key ] : $default;
  }

  /**
   * Get all options.
   *
   * @since 1.0.0
   *
   * @return array
   */
  final public function get_options() {
    return $this->options;
  }

  /**
   * Add new settings to this field.
   *
   * @since 1.0.0
   *
   * @param string|array $key   Key string or entire array of Key-Value pairs.
   * @param object|null  $value Setting value or null (if $key is array).
   */
  final public function add_setting( $key, $value = null ) {
    if ( is_array( $key ) && ! isset( $value ) ) {

      // Trim all keys and values and merge with data array.
      $keys   = array_map( 'trim', array_keys( $key ) );
      $values = array_map( 'trim', array_values( $key ) );

      $this->settings = array_merge( $this->settings, array_combine ( $keys , $values ) );
    } elseif ( isset( $key ) && isset( $value ) ) {
      $this->settings[ trim( $key ) ] = trim( $value );
    }
  }

  /**
   * Add new settings to this field.
   * (Facade function to add multiple settings as an array.)
   *
   * @since 1.0.0
   *
   * @param array $settings Array of Key-Value pairs.
   */
  final public function add_settings( $settings ) {
    $this->add_setting( $settings );
  }

  /**
   * Remove settings from this field.
   *
   * @since 1.0.0
   *
   * @param string|array $keys Settings to remove. (Single key, comma seperated keys, array of keys.)
   */
  final public function remove_setting( $keys ) {
    if ( ! isset( $keys ) ) {
      return;
    }

    // Make sure we have an array to work with. If we have comma seperated values, make them into an array.
    if ( ! is_array( $keys ) ) {
      $keys = explode( ',', $keys );
    }

    // Trim all entries.
    $keys = array_map( 'trim', $keys );

    // Remove all settings.
    $this->settings = array_diff_key( $this->settings, array_flip( $keys ) );
  }

  /**
   * Remove settings from this field.
   * (Facade function to remove multiple settings as an array.)
   *
   * @since 1.0.0
   *
   * @param array $settings.
   */
  final public function remove_settings( $settings ) {
    $this->remove_setting( $settings );
  }

  /**
   * Get a specific setting.
   *
   * @since 1.0.0
   *
   * @param  string $key
   * @param  string|array $default Default value if setting doesn't exist.
   * @return object|array
   */
  final public function get_setting( $key, $default = null ) {
    return ( isset( $key ) && array_key_exists( $key, $this->settings ) ) ? $this->settings[ $key ] : $default;
  }

  /**
   * Get all settings.
   *
   * @since 1.0.0
   *
   * @return array
   */
  final public function get_settings() {
    return $this->settings;
  }

  /**
   * Get all repeatable fields.
   *
   * @since 1.0.0
   *
   * @return array
   */
  final public function get_repeatable_fields() {
    return $this->repeatable_fields;
  }

  /**
   * Set the old/current meta value of this field.
   *
   * @since 1.0.0
   *
   * @param object|array $value
   */
  public function set_value( $value ) {
    $this->value = $value;
  }

  /**
   * Get the old/current meta value of this field.
   *
   * @since 1.0.0
   *
   * @return object|array
   */
  public function get_value() {
    return $this->value;
  }

  /**
   * Set the new meta value for this field.
   *
   * @since 1.0.0
   *
   * @param object|array $value_new
   */
  public function set_value_new( $value_new ) {
    $this->value_new = $value_new;
  }

  /**
   * Get the new meta value for this field.
   *
   * @since 1.0.0
   *
   * @return object|array
   */
  public function get_value_new() {
    return $this->value_new;
  }

  /**
   * Add a child field to this field.
   *
   * @since 1.0.0
   *
   * @param AM_MBF|array $fields Object or array of AM_MBF to add to this field.
   */
  public function add_field( $fields ) {
    // Gets overridden by repeatable field type, because only the repeatable field can contain child fields.
  }

  /**
   * Saves the field data as meta data for the passed post id.
   *
   * @since 1.0.0
   *
   * @param integer $post_id ID of the post being saved.
   */
  public function save( $post_id ) {
    if ( ! isset( $this->value_new ) || '' == $this->value_new || array() == $this->value_new ) {
      // Remove the post meta data.
      delete_post_meta( $post_id, $this->id, $this->value );
    } elseif ( $this->value_new != $this->value ) {
      // Add / update the post meta data.
      update_post_meta( $post_id, $this->id, $this->value_new );
    }
  }

  /**
   * Output the HTML of the current field. This must be overridden by each field individually.
   *
   * @since 1.0.0
   *
   * @return string
   */
  abstract public function output();
}

?>