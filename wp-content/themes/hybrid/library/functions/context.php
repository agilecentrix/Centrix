<?php
/**
 * Functions for making various theme elements context-aware. This controls things such as the
 * <body> and entry CSS classes as well as contextual hooks. By using a context, developers and
 * users can create page-specific code.
 *
 * Many of the ideas behind context-aware functions originated with the great Sandbox theme.
 * @link http://www.plaintxt.org/themes/sandbox
 *
 * @package Hybrid
 * @subpackage Functions
 */

/**
 * Hybrid's main contextual function.  This allows code to be used more than once without running 
 * hundreds of conditional checks within the theme.  It returns an array of contexts based on what 
 * page a visitor is currently viewing on the site.  This function is useful for making dynamic/contextual
 * classes, action and filter hooks, and handling the templating system.
 *
 * Note that time and date can be tricky because any of the conditionals may be true on time-/date-
 * based archives depending on several factors.  For example, one could load an archive for a specific
 * second during a specific minute within a specific hour on a specific day and so on.
 *
 * @since 0.7
 * @global $wp_query The current page's query object.
 * @global $hybrid The global Hybrid object.
 * @return array $hybrid->context Several contexts based on the current page.
 */
function hybrid_get_context() {
	global $wp_query, $hybrid;

	/* If $hybrid->context has been set, don't run through the conditionals again. Just return the variable. */
	if ( is_array( $hybrid->context ) )
		return $hybrid->context;

	$hybrid->context = array();

	/* Front page of the site. */
	if ( is_front_page() )
		$hybrid->context[] = 'home';

	/* Blog page. */
	if ( is_home() ) {
		$hybrid->context[] = 'blog';
	}

	/* Singular views. */
	elseif ( is_singular() ) {
		$hybrid->context[] = 'singular';
		$hybrid->context[] = "singular-{$wp_query->post->post_type}";
		$hybrid->context[] = "singular-{$wp_query->post->post_type}-{$wp_query->post->ID}";
	}

	/* Archive views. */
	elseif ( is_archive() ) {
		$hybrid->context[] = 'archive';

		/* Taxonomy archives. */
		if ( is_tax() || is_category() || is_tag() ) {
			$term = $wp_query->get_queried_object();
			$hybrid->context[] = 'taxonomy';
			$hybrid->context[] = $term->taxonomy;
			$hybrid->context[] = "{$term->taxonomy}-" . sanitize_html_class( $term->slug, $term->term_id );
		}

		/* User/author archives. */
		elseif ( is_author() ) {
			$hybrid->context[] = 'user';
			$hybrid->context[] = 'user-' . sanitize_html_class( get_the_author_meta( 'user_nicename', get_query_var( 'author' ) ), $wp_query->get_queried_object_id() );
		}

		/* Time/Date archives. */
		else {
			if ( is_date() ) {
				$hybrid->context[] = 'date';
				if ( is_year() )
					$hybrid->context[] = 'year';
				if ( is_month() )
					$hybrid->context[] = 'month';
				if ( get_query_var( 'w' ) )
					$hybrid->context[] = 'week';
				if ( is_day() )
					$hybrid->context[] = 'day';
			}
			if ( is_time() ) {
				$hybrid->context[] = 'time';
				if ( get_query_var( 'hour' ) )
					$hybrid->context[] = 'hour';
				if ( get_query_var( 'minute' ) )
					$hybrid->context[] = 'minute';
			}
		}
	}

	/* Search results. */
	elseif ( is_search() ) {
		$hybrid->context[] = 'search';
	}

	/* Error 404 pages. */
	elseif ( is_404() ) {
		$hybrid->context[] = 'error-404';
	}

	return $hybrid->context;
}

/**
 * Creates a set of classes for each site entry upon display. Each entry is given the class of 
 * 'hentry'. Posts are given category, tag, and author classes. Alternate post classes of odd, 
 * even, and alt are added.
 *
 * @since 0.5
 * @global $post The current post's DB object.
 * @param string|array $class Additional classes for more control.
 * @return string $class
 */
function hybrid_entry_class( $class = '' ) {
	global $post;
	static $post_alt;

	/* Add hentry for microformats compliance and the post type. */
	$classes = array( 'hentry', $post->post_type, $post->post_status );

	/* Post alt class. */
	$classes[] = 'post-' . ++$post_alt;
	$classes[] = ( $post_alt % 2 ) ? 'odd' : 'even alt';

	/* Author class. */
	$classes[] = 'author-' . sanitize_html_class( get_the_author_meta( 'user_nicename' ), get_the_author_meta( 'ID' ) );

	/* Sticky class (only on home/blog page). */
	if ( is_home() && is_sticky() )
		$classes[] = 'sticky';

	/* Password-protected posts. */
	if ( post_password_required() )
		$classes[] = 'protected';

	/* Add category and post tag terms as classes. */
	if ( 'post' == $post->post_type ) {

		foreach ( array( 'category', 'post_tag' ) as $tax ) {

			foreach ( (array)get_the_terms( $post->ID, $tax ) as $term ) {
				if ( !empty( $term->slug ) )
					$classes[] = $tax . '-' . sanitize_html_class( $term->slug, $term->term_id );
			}
		}
	}

	/* User-created classes. */
	if ( !empty( $class ) ) {
		if ( !is_array( $class ) )
			$class = preg_split( '#\s+#', $class );
		$classes = array_merge( $classes, $class );
	}

	/* Join all the classes into one string and echo them. */
	$class = join( ' ', $classes );

	echo apply_atomic( 'entry_class', $class );
}

/**
 * Sets a class for each comment. Sets alt, odd/even, and author/user classes. Adds author, user, 
 * and reader classes. Needs more work because WP, by default, assigns even/odd backwards 
 * (Odd should come first, even second).
 *
 * @since 0.2
 * @global $wpdb WordPress DB access object.
 * @global $comment The current comment's DB object.
 */
function hybrid_comment_class() {
	global $post, $comment, $hybrid;

	/* Gets default WP comment classes. */
	$classes = get_comment_class();

	/* Get the comment type. */
	$classes[] = get_comment_type();

	/* User classes to match user role and user. */
	if ( $comment->user_id > 0 ) {

		/* Create new user object. */
		$user = new WP_User( $comment->user_id );

		/* Set a class with the user's role. */
		if ( is_array( $user->roles ) ) {
			foreach ( $user->roles as $role )
				$classes[] = "role-{$role}";
		}

		/* Set a class with the user's name. */
		$classes[] = 'user-' . sanitize_html_class( $user->user_nicename, $user->user_id );
	}

	/* If not a registered user */
	else {
		$classes[] = 'reader';
	}

	/* Comment by the entry/post author. */
	if ( $post = get_post( $post_id ) ) {
		if ( $comment->user_id === $post->post_author )
			$classes[] = 'entry-author';
	}

	/* Join all the classes into one string and echo them. */
	$class = join( ' ', $classes );

	echo apply_filters( "{$hybrid->prefix}_comment_class", $class );
}

/**
 * Provides classes for the <body> element depending on page context.
 *
 * @since 0.1
 * @uses $wp_query
 * @param string|array $class Additional classes for more control.
 * @return string
 */
function hybrid_body_class( $class = '' ) {
	global $wp_query, $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome;

	/* Text direction (which direction does the text flow). */
	$classes = array( 'wordpress', get_bloginfo( 'text_direction' ), get_locale() );

	/* Layout class. */
	if ( ( current_theme_supports( 'post-layouts' ) || 'hybrid' == get_stylesheet() ) && is_singular() ) {
		$layout = get_post_meta( $wp_query->post->ID, 'Layout', true );
		if ( !empty( $layout ) )
			$classes[] = 'layout-' . strip_tags( esc_attr( $layout ) );
	}

	/* Date classes. */
	$time = time() + ( get_option( 'gmt_offset' ) * 3600 );
	$classes[] = strtolower( gmdate( '\yY \mm \dd \hH l', $time ) );

	/* Is the current user logged in. */
	$classes[] = ( is_user_logged_in() ) ? 'logged-in' : 'not-logged-in';

	/* Merge base contextual classes with $classes. */
	$classes = array_merge( $classes, hybrid_get_context() );

	/* Singular post (post_type) classes. */
	if ( is_singular() ) {

		/* Checks for custom template. */
		$template = str_replace( array ( "{$wp_query->post->post_type}-", "{$wp_query->post->post_type}-template-", '.php' ), '', get_post_meta( $wp_query->post->ID, "_wp_{$wp_query->post->post_type}_template", true ) );
		if ( $template ) {
			//$template = str_replace(  ), '', $template );
			$classes[] = "{$wp_query->post->post_type}-template-{$template}";
		}

		/* Attachment mime types. */
		if ( is_attachment() ) {
			foreach ( explode( '/', get_post_mime_type() ) as $type )
				$classes[] = "attachment-{$type}";
		}

		/* Deprecated classes. */
		elseif ( is_page() )
			$classes[] = "page-{$wp_query->post->ID}"; // Use singular-page-ID
		elseif ( is_singular( 'post' ) )
			$classes[] = "single-{$wp_query->post->ID}"; // Use singular-post-ID
	}

	/* Paged views. */
	if ( ( ( $page = $wp_query->get( 'paged' ) ) || ( $page = $wp_query->get( 'page' ) ) ) && $page > 1 ) {
		$page = intval( $page );
		$classes[] = 'paged paged-' . $page;
	}

	/* Browser detection. */
	$browsers = array( 	'gecko' => $is_gecko, 'opera' => $is_opera, 'lynx' => $is_lynx, 'ns4' => $is_NS4, 'safari' => $is_safari, 'chrome' => $is_chrome, 'msie' => $is_IE );
	foreach ( $browsers as $key => $value ) {
		if ( $value ) {
			$classes[] = $key;
			break;
		}
	}

	/* Hybrid theme widgets detection. */
	foreach ( array( 'primary', 'secondary', 'subsidiary' ) as $sidebar )
		$classes[] = ( is_active_sidebar( $sidebar ) ) ? "{$sidebar}-active" : "{$sidebar}-inactive";

	if ( in_array( 'primary-inactive', $classes ) && in_array( 'secondary-inactive', $classes ) && in_array( 'subsidiary-inactive', $classes ) )
		$classes[] = 'no-widgets';

	/* Input class. */
	if ( !empty( $class ) ) {
		if ( !is_array( $class ) )
			$class = preg_split( '#\s+#', $class );
		$classes = array_merge( $classes, $class );
	}

	/* Join all the classes into one string. */
	$class = join( ' ', $classes );

	/* Print the body class. */
	echo apply_atomic( 'body_class', $class );
}

/**
 * Function for handling what the browser/search engine title should be. Tries to handle every 
 * situation to make for the best SEO.
 *
 * @since 0.1
 * @global $wp_query
 */
function hybrid_document_title() {
	global $wp_query;

	$domain = hybrid_get_textdomain();

	$separator = ':';

	if ( is_front_page() && is_home() )
		$doctitle = get_bloginfo( 'name' ) . $separator . ' ' . get_bloginfo( 'description' );

	elseif ( is_home() || is_singular() ) {
		$id = $wp_query->get_queried_object_id();

		$doctitle = get_post_meta( $id, 'Title', true );

		if ( !$doctitle && is_front_page() )
			$doctitle = get_bloginfo( 'name' ) . $separator . ' ' . get_bloginfo( 'description' );
		elseif ( !$doctitle )
			$doctitle = get_post_field( 'post_title', $id );
	}

	elseif ( is_archive() ) {

		if ( is_category() || is_tag() || is_tax() ) {
			$term = $wp_query->get_queried_object();
			$doctitle = $term->name;
		}

		elseif ( is_author() )
			$doctitle = get_the_author_meta( 'display_name', get_query_var( 'author' ) );

		elseif ( is_date () ) {
			if ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
				$doctitle = sprintf( __( 'Archive for %1$s', $domain ), get_the_time( __( 'g:i a', $domain ) ) );

			elseif ( get_query_var( 'minute' ) )
				$doctitle = sprintf( __( 'Archive for minute %1$s', $domain ), get_the_time( __( 'i', $domain ) ) );

			elseif ( get_query_var( 'hour' ) )
				$doctitle = sprintf( __( 'Archive for %1$s', $domain ), get_the_time( __( 'g a', $domain ) ) );

			elseif ( is_day() )
				$doctitle = sprintf( __( 'Archive for %1$s', $domain ), get_the_time( __( 'F jS, Y', $domain ) ) );

			elseif ( get_query_var( 'w' ) )
				$doctitle = sprintf( __( 'Archive for week %1$s of %2$s', $domain ), get_the_time( __( 'W', $domain ) ), get_the_time( __( 'Y', $domain ) ) );

			elseif ( is_month() )
				$doctitle = sprintf( __( 'Archive for %1$s', $domain ), single_month_title( ' ', false) );

			elseif ( is_year() )
				$doctitle = sprintf( __( 'Archive for %1$s', $domain ), get_the_time( __( 'Y', $domain ) ) );
		}
	}

	elseif ( is_search() )
		$doctitle = sprintf( __( 'Search results for &quot;%1$s&quot;', $domain ), esc_attr( get_search_query() ) );

	elseif ( is_404() )
		$doctitle = __( '404 Not Found', $domain );

	/* If paged. */
	if ( ( ( $page = $wp_query->get( 'paged' ) ) || ( $page = $wp_query->get( 'page' ) ) ) && $page > 1 )
		$doctitle = sprintf( __( '%1$s Page %2$s', $domain ), $doctitle . $separator, $page );

	/* Apply the wp_title filters so we're compatible with plugins. */
	$doctitle = apply_filters( 'wp_title', $doctitle, $separator, '' );

	echo apply_atomic( 'document_title', esc_attr( $doctitle ) );
}

?>