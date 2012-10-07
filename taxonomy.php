<?php
remove_action('genesis_before_post_content', 'genesis_post_info');
remove_action('genesis_after_post_content', 'genesis_post_meta');
remove_action('genesis_post_title', 'genesis_do_post_title');
remove_action('genesis_post_content', 'genesis_do_post_content');
remove_action('genesis_loop', 'genesis_do_loop');

add_action('genesis_loop', 'ec_cust_loop');
function ec_cust_loop() {
	global $post;
	// get current term ID
	$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
	// echo '<pre>'; print_r($term); echo '</pre>';

	print_r($term);
	echo '<div class="taxcathead">'.$invcat->name.'</div>';
		echo '<div class="taxcatprods">';
		// now grab associated products
		$prodargs = array (
			'post_type' => 'hours',
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'order' => 'asc',
			'tax_query' => array(
				array(
					'taxonomy' => 'client',
					'field' => 'id',
					'terms' => $invcat->term_id
				)
			)
		);

		$the_query = new WP_Query( $prodargs );
		$count = 0;
		// display the associated products
		while ( $the_query->have_posts() ) : $the_query->the_post();
			global $count;


		endwhile;

		// Reset Post Data
		wp_reset_postdata();
	echo '</div>';

		
}

genesis();
