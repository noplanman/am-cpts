<?php

/**
 * A repeatable field of other fields.
 *
 * @todo AJAX for repeatable fields!!! This will solve many issues with different field types like slider and editor.
 * @todo Add "undo" feature to undo removal of fields.
 *
 * @since 1.0.0
 */
class AM_MBF_Repeatable extends AM_MBF {
  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected static $type = 'repeatable';

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  protected $is_repeatable = false;

  /**
   * All fields that are to be repeated for this field. This array contains other fields.
   *
   * @since 1.0.0
   *
   * @var array
   */
  protected $repeatable_fields = array();

  /**
   * The old value as it is in the database.
   *
   * @todo Instead of this variable, have an array with the "real" repeatable field objects.
   *
   * @since 1.0.0
   *
   * @var array
   */
  private $_value = array();

  /**
   * The new value as it will be saved to the database.
   *
   * @todo Instead of this variable, have an array with the "real" repeatable field objects.
   *
   * @since 1.0.0
   *
   * @var array
   */
  private $_value_new = array();

  /**
   * Constructor to optionally add fields.
   *
   * @since 1.0.1
   *
   * @param null|AM_MBF|array $fields Object or array of AM_MBF fields to add to this repeatable field.
   */
  public function init( $fields = null ) {
    $this->add_fields( $fields );

    //Register AJAX callback.
    add_action( 'wp_ajax_output_repeatable_fields_' . $this->id, array( $this, '_output_repeatable_fields' ) );
  }

  /**
   * Set saving flag for all repeatable fields. Check AM_MBF for description.
   *
   * @since 1.1.0
   */
  public function is_saving( $is_saving = null ) {
    if ( is_bool( $is_saving ) ) {
      $this->is_saving = $is_saving;

      // Set all current fields.
      foreach ( (array) $this->value as $rep_fields ) {
        foreach ( $rep_fields as $rep_field ) {
          $rep_field->is_saving( $is_saving );
        }
      }

      // Set all new fields.
      foreach ( (array) $this->value_new as $rep_fields ) {
        foreach ( $rep_fields as $rep_field ) {
          $rep_field->is_saving( $is_saving );
        }
      }
    }
    return $this->is_saving;
  }


  /**
   * Add fields to this repeatable field.
   *
   * @since 1.0.0
   *
   * @param AM_MBF|array $fields Object or array of AM_MBF to add to this repeatable field.
   */
  public function add_fields( $fields ) {
    if ( ! isset( $fields ) ) {
      return;
    }

    // Make sure we have an array to work with.
    if ( ! is_array( $fields ) ) {
      $fields = array( $fields );
    }

    foreach ( $fields as $field ) {
      if ( $field instanceof AM_MBF && $field->is_repeatable() ) {

        // Set the new repeatable names and ids, set up as arrays for the repeatable field.
        $field->add_data( 'id', $field->get_id() );
        $field->add_data( 'parent', $this->id );

        // Set this field's parent.
        $field->set_parent( $this );

        $this->repeatable_fields[ $field->get_id() ] = $field;
      }
    }
  }

  /**
   * Add a field to this repeatable field.
   *
   * This function can be regarded as a facade function.
   *
   * @since 1.0.1
   *
   * @param AM_MBF $field Object of AM_MBF to add to this repeatable field.
   */
  public function add_field( $field ) {
    $this->add_fields( $field );
  }

  /**
   * Saves the field data as meta data for the passed post id.
   *
   * @since 1.0.0
   *
   * @param integer $post_id ID of the post being saved.
   */
  public function save( $post_id ) {
//fu($this->_value_new);
    if ( ! isset( $this->_value_new ) || '' == $this->_value_new || array() == $this->_value_new ) {
      // Remove the post meta data.
      delete_post_meta( $post_id, $this->id, $this->_value );
    } elseif ( $this->_value_new != $this->_value ) {
      // Add / update the post meta data.
      update_post_meta( $post_id, $this->id, $this->_value_new );
    }
  }

  /**
   * Sanitize the new value of this field and all repeatable fields.
   *
   * @since 1.0.0
   */
  public function sanitize() {
    // Check which values have to be sanitized, the old or new ones.
    $values_to_sanitize = ( $this->is_saving ) ? $this->value_new : $this->value;
    if ( is_array( $values_to_sanitize ) ) {
      // Loop all values.
      foreach ( $values_to_sanitize as $rep_fields ) {
        // Loop all fields.
        if ( is_array( $rep_fields ) ) {
          foreach ( $rep_fields as $rep_field ) {
            if ( $rep_field instanceof AM_MBF ) {
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
   * @param array $value The old values to assign to this repeatable field.
   */
  public function set_value( $value ) {
    // Keep 'original' old value.
    $this->_value = $value;

    if ( is_array( $this->repeatable_fields ) && ! empty( $this->repeatable_fields ) ) {

      $new_values_old = array();

      $values_old = ( is_array( $value ) ) ? array_values( $value ) : array();
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

            $rep_field->set_value( $values[ $rep_field_id ] );
//            $rep_field->set_id( $rep_field_id . '-' . $i );
//            $rep_field->set_name( $this->id . '[' . $i . '][' . $rep_field_id .']' );

            $new_values[ $rep_field_id ] = $rep_field;
          }
          // Only add to values if an old value has been set.
          if ( $is_set ) {
            $new_values_old[] = $new_values;
          }
        }
      }
      // Reverse the entries of the array to have them sorted correctly.
      $value = array_reverse( $new_values_old );
    }
    $this->value = $value;
  }

  /**
   * Convert field values into field objects and also prepare an empty field template to be used to dynamically add new fields.
   *
   * @since 1.0.0
   *
   * @param array $value The old values to assign to this repeatable field.
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
   * AJAX callback for repeatable field output.
   *
   * @param  array $rep_fields Array of repeatable fields to display.
   * @return string            Output.
   */
  public function _output_repeatable_fields() {

    if ( isset( $_POST['iterator'] ) ) {
      echo $this->output_repeatable_fields( intval( $_POST['iterator'] ) );
    }

    die();
  }


  public function output_repeatable_fields( $iterator, $rep_fields = null ) {

    // Required to keep repeatable field templates clean.
    $revert = false;

    if ( ! isset( $rep_fields ) ) {
      $revert = true;
      $rep_fields = $this->repeatable_fields;
    }

    $field_outputs = sprintf( '
      <tr>
        <td><span class="sort" title="%1$s"></span></td>
        <td>',
      esc_attr__( 'Click & Drag to rearrange field', 'am-cpts' )
    );

    // Output all repeatable fields.
    foreach ( $rep_fields as $rep_field ) {
      // Remember in case we need to revert.
      $rep_field_id   = $rep_field->get_id();
      $rep_field_name = $rep_field->get_name();

      $rep_field->set_id( $rep_field_id . '-' . $iterator );
      $rep_field->set_name( $this->id . '[' . $iterator . '][' . $rep_field_id .']' );

      if ( 'plaintext' == $rep_field->get_type() ) {
        $field_outputs .= $rep_field->output();
      } else {
        $field_outputs .= sprintf( '<span class="meta-box-field-label">%1$s</span>%2$s<span class="description">%3$s</span>',
          $rep_field->get_label(),
          $rep_field->output(),
          $rep_field->get_desc()
        );
      }
      // Revert if necessary.
      if ( $revert ) {
        $rep_field->set_id( $rep_field_id );
        $rep_field->set_name( $rep_field_name );
      }
    }
    $field_outputs .= sprintf( '
        </td>
        <td><a class="meta-box-repeatable-remove" href="#" title="%1$s"></a></td>
      </tr>',
      esc_attr__( 'Remove field', 'am-cpts' )
    );

    return $field_outputs;
  }

  /**
   * Check AM_MBF for description.
   *
   * @since 1.0.0
   */
  public function output() {

    if ( $this->get_repeatable_fields() ) {

      $field_outputs = '';
      $values_old = $this->value;

      // Make sure we have an array to work with.
      if ( ! is_array( $values_old ) ) {
        $values_old = array( $values_old );
      }
      // Clean away empty entries.
      $values_old = array_filter( array_values( $values_old ) );

      $iterator = 0;
      foreach ( $values_old as $value_old ) {
        // Output all saved values.
        $field_outputs .= $this->output_repeatable_fields( $iterator++, $value_old );
      }

      return sprintf( '
        <table id="%1$s-repeatable" class="meta-box-repeatable" cellspacing="0" data-id="%1$s" data-iid="%2$d">
          <thead>
            <tr>
              <th>&nbsp;</th>
              <th>%5$s</th>
              <th><a class="meta-box-repeatable-add" href="#" data-position="top" title="%6$s"></a></th>
            </tr>
            <tr>
              <td colspan="3" class="repeatable-empty-message">%3$s</td>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>&nbsp;</th>
              <th>%5$s</th>
              <th><a class="meta-box-repeatable-add" href="#" data-position="bottom" title="%6$s"></a></th>
            </tr>
          </tfoot>
          <tbody>
            %4$s
          </tbody>
        </table>',
        esc_attr( $this->id ),
        $iterator,
        esc_html__( 'No fields added yet! Click the + on the right.', 'am-cpts' ),
        $field_outputs,
        esc_attr__( 'Repeatable Fields', 'am-cpts' ),
        esc_attr__( 'Add new field', 'am-cpts' )
      );
    } else {
      return __( 'No repeatable fields assigned!', 'am-cpts' );
    }
  }
}

?>