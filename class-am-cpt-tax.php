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
   * Register the CPT with all it's taxonomies and meta boxes (callback for WP add_action).
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
    return ( isset( $this->args['labels'][ $key ] ) ) ? $this->args['labels'][ $key ] : null;
  }

  /**
   * Get all labels.
   *
   * @since 1.0.0
   *
   * @return array|null
   */
  final public function get_labels() {
    return ( isset( $this->args['labels'] ) ) ? $this->args['labels'] : null;
  }
}

/**
 * AM_Tax class, that builds on AM_CPT_Tax.
 */
class AM_Tax extends AM_CPT_Tax {
  /**
   * An array of all created AM_Tax.
   *
   * @since 1.2.0
   *
   * @var array
   */
  private static $_all_taxs = array();

  /**
   * Get one or more already created AM_Tax.
   *
   * @since 1.2.0
   *
   * @param  null|string|array $tax_slug Slug of the AM_Tax(s) to get. If null, the current taxonomies are used.
   * @return array                       Array of requested AM_Tax(s).
   */
  public static function get( $tax_slug = null ) {
    $tax_slug = ( isset( $tax_slug ) ) ? $tax_slug : array_keys( get_the_taxonomies() );

    // Make sure we have an array to work with. If we have comma seperated values, make them into an array.
    if ( ! is_array( $tax_slug ) ) {
      $tax_slug = explode( ',', $tax_slug );
    }

    return array_intersect_key( self::$_all_taxs, array_flip( $tax_slug ) );
  }

  /**
   * Get a single already created AM_Tax.
   *
   * @since 1.2.0
   *
   * @param  null|string $tax_slug Slug of the AM_Tax to get. If null, the first current taxonomy is used.
   * @return null|AM_Tax           The requested AM_Tax, if it exists.
   */
  public static function get_single( $tax_slug = null ) {
    $tax_slug = ( isset( $tax_slug ) ) ? (array) $tax_slug : array_keys( get_the_taxonomies() );
    // Get the first entry.
    $tax_slug = reset( $tax_slug );

    return ( array_key_exists( $tax_slug, self::$_all_taxs ) ) ? self::$_all_taxs[ $tax_slug ] : null;
  }

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
    if ( ! isset( $post_types ) ) {
      return;
    }

    // Make sure we have an array to work with. If we have comma seperated values, make them into an array.
    if ( ! is_array( $post_types ) ) {
      $post_types = explode( ',', $post_types );
    }

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
    if ( ! isset( $post_types ) ) {
      return;
    }

    // Make sure we have an array to work with. If we have comma seperated values, make them into an array.
    if ( ! is_array( $post_types ) ) {
      $post_types = explode( ',', $post_types );
    }

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

    // Save this taxonomy to $_all_taxs so it can easily be fetched again when required.
    self::$_all_taxs[ $this->slug ] = $this;
  }

  /**
   * Register taxonomy / taxonomies (callback for WP add_action).
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
   *
   * @var array
   */
  protected $taxonomies = array();

  /**
   * Meta boxes assigned to this CPT.
   *
   * @since 1.0.0
   *
   * @var array
   */
  protected $meta_boxes = array();

  /**
   * An array of all created AM_CPT.
   *
   * @since 1.0.0
   *
   * @var array
   */
  private static $_all_cpts = array();

  /**
   * Get one or more already created AM_CPT.
   *
   * @since 1.2.0
   *
   * @param  null|string|array $cpt_slug Slug of the AM_CPT(s) to get. If null, the current post type is used.
   * @return array                       Array of requested AM_CPT(s).
   */
  public static function get( $cpt_slug = null ) {
    $cpt_slug = ( isset( $cpt_slug ) ) ? $cpt_slug : get_post_type();

    // Make sure we have an array to work with. If we have comma seperated values, make them into an array.
    if ( ! is_array( $cpt_slug ) ) {
      $cpt_slug = explode( ',', $cpt_slug );
    }

    return array_intersect_key( self::$_all_cpts, array_flip( $cpt_slug ) );
  }

  /**
   * Get a single already created AM_CPT.
   *
   * @since 1.2.0
   *
   * @param  null|string $cpt_slug Slug of the AM_CPT to get. If null, the current post type is used.
   * @return null|AM_CPT           The requested AM_CPT, if it exists.
   */
  public static function get_single( $cpt_slug = null ) {
    $cpt_slug = ( isset( $cpt_slug ) ) ? $cpt_slug : get_post_type();

    return ( array_key_exists( $cpt_slug, self::$_all_cpts ) ) ? self::$_all_cpts[ $cpt_slug ] : null;
  }

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
    if ( ! isset( $taxonomies ) ) {
      return;
    }

    // Make sure we have an array to work with.
    if ( ! is_array( $taxonomies ) ) {
      $taxonomies = array( $taxonomies );
    }

    // Assign all taxonomies.
    foreach ( $taxonomies as $taxonomy ) {
      if ( $taxonomy instanceof AM_Tax ) {
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
    if ( ! isset( $taxonomies ) ) {
      return;
    }

    // Make sure we have an array to work with. If we have comma seperated values, make them into an array.
    if ( ! is_array( $taxonomies ) ) {
      $taxonomies = explode( ',', $taxonomies );
    }

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
    if ( ! isset( $meta_boxes ) ) {
      return;
    }

    // Make sure we have an array to work with.
    if ( ! is_array( $meta_boxes ) ) {
      $meta_boxes = array( $meta_boxes );
    }

    // Add all meta boxes.
    foreach ( $meta_boxes as $meta_box ) {
      if ( $meta_box instanceof AM_MB ) {
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
    if ( ! isset( $meta_boxes ) ) {
      return;
    }

    // Make sure we have an array to work with. If we have comma seperated values, make them into an array.
    if ( ! is_array( $meta_boxes ) ) {
      $meta_boxes = explode( ',', $meta_boxes );
    }

    // Remove all meta boxes.
    $this->meta_boxes = array_diff_key( $this->meta_boxes, array_flip( $meta_boxes ) );
  }

  /**
   * Get meta boxes assigned to this CPT.
   *
   * @since 1.0.0
   *
   * @param string|array $meta_boxes Meta box / boxes to get from this CPT. Single key, comma seperated keys, array of keys.
   * @return array List of all AM_MB objects.
   */
  final public function get_meta_boxes( $meta_boxes = null) {

    $ret_meta_boxes = array();

    if ( ! isset( $meta_boxes ) ) {
      // Return them all.
      $ret_meta_boxes = $this->meta_boxes;
    } else {

      // Make sure we have an array to work with. If we have comma seperated values, make them into an array.
      if ( ! is_array( $meta_boxes ) ) {
        $meta_boxes = explode( ',', $meta_boxes );
      }

      // Return only the requested meta boxes.
      $ret_meta_boxes = array_intersect_key( $this->meta_boxes, array_flip( $meta_boxes ) );
    }

    // Load meta data for all meta boxes to be returned.
    foreach ( $ret_meta_boxes as $meta_box ) {
      $meta_box->load_data();
    }

    // Return the requested meta boxes.
    return $ret_meta_boxes;
  }

  /**
   * Get the selected meta box assigned to this CPT.
   *
   * @since 1.0.0
   *
   * @param string $meta_box ID of the meta box to get.
   * @return AM_MB The requested meta box, if it exists.
   */
  final public function get_meta_box( $meta_box ) {
    if ( array_key_exists( $meta_box, $this->meta_boxes ) ) {
      $ret_meta_box = $this->meta_boxes[ $meta_box ];
      $ret_meta_box->load_data();
      return $ret_meta_box;
    }
    return null;
  }

  /**
   * Add all actions related to registering this custom post type.
   */
  final public function register() {
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

    // Save this CPT to $_all_cpts so it can easily be fetched again when required.
    self::$_all_cpts[ $this->slug ] = $this;
  }

  /**
   * Register the CPT (callback for WP add_action).
   */
  final public function _register() {
    register_post_type( $this->slug, $this->args );
  }
}

?>