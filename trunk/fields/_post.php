<?php

/**
 * Checkboxes of posts.
 *
 * Valid settings:
 * order     Direction to order posts.
 * orderby   Field to order posts by.
 * post_type Get posts from this post type.
 *
 * @since 1.0.0
 */
class AM_MBF_PostCheckboxes extends AM_MBF {
  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected static $type = 'post_checkboxes';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected $sanitizer = 'intval';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.2.0
   */
  protected $validator = 'intval';

  /**
   * Constructor to optionally set post type and settings.
   *
   * @since 1.1.0
   *
   * @param string $post_type Post type for posts to load.
   * @param null|array $settings Associative array of key-value pairs.
   */
  public function init( $post_type = 'post', $settings = null ) {
    $this->add_setting( 'post_type', $post_type );
    $this->add_settings( $settings );
  }

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function output() {
    // Make sure the post type is set correctly.
    $post_type = $this->get_setting( 'post_type' );
    $post_type_object = null;

    // Warning message.
    $warning = null;

    if ( ! isset( $post_type ) || '' == $post_type ) {
      return __( 'Post type has not been set.', 'am-cpts' );
    } elseif ( ! $post_type_object = get_post_type_object( $post_type ) ) {
      $warning = __( sprintf( 'Post type "%1$s" is not registered.', $post_type ), 'am-cpts' );
    }

    // Backup id because it gets modified for each checkbox.
    $id_bkp = $this->id;

    // Get all posts for the current post type.
    $posts = get_posts( array(
      'post_type' => $post_type,
      'posts_per_page' => -1,
      'orderby' => $this->get_setting( 'orderby', 'none' ),
      'order'   => $this->get_setting( 'order', 'ASC' )
    ) );

    $ret = '';
    if ( ! empty( $posts ) ) {
      $ret = '<ul class="meta-box-items">';
      foreach ( $posts as $item ) {
        $this->id = $id_bkp . '-' . $item->ID;
        $ret .= sprintf( '<li><input type="checkbox" value="%3$s" name="%2$s[]" id="%1$s"%5$s%6$s%7$s /><label for="%1$s">%4$s</label></li>',
          esc_attr( $this->id ),
          esc_attr( $this->name ),
          esc_attr( $item->ID ),
          esc_html( $item->post_title ),
          checked( is_array( $this->value ) && in_array( $item->ID, $this->value ), true, false ),
          $this->get_classes(),
          $this->get_data_atts()
        );
      }
      $ret .= '</ul>';
    } elseif( isset( $post_type_object->labels->not_found ) ) {
      $ret = $post_type_object->labels->not_found;
    } else {
      $ret = __( sprintf( 'No posts of type "%1$s" found.', $post_type ), 'am-cpts' );
    }

    if ( isset( $post_type_object->label ) ) {
      $ret .= sprintf( '<span class="description alignright"><a href="%2$s" target="_blank">Manage %1$s</a></span>',
        $post_type_object->label,
        admin_url( 'edit.php?post_type=' . $post_type )
      );
    } elseif ( isset( $warning ) ) {
      $ret .= sprintf( '<span class="description alignright">%1$s</span>',
        $warning
      );
    }

    // Revert id.
    $this->id = $id_bkp;

    return $ret;
  }
}

/**
 * Drop down of posts.
 *
 * Valid settings:
 * chosen    Display this drop down as a 'chosen' drop down.
 * force     Force a selection.
 * multiple  Multiple selection possible.
 * order     Direction to order posts.
 * orderby   Field to order posts by.
 * post_type Get posts from this post type.
 *
 * @since 1.0.0
 */
class AM_MBF_PostSelect extends AM_MBF {
  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected static $type = 'post_select';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected $sanitizer = 'intval';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.2.0
   */
  protected $validator = 'intval';

  /**
   * Constructor to optionally set post type and settings.
   *
   * @since 1.1.0
   *
   * @param string $post_type Post type for posts to load.
   * @param null|array $settings Associative array of key-value pairs.
   */
  public function init( $post_type = 'post', $settings = null ) {
    $this->add_setting( 'post_type', $post_type );
    $this->add_settings( $settings );
  }

  /**
   * Set sub type. Check AM_MBF for description.
   *
   * @since 1.1.0
   */
  public function get_sub_type() {
    if ( $this->get_setting( 'chosen' ) ) {
      return 'post_chosen';
    }
  }

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function output() {
    // Make sure the post type is set correctly.
    $post_type = $this->get_setting( 'post_type' );
    $post_type_object = null;

    // Warning message.
    $warning = null;

    if ( ! isset( $post_type ) || '' == $post_type ) {
      return __( 'Post type has not been set.', 'am-cpts' );
    } elseif ( ! $post_type_object = get_post_type_object( $post_type ) ) {
      $warning = __( sprintf( 'Post type "%1$s" is not registered.', $post_type ), 'am-cpts' );
    }

    // Get all posts for the current post type.
    $posts = get_posts( array(
      'post_type' => $post_type,
      'posts_per_page' => -1,
      'orderby' => $this->get_setting( 'orderby', 'name' ),
      'order'   => $this->get_setting( 'order', 'ASC' )
    ) );

    $ret = '';
    if ( ! empty( $posts ) ) {
      // Check if this is a 'chosen' drop down.
      $chosen = $this->get_setting( 'chosen', false );

      // Add placeholder value used by chosen.
      if ( $chosen ) {
        $this->add_data( 'placeholder', __( 'Select One', 'am-cpts' ) );
      }

      $ret = sprintf( '<select name="%2$s" id="%1$s"%3$s%4$s%5$s>',
        esc_attr( $this->id ),
        esc_attr( $this->name ),
        ( $this->get_setting( 'multiple' ) ) ? ' multiple="multiple"' : '',
        $this->get_classes( ( $chosen ) ? 'chosen' : '' ),
        $this->get_data_atts()
      );
      if ( ! $this->get_setting( 'multiple' ) && ! $this->get_setting( 'force', false ) ) {
        $ret .= '<option value=""></option>'; // Select One
      }

      foreach ( $posts as $item ) {
        $ret .= sprintf( '<option value="%1$s"%3$s>%2$s</option>',
          esc_attr( $item->ID ),
          esc_html( $item->post_title ),
          selected( is_array( $this->value ) && in_array( $item->ID, $this->value ), true, false )
        );
      }
      $ret .= '</select>';
    } elseif ( isset( $post_type_object->labels->not_found ) ) {
      $ret = $post_type_object->labels->not_found;
    } else {
      $ret = __( sprintf( 'No posts of type "%1$s" found.', $post_type ), 'am-cpts' );
    }

    if ( isset( $post_type_object->label ) ) {
      $ret .= sprintf( '<span class="description alignright"><a href="%2$s" target="_blank">Manage %1$s</a></span>',
        $post_type_object->label,
        admin_url( 'edit.php?post_type=' . $post_type )
      );
    } elseif ( isset( $warning ) ) {
      $ret .= sprintf( '<span class="description alignright">%1$s</span>',
        $warning
      );
    }

    return $ret;
  }
}

?>