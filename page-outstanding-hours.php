<?php

remove_action('genesis_before_post_content', 'genesis_post_info');
remove_action('genesis_after_post_content', 'genesis_post_meta');
remove_action('genesis_post_title', 'genesis_do_post_title');
remove_action('genesis_post_content', 'genesis_do_post_content');


add_action( 'genesis_before_loop', 'ec_do_query', 99 );
/** Changes the Query before the Loop */


function ec_do_query() {
	global $paged;

	$term = isset($_GET['client']) ? get_term( $_GET['client'], 'client' ) : NULL;

	$args = array(
		'post_type' => 'hours',
		'posts_per_page' => -1,
		'orderby' => 'author',
		'order' => 'ASC',
		'tax_query' => array(
			array(
				'taxonomy' => 'hstatus',
				'field' => 'slug',
				'terms' => 'invoiced',
				'operator' => 'NOT IN'
			)
		)
	);

	if ( $term ) {
		$args['client'] = $term->name;
	}

/*
	if($_GET['client']) {
		$args['meta_query'] = array(
			array(
				'key' => 'tt_work_date',
				'value' => array( $_GET['fromdate'], $_GET['todate'] ),
				'compare' => 'BETWEEN'
			)
	);
	}
*/
	$from_date = isset( $_GET['fromdate'] ) ? $_GET['fromdate'] : '';
	$to_date = isset( $_GET['todate'] ) ? $_GET['todate'] : '';

	if ( '' != $from_date && '' != $to_date ) {
		$args['meta_query'][] = array(
			array(
				'key' => 'tt_work_date',
				'value' => array( $from_date, $to_date ),
				'compare' => 'BETWEEN'
			)
		);
	}

	query_posts( wp_parse_args( $args ) );
}

add_action ( 'genesis_before_loop', 'tt_do_title' );
function tt_do_title() {

	echo '<script type="text/javascript" src="https://www.google.com/jsapi"></script>';


	echo '<h1>All Hours</h1>';

	echo '<form method="GET" action="">';
	//echo '<input type="hidden" name="doact" value="make-invoice">';
	echo '<div class="invoice-form">';

	$client_id = isset( $_GET['client'] ) ? (int) $_GET['client'] : -1;

	$args = array(
    'orderby'            => 'title',
    'order'              => 'ASC',
	'show_option_none'   => 'Please Select',
    'hide_empty'         => 1,
    'echo'               => 1,
    'selected'           => $client_id,
    'name'               => 'client',
    'class'              => 'postform',
    'taxonomy'           => 'client',
    'hide_if_empty'      => false );

	wp_dropdown_categories( $args );

	$fdate = (empty($_GET['fromdate'])) ? date('m/d/Y', mktime(0, 0, 0, date("m")-2  , date("d"), date("Y") ) ) : $_GET['fromdate'];
	$tdate = (empty($_GET['todate'])) ? date('m/d/Y', mktime(0, 0, 0, date("m")  , date("d")+1, date("Y") ) ) : $_GET['todate'];

	echo 'From: <input type="text" name="fromdate" value='.$fdate.'>';
	echo 'To: <input type="text" name="todate" value="'.$tdate.'">';

	echo '<input type="submit" value="Search">';
	echo '</div>';
	echo '</form>';

	echo '<div class="hours-head">';
	echo '<span class="hours-user">User</span><span class="hours-work-date">Work Date</span><span class="hours-client">Client</span>';

	echo '<span class="user-total-hours">User Hours</span>';

	echo '<span class="hours-hours-worked">Hours</span>';

	echo '</div>';
}


add_action('genesis_post_content', 'short_post');

$prev_post_author_id = -1;
$user_total_hours = 0;
$users_hours = array();
$clients_hours = array();
$total_hours = 0;
function short_post() {
	global $post, $total_hours, $prev_post_author_id, $user_total_hours;
	global $users_hours, $clients_hours;

	$post_author_id = get_the_author_meta('ID');
	$hours_worked = get_post_meta( $post->ID, 'tt_hours_worked', TRUE );
	$user_total_hours += $hours_worked;


	//If next post empty or next post has a different author, calculate author's total

	global $wp_query;
	$calculate_user_total_hours = FALSE;
	$current_post = $wp_query->current_post;
	if ( $current_post + 1 >= $wp_query->post_count ) {
		$calculate_user_total_hours = TRUE;
	}
	elseif ( $current_post >= 0 ) {
		$next_post = $wp_query->posts[$current_post + 1];
		if ( $next_post->post_author != $post_author_id ) {
			$calculate_user_total_hours = TRUE;
		}
	}

	$user_name = '';
	$user = get_userdata( $post_author_id );

	if ( $user != FALSE ) {
		$user_name = ( '' == $user->user_firstname && '' == $user->user_lastname ) ? $user->user_login : $user->user_firstname . ' ' . $user->user_lastname;

	}

	if ( $calculate_user_total_hours == TRUE ) {
		if ( $user !== FALSE ) {
			$users_hours[$post_author_id] = array(
				'name' => $user->user_login,
				'hours' => $user_total_hours
			);
		}
		$class_separator = "tt-separator";
	}
	else {
		$class_separator = '';
	}

	// figure out if the item has already been invoiced
	if ( has_term( 'invoiced', 'hstatus')) {
		$row_color = 'invoiced';
	} else {
		$row_color = '';
	}
	// used to grab the client's name
	$term_list = wp_get_post_terms($post->ID, 'client');

	// Get the custom fields based on the $presenter term ID
	$client_custom_fields = get_option( "taxonomy_term_".$term_list[0]->term_id );
	if ( $client_custom_fields['client_is_prepay'] != 'yes') {
		$total_hours = $total_hours + $hours_worked;
		echo '<div class="tt-row ' . $row_color . ' ' . $class_separator . '">';
			//echo $post->ID;
			echo '<span class="hours-user">';
			echo '<a href="'. get_author_posts_url( $post_author_id ) .'">';
			echo get_avatar( $post_author_id , $size = '24' );
			echo '</a>';
			echo $user_name;
			echo '<div class="clear"></div>';
			echo '</span>';
			echo '<span class="hours-work-date">'. get_post_meta( $post->ID, 'tt_work_date', TRUE ) .'</span>';
			echo '<span class="hours-client">';
			$client_id = $term_list[0]->term_id;
			$client_name = $term_list[0]->name;
			echo $client_name;
			//print_r($term_list);
			echo '</span>';

			if ( $calculate_user_total_hours == TRUE ) {
				echo '<span class="user-total-hours">'. $user_total_hours .'</span>';
			}
			else {
				echo '<span class="user-total-hours">&nbsp;</span>';
			}

			echo '<span class="hours-hours-worked">'. $hours_worked .'</span>';
		echo '</div>';

		if ( $calculate_user_total_hours == TRUE ) {
			$user_total_hours = 0; //reset
	}

		if ( !isset( $clients_hours[$client_id] ) ) {
			$clients_hours[$client_id] = array( 'name' => $client_name, 'hours' => 0 );
}
		$clients_hours[$client_id]['hours'] = $clients_hours[$client_id]['hours'] + $hours_worked;
	}

	$prev_post_author_id = $post_author_id;
}

//==============================================================================

add_action ( 'genesis_after_loop', 'tt_do_foot' );
function tt_do_foot() {
	global $total_hours;
	echo '<p>Total Hours -> '.$total_hours.'</p>';
	//echo '<input type="submit" value="Mark items invoiced">';
	echo '</form>';

	echo '<br />';
	global $users_hours, $clients_hours;

	if ( count( $users_hours ) > 0 ) {
		echo '<div id="chart_users" style="width: 400px; height: 400px; float: left;"></div>';
		echo '<div id="chart_clients" style="width: 400px; height: 400px; float: right;"></div>';
		echo '<div class="clear">&nbsp;</div>';

		echo '<script type="text/javascript">
		  google.load("visualization", "1", {packages:["corechart"]});
		  google.setOnLoadCallback(drawChart);
		  function drawChart() {
			var data_users = google.visualization.arrayToDataTable([';
			$arr = array( '["User", "Hours"]' );
			foreach ( $users_hours as $user_id => $user_data ) {
			  $arr[] = '["' . $user_data['name'] . '", ' . $user_data['hours'] . ']';
			}
			echo join( ',', $arr );
			echo ']);';

			echo 'var data_clients = google.visualization.arrayToDataTable([';

			$arr = array('["Client", "Hours"]');
			foreach ( $clients_hours as $client_id => $client_data ) {
			  $arr[] = '["' . $client_data['name'] . '", ' . $client_data['hours'] . ']';
			}
			echo join( ',', $arr );
			echo ']);';

			echo '
			var options_users = {
			  title: "User Hours"
			};
			var options_clients = {
			  title: "Client Hours"
			};

			var chart = new google.visualization.PieChart(document.getElementById("chart_users"));
			chart.draw(data_users, options_users);

			var chart_clients = new google.visualization.PieChart(document.getElementById("chart_clients"));
			chart_clients.draw(data_clients, options_clients);

		  }
		</script>';
	}
}


genesis();