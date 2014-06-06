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
   */
  protected static $type = 'repeatable';

  /**
   * Check AM_MBF for description.
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
  public function __construct( $fields = null ) {
    $this->add_fields( $fields );
  }

  /**
   * Add fields to this repeatable field.
   *
   * @since 1.0.0
   *
   * @param AM_MBF|array $fields Object or array of AM_MBF to add to this repeatable field.
   */
  public function add_fields( $fields ) {
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

        // This field is being repeated.
        $rep_field->is_being_repeated( true );

        $this->repeatable_fields[ $rep_field->get_id() ] = $rep_field;
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
    if ( is_null( $this->_value_new ) || '' == $this->_value_new || array() == $this->_value_new ) {
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
            $rep_field->set_id( $rep_field_id . '-' . $i );
            $rep_field->set_name( $this->id . '[' . $i . '][' . $rep_field_id .']' );

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
   * Check AM_MBF for description.
   */
  public function output() {

    $empty_fields_template = '';
    $field_outputs = '';

    if ( $this->get_repeatable_fields() ) {
      $values_old = $this->value;

      // Make sure we have an array to work with.
      if ( ! is_array( $values_old ) ) {
        $values_old = array( $values_old );
      }
      // Clean away empty entries.
      $values_old = array_filter( array_values( $values_old ) );

      for ( $i = -1; $i < count( $values_old ); $i++ ) {
        $is_empty_template = ( -1 == $i );

        $field_outputs .= sprintf( '
          <tr%1$s>
            <td><span class="ui-icon ui-icon-grip-dotted-horizontal sort" title="%2$s"></span></td>
            <td>',
          ( $is_empty_template ) ? ' class="empty-fields-template" style="display:none;"' : '',
          esc_attr__( 'Click & Drag to rearrange field', 'am-cpts' )
        );

        // Add all repeatable fields to empty template / Output all saved values.
        $rep_fields = ( $is_empty_template ) ? $this->repeatable_fields : $values_old[ $i ];

        // Output all repeatable fields.
        foreach ( $rep_fields as $rep_field ) {
          if ( 'plaintext' == $rep_field->get_type() ) {
            $field_outputs .= $rep_field->output();
          } else {
            $field_outputs .= sprintf( '<span class="meta-box-field-label">%1$s</span>%2$s<span class="description">%3$s</span>',
              $rep_field->get_label(),
              $rep_field->output(),
              $rep_field->get_desc()
            );
          }
        }
        $field_outputs .= sprintf( '
            </td>
            <td><a class="ui-icon ui-icon-minusthick meta-box-repeatable-remove" href="#" title="%1$s"></a></td>
          </tr>',
          esc_attr__( 'Remove field', 'am-cpts' )
        );

        // Save empty template seperately.
        if ( $is_empty_template ) {
          $empty_fields_template = $field_outputs;

          // Reset the field outputs to prevent having the empty template with them.
          $field_outputs = '';
        }
      }

      return sprintf( '
        <table id="%1$s-repeatable" class="meta-box-repeatable" cellspacing="0">
          <thead>
            <tr>
              <th><span class="ui-icon ui-icon-arrowthick-2-n-s sort-label" title="%4$s"></span></th>
              <th>%5$s</th>
              <th><a class="ui-icon ui-icon-plusthick meta-box-repeatable-add" href="#" data-position="top" title="%6$s"></a></th>
            </tr>
            %2$s
          </thead>
          <tfoot>
            <tr>
              <th><span class="ui-icon ui-icon-arrowthick-2-n-s sort-label" title="%4$s"></span></th>
              <th>%5$s</th>
              <th><a class="ui-icon ui-icon-plusthick meta-box-repeatable-add" href="#" data-position="bottom" title="%6$s"></a></th>
            </tr>
          </tfoot>
          <tbody>
            %3$s
          </tbody>
        </table>',
        esc_attr( $this->id ),
        $empty_fields_template,
        $field_outputs,
        esc_attr__( 'Rearrange fields', 'am-cpts' ),
        esc_attr__( 'Repeatable Fields', 'am-cpts' ),
        esc_attr__( 'Add new field', 'am-cpts' )
      );
    } else {
      return __( 'No repeatable fields assigned!', 'am-cpts' );
    }
  }
}

?>