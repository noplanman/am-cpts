<?php

/**
 * @package AM_CPTS
 * @version 1.0.0
 */
/*
Plugin Name: AM CPTS
Plugin URI:
Description: Create custom post types, taxonomies and meta boxes really easily.
Author: Armando Lüscher
Version: 1.0.0
Author URI: http://armyman.ch/
*/


/**
 * Require main classes.
 */
require_once 'class-am-cpt-tax.php';
require_once 'class-am-mb.php';
require_once 'class-am-mbf.php';







/*

Examples!!

 */

/*

// Easily extend the AM_ classes to create classes that fit exaclty your needs!
class Tax_Note_Styles extends AM_Tax {

  public function __construct() {
    $labels = array(
      'name'                  => _x( 'Styles', 'Taxonomy plural name', 'am-cpts' ),
      'singular_name'         => _x( 'Style', 'Taxonomy singular name', 'am-cpts' ),
      'search_items'          => __( 'Search Styles', 'am-cpts' ),
      'popular_items'         => __( 'Popular Styles', 'am-cpts' ),
      'all_items'             => __( 'All Styles', 'am-cpts' ),
      'parent_item'           => __( 'Parent Style', 'am-cpts' ),
      'parent_item_colon'     => __( 'Parent Style', 'am-cpts' ),
      'edit_item'             => __( 'Edit Style', 'am-cpts' ),
      'update_item'           => __( 'Update Style', 'am-cpts' ),
      'add_new_item'          => __( 'Add New Style', 'am-cpts' ),
      'new_item_name'         => __( 'New Style Name', 'am-cpts' ),
      'add_or_remove_items'   => __( 'Add or remove Styles', 'am-cpts' ),
      'choose_from_most_used' => __( 'Choose from most used Styles', 'am-cpts' ),
      'menu_name'             => __( 'Styles', 'am-cpts' ),
      'not_found'             => __( 'No styles found', 'am-cpts' )
    );

    $args = array(
      'labels'            => $labels,
      'public'            => true,
      'show_in_nav_menus' => true,
      'show_admin_column' => false,
      'hierarchical'      => false,
      'show_tagcloud'     => true,
      'show_ui'           => true,
      'query_var'         => true,
      'rewrite'           => true,
      'query_var'         => true,
      'capabilities'      => array(),
    );

    // Create AM_Tax.
    parent::__construct( 'styles', $args );
  }
}

class CPT_Note extends AM_CPT {

  public function __construct() {
    $labels = array(
      'name'                => __( 'Notes', 'am-cpts' ),
      'singular_name'       => __( 'Note', 'am-cpts' ),
      'add_new'             => _x( 'Add New Note', 'am-cpts', 'am-cpts' ),
      'add_new_item'        => __( 'Add New Note', 'am-cpts' ),
      'edit_item'           => __( 'Edit Note', 'am-cpts' ),
      'new_item'            => __( 'New Note', 'am-cpts' ),
      'view_item'           => __( 'View Note', 'am-cpts' ),
      'search_items'        => __( 'Search Notes', 'am-cpts' ),
      'not_found'           => __( 'No Notes found', 'am-cpts' ),
      'not_found_in_trash'  => __( 'No Notes found in Trash', 'am-cpts' ),
      'parent_item_colon'   => __( 'Parent Note:', 'am-cpts' ),
      'menu_name'           => __( 'Notes', 'am-cpts' ),
    );

    $args = array(
      'labels'              => $labels,
      'hierarchical'        => false,
      'description'         => 'description',
      'taxonomies'          => array(),
      'public'              => true,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'show_in_admin_bar'   => true,
      'menu_position'       => null,
      'menu_icon'           => null,
      'show_in_nav_menus'   => true,
      'publicly_queryable'  => true,
      'exclude_from_search' => false,
      'has_archive'         => true,
      'query_var'           => true,
      'can_export'          => true,
      'rewrite'             => true,
      'capability_type'     => 'post',
      'supports'            => array(
        'title', 'editor', 'author', 'thumbnail',
        'excerpt','custom-fields', 'trackbacks', 'comments',
        'revisions', 'page-attributes', 'post-formats'
        )
    );

    // Create AM_CPT.
    parent::__construct( 'note', $args );
  }
}

// Create own AM_CPT.
$cpt_note = new CPT_Note();

// Create own AM_Tax.
$tax_styles = new Tax_Note_Styles();

// Also assign this taxonomy to the 'post' post type.
$tax_styles->assign_post_type( 'post' );

// Assign taxonomy.
$cpt_note->assign_taxonomy( $tax_styles );

$mb_basic = new AM_MB( 'metabox1', 'First Metabox' );
$mb_basic->add_field( 'plaintext', 'plaintext1', 'Just some plain text...', '...and the fitting description for it.' );
$mb_basic->add_field( 'text','text1','Simple text input', 'Text description' );
$mb_basic->add_field( 'tel','tel1','Simple tel input', 'Tel description' );
$mb_basic->add_field( 'url','url1','Simple URL input', 'URL description' );
$mb_basic->add_field( 'email','email1','Simple email input', 'Email description' );
$mb_basic->add_field( 'number','number1','Simple number input', 'Number description' );

// Assign meta box.
$cpt_note->assign_meta_box( $mb_basic );

// Register it all!!
$cpt_note->register();

// You expected more?!

*/

?>