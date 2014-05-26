<?php

/**
 * Checkboxes of posts.
 */
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

/**
 * Drop down of posts.
 */
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

/**
 * 'Chosen' drop down of posts.
 */
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
/**
 * Drop sort of posts.
 */
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

?>