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
$hybrid = new Hybrid();
$hybrid->init();

add_filter('the_content', custom_the_content, 9);
add_action('hybrid_header', custom_hybrid_header);

/*
* @author: Eldar
* @desription: makes a changes to the content of post
*/
function custom_the_content($content)
{
	if(!is_single()&&!empty($content))
	{
		global $post;
		return getWords($content, 40).'...<br/><a href="'.get_permalink( $post->ID ).'">'.__('Read', 'hybrid').'...</a>';
	}
	else
	{
		return $content;
	}
}

/*
* @author: Eldar
* @desription: custom header. creates a login/registration links
*/
function custom_hybrid_header()
{
	?>
		<div id="header-auth-info-container">
		
			<?php if ( is_user_logged_in() ) : // Already logged in ?>
			
				<?php global $user_ID; $login = get_userdata( $user_ID ); ?>
				<?php echo $login->display_name;?>|<a href="<?php echo wp_logout_url('/'); ?>"><?php _e('Logout', 'hybrid');?></a>
			<?php else:?>
				
				<a href="<?php echo get_permalink_by_name('login');?>"><?php _e('Login', 'hybrid');?></a> | 
				<a href=""><?php _e('Register', 'hybrid');?></a>
			
			<?php endif;?>
		</div>
	<?php
}

/*
* @author: Eldar
* @desription: limits text by word
*/
function getWords($text, $limit)
{
	$array = explode(" ", $text, $limit+1);

	if (count($array) > $limit)
	{
	unset($array[$limit]);
	}
	return implode(" ", $array);
}

/*
* @author: Eldar
* @desription: gets the permalink by page name
*/
function get_permalink_by_name($page_name)
{
	global $post;
	global $wpdb;
	$pageid_name = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$page_name."'");
	return get_permalink($pageid_name);
}

?>