<?php

remove_action('genesis_before_post_content', 'genesis_post_info');
remove_action('genesis_after_post_content', 'genesis_post_meta');
remove_action('genesis_post_title', 'genesis_do_post_title');
remove_action('genesis_post_content', 'genesis_do_post_content');
remove_action('genesis_loop', 'genesis_do_loop' );

add_action( 'genesis_before_loop', 'ec_do_query', 45 );

/** Changes the Query before the Loop */
function ec_do_query() {
$terms = get_terms("client");
$r_count = 0;
$not_billed = 0;
$not_billed_total = 0;

 $count = count( $terms );

foreach ( $terms as $term ) {
	   $args1 = array(
		   'post_type' => 'hours',
		   'posts_per_page'	=> -1,
		   'tax_query' => array(
			   array(
				   'taxonomy' => 'client',
				   'field' => 'slug',
				   'terms' => array( $term->slug ),
				   'operator' => 'IN'
			   )
		   )
	   );

	   // call the query
	   $not_billed_query = new WP_Query( $args1 );

	   if( $not_billed_query->have_posts() ) {
		   while ( $not_billed_query->have_posts() ) : $not_billed_query->the_post();
			   $not_billed += get_post_meta( $not_billed_query->post->ID, 'tt_hours_worked', TRUE );
		   endwhile;
	   }



	   if( isset( $not_billed ) && $not_billed > 0) {
		   if ( isset( $r_count ) && $r_count > 0) {
			   $row_color = "#DDD";
			   $r_count = 0;
		   } else {
			   $row_color = "#FFF";
			   $r_count++;
		   }

		   echo '<div class="tt-row" style="background-color: '.$row_color.'">';
			   echo '<span class="hours-client">';
			   echo $term->name;
			   echo '</span>';
			   echo '<span class="hours-hours-worked">'. number_format( $not_billed, 2) .'</span>';
		   echo '</div>';
	   $not_billed_total += $not_billed;
	   }

	   $not_billed = 0;
	}
	echo '<div class="tt-row">Total Hours '.$not_billed_total.'</div>';
}

add_action ( 'genesis_before_loop', 'tt_do_title' );
function tt_do_title() {
	echo '<h1>Total Hours</h1>';
	echo '<div class="hours-head"><span class="hours-client">Client</span><span class="hours-hours-worked">Total Billed</span></div>';
}

genesis();