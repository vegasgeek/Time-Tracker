<?php

/**** Custom Post Type: Hours ****/

function cl_hours_post_type() {
	$labels = array(
	'name' => _x('Hours', 'post type general name'),
	'singular_name' => _x('Hours', 'post type singular name'),
	'add_new' => _x('Add New Hours', 'services'),
	'add_new_item' => __('Add New Hours'),
	'edit_item' => __('Edit Hours'),
	'edit' => _x('Edit Hours', 'services'),
	'new_item' => __('New Hours'),
	'view_item' => __('View Hours'),
	'search_items' => __('Search Hours'),
	'not_found' =>  __('No Hours found'),
	'not_found_in_trash' => __('No Hours found in Trash'), 
	'view' =>  __('View Hours'),
	'parent_item_colon' => ''
	);
	$args = array(
	'labels' => $labels,
	'public' => true,
	'publicly_queryable' => true,
	'show_ui' => true,
	'query_var' => true,
	'rewrite' => array("slug" => "service"),
	'capability_type' => 'post',
	'hierarchical' => false,
	'menu_position' => null,
	'supports' => array( 'editor', 'author' )
	); 

	register_post_type( 'hours', $args);
}

add_action( 'init', 'cl_hours_post_type', 1 );

// import custom metaboxes
require_once( 'lib/metabox/custom-metaboxes.php' );
