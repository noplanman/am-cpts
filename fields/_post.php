<?php

/**
 * Checkboxes of posts.
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
   * Constructor to optionally set post type.
   *
   * @since 1.1.0
   *
   * @param string $post_type Post type for posts to load.
   */
  public function init( $post_type = 'post' ) {
    $this->set_post_type( $post_type );
  }

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function output() {
    // Make sure the post type is set correctly.
    $post_type_object = null;
    $warning = null;
    if ( ! isset( $this->post_type ) || '' == $this->post_type ) {
      return __( 'Post type has not been set.', 'am-cpts' );
    } elseif ( ! $post_type_object = get_post_type_object( $this->post_type ) ) {
      $warning = __( sprintf( 'Post type "%1$s" is not registered.', $this->post_type ), 'am-cpts' );
    }

    // Backup id because it gets modified for each checkbox.
    $id_bkp = $this->id;

    // Get all posts for the current post type.
    $posts = get_posts( array( 'post_type' => $this->post_type, 'posts_per_page' => -1 ) );

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
      // Should theoretically never get here, but just in case...
      $ret = __( sprintf( 'No posts of type "%1$s" found.', $this->post_type ), 'am-cpts' );
    }

    if ( isset( $post_type_object->label ) ) {
      $ret .= sprintf( '<span class="description alignright"><a href="%2$s" target="_blank">Manage %1$s</a></span>',
        $post_type_object->label,
        admin_url( 'edit.php?post_type=' . $this->post_type )
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
   * Constructor to optionally set post type.
   *
   * @since 1.1.0
   *
   * @param string $post_type Post type for posts to load.
   */
  public function init( $post_type = 'post' ) {
    $this->set_post_type( $post_type );
  }

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function output() {
    // Make sure the post type is set correctly.
    $post_type_object = null;
    $warning = null;
    if ( ! isset( $this->post_type ) || '' == $this->post_type ) {
      return __( 'Post type has not been set.', 'am-cpts' );
    } elseif ( ! $post_type_object = get_post_type_object( $this->post_type ) ) {
      $warning = __( sprintf( 'Post type "%1$s" is not registered.', $this->post_type ), 'am-cpts' );
    }

    // Get all posts for the current post type.
    $posts = get_posts( array( 'post_type' => $this->post_type, 'posts_per_page' => -1, 'orderby' => 'name', 'order' => 'ASC' ) );

    $ret = '';
    if ( ! empty( $posts ) ) {
      $ret = sprintf( '<select name="%2$s" id="%1$s"%3$s%4$s%5$s>',
        esc_attr( $this->id ),
        esc_attr( $this->name ),
        ( $this->is_multiple ) ? ' multiple="multiple"' : '',
        $this->get_classes(),
        $this->get_data_atts()
      );
      if ( ! $this->is_multiple ) {
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
      // Should theoretically never get here, but just in case...
      $ret = __( sprintf( 'No posts of type "%1$s" found.', $this->post_type ), 'am-cpts' );
    }

    if ( isset( $post_type_object->label ) ) {
      $ret .= sprintf( '<span class="description alignright"><a href="%2$s" target="_blank">Manage %1$s</a></span>',
        $post_type_object->label,
        admin_url( 'edit.php?post_type=' . $this->post_type )
      );
    } elseif ( isset( $warning ) ) {
      $ret .= sprintf( '<span class="description alignright">%1$s</span>',
        $warning
      );
    }

    return $ret;
  }
}

/**
 * 'Chosen' drop down of posts.
 *
 * @since 1.0.0
 */
class AM_MBF_PostChosen extends AM_MBF {
  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected static $type = 'post_chosen';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected $sanitizer = 'intval';

  /**
   * Constructor to optionally set post type.
   *
   * @since 1.1.0
   *
   * @param string $post_type Post type for posts to load.
   */
  public function init( $post_type = 'post' ) {
    $this->set_post_type( $post_type );
  }

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function output() {
    // Make sure the post type is set correctly.
    $post_type_object = null;
    $warning = null;
    if ( ! isset( $this->post_type ) || '' == $this->post_type ) {
      return __( 'Post type has not been set.', 'am-cpts' );
    } elseif ( ! $post_type_object = get_post_type_object( $this->post_type ) ) {
      $warning = __( sprintf( 'Post type "%1$s" is not registered.', $this->post_type ), 'am-cpts' );
    }

    // Get all posts for the current post type.
    $posts = get_posts( array( 'post_type' => $this->post_type, 'posts_per_page' => -1, 'orderby' => 'name', 'order' => 'ASC' ) );

    $ret = '';
    if ( ! empty( $posts ) ) {
      $ret = sprintf( '<select data-placeholder="%3$s" name="%2$s" id="%1$s"%4$s%5$s%6$s>',
        esc_attr( $this->id ),
        esc_attr( $this->name ),
        esc_attr__( 'Select One', 'am-cpts' ),
        ( $this->is_multiple ) ? ' multiple="multiple"' : '',
        $this->get_classes( 'chosen' ),
        $this->get_data_atts()
      );
      if ( ! $this->is_multiple ) {
        $ret .= '<option value=""></option>'; // Select One
      }

      $posts = get_posts( array( 'post_type' => $this->post_type, 'posts_per_page' => -1, 'orderby' => 'name', 'order' => 'ASC' ) );
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
      // Should theoretically never get here, but just in case...
      $ret = __( sprintf( 'No posts of type "%1$s" found.', $this->post_type ), 'am-cpts' );
    }

    if ( isset( $post_type_object->label ) ) {
      $ret .= sprintf( '<span class="description alignright"><a href="%2$s" target="_blank">Manage %1$s</a></span>',
        $post_type_object->label,
        admin_url( 'edit.php?post_type=' . $this->post_type )
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