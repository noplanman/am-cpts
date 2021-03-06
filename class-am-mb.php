<?php

/**
 * Meta box class which handles all meta box functionality.
 * Meta box fields (AM_MBF) can be added to meta boxes.
 *
 * Based on https://github.com/tammyhart/Reusable-Custom-WordPress-Meta-Boxes
 *
 * @since 1.0.0
 */
class AM_MB {

  /**
   * Array of post types that will have this meta box assigned to.
   *
   * @since 1.0.0
   * @var array
   */
  protected $post_types = array();

  /**
   * ID of this meta box.
   *
   * @since 1.0.0
   * @var string
   */
  protected $id = '';

  /**
   * Title of this meta box.
   *
   * @since 1.0.0
   * @var string
   */
  protected $title = '';

  /**
   * All fields (AM_MBF) assigned to this meta box.
   *
   * @since 1.0.0
   * @var array
   */
  protected $fields = array();

  /**
   * Meta box priority.
   *
   * @since 1.0.0
   * @var string
   */
  protected $priority = 'high';

  /**
   * Meta box context.
   *
   * @since 1.0.0
   * @var string
   */
  protected $context = 'normal';

  /**
   * An array of all created AM_MB.
   *
   * @since 1.2.0
   *
   * @var array
   */
  private static $_all_mbs = array();

  /**
   * Get one or more already created AM_MB.
   *
   * @since 1.2.0
   *
   * @param  null|string|array $meta_box_id Slug of the AM_MB(s) to get. If null, the current post type is used.
   * @return array                       Array of requested AM_MB(s).
   */
  public static function get( $meta_box_id = null ) {
    // If only a single meta box should be returned.
    $single = null;

    if ( ! isset( $meta_box_id ) ) {
      $single = false;
      $meta_box_id = array_keys( self::$_all_mbs );
    }

    // Make sure we have an array to work with. If we have comma seperated values, make them into an array.
    if ( ! is_array( $meta_box_id ) ) {
      $meta_box_id = explode( ',', $meta_box_id );
      if ( ! isset( $single ) ) {
        $single = ( 1 == count( $meta_box_id ) );
      }
    }

    $ret_mbs = array();
    $post_type = get_post_type();

    foreach ( self::$_all_mbs as $mb ) {
      if ( in_array( $post_type, $mb->get_post_types() ) ) {
        $ret_mbs[ $mb->get_id() ] = $mb;
      }
    }

    $ret_mbs = array_intersect_key( $ret_mbs, array_flip( $meta_box_id ) );

    return ( $single ) ? reset( $ret_mbs ) : $ret_mbs;
  }

  /**
   * Create a new AM_MB object.
   *
   * @since 1.0.0
   *
   * @param string $id           Meta box id.
   * @param string $title        Meta box title.
   * @param AM_MBF|array $fields Object or array of all fields in the meta box.
   */
  public function __construct( $id, $title, $fields = null ) {
    $this->id = sanitize_title( $id );
    $this->title = $title;
    $this->add_field( $fields );
  }

  /**
   * Get the id.
   *
   * @since 1.0.0
   *
   * @return string
   */
  final public function get_id() {
    return $this->id;
  }

  /**
   * Get the title.
   *
   * @since 1.0.0
   *
   * @return string
   */
  final public function get_title() {
    return $this->title;
  }

  /**
   * Get the fields (AM_MBF) assigned to this meta box.
   *
   * @since 1.0.0
   *
   * @return array
   */
  final public function get_fields() {
    // Load meta data first.
    $this->load_data();

    return $this->fields;
  }

  /**
   * Get the values of all fields.
   *
   * @since 1.2.0
   *
   * @param bool $raw If the values should be escaped or returned raw.
   * @return array Values of all fields.
   */
  final public function get_field_values( $raw = false ) {
    // Load meta data first.
    $this->load_data();

    $field_values = array();
    foreach ( $this->fields as $field ) {
      $field_values[ $field->get_id() ] = $field->get_value( $raw );
    }
    return $field_values;
  }

  /**
   * Set the meta box context.
   *
   * @since 1.0.0
   *
   * @param string $context
   */
  final public function set_context( $context ) {
    $this->context = $context;
  }

  /**
   * Get the meta box context.
   *
   * @since 1.0.0
   *
   * @return string
   */
  final public function get_context() {
    return $this->context;
  }

  /**
   * Set the meta box priority.
   *
   * @since 1.0.0
   *
   * @param string $priority
   */
  final public function set_priority( $priority ) {
    $this->priority = $priority;
  }

  /**
   * Get the meta box priority.
   *
   * @since 1.0.0
   *
   * @return string
   */
  final public function get_priority() {
    return $this->priority;
  }

  /**
   * Add a field to this meta box.
   *
   * @since 1.0.0
   *
   * @param AM_MBF|array $fields Object or array of AM_MBF to add to this meta box.
   */
  final public function add_field( $fields ) {
    if ( empty( $fields ) ) {
      return;
    }

    // Make sure we have an array to work with.
    if ( ! is_array( $fields ) ) {
      $fields = array( $fields );
    }

    // Add all fields.
    foreach ( $fields as $field ) {
      if ( $field instanceof AM_MBF ) {
        $field->set_meta_box( $this );
        $this->fields[ $field->get_id() ] = $field;
      }
    }
  }

  /**
   * Get a list of all field types being used by this meta box.
   *
   * @since 1.0.0
   *
   * @param  array|null $fields Fields to search types in.
   * @param  array      $types  In case of a repeatable field's recursion, this holds already found field types.
   * @return array              All found field types used by this meta box, including those used by repeatable fields.
   */
  final public function get_types( $fields = null, $types = array() ) {
    if ( ! isset( $fields ) ) {
      $fields = $this->fields;
    }

    foreach ( $fields as $field ) {
      if ( 'repeatable' == $field->get_type() ) {
        // If repeatable, get those types too, recursively.
        $types = array_merge( $types, $this->get_types( $field->get_repeatable_fields(), $types ) );
      }

      $new_type = $field->get_type();
      // Check if this field has a special sub type, use that instead.
      if ( $new_sub_type = $field->get_sub_type() ) {
        $new_type = $new_sub_type;
      }

      $types[] = $new_type;
    }

    return array_unique( $types );
  }

  /**
   * Assign a post type that will have this meta box assigned to it.
   *
   * @since 1.0.0
   *
   * @param array|string $post_types
   */
  final public function assign_post_type( $post_types ) {
    if ( ! isset( $post_types ) ) {
      return;
    }

    // Make sure we have an array to work with. If we have comma seperated values, make them into an array.
    if ( ! is_array( $post_types ) ) {
      $post_types = explode( ',', $post_types );
    }

    // Trim all entries.
    $post_types = array_map( 'trim', $post_types );

    // Assign all post types.
    foreach ( $post_types as $post_type ) {
      if ( ! in_array( $post_type, $this->post_types ) ) {
        $this->post_types[] = $post_type;
      }
    }
  }

  /**
   * Remove a post type from being assigned to this meta box.
   *
   * @since 1.0.0
   *
   * @param array|string $post_types
   */
  final public function remove_post_type( $post_types ) {
    if ( ! isset( $post_types ) ) {
      return;
    }

    // Make sure we have an array to work with. If we have comma seperated values, make them into an array.
    if ( ! is_array( $post_types ) ) {
      $post_types = explode( ',', $post_types );
    }

    // Trim all entries.
    $post_types = array_map( 'trim', $post_types );

    // Remove all post types.
    $this->post_types = array_diff( $this->post_types, $post_types );
  }

  /**
   * Get all post types this meta box is assigned to.
   *
   * @since 1.0.0
   *
   * @return array List of all post types.
   */
  final public function get_post_types() {
    return $this->post_types;
  }

  /**
   * Add actions for adding meta boxes and saving them.
   *
   * @since 1.0.0
   */
  final public function register() {
    if ( ! empty( $this->post_types ) ) {
      // Enqueue necessary JS and CSS scripts.
      add_action( 'admin_enqueue_scripts', array( $this, '_admin_enqueue_scripts' ) );

      // Register only for selected post types.
      foreach( $this->post_types as $post_type ) {
        add_action( 'add_meta_boxes_' . $post_type, array( $this, '_register' ) );
      }
      add_action( 'save_post',  array( $this, '_save' ) );

      // Save this meta box to $_all_mbs so it can easily be fetched again when required.
      self::$_all_mbs[ $this->id ] = $this;
    }
  }

  /**
   * Load meta data for all field of this meta box and sanitize it.
   *
   * This needs to be a seperate function, to allow the data to be loaded outside wp-admin.
   *
   * @since 1.0.0
   */
  final public function load_data() {
    // Set all fields values.
    foreach ( $this->fields as $field ) {
      $field->is_saving( false );
      $field->set_value( get_post_meta( get_the_ID(), $field->get_id(), true ) );
    }
  }

  /**
   * Register the meta box (callback for WP add_action).
   *
   * @since 1.0.0
   */
  final public function _register() {
    // Load meta data for this meta box.
    $this->load_data();

    add_meta_box( $this->id, $this->title, array( $this, '_output' ), get_post_type(), $this->context, $this->priority );
  }

  /**
   * Enqueue necessary scripts and styles (callback for WP add_action).
   *
   * @since 1.0.0
   */
  final public function _admin_enqueue_scripts() {
    global $pagenow;

    if ( in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) && in_array( get_post_type(), $this->post_types ) ) {
      $used_field_types = $this->get_types();

      $plugin_dir_url = plugin_dir_url( __FILE__ );

      // JS and CSS handles to be enqueued.
      $enq_js = array();
      $enq_css = array();

      if ( in_array( 'date', $used_field_types ) ) {
        $enq_js[] = 'jquery-ui-datepicker';
      }
      if ( in_array( 'slider', $used_field_types ) ) {
        $enq_js[] = 'jquery-ui-slider';
      }
      if ( in_array( 'color', $used_field_types ) ) {
       $enq_js[] = 'wp-color-picker';
       $enq_css[] = 'wp-color-picker';
      }
      if ( array_intersect( array( 'image', 'file' ), $used_field_types ) ) {
        $enq_js[] = 'media-upload';
      }
      if ( array_intersect( array( 'chosen', 'post_chosen' ), $used_field_types ) ) {
        if ( ! wp_script_is( 'chosen', 'registered' ) ) {
          wp_register_script( 'chosen', $plugin_dir_url . 'js/chosen.js', array( 'jquery' ), null, true );
        }
        $enq_js[] = 'chosen';

        if ( ! wp_style_is( 'chosen', 'registered' ) ) {
          wp_register_style( 'chosen', $plugin_dir_url . 'css/chosen.css' );
        }
        $enq_css[] = 'chosen';
      }

      if ( ! wp_style_is( 'jquery-ui' ) ) {
        // Interesting read, see "jQuery UI Styling". Didn't use this method, cause it was incomplete.
        // http://code.tutsplus.com/tutorials/integrating-with-wordpress-ui-the-basics--wp-26713
        wp_enqueue_style( 'jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.min.css' );
      }

      if ( ! wp_style_is( 'meta-box' ) ) {
        wp_enqueue_style( 'meta-box', $plugin_dir_url . 'css/meta-box.css' );
      }

      if ( array_intersect( array( 'date', 'editor', 'slider', 'color', 'chosen', 'post_chosen', 'repeatable', 'image', 'file' ), $used_field_types )
        && ! wp_script_is( 'meta-box' ) ) {
        wp_enqueue_script( 'meta-box', $plugin_dir_url . 'js/scripts.js', array( 'jquery' ), null, true );
      }

      // Enqueue all scripts.
      foreach ( $enq_js as $enq ) {
        if ( ! wp_script_is( $enq ) ) {
          wp_enqueue_script( $enq );
        }
      }

      // Enqueue all styles.
      foreach ( $enq_css as $enq ) {
        if ( ! wp_style_is( $enq ) ) {
          wp_enqueue_style( $enq );
        }
      }
    }
  }

  /**
   * Saves the entered meta box data (callback for WP add_action).
   *
   * @since 1.0.0
   */
  final public function _save( $post_id ) {
    // Check permissions before saving.
    if (
      // Make sure the post type is correct.
      ! in_array( get_post_type(), $this->post_types )

      // Verify nonce.
      || ! isset( $_POST['am_meta_box_nonce_field'] )
      || ! wp_verify_nonce( $_POST['am_meta_box_nonce_field'],  'am_meta_box_nonce_action' )

      // Check autosave state.
      || defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE

      // Check user permissions.
      || ( 'page' == $_POST['post_type'] && ! current_user_can( 'edit_page', $post_id ) )
      || ! current_user_can( 'edit_post', $post_id )
    ) {
      return $post_id;
    }


    //fu($_POST);

    // Loop through all fields and save the meta data.
    foreach ( $this->fields as $field ) {
      // Set field to saving state.
      $field->is_saving( true );

      $value_new = ( isset( $_POST[ $field->get_id() ] ) ) ? $_POST[ $field->get_id() ] : null;
      $field->set_value_new( $value_new );
      $field->set_value( get_post_meta( $post_id, $field->get_id(), true ) );

      // Save field.
      $field->save( $post_id );
    }
  }

  /**
   * Output the meta box (callback for WP add_action).
   *
   * @todo Error handling output.
   *
   * @since 1.0.0
   */
  public function _output() {
    // Use nonce for verification.
    wp_nonce_field( 'am_meta_box_nonce_action', 'am_meta_box_nonce_field' );

    // Begin the field table and loop.
    if ( $this->fields ) {
      $out = sprintf( '<table class="form-table meta-box mb-id-%1$s" data-id="%1$s">', esc_attr( $this->id ) );

    //  $errors = array();
      foreach ( $this->fields as $field ) {

        // Add class to description.
        if ( '' != $field->get_desc() ) {
          $field->set_desc( sprintf( '<span class="description">%1$s</span>', $field->get_desc() ) );
        }

        $out .= '<tr>';

        if ( 'plaintext' == $field->get_type() ) {
          $out .= sprintf( '<td colspan="2">%1$s',
            $field->output()
          );
        } else {
          $out .= sprintf( '<th><label class="meta-box-field-label" for="%1$s">%2$s</label>%3$s</th>',
            ( 'repeatable' != $field->get_type() ) ? esc_attr( $field->get_id() ) : '',
            $field->get_label(),
            $field->get_desc()
          );
          $out .= '<td>';
          $out .= $field->output();
        }

    //    $errors[] = $field->get_label();

        $out .= '
            </td>
          </tr>
        ';
      }

      $out .= '</table>';

      echo $out;

    /*  $err = '';
      if ( $errors ) {
        $err = sprintf( '<div class="error">' . __( 'Meta box "%s" contains input errors (%s)', 'am-cpts' ) . '</div>',
          $this->id,
          join( ', ', $errors )
        );
      }

      echo $out . $err;*/

    } else {
      _e( 'No fields have been assigned to this meta box.', 'am-cpts' );
    }
  }
}

?>