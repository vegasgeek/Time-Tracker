<?php

remove_action('genesis_before_post_content', 'genesis_post_info');
remove_action('genesis_after_post_content', 'genesis_post_meta');
remove_action('genesis_post_title', 'genesis_do_post_title');
remove_action('genesis_post_content', 'genesis_do_post_content');

add_action( 'genesis_before_loop', 'ec_do_query', 99 );
/** Changes the Query before the Loop */
function ec_do_query() {
	global $_GET;
//	$tl = wp_get_post_terms($_GET['client'], 'client');
	if( isset( $_GET['client'] ) ) {
		$term = get_term( $_GET['client'], 'client' );
	}

	if( isset( $_GET['client'] ) && $_GET['client']) {
		$args = array(
			'post_type' => 'hours',
			'client' => $term->name,
			'orderby' => 'author',
			'order' => 'ASC',
			'tax_query' => array(
				array(
					'taxonomy' => 'hstatus',
					'field' => 'slug',
					'terms' => 'invoiced',
					'operator' => 'NOT IN'
				)
			),
			'meta_query' => array(
				array(
					'key' => 'tt_work_date',
					'value' => array( $_GET['fromdate'], $_GET['todate'] ),
					'compare' => 'BETWEEN'
				)
			)
		);
	} else {
		$args = array(
			'post_type' => 'hours',
			'tax_query' => array(
				array(
					'taxonomy' => 'hstatus',
					'field' => 'slug',
					'terms' => 'doesnt exist',
					'operator' => 'IN'
				)
			)
		);
	}

	query_posts( wp_parse_args( $args ) );
}



add_action ( 'genesis_before_loop', 'tt_comp_invoice', 5 );
function tt_comp_invoice() {
	if ( isset( $_GET['doact'] ) && $_GET['doact'] == 'complete-invoice') {

		foreach($_GET['ticket'] as $tick ) {
			wp_set_object_terms($tick, 'invoiced', 'hstatus', true);
		}
	}
}


add_action ( 'genesis_before_loop', 'tt_do_title' );
function tt_do_title() {
	echo '<h1>Create Invoice</h1>';
	// make invoice form
	echo '<form method="GET" action="">';
	echo '<input type="hidden" name="doact" value="make-invoice">';
	echo '<div class="invoice-form">';

	if( isset( $_GET['client'] ) ) {
		$selected_client = $_GET['client'];
	} else {
		$selected_client = '';
	}

	$args = array(
    'orderby'            => 'title',
    'order'              => 'ASC',
	'show_option_none'   => 'Please Select',
    'hide_empty'         => 1,
    'echo'               => 1,
    'selected'           => $selected_client,
    'name'               => 'client',
    'class'              => 'postform',
    'taxonomy'           => 'client',
    'hide_if_empty'      => false );

	wp_dropdown_categories( $args );

	$fdate = (empty($_GET['fromdate'])) ? date('Ymd', mktime(0, 0, 0, date("m")-2  , date("d"), date("Y") ) ) : $_GET['fromdate'];
	$tdate = (empty($_GET['todate'])) ? date('Ymd', mktime(0, 0, 0, date("m")  , date("d")+1, date("Y") ) ) : $_GET['todate'];

	echo 'From: <input type="text" name="fromdate" value='.$fdate.'>';
	echo 'To: <input type="text" name="todate" value="'.$tdate.'">';
	echo '<input type="submit" value="Search">';
	echo '</div>';
	echo '</form>';


	echo '<form action="" method="GET">';
	echo '<input type="hidden" name="doact" value="complete-invoice">';
	echo '<div class="hours-head"><span class="hours-gravatar">&nbsp;</span><span class="hours-work-date">Work Date</span><span class="hours-client">Client</span><span class="hours-work-done">Work Done</span><span class="hours-hours-worked">Hours</span></div>';

}

add_action ( 'genesis_after_loop', 'tt_do_foot' );
function tt_do_foot() {
	global $total_hours;
	echo '<p>Total Hours -> '.$total_hours.'</p>';
	echo '<input type="submit" value="Mark items invoiced">';
	echo '</form>';
}


add_action('genesis_post_content', 'short_post');
function short_post() {
	global $post, $total_hours;

	echo '<div class="tt-row">';
		echo '<span class="hours-check"><input type="checkbox" checked name="ticket[]" value="'.$post->ID.'"></span>';
		echo '<span class="hours-gravatar">';
		echo '<a href="'. get_author_posts_url( get_the_author_meta('ID') ) .'">';
		echo get_avatar( get_the_author_meta('ID') , $size = '24' );
		echo '</a>';
		echo '</span>';
		echo '<span class="hours-work-date">'. date( 'm/d/Y', strtotime( str_replace( '.', '', get_post_meta( $post->ID, 'tt_work_date', TRUE ) ) ) ) .'</span>';
		echo '<span class="hours-client">';
		$term_list = wp_get_post_terms($post->ID, 'client');
		echo $term_list[0]->name;
		//print_r($term_list);
		echo '</span>';
		echo '<span class="hours-work-done">'. get_the_content() .'</span>';
		echo '<span class="hours-hours-worked">'. get_post_meta( $post->ID, 'tt_hours_worked', TRUE ) .'</span>';
	echo '</div>';
	$total_hours = $total_hours + get_post_meta( $post->ID, 'tt_hours_worked', TRUE );
}


genesis();