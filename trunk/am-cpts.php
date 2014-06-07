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


/*$mb = new AM_MB( 'mb1', 'Metabox1', AM_MBF::create_batch(
    array( 'repeatable', 'rep1', 'Repeatable 1', 'Repeatable 1 Description', AM_MBF::create_batch(
        array( 'textarea', 'txt1', 'Rep Text 1', 'Rep Text 1 Description' ),
        array( 'plaintext', 'plaintext1', 'Just some plain text...', '...and the fitting description for it.' ),
        array( 'text','text1','Simple text input', 'Text description' ),
        array( 'tel','tel1','Simple tel input', 'Tel description' )
      )
    ),
    array( 'text', 'txt1', 'Text 1', 'Text 1 Description' )
  )
);
$mb->assign_post_type( 'post' );
$mb->register();
*/

$mb = new AM_MB( 'mb1', 'Metabox1' );
$rep = AM_MBF::create( 'repeatable', 'rep1', 'Repeatable 1', 'Repeatable 1 Description' );
$ta = AM_MBF::create( 'textarea', 'txt1', 'Rep Text 1', 'Rep Text 1 Description' );
$ta->set_size('30,10');
//$rep->add_field( $ta );
$rep->add_field( AM_MBF::create( 'slider','sl1','slider 1', 'my description', array('min'=>5,'max'=>100,'step'=>5,'handles'=>3 ) ) );
//$rep->add_field( AM_MBF::create( 'plaintext', 'plaintext1', 'Just some plain text...', '...and the fitting description for it.' ) );
$rep->add_field( AM_MBF::create( 'text','text1','Simple text input', 'Text description' ) );

$mb->add_field( $rep );

$mb->add_field( AM_MBF::create( 'text', 'txt1', 'Text 1', 'Text 1 Description' ) );

$mb->assign_post_type( 'post' );
$mb->register();



return;


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

//$cpt_note->register();

// You expected more?!



//);

//if(isset($rep[0])) $rep[0]->add_field( clone($rep[1]) );

//$rep[0]->add_field( AM_MBF::create('file','pc4','post checkboxes', 'some description...'));


$rep = AM_MBF::create( 'repeatable', 'rep1', 'Rep nr.1', 'desc...' );
//$rep = AM_MBF::create('repeatable', 'rep1', 'Rep nr.1', 'desc...' );
$rep->add_field( AM_MBF::create('checkbox_group','pc9','post checkboxes', 'some description...', array('key1'=>'value number 1','key2'=>'value number 2','key3'=>'value number 3','key4'=>'value number 4','key5'=>'value number 5')) );
$rep->add_field( AM_MBF::create('radio_group','pc6','post checkboxes', 'some description...', array('key1'=>'value number 1','key2'=>'value number 2','key3'=>'value number 3','key4'=>'value number 4','key5'=>'value number 5')) );
$rep->add_field( AM_MBF::create('color','pc1','post checkboxes', 'some description...'));
// $rep->add_field( AM_MBF::create('image','pc3','post checkboxes', 'some description...'));
// $rep->add_field( AM_MBF::create('textarea','pc30','post checkboxes', 'some description...'));
$rep->add_field( AM_MBF::create( 'plaintext','pt2','Additional options','Choose the additional options with the checkbox(es) below.' ) );

$mb2 = new AM_MB( 'metabox2', 'Second Metabox' );
$mb2->add_field( AM_MBF::create( 'chosen','sl1','slider 1', 'my description', array('min'=>5,'max'=>100,'step'=>5,'handles'=>3 ) ) );
$mb2->add_field( AM_MBF::create( 'plaintext','pt1','Additional options','Choose the additional options with the checkbox(es) below.' ) );
$mb2->add_field( AM_MBF::create( 'checkbox','cb1','<em>a checkbox field!!</em>','some description...' ) );
$mb2->add_field( AM_MBF::create('text','text1','a simple text input') );
//$rep->add_field($f);
//$mb->add_field($f2);
//$f->add_settings(array('min'=>5,'max'=>100,'step'=>0.5,'handles'=>3));
//$f->set_post_type( 'pages' );



//$f->add_options( array('one'=>'first','two'=>'second','three'=>'third','four'=>'fourth','five'=>'fifth') );
//$f->is_multiple(true);


$mb_basic->add_field( $rep );
$mb_basic->add_field( AM_MBF::create( 'slider','sl1','slider 1', 'my description', array('min'=>5,'max'=>100,'step'=>5,'handles'=>3 ) ) );


//fu($mb_basic);

$cpt_note->assign_meta_box( array( $mb_basic, $mb2 ) );

$cpt_note->register();


$new_mb = new AM_MB( 'new_meta_box_1', 'This is just a single AM_MB',
  AM_MBF::create_batch(
    // array( 'text','text1','a simple text input', 'text description' ),
    // array( 'tel','tel1','a simple tel input', 'tel description' ),
    // array( 'url','url1','a simple url input', 'url description' ),
    // array( 'email','email1','a simple email input', 'email description' ),
    // array( 'number','number1','a simple number input', 'number description' ),
    array('image','pc3','post checkboxes', 'some description...'),
    array('file','pc36','post checkboxes', 'some description...'),
    array('repeatable','rep','asdf','desc',
      AM_MBF::create('color','pc1','post checkboxes', 'some description...')
    )
  )
);
$new_mb->assign_post_type('post');
$new_mb->register();



?>