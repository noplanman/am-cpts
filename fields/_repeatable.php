<?php

/**
 * A repeatable field of other fields.
 */
class AM_MBF_Repeatable extends AM_MBF {
  protected static $type = 'repeatable';
  protected $is_repeatable = false;

  //TODO: instead of these variables, have an array with the "real" repeatable field objects.
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

    // Make sure we have an array to work with.
    if ( ! is_array( $fields ) ) {
      $fields = array( $fields );
    }

    foreach ( $fields as $rep_field ) {
      if ( is_a( $rep_field, 'AM_MBF' ) && $rep_field->is_repeatable() ) {

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
    // Clean up _value_new.
    if ( '' == $this->_value_new || array() == $this->_value_new ) {
      $this->_value_new = null;
    }

    if ( isset( $this->_value_new ) && $this->_value_new != $this->_value_old ) {
      update_post_meta( $post_id, $this->id, $this->_value_new );
    } else {
      delete_post_meta( $post_id, $this->id, $this->_value_old );
    }
  }

  /**
   * Sanitize the new value of this field and all repeatable fields.
   */
  public function sanitize() {
    // Check which values have to be sanitized, the old or new ones.
    $values_to_sanitize = ( $this->is_saving ) ? $this->value_new : $this->value_old;

    if ( is_array( $values_to_sanitize ) ) {
      // Loop all values.
      foreach ( $values_to_sanitize as $rep_fields ) {
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
   * Convert field values into field objects and also prepare an empty field template to be used to dynamically add new fields.
   *
   * @since 1.0.0
   *
   * @param array $value_old The old values to assign to this repeatable field.
   */
  public function set_value_old( $value_old ) {
    // Keep 'original' old value.
    $this->_value_old = $value_old;

    if ( is_array( $this->repeatable_fields ) && ! empty( $this->repeatable_fields ) ) {

      $new_values_old = array();

      $values_old = ( is_array( $value_old ) ) ? array_values( $value_old ) : array();
      $i = count( $values_old );

      // Do a backwards loop.
      while ( $i-- > -1 ) {

        if ( -1 == $i ) {
          // Prepare id and name for template fields.
          foreach ( $this->repeatable_fields as $rep_field ) {
            $rep_field->set_id( $rep_field->get_id() . '-empty' );
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

  /**
   * Convert field values into field objects and also prepare an empty field template to be used to dynamically add new fields.
   *
   * @since 1.0.0
   *
   * @param array $value_old The old values to assign to this repeatable field.
   */
  public function set_value_new( $value_new ) {

    if ( is_array( $this->repeatable_fields ) && ! empty( $this->repeatable_fields ) && is_array( $value_new ) ) {

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

      // Make sure we have an array to work with.
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
          $rep_field->is_being_repeated( true );
          $field_outputs .= '<label class="meta-box-field-label" for="' . $rep_field->get_id() . '">' . $rep_field->get_label() . '</label>';
          $field_outputs .= $rep_field->output();
          $field_outputs .= ( 'plaintext' != $rep_field->get_type() ) ? '<br class="clear" />' : '';
          $field_outputs .= '<span class="description">' . $rep_field->get_desc() . '</span>';
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
              <th>' . __( 'Repeatable Fields', 'am-cpts' ) . '</th>
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
              <th>' . __( 'Repeatable Fields', 'am-cpts' ) . '</th>
              <th><a class="ui-icon ui-icon-plusthick meta-box-repeatable-add" href="#" data-position="bottom"></a></th>
            </tr>
          </tfoot>
        </table>
      ';
    } else {
      return __( 'No repeatable fields assigned!', 'am-cpts' );
    }
  }
}

?>