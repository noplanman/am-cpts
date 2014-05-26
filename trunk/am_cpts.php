<?php

/**
 * @package AM_CPTS
 * @version 1.0
 */
/*
Plugin Name: AM CPTS
Plugin URI:
Description: Create custom post types, taxonomies and meta boxes really easily.
Author: Armando Lüscher
Version: 1.0
Author URI: http://armyman.ch/
*/

/**
 * Base class for AM_CPT and AM_Tax
 */
abstract class AM_CPT_Tax {
  /**
   * Slug of CPT or Taxonomy.
   *
   * @since 1.0.0
   * @var string
   */
  protected $slug = '';

  /**
   * Arguments of CPT or Taxonomy.
   *
   * @since 1.0.0
   * @var array
   */
  protected $args = array();

  /**
   * Priority to initialize CPT or Taxonomy. (Default: 10)
   * @since 1.0.0
   * @var integer
   */
  protected $priority = 10;

  /**
   * Required function to pass to WPs add_action.
   *
   * @since 1.0.0
   */
  abstract public function _register();

  /**
   * Set the slug.
   *
   * @since 1.0.0
   *
   * @param string $slug
   */
  final public function set_slug( $slug ) {
    $this->slug = sanitize_title( $slug );
  }

  /**
   * Get the slug.
   *
   * @since 1.0.0
   *
   * @return string
   */
  final public function get_slug() {
    return $this->slug;
  }

  /**
   * Set a specific argument.
   *
   * @since 1.0.0
   *
   * @param string $key
   * @param string $value
   */
  final public function set_arg( $key, $value ) {
    $this->args[ $key ] = $value;
  }

  /**
   * Set all arguments.
   *
   * @since 1.0.0
   *
   * @param array $args
   */
  final public function set_args( $args ) {
    if ( is_array( $args ) ) {
      $this->args = $args;
    }
  }

  /**
   * Get a specific argument.
   *
   * @since 1.0.0
   *
   * @param  string $key
   * @return string|array
   */
  final public function get_arg( $key ) {
    return ( isset( $this->args[ $key ] ) ) ? $this->args[ $key ] : null;
  }

  /**
   * Get all the arguments.
   *
   * @since 1.0.0
   *
   * @return array
   */
  final public function get_args() {
    return $this->args;
  }

  /**
   * Set the initialization priority.
   *
   * @since 1.0.0
   *
   * @param integer $priority
   */
  final public function set_priority( $priority ) {
    $this->priority = intval( $priority );
  }

  /**
   * Get the initialization priority.
   *
   * @since 1.0.0
   *
   * @return integer
   */
  final public function get_priority() {
    return $this->priority;
  }

  /**
   * Set a specific label.
   *
   * @since 1.0.0
   *
   * @param string $key
   * @param string $value
   */
  final public function set_label( $key, $value ) {
    $this->args['labels'][ $key ] = $value;
  }

  /**
   * Get a specific label.
   *
   * @since 1.0.0
   *
   * @param  string $key
   * @return string|null
   */
  final public function get_label( $key ) {
    return ( isset( $this->args[ 'labels' ][ $key ] ) ) ? $this->args[ 'labels' ][ $key ] : null;
  }

  /**
   * Get all labels.
   *
   * @since 1.0.0
   *
   * @return array|null
   */
  final public function get_labels() {
    return ( isset( $this->args[ 'labels' ] ) ) ? $this->args[ 'labels' ] : null;
  }
}

/**
 * AM_Tax class, that builds on AM_CPT_Tax.
 */
class AM_Tax extends AM_CPT_Tax {
  /**
   * Array of post types that will have this taxonomy assigned to.
   *
   * @since 1.0.0
   * @var array
   */
  protected $post_types = array();

  /**
   * Create new AM_Tax object.
   *
   * @since 1.0.0
   *
   * @param string  $slug     Slug of this taxonomy.
   * @param array   $args     Arguments of this taxonomy.
   * @param integer $priority Priority to initialize this taxonomy. (Default: 10)
   */
  public function __construct( $slug, $args, $priority = 10 ) {
    $this->set_slug( $slug );
    $this->set_args( $args );
    $this->set_priority( $priority );
  }

  /**
   * Assign a post type that will have this taxonomy assigned to it.
   *
   * @since 1.0.0
   *
   * @param array|string $post_types Single post type, comma seperated post types, array of post types.
   */
  final public function assign_post_type( $post_types ) {
    if ( is_null( $post_types ) ) {
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
   * Remove a post type from being assigned to this taxonomy.
   *
   * @since 1.0.0
   *
   * @param array|string $post_types Single post type, comma seperated post types, array of post types.
   */
  final public function remove_post_type( $post_types ) {
    if ( is_null( $post_types ) ) {
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
   * Add init action to register this taxonomy.
   *
   * @since 1.0.0
   */
  final public function register() {
    add_action( 'init', array( $this, '_register' ), $this->priority );
  }

  /**
   * Register taxonomy / taxonomies.
   *
   * @since 1.0.0
   */
  final public function _register() {
    // Register taxonomy for all selected post types.
    foreach ( $this->post_types as $post_type ) {

      /**
       * Check if the taxonomy has already been registered.
       * If already registered, also register it to the post type, else register it as a new taxonomy.
       */
      if ( $taxonomy = get_taxonomy( $this->slug ) ) {
        register_taxonomy_for_object_type( $taxonomy->name, $post_type );
      } else {
        register_taxonomy( $this->slug, $post_type, $this->args );
      }
    }
  }
}

class AM_CPT extends AM_CPT_Tax {
  /**
   * Taxonomies assigned to this CPT.
   *
   * @since 1.0.0
   * @var array
   */
  protected $taxonomies = array();

  /**
   * Meta boxes assigned to this CPT.
   *
   * @since 1.0.0
   * @var array
   */
  protected $meta_boxes = array();

  /**
   * Create new AM_CPT object.
   *
   * @since 1.0.0
   *
   * @param string  $slug     Slug of this taxonomy.
   * @param array   $args     Arguments of this taxonomy.
   * @param AM_Tax|array $taxonomies Object or array of AM_Tax to add to this CPT.
   * @param AM_MB|array $meta_boxes Object or array of AM_MB to add to this CPT.
   * @param integer $priority Priority to initialize this CPT. (Default: 10)
   */
  public function __construct( $slug, $args, $taxonomies = null, $meta_boxes = null, $priority = 10 ) {
    $this->set_slug( $slug );
    $this->set_args( $args );
    $this->assign_taxonomy( $taxonomies );
    $this->assign_meta_box( $meta_boxes );
    $this->set_priority( $priority );
  }

  /**
   * Assign taxonomy / taxonomies to this CPT.
   *
   * @since 1.0.0
   *
   * @param AM_Tax|array $taxonomies Object or array of AM_Tax to assign to this CPT.
   */
  final public function assign_taxonomy( $taxonomies ) {
    if ( is_null( $taxonomies ) ) {
      return;
    }

    // Make sure we have an array to work with.
    if ( ! is_array( $taxonomies ) ) {
      $taxonomies = array( $taxonomies );
    }

    // Assign all taxonomies.
    foreach ( $taxonomies as $taxonomy ) {
      if ( is_a( $taxonomy, 'AM_Tax' ) ) {
        $taxonomy->assign_post_type( $this->slug );
        $this->taxonomies[ $taxonomy->get_slug() ] = $taxonomy;
      }
    }
  }

  /**
   * Remove taxonomy / taxonomies from this CPT.
   *
   * @since 1.0.0
   *
   * @param string|array $taxonomies Taxonomy / taxonomies to remove / unassign from this CPT. Single key, comma seperated keys, array of keys.
   */
  final public function remove_taxonomy( $taxonomies ) {
    if ( is_null( $taxonomies ) ) {
      return;
    }

    // Make sure we have an array to work with. If we have comma seperated values, make them into an array.
    if ( ! is_array( $taxonomies ) ) {
      $taxonomies = explode( ',', $taxonomies );
    }

    // Trim all entries.
    $taxonomies = array_map( 'trim', $taxonomies );

    // Remove all taxonomies.
    $this->taxonomies = array_diff_key( $this->taxonomies, array_flip( $taxonomies ) );
  }

  /**
   * Get all taxonomies assigned to this CPT.
   *
   * @since 1.0.0
   *
   * @return array List of all AM_Tax objects.
   */
  final public function get_taxonomies() {
    return $this->taxonomies;
  }

  /**
   * Assign meta box(es) to this CPT.
   *
   * @since 1.0.0
   *
   * @param AM_MB|array $meta_boxes Object or Array of AM_MB to add to this CPT.
   */
  final public function assign_meta_box( $meta_boxes ) {
    if ( is_null( $meta_boxes ) ) {
      return;
    }

    // Make sure we have an array to work with.
    if ( ! is_array( $meta_boxes ) ) {
      $meta_boxes = array( $meta_boxes );
    }

    // Add all meta boxes.
    foreach ( $meta_boxes as $meta_box ) {
      if ( is_a( $meta_box, 'AM_MB' ) ) {
        $meta_box->assign_post_type( $this->slug );
        $this->meta_boxes[ $meta_box->get_id() ] = $meta_box;
      }
    }
  }

  /**
   * Remove meta box(es) from this CPT.
   *
   * @since 1.0.0
   *
   * @param string|array $meta_boxes Meta box / boxes to remove / unassign from this CPT. Single key, comma seperated keys, array of keys.
   */
  final public function remove_meta_box( $meta_boxes ) {
    if ( is_null( $meta_boxes ) ) {
      return;
    }

    // Make sure we have an array to work with. If we have comma seperated values, make them into an array.
    if ( ! is_array( $meta_boxes ) ) {
      $meta_boxes = explode( ',', $meta_boxes );
    }

    // Trim all entries.
    $meta_boxes = array_map( 'trim', $meta_boxes );

    // Remove all meta boxes.
    $this->meta_boxes = array_diff_key( $this->meta_boxes, array_flip( $meta_boxes ) );
  }

  /**
   * Get all meta boxes assigned to this CPT.
   *
   * @since 1.0.0
   *
   * @return array List of all AM_MB objects.
   */
  final public function get_meta_boxes() {
    return $this->meta_boxes;
  }

  /**
   * Find out which meta box types are being used by this CPT.
   *
   * @since 1.0.0
   *
   * @return array An array of meta box types.
   */
  final public function used_meta_box_types() {
    $types = array();
    foreach ( $this->meta_boxes as $meta_box ) {
      $types = array_merge( $types, $meta_box->get_types() );
    }

    return array_unique( $types );
  }

  /**
   * Enqueue necessary scripts and styles.
   *
   * @since 1.0.0
   */
  final public function _admin_enqueue_scripts() {
    global $pagenow;
    if ( in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) && get_post_type() == $this->slug ) {
      $used_types = $this->used_meta_box_types();

      $plugin_dir_url = plugin_dir_url( __FILE__ );


      $deps_js = array( 'jquery' );
      $deps_css = array();

      if ( in_array( 'date', $used_types ) ) {
        $deps_js[] = 'jquery-ui-datepicker';
      }
      if ( in_array( 'slider', $used_types ) ) {
        $deps_js[] = 'jquery-ui-slider';
      }
      if ( in_array( 'color', $used_types ) ) {
        $deps_js[] = 'farbtastic';
        $deps_css[] = 'farbtastic';
      }
      if ( array_intersect( array( 'chosen', 'post_chosen' ), $used_types ) ) {
        wp_register_script( 'chosen', $plugin_dir_url . 'js/chosen.js' );
        $deps_js[] = 'chosen';

        wp_register_style( 'chosen', $plugin_dir_url . 'css/chosen.css' );
        $deps_css[] = 'chosen';
      }

      if ( array_intersect( array( 'date', 'slider', 'color', 'chosen', 'post_chosen', 'repeatable', 'image', 'file' ), $used_types ) ) {
        wp_enqueue_script( 'meta-box', $plugin_dir_url . 'js/scripts.js', $deps_js, null, true );
      }

    //  wp_register_style( 'jqueryui', $plugin_dir_url . '/css/jqueryui.css' );
      wp_register_style( 'jqueryui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.min.css' );
      $deps_css[] = 'jqueryui';

      wp_enqueue_style( 'meta-box', $plugin_dir_url . 'css/meta-box.css', $deps_css );
    }
  }

  /**
   * Adds JS to the admin head for special fields with extra requirements.
   *
   * @since 1.0.0
   */
  final public function _admin_head() {
    global $pagenow;
    if ( in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) && get_post_type() == $this->slug ) {
      $used_types = $this->used_meta_box_types();

      if ( array_intersect( array( 'date', 'slider' ), $used_types ) ) {

        $js_out = '<script type="text/javascript">
          jQuery(function( $) {';

        foreach ( $this->meta_boxes as $meta_box ) {
          foreach ( $meta_box->get_fields() as $field ) {
    // TODO: move to fields themselves, decouple!
    // TODO: repeatables!
            switch( $field->get_type() ) {
              // Date.
              case 'date' :
                $date_format = $field->get_setting( 'format', 'dd.mm.yy' );
                $js_out .= '
                  $("#' . $field->get_id() . '").datepicker({
                    dateFormat: "' . $date_format . '"
                  });
                ';
              break;
              // Slider.
              case 'slider' :

                $min = $field->get_setting( 'min', 0 );
                $max = $field->get_setting( 'max', 100 );
                $step = $field->get_setting( 'step', 1 );
                $range = ( $field->get_setting( 'range', false ) ) ? 'true' : 'false';
                $handles = $field->get_setting( 'handles', 1 );

                $values = $field->get_value_old();

                if ( ! is_array( $values ) ) {
                  $values = explode( ',', $values );
                }
                while ( count( $values ) < intval( $handles ) ) {
                  $values[] = $min;
                }
                $values = implode( ',', $values );

                $js_out .= '
                  $( "#' . $field->get_id() . '-slider" ).slider({
                    min:' . $min . ',
                    max:' . $max . ',
                    step:' . $step . ',
                    values:[' . $values . '],
                    range:' . $range . ',

                    create: function(event, ui) {
                      // Create all labels and add them to their respective handle.
                      var handles = $(this).find(".ui-slider-handle");
                      for(var i = 0;i < handles.length;i++) {
                        $("<span></span>")
                          .html($(this).slider("values", i))
                          .appendTo(handles[i])
                          .position({
                            my: "center top",
                            at: "center bottom+1",
                            of: handles[i],
                            collision: "none"
                          });
                      }
                    },
                    slide: function( event, ui ) {
                      $(ui.handle).find("span")
                        .html(ui.value)
                        .position({
                          my: "center top",
                          at: "center bottom+1",
                          of: ui.handle,
                          collision: "none"
                        });
                      $("#' . $field->get_id() . '").val(ui.values);
                    }
                  });
                ';
              break;
            }
          }
        }

        $js_out .= '
            });
          </script>
        ';
        echo $js_out;
      }
    }
  }

  /**
   * Add all actions related to registering this custom post type.
   */
  final public function register() {
    // Metabox related!
    add_action( 'admin_enqueue_scripts', array( $this, '_admin_enqueue_scripts' ) );
    add_action( 'admin_head',  array( $this, '_admin_head' ) );


    // Set the 'taxonomy' argument for the CPT.
    $this->set_arg( 'taxonomies', array_keys( $this->taxonomies ) );

    // Register all assigned taxonomies.
    foreach ( $this->taxonomies as $taxonomy ) {
      $taxonomy->register();
    }

    // Register all assigned meta boxes.
    foreach ( $this->meta_boxes as $meta_box ) {
      $meta_box->register();
    }

    add_action( 'init', array( $this, '_register' ), $this->priority );
  }

  /**
   * Register the custom post type.
   */
  final public function _register() {
    register_post_type( $this->slug, $this->args );
  }
}
















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
    return $this->fields;
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
      if ( is_subclass_of( $field, 'AM_MBF' ) ) {
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
    if ( is_null( $fields ) ) {
      $fields = $this->fields;
    }

    foreach ( $fields as $field ) {
      if ( 'repeatable' == $field->get_type() ) {
        // If repeatable, get those types too, recursively.
        $types = array_merge( $types, $this->get_types( $field->get_repeatable_fields(), $types ) );
      }
      $types[] = $field->get_type();
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
    if ( is_null( $post_types ) ) {
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
    if ( is_null( $post_types ) ) {
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
    // Register only for selected post types.
    foreach( $this->post_types as $post_type ) {
      add_action( 'add_meta_boxes_' . $post_type, array( $this, '_register' ) );
    }
    add_action( 'save_post',  array( $this, '_save' ) );
  }

  /**
   * Register the meta box.
   *
   * @since 1.0.0
   */
  final public function _register() {
    // Set all fields values and sanitize before output.
    foreach ( $this->fields as $field ) {
      $field->set_value_old( get_post_meta( get_the_ID(), $field->get_id(), true ) );
      $field->clean_data();
      $field->sanitize();
    }

    add_meta_box( $this->id, $this->title, array( $this, '_output' ), get_post_type(), $this->context, $this->priority );
  }

  /**
   * Saves the entered meta box data.
   *
   * @since 1.0.0
   */
  final public function _save( $post_id ) {
    // Make sure the post type is correct.
    if ( ! in_array( get_post_type(), $this->post_types ) ) {
      return $post_id;
    }

    // Verify nonce.
    if ( ! ( isset( $_POST['am_meta_box_nonce_field'] ) && wp_verify_nonce( $_POST['am_meta_box_nonce_field'],  'am_meta_box_nonce_action' ) ) ) {
      return $post_id;
    }

    // Check autosave state.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return $post_id;
    }

    // Check user permissions.
    if ( ! current_user_can( 'edit_page', $post_id ) ) {
      return $post_id;
    }

//  fu($_POST);

    // Loop through all fields and save the meta data.
    foreach ( $this->fields as $field ) {
      $value_new = ( isset( $_POST[ $field->get_id() ] ) ) ? $_POST[ $field->get_id() ] : null;
      $field->set_value_new( $value_new );
      $field->set_value_old( get_post_meta( $post_id, $field->get_id(), true ) );

      // Validate field before save.
      $field->validate();
      $field->save( $post_id );
    }
  }

  /**
   * Output the meta box.
   *
   * @since 1.0.0
   */
  public function _output() {
    // Use nonce for verification.
    wp_nonce_field( 'am_meta_box_nonce_action', 'am_meta_box_nonce_field' );

    // Begin the field table and loop.
    if ( $this->fields ) {
      $out = '<table class="form-table meta-box mb-id-' . $this->id . '">';

      $errors = array();
      foreach ( $this->fields as $field ) {
        $out .= '<tr>';

        if ( 'plaintext' == $field->get_type() ) {
//          $out .= '<td colspan="2" class="meta-box-plaintext"><span' . $field->get_classes() . '>' . $field->get_label() . '</span>' . $field->get_desc();
          $out .= '<td colspan="2" class="meta-box-plaintext">';
        } else {
          $for = $field->get_id();
          // If field is repeatable, set label for first field.
          if ( 'repeatable' == $field->get_type() && $field->get_repeatable_fields() ) {
            $for = reset( $field->get_repeatable_fields() )->get_id( true ) . '-0';
          }
          $out .= '<th><label class="meta-box-field-label" for="' . $for . '">' . $field->get_label() . '</label>' . $field->get_desc() . '</th>
            <td>
          ';
        }

        $errors[] = $field->get_label();

        $out .= $field->output() . '
            </td>
          </tr>
        ';
      }

      $out .= '</table>';

      echo $out;

/*      $err = '';
      if ( $errors ) {
        $err = sprintf( '<div class="error">' . __( 'Meta box "%s" contains input errors (%s)', 'textdomain' ) . '</div>',
          $this->id,
          join( ', ', $errors )
        );
      }

      echo $out . $err;*/
    } else {
      _e( 'No fields have been assigned to this meta box.', 'textdomain' );
    }
  }
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
   * The validator to use for this field.
   *
   * @since 1.0.0
   * @var string
   */
  protected $validator = 'text_field'; // TODO: really necessary?!

  /**
   * The size of this field's input.
   *
   * @since 1.0.0
   * @var integer
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
   * All fields that are to be repeated for this field. This array contains other fields.
   *
   * @since 1.0.0
   * @var array
   */
  protected $repeatable_fields = array();

  /**
   * The currently set meta value.
   *
   * @since 1.0.0
   * @var string|array
   */
  protected $value_old = null;

  /**
   * The new meta value to be set.
   *
   * @since 1.0.0
   * @var string|array
   */
  protected $value_new = null;

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
      if ( 0 === strpos( $mbf_class, 'AM_MBF_' ) ) {
        if ( $type == call_user_func( array( $mbf_class, 'get_type' ) ) ) {

          /**
           *  Create a new class with the passed parameters.
           *  These parameters are variable, so the first elements must be sliced off.
           */
          $field = new ReflectionClass( $mbf_class );
          if ( $field->getConstructor() ) {
            $field = $field->newInstanceArgs( array_slice( func_get_args(), 4 ) );
          } else {
            $field = $field->newInstanceWithoutConstructor();
          }

          // Make sure our field is legitimate and initialize it.
          if ( is_subclass_of( $field, 'AM_MBF' ) ) {
            $field->init( $id, $label, $desc );
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

    return $new_fields;
  }

  /**
   * Initialize the field with core data.
   *
   * @since 1.0.0
   *
   * @param  string $id    Field id to set.
   * @param  string $label Field label to display.
   * @param  string $desc  Field description to display.
   */
  final public function init( $id, $label, $desc ) {
    $this->set_id( $id );
    // Set raw id.
    $this->_id = $this->id;
    $this->set_name( $id ); // Set name to be the same as id.
    $this->set_label( $label );
    $this->set_desc( $desc );
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




//?
  /**
   * Clean up the field data.
   *
   * @since 1.0.0
   */
  public function clean_data() {
    $this->id       = $this->id;
    $this->size     = ( intval( $this->size ) > 0 ) ? intval( $this->size ) : 30;
    $this->desc     = ( isset( $this->desc ) && '' != $this->desc ) ? '<span class="description">' . $this->desc . '</span>' : '';
    $this->multiple = (bool)$this->multiple;

    // Clean up all repeatable fields.
    foreach ( $this->get_repeatable_fields() as $rep_field ) {
      $rep_field->clean_data();
    }
  }
//?
  /**
   * Modify the new values before it gets sanitized. Could be used to bring the data into the right format.
   */
  protected function pre_sanitize() {
    // This can be overridden if necessary.
  }
//?
  /**
   * Modify the new values after they have been  it gets sanitized. Could be used to bring the data into the right format.
   */
  protected function post_sanitize() {
    // This can be overridden if necessary.
  }
//?
  /**
   * Sanitize the new value of this field and all repeatable fields.
   */
  public function sanitize() {
    // Pre-Sanitize new value data.
    $this->pre_sanitize();

    if ( isset( $this->value_old ) ) {

      $was_array = true;
      $values = $this->value_old;
      if ( ! is_array( $values ) ) {
        $values = array ( $values );
        $was_array = false;
      }

      $values_sanitized = array();
      foreach ( $values as $key => $value ) {
        switch ( $this->sanitizer ) {
          case 'absint':
            $values_sanitized[ $key ] = absint( $value ); break;
          case 'intval':
            $values_sanitized[ $key ] = intval( $value ); break;
          case 'floatval':
            $values_sanitized[ $key ] = floatval( $value ); break;
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
            $values_sanitized[ $key ] = ( isset( $value ) && ( $value == 1 || $value == true ) ); break;
          case 'text_field':
          default:
            $values_sanitized[ $key ] = sanitize_text_field( $value );
        }
      }
      if ( ! $was_array ) {
        $values_sanitized = reset( $values_sanitized );
      }
      $this->value_old = $values_sanitized;
    }

    // Post-Sanitize new value data.
    $this->post_sanitize();
  }
//?
  /**
   * Pre-Validate the new values before they get validated. Could be used to bring the data into the right format.
   */
  protected function pre_validate() {
    // This can be overridden if necessary.
  }
//?
  /**
   * Post-Validate the new values before they get saved. Could be used to bring the data into the right format.
   */
  protected function post_validate() {
    // This can be overridden if necessary.
  }
//?
  /**
   * Validate the new values of this field and all repeatable fields before being saved as meta data.
   */
  public function validate() {
    // Pre-Validate new value data.
    $this->pre_validate();

    if ( isset( $this->value_new ) ) {
      $was_array = true;
      $values = $this->value_new;
      if ( ! is_array( $values ) ) {
        $values = array ( $values );
        $was_array = false;
      }
      $values_validated = array();
      foreach ( $values as $key => $value ) {
        switch ( $this->validator ) {
          case 'absint':
            $values_validated[ $key ] = absint( $value ); break;
          case 'intval':
            $values_validated[ $key ] = intval( $value ); break;
          case 'floatval':
            $values_validated[ $key ] = floatval( $value ); break;
          case 'url':
            $values_validated[ $key ] = esc_url_raw( $value ); break;
          case 'email':
            $values_validated[ $key ] = sanitize_email( $value ); break;
          case 'title':
            $values_validated[ $key ] = sanitize_title( $value ); break;
          case 'boolean':
            $values_validated[ $key ] = ( isset( $value ) && ( $value == 1 || $value == true ) ); break;
          case 'text_field':
          default:
            $values_validated[ $key ] = sanitize_text_field( $value );
        }
      }
      if ( ! $was_array ) {
        $values_validated = reset( $values_validated );
      }
      $this->value_new = $values_validated;
    }

    // Post-Validate new value data.
    $this->post_validate();
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
    if ( is_a( $meta_box, 'AM_MB' ) ) {
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
   * @param integer $size
   */
  final public function set_size( $size ) {
    if ( is_numeric( $size ) ) {
      $this->size = intval( $size );
    }
  }

  /**
   * Get this field's input size.
   *
   * @since 1.0.0
   *
   * @return integer
   */
  final public function get_size() {
    return intval( $this->size );
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
    if ( is_array( $key ) && is_null( $value ) ) {

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
    if ( is_null( $keys ) ) {
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
   * Get concatenated string of 'data-$key="$value"' atributes.
   *
   * @since 1.0.0
   *
   * @return string
   */
  final public function get_data_atts() {
    $data_atts = '';
    foreach ( $this->data as $key => $value ) {
      $data_atts .= ' data-' . $key . '="' . esc_attr( $value ) . '"';
    }
    return $data_atts;
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
    if ( is_array( $key ) && is_null( $value ) ) {

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
    if ( is_null( $keys ) ) {
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
    if ( is_array( $key ) && is_null( $value ) ) {

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
    if ( is_null( $keys ) ) {
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
   * @param object|array $value_old
   */
  public function set_value_old( $value_old ) {
    $this->value_old = $value_old;
  }

  /**
   * Get the old/current meta value of this field.
   *
   * @since 1.0.0
   *
   * @return object|array
   */
  public function get_value_old() {
    return $this->value_old;
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
   * @param integer $post_id ID of the current post being saved.
   */
  public function save( $post_id ) {
    if ( isset( $this->value_old ) && ( is_null( $this->value_new ) || '' == $this->value_new ) ) {
      // Remove the post meta data.
      delete_post_meta( $post_id, $this->id, $this->value_old );
    } elseif ( $this->value_new != $this->value_old ) {
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
  public function save() {
    // Nothing to be saved...
  }
}

class AM_MBF_Text extends AM_MBF {
  protected static $type = 'text';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $size = ( isset( $this->size ) ) ? $this->size : '30';
    return '<input type="text" name="' . $this->name . '" id="' . $this->id . '" value="' . esc_attr( $this->value_old ) . '"' . $this->get_classes( 'regular-text' ) . ' size="' . $size . '"' . $this->get_data_atts() . ' />';
  }
}

class AM_MBF_Tel extends AM_MBF {
  protected static $type = 'tel';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $size = ( isset( $this->size ) ) ? $this->size : '30';
    return '<input type="tel" name="' . $this->name . '" id="' . $this->id . '" value="' . esc_attr( $this->value_old ) . '"' . $this->get_classes( 'regular-text' ) . ' size="' . $size . '"' . $this->get_data_atts() . ' />';
  }
}

class AM_MBF_Email extends AM_MBF {
  protected static $type = 'email';
  protected $sanitizer = 'email';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $size = ( isset( $this->size ) ) ? $this->size : '30';
    return '<input type="email" name="' . $this->name . '" id="' . $this->id . '" value="' . esc_attr( $this->value_old ) . '"' . $this->get_classes( 'regular-text' ) . ' size="' . $size . '"' . $this->get_data_atts() . ' />';
  }
}

class AM_MBF_Url extends AM_MBF {
  protected static $type = 'url';
  protected $sanitizer = 'url';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $size = ( isset( $this->size ) ) ? $this->size : '30';
    return '<input type="url" name="' . $this->name . '" id="' . $this->id . '" value="' . esc_url_raw( $this->value_old ) . '"' . $this->get_classes( 'regular-text' ) . ' size="' . $size . '"' . $this->get_data_atts() . ' />';
  }
}

class AM_MBF_Number extends AM_MBF {
  protected static $type = 'number';
  protected $sanitizer = 'intval';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $size = ( isset( $this->size ) ) ? $this->size : '30';
    return '<input type="number" name="' . $this->name . '" id="' . $this->id . '" value="' . intval( $this->value_old ) . '"' . $this->get_classes( 'regular-text' ) . ' size="' . $size . '"' . $this->get_data_atts() . ' />';
  }
}

// TODO: $size for cols and rows
class AM_MBF_TextArea extends AM_MBF {
  protected static $type = 'textarea';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    return '<textarea name="' . $this->name . '" id="' . $this->id . '" cols="60" rows="4"' . $this->get_classes() . $this->get_data_atts() . '>' . esc_textarea( $this->value_old ) . '</textarea>';
  }
}

// TODO!! repeatable field!
class AM_MBF_Editor extends AM_MBF {
  protected static $type = 'editor';
  protected $sanitizer = 'text';
  protected $is_repeatable = false;

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $this->settings['textarea_name'] = $this->name;
    $this->settings['editor_class']  = $this->get_classes( '', false );

    ob_start();
    wp_editor( $this->value_old, $this->id, $this->settings );
    return ob_get_clean() .
      '<br class="clear" />' . $this->desc;
  }
}

// TODO: get data atts need to be compatible with options, get_data_atts() on radio elements?
class AM_MBF_RadioGroup extends AM_MBF {
  protected static $type = 'radio_group';
  protected $sanitizer = 'text';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    // Backup id.
    $id_bkp = $this->id;

    $ret .= '<ul class="meta-box-items">';
    foreach ( $this->options as $opt_value => $opt_label ) {
      $checked = checked( $this->value_old, $opt_value, false );
      $this->id = $this->id . '-' . $opt_value;

      $ret .= '
        <li>
          <input type="radio" name="' . $this->name . '" id="' . $this->id . '" value="' . esc_attr( $opt_value ) . '" ' . $checked . $this->get_classes() . $this->get_data_atts() . ' />
          <label for="' . $this->id . '">' . $opt_label . '</label>
        </li>
      ';
    }
    $ret .= '</ul>';
    $ret .= '<br class="clear" />' . $this->desc;

    // Revert id.
    $this->id = $id_bkp;

    return $ret;
  }
}

// TODO: datetime, time
class AM_MBF_Date extends AM_MBF {
  protected static $type = 'date';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $size = ( isset( $this->size ) ) ? $this->size : '30';
    return '<input type="text"' . $this->get_classes( 'datepicker' ) . ' name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value_old . '" size="' . $size . '"' . $this->get_data_atts() . ' />';
  }
}

class AM_MBF_Checkbox extends AM_MBF {
  protected static $type = 'checkbox';
  protected $sanitizer = 'boolean';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $checked = checked( $this->value_old, true, false );
    return '<input type="checkbox"' . $this->get_classes() . ' name="' . $this->name . '" id="' . $this->id . '"' . $checked . ' value="1"' . $this->get_data_atts() . ' />
      <label for="' . $this->id . '">' . $this->get_label() . '</label>';
  }
}

class AM_MBF_CheckboxGroup extends AM_MBF {
  protected static $type = 'checkbox_group';
  protected $sanitizer = 'boolean';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    // Backup id.
    $id_bkp = $this->id;

    $ret = '<ul class="meta-box-items">';
    foreach ( $this->options as $opt_value => $opt_label ) {
      $checked = checked( is_array( $this->value_old ) && in_array( $opt_value, $this->value_old ), true, false );
      $this->id = $this->id . '-' . $opt_value;
      $ret .= '
        <li>
          <input type="checkbox" value="' . $opt_value . '" name="' . $this->name . '[]" id="' . $this->id . '"' . $checked . $this->get_classes() . $this->get_data_atts() . ' />
          <label for="' . $this->id . '">' . $opt_label . '</label>
        </li>
      ';
    }
    $ret .= '</ul>';
    $ret .= '<br class="clear" />' . $this->desc;

    // Revert id.
    $this->id = $id_bkp;

    return $ret;
  }
}

class AM_MBF_Select extends AM_MBF {
  protected static $type = 'select';
  protected $sanitizer = 'text';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $multiple = ( $this->is_multiple ) ? ' multiple="multiple"' : '';
    $ret = '<select name="' . $this->name . '" id="' . $this->id . '"' . $multiple . $this->get_classes() . $this->get_data_atts() . '>';
    if ( ! $this->is_multiple ) {
      $ret .= '<option value=""></option>'; // Select One
    }
    foreach ( $this->options as $opt_value => $opt_label ) {
      $selected = selected( $this->value_old, $opt_value, false );
      $ret .= '<option value="' . $opt_value . '"' . $selected . '>' . $opt_label . '</option>';
    }
    $ret .= '</select>';
    $ret .= '<br class="clear" />' . $this->desc;
    return $ret;
  }
}

class AM_MBF_Chosen extends AM_MBF {
  protected static $type = 'chosen';
  protected $sanitizer = 'text';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $multiple = ( $this->is_multiple ) ? ' multiple="multiple"' : '';
    $ret = '<select data-placeholder="' . __( 'Select One', 'textdomain' ) . '" name="' . $this->name . '" id="' . $this->id . '"' . $multiple . $this->get_classes( 'chosen' ) . $this->get_data_atts() . '>';
    if ( ! $this->is_multiple ) {
      $ret .= '<option value=""></option>'; // Select One
    }
    foreach ( $this->options as $opt_value => $opt_label ) {
      $selected = selected( $this->value_old, $opt_value, false );
      $ret .= '<option value="' . $opt_value . '"' . $selected . '>' . $opt_label . '</option>';
    }
    $ret .= '</select>';
    $ret .= '<br class="clear" />' . $this->desc;
    return $ret;
  }
}

// TODO: use wp-color-picker?
class AM_MBF_Color extends AM_MBF {
  protected static $type = 'color';
  protected $sanitizer = 'color';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $value = ( $this->value_old ) ? $this->value_old : '#';
    $size = ( isset( $this->size ) ) ? $this->size : '7';
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
    $ret .= '<br class="clear" />' . $this->desc;
    return $ret;
  }
}

class AM_MBF_PostCheckboxes extends AM_MBF {
  protected static $type = 'post_checkboxes';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    // Backup id.
    $id_bkp = $this->id;

    $posts = get_posts( array( 'post_type' => $this->post_type, 'posts_per_page' => -1 ) );
    $ret = '<ul class="meta-box-items">';
    foreach ( $posts as $item ) {
      $checked = checked( is_array( $this->value_old ) && in_array( $item->ID, $this->value_old ), true, false );
      $this->id = $this->id . '-' . $item->ID;
      $ret .= '
        <li>
          <input type="checkbox" value="' . $item->ID . '" name="' . $this->name . '[]" id="' . $this->id . '"' . $checked . $this->get_classes() . $this->get_data_atts() . ' />
          <label for="' . $this->id . '">' . $item->post_title . '</label>
        </li>
      ';
    }
    $post_type_object = get_post_type_object( $this->post_type );
    $ret .= '</ul>';
    $ret .= '<br class="clear" /><span class="description alignright"><a href="' . admin_url( 'edit.php?post_type=' . $this->post_type . '">Manage ' . $post_type_object->label ) . '</a></span>';
    $ret .= '<br class="clear" />' . $this->desc;

    // Revert id.
    $this->id = $id_bkp;

    return $ret;
  }
}

class AM_MBF_PostSelect extends AM_MBF {
  protected static $type = 'post_select';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $multiple = ( $this->is_multiple ) ? ' multiple="multiple"' : '';
    $ret = '<select name="' . $this->name . '[]" id="' . $this->id . '"'  . $multiple . $this->get_classes() . $this->get_data_atts() . '>';
    if ( ! $this->is_multiple ) {
      $ret .= '<option value=""></option>'; // Select One
    }

    $posts = get_posts( array( 'post_type' => $this->post_type, 'posts_per_page' => -1, 'orderby' => 'name', 'order' => 'ASC' ) );
    foreach ( $posts as $item ) {
      $selected = selected( is_array( $this->value_old ) && in_array( $item->ID, $this->value_old ), true, false );
      $ret .= '<option value="' . $item->ID . '"' . $selected . '>' . $item->post_title . '</option>';
    }
    $post_type_object = get_post_type_object( $this->post_type );
    $ret .= '</select>';
    $ret .= '&nbsp;<span class="description"><a href="' . admin_url( 'edit.php?post_type=' . $this->post_type . '">Manage ' . $post_type_object->label ) . '</a></span>';
    $ret .= '<br class="clear" />' . $this->desc;
    return $ret;
  }
}

class AM_MBF_PostChosen extends AM_MBF {
  protected static $type = 'post_chosen';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $multiple = ( $this->is_multiple ) ? ' multiple="multiple"' : '';
    $ret = '<select data-placeholder="' . __( 'Select One', 'textdomain' ) . '" name="' . $this->name . '[]" id="' . $this->id . '"' . $multiple . $this->get_classes( 'chosen' ) . $this->get_data_atts() . '>';
    if ( ! $this->is_multiple ) {
      $ret .= '<option value=""></option>'; // Select One
    }

    $posts = get_posts( array( 'post_type' => $this->post_type, 'posts_per_page' => -1, 'orderby' => 'name', 'order' => 'ASC' ) );
    foreach ( $posts as $item ) {
      $selected = selected( is_array( $this->value_old ) && in_array( $item->ID, $this->value_old ), true, false );
      $ret .= '<option value="' . $item->ID . '"' . $selected . '>' . $item->post_title . '</option>';
    }
    $post_type_object = get_post_type_object( $this->post_type );
    $ret .= '</select>';
    $ret .= '&nbsp;<span class="description"><a href="' . admin_url( 'edit.php?post_type=' . $this->post_type . '">Manage ' . $post_type_object->label ) . '</a></span>';
    $ret .= '<br class="clear" />' . $this->desc;
    return $ret;
  }
}

// TODO: dafuq?
class AM_MBF_PostDropSort extends AM_MBF {
  protected static $type = 'post_drop_sort';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    // Areas.
    $post_type_object = get_post_type_object( $this->post_type );
    $ret = '<div class="post_drop_sort_areas">';
    foreach ( $areas as $area_id => $area_label ) {
      $ret .= '<ul id="area-' . $area_id  . '" class="sort_list">
        <li class="post_drop_sort_area_name">' . $area_label . '</li>';
      if ( is_array( $value_old ) ) {
        $items = explode( ',', $value_old[ $area_id ] );
        foreach ( $items as $item ) {
          $ret .= '<li id="' . $item . '">';
          $ret .= ( 'thumbnail' == $display ) ? get_the_post_thumbnail( $item, array( 204, 30 ) ) : get_the_title( $item );
          $ret .= '</li>';
        }
      }
      $ret .= '</ul>
        <input type="hidden" name="' . $this->name . '[' . $area_id . ']"
        class="store-area-' . $area_id . '"
        value="' . ( ( $value_old ) ? $value_old[ $area_id ] : '' ) . '" />';
    }
    $ret .= '</div>';

    // Source.
    $exclude = null;
    if ( ! empty( $value_old ) ) {
      $exclude = array_values( $value_old );
    }
    $posts = get_posts( array( 'post_type' => $this->post_type, 'posts_per_page' => -1, 'post__not_in' => $exclude ) );
    $ret .= '<ul class="post_drop_sort_source sort_list">
      <li class="post_drop_sort_area_name">Available ' . $this->label . '</li>';
    foreach ( $posts as $item ) {
      $ret .= '<li id="' . $item->ID . '">';
      $ret .= ( 'thumbnail' == $display ) ? get_the_post_thumbnail( $item->ID, array( 204, 30 ) ) : get_the_title( $item->ID );
      $ret .= '</li>';
    }
    $ret .= '</ul>';
    $ret .= '<br /><span class="description"><a href="' . admin_url( 'edit.php?post_type=' . $this->post_type . '">Manage ' . $post_type_object->label ) . '</a></span>';
    $ret .= '<br class="clear" />' . $this->desc;
    return $ret;
  }
}

// TODO: admin notices for errors.
// save empty array / remove all checkboxes...
// does it make sense to have this? duplicate to default WP metabox.
class AM_MBF_TaxSelect extends AM_MBF {
  protected static $type = 'tax_select';
  protected $sanitizer = 'sanitize_title';

  public function save( $post_id ) {
    wp_set_object_terms( $post_id, $this->value_new, $this->id );
  }

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $terms = get_terms( $this->id, 'get=all' );
    if ( ! is_wp_error( $terms ) ) {
      $taxonomy = get_taxonomy( $this->id );
      if ( count( $terms ) > 0 ) {

        $multiple = ( $this->is_multiple ) ? ' multiple="multiple"' : '';
        $ret = '<select name="' . $this->name . '" id="' . $this->id . '"'  . $multiple . $this->get_data_atts() . '>';
        if ( ! $this->is_multiple ) {
          $ret .= '<option value=""></option>'; // Select One
        }

        $terms_selected = array();
        foreach ( wp_get_object_terms( get_the_ID(), $this->id ) as $post_term ) {
    //      $terms_selected[] = ( $taxonomy->hierarchical ) ? $post_term->term_id : $post_term->slug;
          $terms_selected[] = $post_term->slug;
        }
        foreach ( $terms as $term ) {
    //      $term_value = ( $taxonomy->hierarchical ) ? $term->term_id : $term->slug;
          $term_value = $term->slug;
          $selected = selected( in_array( $term_value, $terms_selected ), true, false );
          $ret .= '<option value="' . $term_value . '"' . $selected . '>' . $term->name . '</option>';
        }
        $ret .= '</select>';
      } else {
        $ret = '<em>' . $taxonomy->labels->not_found . '</em>';
      }
      $ret .= '&nbsp;<span class="description"><a href="' . get_bloginfo( 'url' ) . '/wp-admin/edit-tags.php?taxonomy=' . $this->id . '">Manage ' . $taxonomy->label . '</a></span>';
    } else {
      // TODO!!!
      $error = $terms->get_error_message() . ' "' . $this->id . '"';
      $ret = '<div class="error">' . $this->meta_box->get_title() . ': ' . $error . '</div>';
      $ret .= $error;
    }

    return $ret;
  }
}

// TODO: admin notices for errors.
// save empty array / remove all checkboxes...
// does it make sense to have this? duplicate to default WP metabox.
class AM_MBF_TaxCheckboxes extends AM_MBF {
  protected static $type = 'tax_checkboxes';
  protected $sanitizer = 'sanitize_title';

  public function save( $post_id ) {
    wp_set_object_terms( $post_id, $this->value_new, $this->id );
  }

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $terms = get_terms( $this->id, 'get=all' );
    if ( ! is_wp_error( $terms ) ) {
      $taxonomy = get_taxonomy( $this->id );
      if ( count( $terms ) > 0 ) {
        $terms_checked = array();
        foreach ( wp_get_object_terms( get_the_ID(), $this->id ) as $post_term ) {
    //      $terms_checked[] = ( $taxonomy->hierarchical ) ? $post_term->term_id : $post_term->slug;
          $terms_checked[] = $post_term->slug;
        }

        $ret = '<ul class="meta-box-items">';
        foreach ( $terms as $term ) {
    //      $term_value = ( $taxonomy->hierarchical ) ? $term->term_id : $term->slug;
          $term_value = $term->slug;
          $checked = checked( in_array( $term_value, $terms_checked ), true, false );
          $ret .= '
            <li>
              <input type="checkbox" value="' . $term_value . '" name="' . $this->name . '[]" id="term-' . $term_value . '"' . $checked . $this->get_data_atts() . ' />
              <label for="term-' . $term_value . '">' . $term->name . '</label>
            </li>
          ';
        }
        $ret .= '</ul>';
      } else {
        // No terms found!
        $ret = '<em>' . $taxonomy->labels->not_found . '</em>';
      }

      $post_type = ( isset( $this->post_type ) && in_array( $this->post_type, $taxonomy->object_type ) ) ? $this->post_type : end( $taxonomy->object_type );
      $ret .= '<br class="clear" /><span class="description alignright"><a href="' . get_bloginfo( 'url' ) . '/wp-admin/edit-tags.php?taxonomy=' . $this->id . '&post_type=' . $post_type . '">Manage ' . $taxonomy->label . '</a></span>';
    } else {
      // TODO!!!
      $error = $terms->get_error_message() . ' "' . $this->id . '"';
      $ret = '<div class="error">' . $this->meta_box->get_title() . ': ' . $error . '</div>';
      $ret .= $error;
    }
    return $ret;
  }
}

class AM_MBF_Slider extends AM_MBF {
  protected static $type = 'slider';
  protected $sanitizer = 'floatval';

  public function pre_sanitize() {
    if ( isset( $this->value_new ) ) {
      $this->value_new = split( ',', $this->value_new );
    }
  }

  public function post_sanitize() {
    if ( is_array( $this->value_new ) ) {
      $this->value_new = join( ',', $this->value_new );
    }
  }

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    return '<div id="' . $this->id . '-slider"' . $this->get_classes() . '></div>
      <input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value_old . '"' . $this->get_data_atts() . ' />';
  }
}

class AM_MBF_Image extends AM_MBF {
  protected static $type = 'image';
  protected $sanitizer = 'intval';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $image_url = '';
    $hide_upload_button = $hide_clear_button = ' style="display:none;"';

    if ( ! empty( $this->value_old ) ) {
      if ( ! $image_url = esc_url( wp_get_attachment_image_src( $this->value_old, 'medium' )[0] ) ) {
        $image_url = '';
      }
      $hide_clear_button = '';
    } else {
      $hide_upload_button = '';
    }

    // Text used by wp.media frame.
    $wp_media_data = '
      data-title="' . esc_attr__( 'Choose an Image', 'textdomain' ) . '"
      data-button="' . esc_attr__( 'Use this Image', 'textdomain' ) . '"
    ';

    return  '
      <div' . $this->get_classes( 'meta-box-image' ) . '>
        <input name="' . $this->name . '" type="hidden" class="meta-box-upload-image" value="' . esc_attr( $this->value_old ) . '"' . $this->get_data_atts() . ' />
        <img src="' . $image_url . '" class="meta-box-preview-image" alt="' . __( 'Selected image', 'text-domain' ) . '"' . $hide_clear_button . ' />
        <a href="#" class="meta-box-upload-image-button button" rel="' . get_the_ID() . '"' . $hide_upload_button . $wp_media_data .  '>' . __( 'Choose Image', 'textdomain' ) . '</a>
        <a href="#" class="meta-box-clear-image-button"' . $hide_clear_button . '>' . __( 'Remove Image', 'textdomain' ) . '</a>
      </div>';
  }
}

class AM_MBF_File extends AM_MBF {
  protected static $type = 'file';
  protected $sanitizer = 'intval';

  /**
   * Return the field output.
   * @return string
   */
  public function output() {
    $class_icon = 'meta-box-file-icon';
    $file_url = '';

    $hide_upload_button = $hide_clear_button = ' style="display:none;"';
    if ( isset( $this->value_old ) && $file_url = esc_url( wp_get_attachment_url( intval( $this->value_old ) ) ) ) {
      $class_icon .= ' checked';
      $hide_clear_button = '';
    } else {
      $hide_upload_button = '';
    }

    // Text used by wp.media frame.
    $wp_media_data = '
      data-title="' . esc_attr__( 'Choose a File', 'textdomain' ) . '"
      data-button="' . esc_attr__( 'Use this File', 'textdomain' ) . '"
    ';

    return '
      <div' . $this->get_classes( 'meta-box-file' ) . '>
        <input name="' . $this->name . '" type="hidden" class="meta_box_upload_file" value="' . intval( $this->value ) . '"' . $this->get_data_atts() . ' />
        <span class="' . $class_icon . '"></span>
        <span class="meta-box-filename">' . $file_url . '</span>
        <a href="#" class="meta-box-upload-file-button button" rel="' . get_the_ID() . '"' . $hide_upload_button . $wp_media_data . '>' . __( 'Choose File', 'textdomain' ) . '</a>
        <a href="#" class="meta-box-clear-file-button"' . $hide_clear_button . '>' . __( 'Remove File', 'textdomain') . '</a>
      </div>';
  }
}

class AM_MBF_Repeatable extends AM_MBF {
  protected static $type = 'repeatable';
  protected $is_repeatable = false;

  //TODO: instead of these variables, have an array with the true repeatable field objects.
  /**
   * The old value as it is in the database.
   *
   * @since 1.0.0
   *
   * @var array
   */
  private $_value_old = array();

  /**
   * The new value as it will be saved to the database.
   *
   * @since 1.0.0
   *
   * @var array
   */
  private $_value_new = array();

  /**
   * Add a field to this repeatable field.
   *
   * @param AM_MBF|array  $fields Object or array of AM_MBF to add to this repeatable field.
   */
  public function add_field( $fields ) {
    if ( is_null( $fields ) ) {
      return;
    }

    if ( ! is_array( $fields ) ) {
      $fields = array( $fields );
    }

    foreach ( $fields as $rep_field ) {
      if ( is_a( $rep_field, 'AM_MBF' ) && $rep_field->is_repeatable() ) {
        $rep_field->clean_data();

        // Set the new repeatable names and ids, set up as arrays for the repeatable field.
        $rep_field->add_data( 'id', $rep_field->get_id() );
        $rep_field->add_data( 'parent', $this->id );

        $this->repeatable_fields[ $rep_field->get_id() ] = $rep_field;
      }
    }
  }

  /**
   * Saves the field data.
   */
  public function save( $post_id ) {
    if ( isset( $this->_value_old ) && ( is_null( $this->_value_new ) || '' == $this->_value_new ) ) {
      delete_post_meta( $post_id, $this->id, $this->_value_old );
    } elseif ( $this->_value_new != $this->_value_old ) {
      update_post_meta( $post_id, $this->id, $this->_value_new );
    }
  }

  /**
   * Sanitize the new value of this field and all repeatable fields.
   */
  public function sanitize() {
    if ( is_array( $this->value_old ) ) {
      // Loop all values.
      foreach ( $this->value_old as $rep_fields ) {
        // Loop all fields.
        if ( is_array( $rep_fields ) ) {
          foreach ( $rep_fields as $rep_field ) {
            if ( is_a( $rep_field, 'AM_MBF' ) ) {
              $rep_field->sanitize();
            }
          }
        }
      }
    }
  }

  /**
   * Validate the new value of this field and all repeatable fields.
   */
  public function validate() {
    if ( is_array( $this->value_old ) ) {
      // Loop all values.
      foreach ( $this->value_old as $rep_fields ) {
        // Loop all fields.
        if ( is_array( $rep_fields ) ) {
          foreach ( $rep_fields as $rep_field ) {
            if ( is_a( $rep_field, 'AM_MBF' ) ) {
              $rep_field->validate();
            }
          }
        }
      }
    }
  }


  public function set_value_old( $value_old ) {
    $this->_value_old = $value_old;

    if ( is_array( $this->repeatable_fields ) && $this->repeatable_fields && is_array( $value_old ) ) {

      $values_old = array_values( $value_old );
      $new_values_old = array();
      $i = count( $values_old );
      while ( $i-- > -1 ) {

        if ( -1 == $i ) {
          // Prepare id and name for template fields.
          foreach ( $this->repeatable_fields as $rep_field ) {
            $rep_field->set_id( $this->id . '-' . $rep_field_id . '-empty' );
            $rep_field->set_name( '' );
          }
        } else {
          // Assign the values to the field objects themselves.

          $values = $values_old[ $i ];
          $new_values = array();

          // Remember if any of the repeatable fields are set.
          $is_set = false;

          foreach ( $this->repeatable_fields as $rep_field ) {
            $rep_field_id = $rep_field->get_id();

            if ( ! array_key_exists( $rep_field_id, $values ) ) {
              $values[ $rep_field_id ] = null;
            } elseif ( isset( $values[ $rep_field_id ] ) && '' != $values[ $rep_field_id ] ) {
              $is_set = true;
            }

            // Clone repeatable field to keep original pristine.
            $rep_field = clone( $rep_field );

            $rep_field->set_value_old( $values[ $rep_field_id ] );
            $rep_field->set_id( $rep_field_id . '-' . $i );
            $rep_field->set_name( $this->id . '[' . $i . '][' . $rep_field_id .']' );

            $new_values[] = $rep_field;
          }
          // Only add to values if an old value has been set.
          if ( $is_set ) {
            $new_values_old[] = $new_values;
          }
        }
      }
      // Reverse the entries of the array to have them sorted correctly.
      $value_old = array_reverse( $new_values_old );
    }
    $this->value_old = $value_old;
  }


  public function set_value_new( $value_new ) {

    if ( is_array( $this->repeatable_fields ) && count( $this->repeatable_fields ) > 0 ) {

      if ( is_array( $value_new ) ) {

        // Get rid of empty entries.
        $values_new = array();
        foreach ( $value_new as $value ) {
          if ( is_array( $value ) ) {
            $value = array_filter( $value );
            if ( count( $value ) > 0 ) {
              $values_new[] = $value;
            }
          }
        }
        $this->_value_new = $values_new;

        $new_values_new = array();
        $i = count( $values_new );
        while ( $i-- > 0 ) {

          // Assign the values to the field objects themselves.

          $values = $values_new[ $i ];
          $new_values = array();

          // Remember if any of the repeatable fields are set.
          $is_set = false;

          foreach ( $this->repeatable_fields as $rep_field ) {
            $rep_field_id = $rep_field->get_id();

            if ( ! array_key_exists( $rep_field_id, $values ) ) {
              $values[ $rep_field_id ] = null;
            } elseif ( isset( $values[ $rep_field_id ] ) && '' != $values[ $rep_field_id ] ) {
              $is_set = true;
            }

            // Clone repeatable field to keep original pristine.
            $rep_field = clone( $rep_field );

            $rep_field->set_value_new( $values[ $rep_field_id ] );
            $rep_field->set_id( $rep_field_id . '-' . $i );
            $rep_field->set_name( $this->id . '[' . $i . '][' . $rep_field_id .']' );

            $new_values[] = $rep_field;
          }
          // Only add to values if an old value has been set.
          if ( $is_set ) {
            $new_values_new[] = $new_values;
          }
        }
        // Reverse the entries of the array to have them sorted correctly.
        $value_new = array_reverse( $new_values_new );
      }
    }
    $this->value_new = $value_new;
  }

  /**
   * Return the field output.
   * @return string
   */
  public function output() {

    $empty_fields_template = '';
    $field_outputs = '';

    if ( $this->get_repeatable_fields() ) {
      $values_old = $this->value_old;
      if ( ! is_array( $values_old ) ) {
        $values_old = array( $values_old );
      }
      // Clean away empty entries.
      $values_old = array_filter( array_values( $values_old ) );

      for ( $i = -1; $i < count( $values_old ); $i++ ) {
        $is_empty_template = ( -1 == $i );

        $class_empty_template = ( $is_empty_template ) ? ' class="empty-fields-template" style="display:none;"' : '';

        $field_outputs .= '
          <tr' . $class_empty_template . '>
            <td><span class="ui-icon ui-icon-grip-dotted-horizontal sort hndle"></span></td>
            <td>
        ';

        // Add all repeatable fields to empty template / Output all saved values.
        $rep_fields = ( $is_empty_template ) ? $this->repeatable_fields : $values_old[ $i ];

        foreach ( $rep_fields as $rep_field ) {
          $field_outputs .= '<label class="meta-box-field-label" for="' . $rep_field->get_id() . '">' . $rep_field->get_label() . '</label>';
          $field_outputs .= $rep_field->output();
          $field_outputs .= '<br class="clear" />' . $rep_field->get_desc();
        }
        $field_outputs .= '
            </td>
            <td><a class="ui-icon ui-icon-minusthick meta-box-repeatable-remove" href="#"></a></td>
          </tr>
        ';

        // Save empty template seperately.
        if ( $is_empty_template ) {
          $empty_fields_template = $field_outputs;

          // Reset the field outputs to prevent having the empty template with them.
          $field_outputs = '';
        }
      }

      return '
        <table id="' . $this->id . '-repeatable" class="meta-box-repeatable" cellspacing="0">
          <thead>
            <tr>
              <th><span class="ui-icon ui-icon-arrowthick-2-n-s sort-label"></span></th>
              <th>' . __( 'Repeatable Fields', 'textdomain' ) . '</th>
              <th><a class="ui-icon ui-icon-plusthick meta-box-repeatable-add" href="#" data-position="top"></a></th>
            </tr>
          ' . $empty_fields_template . '
          </thead>
          <tbody>
          ' . $field_outputs . '
          </tbody>
          <tfoot>
            <tr>
              <th><span class="ui-icon ui-icon-arrowthick-2-n-s sort-label"></span></th>
              <th>' . __( 'Repeatable Fields', 'textdomain' ) . '</th>
              <th><a class="ui-icon ui-icon-plusthick meta-box-repeatable-add" href="#" data-position="bottom"></a></th>
            </tr>
          </tfoot>
        </table>
      ';
    } else {
      return __( 'No repeatable fields assigned!', 'text-domain' );
    }
  }
}




















class Tax_Note_Styles extends AM_Tax {
  public function __construct() {
    $labels = array(
      'name'          => _x( 'Styles', 'Taxonomy plural name', 'text-domain' ),
      'singular_name'      => _x( 'Style', 'Taxonomy singular name', 'text-domain' ),
      'search_items'      => __( 'Search Styles', 'text-domain' ),
      'popular_items'      => __( 'Popular Styles', 'text-domain' ),
      'all_items'        => __( 'All Styles', 'text-domain' ),
      'parent_item'      => __( 'Parent Style', 'text-domain' ),
      'parent_item_colon'    => __( 'Parent Style', 'text-domain' ),
      'edit_item'        => __( 'Edit Style', 'text-domain' ),
      'update_item'      => __( 'Update Style', 'text-domain' ),
      'add_new_item'      => __( 'Add New Style', 'text-domain' ),
      'new_item_name'      => __( 'New Style Name', 'text-domain' ),
      'add_or_remove_items'  => __( 'Add or remove Styles', 'text-domain' ),
      'choose_from_most_used'  => __( 'Choose from most used Styles', 'text-domain' ),
      'menu_name'        => __( 'Styles', 'text-domain' ),
      'not_found' => __( 'No styles found', 'text-domain' )
    );

    $args = array(
      'labels'            => $labels,
      'public'            => true,
      'show_in_nav_menus' => true,
      'show_admin_column' => false,
      'hierarchical'      => false,
      'show_tagcloud'     => true,
      'show_ui'           => true,
      'query_var'         => true,
      'rewrite'           => true,
      'query_var'         => true,
      'capabilities'      => array(),
    );
    parent::__construct( 'styles', $args );
  }
}

class CPT_Note extends AM_CPT {

  public function __construct() {

    $labels = array(
      'name'                => __( 'Notes', 'text-domain' ),
      'singular_name'       => __( 'Note', 'text-domain' ),
      'add_new'             => _x( 'Add New Note', 'text-domain', 'text-domain' ),
      'add_new_item'        => __( 'Add New Note', 'text-domain' ),
      'edit_item'           => __( 'Edit Note', 'text-domain' ),
      'new_item'            => __( 'New Note', 'text-domain' ),
      'view_item'           => __( 'View Note', 'text-domain' ),
      'search_items'        => __( 'Search Notes', 'text-domain' ),
      'not_found'           => __( 'No Notes found', 'text-domain' ),
      'not_found_in_trash'  => __( 'No Notes found in Trash', 'text-domain' ),
      'parent_item_colon'   => __( 'Parent Note:', 'text-domain' ),
      'menu_name'           => __( 'Notes', 'text-domain' ),
    );

    $args = array(
      'labels'              => $labels,
      'hierarchical'        => false,
      'description'         => 'description',
      'taxonomies'          => array(),
      'public'              => true,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'show_in_admin_bar'   => true,
      'menu_position'       => null,
      'menu_icon'           => null,
      'show_in_nav_menus'   => true,
      'publicly_queryable'  => true,
      'exclude_from_search' => false,
      'has_archive'         => true,
      'query_var'           => true,
      'can_export'          => true,
      'rewrite'             => true,
      'capability_type'     => 'post',
      'supports'            => array(
        'title', 'editor', 'author', 'thumbnail',
        'excerpt','custom-fields', 'trackbacks', 'comments',
        'revisions', 'page-attributes', 'post-formats'
        )
    );

    parent::__construct( 'note', $args, new Tax_Note_Styles() );
  }

  public static function create() {
    $cpt_note = new self();
    $cpt_note->register();
  }
}

//CPT_Note::create();

$cpt_note = new CPT_Note();
$tax_styles = new Tax_Note_Styles();
$cpt_note->assign_taxonomy( $tax_styles );

$tax_styles->assign_post_type('post');

// Seperately register new taxonomy for posts.
/*$new_tax = new AM_Tax( $tax_styles->get_slug(), $tax_styles->get_args() );
$new_tax->set_cpt('page');
$new_tax->register();*/


$mb = new AM_MB( 'metabox1', 'First Metabox' );
$rep = AM_MBF::create_batch(
  array( 'repeatable', 'rep1', 'Rep nr.1', 'desc...' ),
  array( 'plaintext', 'plaintext1', 'Just some plain text', 'and the description' ),
  array( 'text','text1','a simple text input', 'text description' ),
  array( 'tel','tel1','a simple tel input', 'tel description' ),
  array( 'url','url1','a simple url input', 'url description' ),
  array( 'email','email1','a simple email input', 'email description' ),
  array( 'number','number1','a simple number input', 'number description' )
);

$rep[0]->add_field( clone($rep[2]) );


//$mb2 = new AM_MB( 'metabox2', 'Second Metabox' );

//$rep = AM_MBF::create('repeatable', 'rep1', 'Rep nr.1', 'desc...' );
//$f = AM_MBF::create( 'text','txt1','a checkbox field!!','some description...' );
//$f2 = AM_MBF::create('text','text1','a simple text input');
//$rep->add_field($f);
//$mb->add_field($f2);
//$f->add_settings(array('min'=>5,'max'=>100,'step'=>0.5,'handles'=>3));
//$f->set_post_type( 'pages' );

//$mb2->add_field();

//$f->add_options( array('one'=>'first','two'=>'second','three'=>'third','four'=>'fourth','five'=>'fifth') );
//$f->is_multiple(true);


$mb->add_field( $rep );


//fu($mb);
$cpt_note->assign_meta_box( array( $mb, $mb2 ) );

$cpt_note->register();




?>