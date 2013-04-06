<?php
/**
 * Functions
 *
 * @package      BE_Genesis_Child
 * @since        1.0.0
 * @link         https://github.com/billerickson/BE-Genesis-Child
 * @author       Bill Erickson <bill@billerickson.net>
 * @copyright    Copyright (c) 2011, Bill Erickson
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 *
 */

/**
 * Theme Setup
 * @since 1.0.0
 *
 * This setup function attaches all of the site-wide functions
 * to the correct hooks and filters. All the functions themselves
 * are defined below this setup function.
 *
 */

add_action('genesis_setup','child_theme_setup', 15);
function child_theme_setup() {

	// ** Backend **

	// Image Sizes
	// add_image_size ('be_featured', 400, 100, true );

	// Menus
	add_theme_support( 'genesis-menus', array( 'primary' => 'Primary Navigation Menu' ) );

	// Sidebars
	//unregister_sidebar('sidebar-alt');
	//genesis_register_sidebar(array('name' => 'Blog Sidebar', 'id' => 'blog-sidebar'));
	//add_theme_support( 'genesis-footer-widgets', 3 );

	// Remove Unused Page Layouts
	//genesis_unregister_layout( 'full-width-content' );
	//genesis_unregister_layout( 'content-sidebar' );
	//genesis_unregister_layout( 'sidebar-content' );
	//genesis_unregister_layout( 'content-sidebar-sidebar' );
	//genesis_unregister_layout( 'sidebar-sidebar-content' );
	//genesis_unregister_layout( 'sidebar-content-sidebar' );

	// Remove Unused Theme Settings
	add_action( 'genesis_theme_settings_metaboxes', 'be_remove_metaboxes' );

	// Remove Unused User Settings
	add_filter( 'user_contactmethods', 'be_contactmethods' );
	remove_action( 'show_user_profile', 'genesis_user_options_fields' );
	remove_action( 'edit_user_profile', 'genesis_user_options_fields' );
	remove_action( 'show_user_profile', 'genesis_user_archive_fields' );
	remove_action( 'edit_user_profile', 'genesis_user_archive_fields' );
	remove_action( 'show_user_profile', 'genesis_user_seo_fields' );
	remove_action( 'edit_user_profile', 'genesis_user_seo_fields' );
	remove_action( 'show_user_profile', 'genesis_user_layout_fields' );
	remove_action( 'edit_user_profile', 'genesis_user_layout_fields' );
	remove_action('genesis_after_post', 'genesis_do_author_box_single');
	// Editor Styles
	add_editor_style( 'editor-style.css' );

	// Setup Theme Settings
	//include_once( CHILD_DIR . '/lib/functions/child-theme-settings.php');

	// Don't update theme
	add_filter( 'http_request_args', 'be_dont_update_theme', 5, 2 );

	// ** Frontend **

	// Remove Edit link
	add_filter( 'genesis_edit_post_link', '__return_false' );

	// Remove Genesis Footer
	remove_action( 'genesis_footer', 'genesis_do_footer' );

	// bring in CPTs, metaboxes, etc.
	require_once( 'cpt-hours.php' );
	require_once( 'lib/metabox/init.php' );
	require_once( 'create-taxonomy.php' );

}

// ** Backend Functions ** //

/**
 * Remove Metaboxes
 * @since 1.0.0
 *
 * This removes unused or unneeded metaboxes from Genesis > Theme Settings.
 * See /genesis/lib/admin/theme-settings for all metaboxes.
 *
 * @author Bill Erickson
 * @link http://www.billerickson.net/code/remove-metaboxes-from-genesis-theme-settings/
 */

function be_remove_metaboxes( $_genesis_theme_settings_pagehook ) {
	remove_meta_box( 'genesis-theme-settings-header', $_genesis_theme_settings_pagehook, 'main' );
//	remove_meta_box( 'genesis-theme-settings-nav', $_genesis_theme_settings_pagehook, 'main' );
	remove_meta_box( 'genesis-theme-settings-breadcrumb', $_genesis_theme_settings_pagehook, 'main' );
	remove_meta_box( 'genesis-theme-settings-blogpage', $_genesis_theme_settings_pagehook, 'main' );
}

/**
 * Customize Contact Methods
 * @since 1.0.0
 *
 * @author Bill Erickson
 * @link http://sillybean.net/2010/01/creating-a-user-directory-part-1-changing-user-contact-fields/
 *
 * @param array $contactmethods
 * @return array
 */
function be_contactmethods( $contactmethods ) {
	unset( $contactmethods['aim'] );
	unset( $contactmethods['yim'] );
	unset( $contactmethods['jabber'] );

	return $contactmethods;
}

/**
 * Don't Update Theme
 * @since 1.0.0
 *
 * If there is a theme in the repo with the same name,
 * this prevents WP from prompting an update.
 *
 * @author Mark Jaquith
 * @link http://markjaquith.wordpress.com/2009/12/14/excluding-your-plugin-or-theme-from-update-checks/
 *
 * @param array $r, request arguments
 * @param string $url, request url
 * @return array request arguments
 */

function be_dont_update_theme( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
		return $r; // Not a theme update request. Bail immediately.
	$themes = unserialize( $r['body']['themes'] );
	unset( $themes[ get_option( 'template' ) ] );
	unset( $themes[ get_option( 'stylesheet' ) ] );
	$r['body']['themes'] = serialize( $themes );
	return $r;
}

// ** Frontend Functions ** //

/**
 * Prepopulates form field with dropdowns
 */

add_filter( 'gform_pre_render_1', 'populate_clients');
add_filter( 'gform_pre_render_3', 'populate_clients');
add_filter( 'gform_pre_render_4', 'populate_clients');

function populate_clients($form) {
	foreach($form['fields'] as &$field) {
		if($field['type'] != 'select' || strpos($field['cssClass'], 'client-drop') === false)
		continue;

		$posts = get_terms( 'client', 'orderby=title&hide_empty=0' );

		$choices = array(array('text' => 'Select a Client', 'value' => ' '));
	//	$choices[] = array ('text' => 'Select a Client', 'value' => '' );

		foreach($posts as $post){
			$choices[] = array('text' => $post->name, 'value' => $post->name);
		}

		$field['choices'] = $choices;
	}

	return $form;
}

/**
 * This addes the client as a taxonomy to the hours entry.
 */

add_action('gform_post_submission', 'tt_tag_to_taxonomy', 10, 2);
function tt_tag_to_taxonomy($entry, $form){

	$post_id = $entry['post_id'];
    $taxonomy = '';

    foreach($form['fields'] as $field) {
        switch($form['id']) {
        case 1: // enter your form ID here
        case 3: // enter your form ID here
        case 4: // enter your form ID here
           switch($field['id']) {
           case 1: // update to a field ID which contains a list of tags
              $taxonomy = 'client';
              $tag_string = $entry[1];
              break;
           }
           break;
        case 99: // enter your form ID here (not currently being used)
           switch($field['id']) {
           case 13: // update to a field ID which contains a list of tags
              $taxonomy = 'your-tax-name';
              $tag_string = $entry[12];
              break;
           case 15: // update to a field ID which contains a list of tags
              $taxonomy = 'different-tax-name';
              $tag_string = $entry[14];
              break;
           }
           break;
        }

        $tags = array_map('trim', explode(',', $tag_string));

        if(!$post_id || empty($tags))
            return;

        foreach($tags as $tag)
            wp_set_object_terms($post_id, $tag, $taxonomy, true);

    }

}

add_filter('wp_nav_menu_items','tt_mylinks');
function tt_mylinks ($nav){
	global $current_user;

	$mylinks = '<li><a href="'.site_url().'/author/'.$current_user->user_login.'/">My Hours</a></li>';

	if(current_user_can('administrator')) {
		$mylinks .= '<li><a href="'.site_url().'/invoice/">Invoice</a></li>';
		$mylinks .= '<li><a href="'.site_url().'/all-hours/">All Hours</a></li>';
		$mylinks .= '<li><a href="'.site_url().'/outstanding-hours/">Outstanding Hours</a></li>';
		$mylinks .= '<li><a href="'.site_url().'/add-client/">Add Client</a></li>';
		$mylinks .= '<li><a href="'.site_url().'/unbilled-hours/">Unbilled</a></li>';
	}

	return $nav.$mylinks;
}


/**
 * Since 1.0
 * This function is related to the add client form
 */

add_action('gform_post_submission', 'hyp_tag_to_taxonomy', 10, 2);
function hyp_tag_to_taxonomy($entry, $form){

//	$post_id = $entry['post_id'];
	$taxonomy = '';

	foreach($form['fields'] as $field) {
		switch($form['id']) {
			case 2: // enter your form ID here
				wp_insert_term( $entry[3], 'client');
			break;
		}
	}
}

/**
 * Not sure what this does right now.
 * @param type $tag
 *
 */

// A callback function to add a custom field to our "hours" taxonomy
function client_taxonomy_custom_fields($tag) {
   // Check for existing taxonomy meta for the term you're editing
    $t_id = $tag->term_id; // Get the ID of the term you're editing
    $term_meta = get_option( "taxonomy_term_$t_id" ); // Do the check
?>

<tr class="form-field">
	<th scope="row" valign="top">
		<label for="client_is_prepay"><?php _e('Client is prepay'); ?></label>
	</th>
	<td>
		<input type="text" name="term_meta[client_is_prepay]" id="term_meta[client_is_prepay]" size="25" style="width:60%;" value="<?php echo $term_meta['client_is_prepay'] ? $term_meta['client_is_prepay'] : ''; ?>"><br />
		<span class="description"><?php _e('Does this client prepay?'); ?></span>
	</td>
</tr>

<?php
}

// Add the fields to the "hours" taxonomy, using our callback function
add_action( 'client_edit_form_fields', 'client_taxonomy_custom_fields', 10, 2 );

// Save the changes made on the "hours" taxonomy, using our callback function
add_action( 'edited_client', 'save_taxonomy_custom_fields', 10, 2 );

// A callback function to save our extra taxonomy field(s)
function save_taxonomy_custom_fields( $term_id ) {
    if ( isset( $_POST['term_meta'] ) ) {
        $t_id = $term_id;
        $term_meta = get_option( "taxonomy_term_$t_id" );
        $cat_keys = array_keys( $_POST['term_meta'] );
            foreach ( $cat_keys as $key ){
            if ( isset( $_POST['term_meta'][$key] ) ){
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        //save the option array
        update_option( "taxonomy_term_$t_id", $term_meta );
    }
}

// Convert the datepicker field from MM/DD/YYYY to YYYYMMDD
add_action('gform_after_submission', 'time_after_submission_handler', 10, 2 );
function time_after_submission_handler( $lead, $form ){
	foreach ( $form['fields'] as $field ) {
		if ( isset( $field['postCustomFieldName'] ) && $field['postCustomFieldName'] == 'tt_work_date' ) {
			$time = strtotime( $_POST['input_' . $field['id']] );
			if ( $time )
				update_post_meta( $lead['post_id'], 'tt_work_date', date( 'Ymd', $time ) );
		}
	}
}