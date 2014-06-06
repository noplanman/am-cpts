=== AM-CPTS ===
Contributors: armyman.ch
Donate link: http://armyman.ch/
Tags: cpt, custom post type, taxonomy, meta box, custom field, basic
Requires at least: 3.8.3
Tested up to: 3.8.3
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Framework for developers to easily create custom post types, taxonomies and meta boxes with custom fields.

== Description ==

With this plugin, developers can create custom post types, taxonomies and meta boxes with custom fields very easily, with a few lines of code. All meta box saving is handled and is easily accessible to display within the template.

The code is well documented throughout for developers to have a look at what's going on and see how it works.
You can imagine a hierarchy like this:
Custom Post Type (AM_CPT)
  -> Taxonomy (AM_Tax)
  -> Meta Box (AM_MB)
     -> Meta Box Fields (AM_MBF)

So once the AM_CPT object has been created, multiple (also already existing) taxonomies can be assigned to it, as can meta boxes with their respective custom fields.

Check am-cpts.php for examples.

== Installation ==

1. Upload the plugin to your WordPress installation and activate it.
2. Code all required custom post types, taxonomies and meta boxes, using the plugin classes.

== Frequently Asked Questions ==

= How do I create a custom post type? =
`$my_cpt = new AM_CPT( 'slug', $args );`
`$args` represents the post type arguments, same as registering a normal post type with `register_post_type()`.

After the object has been created, don't forget to register it!
`$my_cpt->register();`

= How do I create a taxonomy? =
`$my_tax = new AM_Tax( 'slug', $args );`
`$args` represents the taxonomy arguments, same as registering a taxonomy with `register_taxonomy()`.

The taxonomy object can either be registered seperately, or assigned to an AM_CPT object.
`$my_cpt->assign_taxonomy( $my_tax );`

When registering a taxonomy seperately, remember to assign a post type to it before registering it.
`$my_tax->assign_post_type( 'post' );`
`$my_tax->register();`

= How do I create a meta box? =
`$my_mb = new AM_MB( 'id', 'Title' );`

Now let's add the custom fields! Check for available fields in the fields folder.
`$mb_mb->add_field( 'type', 'id', 'Label', 'Description' );`

Depending on which fields are used, you can add further parameters to set options or settings.

The meta box object can either be registered seperately, or assigned to an AM_CPT object.
`$my_cpt->assign_meta_box( $my_mb );`

When registering a meta box seperately, remember to assign a post type to it before registering it.
`$my_mb->assign_post_type( 'post' );`
`$my_mb->register();`


== Changelog ==

= 1.0.1 =
* Initial version.

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.