<?php
/**
 * Rather than lumping all theme functions into a single file, this functions file is used for
 * initializing the theme framework, which activates files in the order that it needs. Users
 * should create a child theme and make changes to its functions.php file (not this one).
 *
 * @package Hybrid
 * @subpackage Functions
 */

/* Load the Hybrid class. */
require_once( TEMPLATEPATH . '/library/classes/hybrid.php' );

/* Initialize the Hybrid framework. */
add_action( 'hybrid_init', hybrid_init, 10 );
$hybrid = new Hybrid();
$hybrid->init();

add_filter('the_content', custom_the_content, 9);
add_filter(hybrid_format_hook("header"), custom_hybrid_header);
add_filter(hybrid_format_hook("doctype"), custom_doctype );
add_filter(hybrid_format_hook("site_title"), custom_site_title);
add_filter(hybrid_format_hook("site_description"), custom_site_description);
add_action(hybrid_format_hook("sub_header"), custom_sub_header);
add_action( 'widgets_init', custom_register_widgets, 10 );
add_action('init', custom_init, 9);

function custom_init()
{
    wp_enqueue_script('jquery');
    $domain = hybrid_get_textdomain();
    register_sidebar( array( 'name' => __( 'Left Widget Container', $domain ), 'id' => 'left-wiget-container', 'description' => __( 'Widgets in this area will be shown on the left-hand side.', $domain ), 'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">', 'after_widget' => '</div></div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );
    register_sidebar( array( 'name' => __( 'Right Widget Container', $domain ), 'id' => 'right-wiget-container', 'description' => __( 'Widgets in this area will be shown on the right-hand side.', $domain ), 'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">', 'after_widget' => '</div></div>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );
}

function hybrid_init()
{
    include(THEME_EXTENSIONS.'/vote.php');
}

/*
* @author: Eldar
* @desription: includes custom widgets
*/
function custom_register_widgets()
{
    	/* Load each widget file. */
	require_once( THEME_CLASSES . '/widget-categories.php' );
	require_once( THEME_CLASSES . '/widget-user-account.php' );

	/* Register each widget. */
	register_widget( 'Widget_Categories' );
	register_widget( 'Widget_User_Account' );
}

/*
* @author: Eldar
* @desription: This function prints sub header
*/
function custom_sub_header() {
    ?>
<div class="pre-content">
    <div class="attached-menu group" id="page-topbar">
        <ul class="pillboxes group">
            <li>
                <a href="http://digg.com/news" class="active" id="filter-recent">
                    Most Recent
                </a>
            </li>
            <li>
                <a href="http://digg.com/news/24hr" id="filter-24hours">
                    Top in 24 Hours
                </a>
            </li>
            <li>
                <a href="http://digg.com/news/week" id="filter-7days">
                    7 Days
                </a>
            </li>
            <li>
                <a href="http://digg.com/news/month" id="filter-30days">
                    30 Days
                </a>
            </li>
        </ul>
    </div>
</div>
    <?php
}


/*
* @author: Eldar
* @desription: sub header hook
*/
function hybrid_sub_header() {
    do_atomic('sub_header');
}

/*
* @author: Eldar
* @desription: makes a changes to the content of post
*/
function custom_the_content($content) {
    if(!is_single()&&!empty($content)) {
        global $post;
        return getWords($content, 40).'...<br/><a href="'.get_permalink( $post->ID ).'">'.__('Read', 'hybrid').'...</a>';
    }
    else {
        return $content;
    }
}

/*
* @author: Eldar
* @desription: This function overrides title of site
*/
function custom_site_title($title) {
    return '';
}

/*
* @author: Eldar
* @desription: This function overrides description of site
*/
function custom_site_description($title) {
    return '';
}

/*
* @author: Eldar
* @desription: overrides doctype of site
*/
function custom_doctype($doctype) {
    return '<!DOCTYPE html>';
}

/*
* @author: Eldar
* @desription: custom header. creates a login/registration links
*/
function custom_hybrid_header($header) {
    ?>
    <?php wp_nav_menu('depth=1&link_before=<span class="elbow-left"></span><span class="elbow-right"></span>&show_home=1&menu_class=page-navi&sort_column=menu_order&menu_class=main-navigation tabbed-navigation group'); ?>
<div class="float-right group">
    <form action="http://digg.com/search" method="get" class="header-search" id="header-search-form">
        <input type="text" name="q" id="q" autocomplete="off" placeholder="Search Users or Stories" />
        <input type="submit" name="submit" id="search-button" value="" />
    </form>
</div>
                    <?php
}

/*
* @author: Eldar
* @desription: limits text by word
*/
function getWords($text, $limit) {
    $array = explode(" ", $text, $limit+1);

    if (count($array) > $limit) {
        unset($array[$limit]);
    }
    return implode(" ", $array);
}

/*
* @author: Eldar
* @desription: gets the permalink by page name
*/
function get_permalink_by_name($page_name) {
    global $post;
    global $wpdb;
    $pageid_name = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$page_name."'");
    return get_permalink($pageid_name);
}

?>