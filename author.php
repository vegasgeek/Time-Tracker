<?php

remove_action('genesis_before_post_content', 'genesis_post_info');
remove_action('genesis_after_post_content', 'genesis_post_meta');
remove_action('genesis_post_title', 'genesis_do_post_title');
remove_action('genesis_post_content', 'genesis_do_post_content');
remove_action('genesis_after_post', 'genesis_do_author_box_archive');

add_action( 'genesis_before_loop', 'ec_do_query' );
/** Changes the Query before the Loop */
function ec_do_query() {
	global $query_string;
	query_posts( wp_parse_args( $query_string, array( 'post_type' => 'hours' ) ) );
}

add_action ( 'genesis_before_loop', 'tt_do_title' );
function tt_do_title() {
	echo '<h1>My Hours</h1>';
	echo '<div class="hours-head"><span class="hours-gravatar">&nbsp;</span><span class="hours-work-date">Work Date</span><span class="hours-client">Client</span><span class="hours-work-done">Work Done</span><span class="hours-hours-worked">Hours</span></div>';
}


add_action('genesis_post_content', 'short_post');
function short_post() {
	global $post;

	if ( has_term( 'invoiced', 'hstatus')) {
		$row_color = 'invoiced';
	} else {
		$row_color = '';
	}

	echo '<div class="tt-row '.$row_color.'">';
		echo '<span class="hours-work-date">'. date( 'm/d/Y', strtotime( str_replace( '.', '', get_post_meta( $post->ID, 'tt_work_date', TRUE ) ) ) ) .'</span>';
		echo '<span class="hours-client">';
		$term_list = wp_get_post_terms($post->ID, 'client');
		echo $term_list[0]->name;
		//print_r($term_list);
		echo '</span>';
		echo '<span class="hours-work-done">'. wpautop(get_the_content()) .'</span>';
		echo '<span class="hours-hours-worked">'. get_post_meta( $post->ID, 'tt_hours_worked', TRUE ) .'</span>';
	echo '</div>';
}


genesis();