<?php
/**
 * Deprecated functions that should be avoided in favor of newer functions. Also handles 
 * removed functions to avoid errors. Users should not use these functions in their child 
 * themes.  The functions below will all be removed at some point in a future release.  If your 
 * child theme is using one of these, you should use the listed alternative or remove it from 
 * your child theme if necessary.
 *
 * @package Hybrid
 * @subpackage Legacy
 */

/**
 * Old equivalent of hybrid_entry_class().
 *
 * @since 0.2
 * @deprecated 0.5 Use hybrid_entry_class() instead.
 */
function hybrid_post_class( $deprecated = '' ) {
	_deprecated_function( __FUNCTION__, '0.5', 'hybrid_entry_class()' );
	hybrid_entry_class( $deprecated );
}

/**
 * Displays the category navigation menu and wraps it in a <div> element.
 *
 * @deprecated 0.6 Child themes should manually add a category menu using wp_list_categories().
 * @internal This function needs to stay for the long haul (post-1.0).
 *
 * @since 0.1
 */
function hybrid_cat_nav() {
	_deprecated_function( __FUNCTION__, '0.6', 'wp_list_categories()' );

	echo "<div id='cat-navigation'>";

	do_action( 'hybrid_before_cat_nav' );

	echo apply_filters( 'hybrid_cat_nav', hybrid_category_menu( 'echo=0' ) );

	do_action( 'hybrid_after_cat_nav' );

	echo '</div>';
}

/**
 * Menu listing for categories. Much like WP's wp_page_menu() functionality.
 *
 * @deprecated 0.6 Child themes should manually add a category menu using wp_list_categories().
 * @internal This function needs to stay for the long haul (post-1.0).
 *
 * @since 0.2.3
 * @uses wp_list_categories() Creates a list of the site's categories
 * @link http://codex.wordpress.org/Template_Tags/wp_list_categories
 * @param array $args
 */
function hybrid_category_menu( $args = array() ) {
	_deprecated_function( __FUNCTION__, '0.6', 'wp_nav_menu()' );

	$defaults = array( 'menu_class' => 'cat-nav', 'style' => 'list', 'hide_empty' => 1, 'use_desc_for_title' => 0, 'depth' => 4, 'hierarchical' => true, 'echo' => 1 );
	$args = apply_filters( 'hybrid_category_menu_args', $args );
	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	$args['title_li'] = false;
	$args['echo'] = false;

	$menu = '<div id="' . $menu_class . '" class="' . $menu_class . '"><ul class="menu sf-menu">';

	$menu .= str_replace( array( "\t", "\n", "\r" ), '', wp_list_categories( $args ) );

	$menu .= '</ul></div>';

	$menu = apply_filters( 'hybrid_category_menu', $menu );

	if ( $echo )
		echo $menu;
	else
		return $menu;
}

/**
 * Loads the theme search form.
 *
 * @deprecated 0.6 Users should add get_search_form() whenever needed.
 * @since 0.1
 */
function hybrid_search_form() {
	_deprecated_function( __FUNCTION__, '0.6', 'get_search_form()' );

	$search = apply_filters( 'hybrid_search_form', false );

	if ( empty( $search ) )
		get_search_form();
	else
		echo $search;
}

/**
 * After single posts but before the comments template.
 * @since 0.2
 * @deprecated 0.7 Use hybrid_after_singular().
 */
function hybrid_after_single() {
	_deprecated_function( __FUNCTION__, '0.7', 'hybrid_after_singular()' );
	hybrid_after_singular();
}

/**
 * After page content but before the comments template.
 * @since 0.2
 * @deprecated 0.7 Use hybrid_after_singular().
 */
function hybrid_after_page() {
	_deprecated_function( __FUNCTION__, '0.7', 'hybrid_after_singular()' );
	hybrid_after_singular();
}

/**
 * Loads the Utility: After Single widget area.
 *
 * @since 0.4
 * @deprecated 0.7 Use hybrid_get_utility_after_singular().
 */
function hybrid_get_utility_after_single() {
	_deprecated_function( __FUNCTION__, '0.7', 'hybrid_get_utility_after_singular()' );
	hybrid_get_utility_after_singular();
}

/**
 * Loads the Utility: After Page widget area.
 *
 * @since 0.4
 * @deprecated 0.7 Use hybrid_get_utility_after_singular().
 */
function hybrid_get_utility_after_page() {
	_deprecated_function( __FUNCTION__, '0.7', 'hybrid_get_utility_after_singular()' );
	hybrid_get_utility_after_singular();
}

/**
 * Displays the page navigation menu.  Also adds some extra classes and IDs for 
 * better CSS styling.  To customize, filter wp_page_menu  or wp_page_menu_args.
 *
 * @deprecated 0.8 Theme now supports the WordPress 3.0+ menu system.  Since this
 * function and its accompanying hooks have been such a huge part of the theme, this 
 * needs to stay for a while.  Maybe remove around v.2.0. ;)
 *
 * @since 0.1
 * @uses wp_page_menu() Creates a menu of pages
 * @link http://codex.wordpress.org/Template_Tags/wp_page_menu
 */
function hybrid_page_nav() {
	_deprecated_function( __FUNCTION__, '0.8', 'hybrid_get_primary_menu()' );

	/* Opening wrapper for the navigation area. */
	echo '<div id="navigation">' . "\n\t\t\t";

	/* Before page nav hook. */
	do_atomic( 'before_page_nav' );

	/* Arguments for wp_page_menu().  Users should filter 'wp_page_menu_args' to change. */
	$args = array(
		'show_home' => __( 'Home', hybrid_get_textdomain() ),
		'menu_class' => 'page-nav',
		'sort_column' => 'menu_order',
		'depth' => 4,
		'echo' => 0
	);

	/* Strips formatting and spacing to make the code less messy on display. */
	$nav = str_replace( array( "\r", "\n", "\t" ), '', wp_page_menu( $args ) );

	/* Adds the #page-nav ID to the wrapping element. */
	$nav = str_replace( '<div class="', '<div id="page-nav" class="', $nav );

	/* Adds the .menu and .sf-menu classes for use with the drop-down JavaScript. */
	echo preg_replace( '/<ul>/', '<ul class="menu sf-menu">', $nav, 1 );

	/* After page nav hook. */
	do_atomic( 'after_page_nav' );

	/* Closes the navigation area wrapper. */
	echo "\n\t</div><!-- #navigation -->\n";
}

/**
 * Check for widgets in widget-ready areas.
 *
 * @since 0.2
 * @deprecated 0.6.1 Use WP's is_active_sidebar() instead.
 * @param string|int $index name|ID of widget area.
 * @return bool
 */
function is_sidebar_active( $index = 1 ) {
	_deprecated_function( __FUNCTION__, '0.6.1', 'is_active_sidebar()' );
	return is_active_sidebar( $index );
}

/**
 * Loads the comment form.
 *
 * @since 0.7
 * @deprecated 0.8 Theme now uses the comment_form() WordPress 3.0+ function.
 */
function hybrid_get_comment_form() {
	_deprecated_function( __FUNCTION__, '0.8', 'comment_form()' );
	comment_form();
}

/**
 * Fires before the comment form.
 *
 * @since 0.6
 * @deprecated 0.8 Theme now uses the comment_form() WordPress 3.0+ function.
 */
function hybrid_before_comment_form() {
	_deprecated_function( __FUNCTION__, '0.8' );
	do_atomic( 'before_comment_form' );
}

/**
 * Fires after the comment form.
 *
 * @since 0.6
 * @deprecated 0.8 Theme now uses the comment_form() WordPress 3.0+ function.
 */
function hybrid_after_comment_form() {
	_deprecated_function( __FUNCTION__, '0.8' );
	do_atomic( 'after_comment_form' );
}
/**
 * Displays an individual comment author.
 *
 * @since 0.2.2
 * @deprecated 0.8 Use hybrid_comment_author_shortcode() instead.
 */
function hybrid_comment_author() {
	_deprecated_function( __FUNCTION__, '0.8', 'hybrid_comment_author_shortcode()' );
	return hybrid_comment_author_shortcode();
}

/* === Removed Functions === */

/* Functions removed in the 0.4 branch. */

function hybrid_theme_meta() {
	hybrid_function_removed( 'hybrid_theme_meta' );
}

function hybrid_meta_other() {
	hybrid_function_removed( 'hybrid_meta_other' );
}

function hybrid_load_SimplePie() {
	hybrid_function_removed( 'hybrid_load_SimplePie' );
}

function hybrid_lifestream() {
	hybrid_function_removed( 'hybrid_lifestream' );
}

function hybrid_admin_enqueue_style() {
	hybrid_function_removed( 'hybrid_admin_enqueue_style' );
}

function hybrid_page_id() {
	hybrid_function_removed( 'hybrid_page_id' );
}

function hybrid_page_class() {
	hybrid_function_removed( 'hybrid_page_class' );
}

/* Functions removed in the 0.5 branch. */

function hybrid_all_tags() {
	hybrid_function_removed( 'hybrid_all_tags' );
}

function hybrid_get_users() {
	hybrid_function_removed( 'hybrid_get_users' );
}

function hybrid_footnote() {
	hybrid_function_removed( 'hybrid_footnote' );
}

function hybrid_related_posts() {
	hybrid_function_removed( 'hybrid_related_posts' );
}

function hybrid_insert() {
	hybrid_function_removed( 'hybrid_insert' );
}

/* Functions removed in the 0.6 branch. */

function hybrid_get_authors() {
	hybrid_function_removed( 'hybrid_get_authors' );
}

function hybrid_credit() {
	hybrid_function_removed( 'hybrid_credit' );
}

function hybrid_query_counter() {
	hybrid_function_removed( 'hybrid_query_counter' );
}

function hybrid_copyright() {
	hybrid_function_removed( 'hybrid_copyright' );
}

function hybrid_series() {
	hybrid_function_removed( 'hybrid_series' );
}

/* Functions removed in the 0.7 branch. */

function hybrid_all_cats() {
	hybrid_function_removed( 'hybrid_all_cats' );
}

function hybrid_all_cat_slugs() {
	hybrid_function_removed( 'hybrid_all_cat_slugs' );
}

function hybrid_all_tag_slugs() {
	hybrid_function_removed( 'hybrid_all_tag_slugs' );
}

function hybrid_mime_type_icon() {
	hybrid_function_removed( 'hybrid_mime_type_icon' );
}

function hybrid_attachment_icon() {
	hybrid_function_removed( 'hybrid_attachment_icon' );
}

function hybrid_widow() {
	hybrid_function_removed( 'hybrid_widow' );
}

function hybrid_dash() {
	hybrid_function_removed( 'hybrid_dash' );
}

function hybrid_text_filter() {
	hybrid_function_removed( 'hybrid_text_filter' );
}

function hybrid_allowed_tags() {
	hybrid_function_removed( 'hybrid_allowed_tags' );
}

function hybrid_typography() {
	hybrid_function_removed( 'hybrid_typography' );
}

function hybrid_before_cat_nav() {
	hybrid_function_removed( 'hybrid_before_cat_nav' );
}

function hybrid_after_cat_nav() {
	hybrid_function_removed( 'hybrid_after_cat_nav' );
}

function hybrid_first_paragraph() {
	hybrid_function_removed( 'hybrid_first_paragraph' );
}

function hybrid_category_term_link() {
	hybrid_function_removed( 'hybrid_category_term_link' );
}

function hybrid_post_tag_term_link() {
	hybrid_function_removed( 'hybrid_post_tag_term_link' );
}

function hybrid_search_highlight() {
	hybrid_function_removed( 'hybrid_search_highlight' );
}

function hybrid_primary_inserts() {
	hybrid_function_removed( 'hybrid_primary_inserts' );
}

function hybrid_secondary_inserts() {
	hybrid_function_removed( 'hybrid_secondary_inserts' );
}

function hybrid_subsidiary_inserts() {
	hybrid_function_removed( 'hybrid_subsidiary_inserts' );
}

function hybrid_utility_inserts() {
	hybrid_function_removed( 'hybrid_utility_inserts' );
}

function hybrid_widget_init() {
	hybrid_function_removed( 'hybrid_widget_init' );
}

function hybrid_primary_var() {
	hybrid_function_removed( 'hybrid_primary_var' );
}

function hybrid_secondary_var() {
	hybrid_function_removed( 'hybrid_secondary_var' );
}

function hybrid_subsidiary_var() {
	hybrid_function_removed( 'hybrid_subsidiary_var' );
}

function hybrid_legacy_comments() {
	hybrid_function_removed( 'hybrid_legacy_comments' );
}

function hybrid_head_feeds() {
	hybrid_function_removed( 'hybrid_head_feeds' );
}

function hybrid_legacy_functions() {
	hybrid_function_removed( 'hybrid_legacy_functions' );
}

function hybrid_capability_check() {
	hybrid_function_removed( 'hybrid_capability_check' );
}

function hybrid_template_in_use() {
	hybrid_function_removed( 'hybrid_template_in_use' );
}

function hybrid_get_utility_404() {
	hybrid_function_removed( 'hybrid_get_utility_404' );
}

function hybrid_before_comments() {
	hybrid_function_removed( 'hybrid_before_comments' );
}

function hybrid_meta_abstract() {
	hybrid_function_removed( 'hybrid_meta_abstract' );
}

function hybrid_child_settings() {
	hybrid_function_removed( 'hybrid_child_settings' );
}

function hybrid_post_meta_boxes() {
	hybrid_function_removed( 'hybrid_post_meta_boxes' );
}

function hybrid_page_meta_boxes() {
	hybrid_function_removed( 'hybrid_page_meta_boxes' );
}

function post_meta_boxes() {
	hybrid_function_removed( 'post_meta_boxes' );
}

function page_meta_boxes() {
	hybrid_function_removed( 'page_meta_boxes' );
}

function hybrid_create_meta_box() {
	hybrid_function_removed( 'hybrid_create_meta_box' );
}

function hybrid_save_meta_data() {
	hybrid_function_removed( 'hybrid_save_meta_data' );
}

function get_meta_text_input() {
	hybrid_function_removed( 'get_meta_text_input' );
}

function get_meta_select() {
	hybrid_function_removed( 'get_meta_select' );
}

function get_meta_textarea() {
	hybrid_function_removed( 'get_meta_textarea' );
}

function hybrid_error() {
	hybrid_function_removed( 'hybrid_error' );
}

function hybrid_head_canonical() {
	hybrid_function_removed( 'hybrid_head_canonical' );
}

function hybrid_disable_pagenavi_style() {
	hybrid_function_removed( 'hybrid_disable_pagenavi_style' );
}

function hybrid_comments_feed() {
	hybrid_function_removed( 'hybrid_comments_feed' );
}

function hybrid_before_page_nav() {
	hybrid_function_removed( 'hybrid_before_page_nav' );
}

function hybrid_after_page_nav() {
	hybrid_function_removed( 'hybrid_after_page_nav' );
}

function hybrid_comment_published_link_shortcode() {
	hybrid_function_removed( 'hybrid_comment_published_link_shortcode' );
}

/* Functions removed in the 0.8 branch. */

function hybrid_content_wrapper() {
	hybrid_function_removed( 'hybrid_content_wrapper' );
}

function hybrid_handle_attachment() {
	hybrid_function_removed( 'hybrid_handle_attachment' );
}

function hybrid_widget_class() {
	hybrid_function_removed( 'hybrid_widget_class' );
}

function hybrid_before_ping_list() {
	hybrid_function_removed( 'hybrid_before_ping_list' );
}

function hybrid_after_ping_list() {
	hybrid_function_removed( 'hybrid_after_ping_list' );
}

function hybrid_pings_callback() {
	hybrid_function_removed( 'hybrid_pings_callback' );
}

function hybrid_pings_end_callback() {
	hybrid_function_removed( 'hybrid_pings_end_callback' );
}

/**
 * Message to display for removed functions.
 * @since 0.5
 */
function hybrid_function_removed( $func = '' ) {
	die( sprintf( __( '<code>%1$s</code> &mdash; This function has been removed or replaced by another function.', hybrid_get_textdomain() ), $func ) );
}

?>