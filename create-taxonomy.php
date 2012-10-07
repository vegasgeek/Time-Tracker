<?php

add_action( 'init', 'create_client_taxonomies', 0 );

//create taxonomy
function create_client_taxonomies() 
{
	// Client Taxonomy
	
	$labels = array(
		'name' => _x( 'Client', 'taxonomy general name' ),
		'singular_name' => _x( 'Client', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Clients' ),
		'popular_items' => __( 'Popular Clients' ),
		'all_items' => __( 'All Clients' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Clients' ), 
		'update_item' => __( 'Update Clients' ),
		'add_new_item' => __( 'Add New Client' ),
		'new_item_name' => __( 'New Client' ),
		'separate_items_with_commas' => __( 'Separate clients with commas' ),
		'add_or_remove_items' => __( 'Add or remove clients' ),
		'choose_from_most_used' => __( 'Choose from the most used clients' ),
		'menu_name' => __( 'Clients' ),
	); 

	register_taxonomy('client','hours',array(
		'hierarchical' => false,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'client' ),
	));

	
	// Client hstatus
	
	$labels = array(
		'name' => _x( 'Status', 'taxonomy general name' ),
		'singular_name' => _x( 'Status', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Statuses' ),
		'popular_items' => __( 'Popular Statuses' ),
		'all_items' => __( 'All Statuses' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Statuses' ), 
		'update_item' => __( 'Update Statuses' ),
		'add_new_item' => __( 'Add New Status' ),
		'new_item_name' => __( 'New Status' ),
		'separate_items_with_commas' => __( 'Separate statuses with commas' ),
		'add_or_remove_items' => __( 'Add or remove statuses' ),
		'choose_from_most_used' => __( 'Choose from the most used statuses' ),
		'menu_name' => __( 'Status' ),
	); 

	register_taxonomy('hstatus','hours',array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'status' ),
	));

}
