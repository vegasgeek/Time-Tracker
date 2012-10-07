<?php

// now set up some custom meta boxes.
add_filter( 'cmb_meta_boxes', 'be_sample_metaboxes' );
function be_sample_metaboxes( $meta_boxes ) {
	$prefix = 'tt_'; // start with an underscore to hide fields from custom fields list
	$meta_boxes[] = array(
		'id' => 'hours_metabox',
		'title' => 'Ticket Details',
		'pages' => array('hours'), // post type
		'context' => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
		'fields' => array(
			array(
				'name' => 'Date Work Performed',
				'desc' => '',
				'id' => $prefix . 'work_date',
				'type' => 'text_date'
			),
			array(
				'name' => '# of hours worked',
				'desc' => 'Use decimal points for partial hours. Ex: 1.5',
				'id' => $prefix . 'hours_worked',
				'type' => 'text_small'
			),

		)
	);

	return $meta_boxes;
}

// Initialize the metabox class
add_action( 'init', 'be_initializecmb_meta_boxes', 9999 );
function be_initializecmb_meta_boxes() {
	if ( !class_exists( 'cmb_Meta_Box' ) ) {
		require_once( 'lib/metabox/init.php' );
	}
}

?>
