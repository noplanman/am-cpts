<?php

/**
 * Checkboxes of posts.
 *
 * @since 1.0.0
 */
class AM_MBF_PostCheckboxes extends AM_MBF {
  protected static $type = 'post_checkboxes';
  protected $sanitizer = 'intval';

  /**
   * Check AM_MBF for description.
   */
  public function output() {
    // Backup id.
    $id_bkp = $this->id;

    if ( ! isset( $this->post_type ) || '' == $this->post_type ) {
      $this->post_type = 'post';
    }

    $posts = get_posts( array( 'post_type' => $this->post_type, 'posts_per_page' => -1 ) );
    $ret = '<ul class="meta-box-items">';
    foreach ( $posts as $item ) {
      $this->id = $id_bkp . '-' . $item->ID;
      $ret .= sprintf( '<li><input type="checkbox" value="%3$s" name="%2$s[]" id="%1$s"%5$s%6$s%7$s /><label for="%1$s">%4$s</label></li>',
        esc_attr( $this->id ),
        esc_attr( $this->name ),
        esc_attr( $item->ID ),
        esc_html( $item->post_title ),
        checked( is_array( $this->value_old ) && in_array( $item->ID, $this->value_old ), true, false ),
        $this->get_classes(),
        $this->get_data_atts()
      );
    }
    $ret .= '</ul>';
    if ( $posts && $post_type_object = get_post_type_object( $this->post_type ) ) {
      $ret .= sprintf( '<br class="clear" /><span class="description alignright"><a href="%2$s">Manage %1$s</a></span>',
        $post_type_object->label,
        admin_url( 'edit.php?post_type=' . $this->post_type )
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
  protected static $type = 'post_select';
  protected $sanitizer = 'intval';

  /**
   * Check AM_MBF for description.
   */
  public function output() {
    if ( ! isset( $this->post_type ) || '' == $this->post_type ) {
      $this->post_type = 'post';
    }

    $ret = sprintf( '<select name="%2$s[]" id="%1$s"%3$s%4$s%5$s>',
      esc_attr( $this->id ),
      esc_attr( $this->name ),
      ( $this->is_multiple ) ? ' multiple="multiple"' : '',
      $this->get_classes(),
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
        selected( is_array( $this->value_old ) && in_array( $item->ID, $this->value_old ), true, false )
      );
    }
    $ret .= '</select>';

    if ( $posts && $post_type_object = get_post_type_object( $this->post_type ) ) {
      $ret .= sprintf( '&nbsp;<span class="description alignright"><a href="%2$s">Manage %1$s</a></span>',
        $post_type_object->label,
        admin_url( 'edit.php?post_type=' . $this->post_type )
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
  protected static $type = 'post_chosen';
  protected $sanitizer = 'intval';

  /**
   * Check AM_MBF for description.
   */
  public function output() {
    $ret = sprintf( '<select data-placeholder="%3$s" name="%2$s[]" id="%1$s"%4$s%5$s%6$s>',
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
        selected( is_array( $this->value_old ) && in_array( $item->ID, $this->value_old ), true, false )
      );
    }
    $ret .= '</select>';

    if ( $posts && $post_type_object = get_post_type_object( $this->post_type ) ) {
      $ret .= sprintf( '&nbsp;<span class="description alignright"><a href="%2$s">Manage %1$s</a></span>',
        $post_type_object->label,
        admin_url( 'edit.php?post_type=' . $this->post_type )
      );
    }

    return $ret;
  }
}

?>