<?php

/**
 * Drop down of taxonomies.
 *
 * @todo Does it make sense to have this?! Duplicate to default WP meta box?!
 * @todo Admin notices for errors.
 * @todo Save empty array / remove all taxonomy assignments.
 */
class _AM_MBF_TaxSelect extends AM_MBF {
  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected static $type = 'tax_select';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected $sanitizer = 'sanitize_title';

  /**
   * Assign the selected taxonomies to the post type.
   *
   * @param  integer $post_id ID of the post to modify.
   */
  public function save( $post_id ) {
    wp_set_object_terms( $post_id, $this->value_new, $this->id );
  }

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function output() {
    $terms = get_terms( $this->id, 'get=all' );
    if ( ! is_wp_error( $terms ) ) {
      $taxonomy = get_taxonomy( $this->id );
      if ( count( $terms ) > 0 ) {

        $multiple = ( $this->get_setting( 'multiple' ) ) ? ' multiple="multiple"' : '';
        $ret = '<select name="' . $this->name . '" id="' . $this->id . '"'  . $multiple . $this->get_data_atts() . '>';
        if ( ! $this->get_setting( 'multiple' ) ) {
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

/**
 * Checkboxes of taxonomies.
 *
 * @todo Does it make sense to have this?! Duplicate to default WP meta box?!
 * @todo Admin notices for errors.
 * @todo Save empty array / remove all taxonomy assignments.
 */
class _AM_MBF_TaxCheckboxes extends AM_MBF {
  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected static $type = 'tax_checkboxes';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected $sanitizer = 'sanitize_title';

  /**
   * Assign the selected taxonomies to the post type.
   *
   * @param  integer $post_id ID of the post to modify.
   */
  public function save( $post_id ) {
    wp_set_object_terms( $post_id, $this->value_new, $this->id );
  }

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
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

?>