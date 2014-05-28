<?php

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
      if ( array_intersect( array( 'image', 'file' ), $used_types ) ) {
        $deps_js[] = 'media-upload';
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

      if ( array_intersect( array( 'slider' ), $used_types ) ) {

        $js_out = '<script type="text/javascript">
          jQuery(function( $) {';

        foreach ( $this->meta_boxes as $meta_box ) {
          foreach ( $meta_box->get_fields() as $field ) {
    // TODO: move to fields themselves, decouple!
    // TODO: repeatables!
            switch( $field->get_type() ) {
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

?>