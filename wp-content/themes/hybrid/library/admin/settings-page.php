<?php
/**
 * Handles the display and functionality of the theme settings page. This provides the needed hooks and
 * meta box calls for developers to create any number of theme settings needed.
 *
 * Provides the ability for developers to add custom meta boxes to the theme settings page by using the 
 * add_meta_box() function.  Developers should hook their meta box registration function to 'admin_menu' 
 * and register the meta box for 'appearance_page-theme-settings'. If data needs to be saved, devs can 
 * use the '$prefix_update_settings_page' action hook to save their data.
 *
 * @package Hybrid
 * @subpackage Admin
 */

/**
 * Initializes all the theme settings page functions. This function is used to create the theme 
 * settings page, then use that as a launchpad for specific actions that need to be tied to the
 * settings page.
 *
 * Users or developers can set a custom capability (default is 'edit_themes') for access to the
 * settings page using the "$prefix_settings_capability" filter hook.
 *
 * @since 0.7
 * @global string $hybrid The global theme object.
 */
function hybrid_settings_page_init() {
	global $hybrid;

	/* Get theme information. */
	$theme_data = get_theme_data( TEMPLATEPATH . '/style.css' );
	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain();

	/* Create the theme settings page. */
	$hybrid->settings_page = add_theme_page( sprintf( __( '%1$s Theme Settings', $domain ), $theme_data['Name'] ), sprintf( __( '%1$s Settings', $domain ), $theme_data['Name'] ), apply_filters( "{$prefix}_settings_capability", 'edit_theme_options' ), 'theme-settings', 'hybrid_settings_page' );

	/* Register the default theme settings meta boxes. */
	add_action( "load-{$hybrid->settings_page}", 'hybrid_create_settings_meta_boxes' );

	/* Make sure the settings are saved. */
	add_action( "load-{$hybrid->settings_page}", 'hybrid_load_settings_page' );

	/* Load the JavaScript and stylehsheets needed for the theme settings. */
	add_action( "load-{$hybrid->settings_page}", 'hybrid_settings_page_enqueue_script' );
	add_action( "load-{$hybrid->settings_page}", 'hybrid_settings_page_enqueue_style' );
	add_action( "admin_head-{$hybrid->settings_page}", 'hybrid_settings_page_load_scripts' );
}

/**
 * This function creates all of the default theme settings and adds them to a single array. By saving 
 * them in one array, the function only creates one setting in the {$wpdb->prefix}_options table.
 *
 * @since 0.4
 * @return array All options for theme settings.
 */
function hybrid_theme_settings() {
	$domain = hybrid_get_textdomain();

	/* Add the default data to the theme settings array. */
	$settings = array(
		'feed_url' => false,
		'feeds_redirect' => false,
		'print_style' => false,
		'superfish_js' => true,
		'seo_plugin' => false,
		'use_menus' => true,
		'footer_insert' => '<p class="copyright">' . __( 'Copyright &#169; [the-year] [site-link].', $domain ) . '</p>' . "\n\n" . '<p class="credit">' . __( 'Powered by [wp-link] and [theme-link].', $domain ) . '</p>',
	);

	/* If there is a child theme active, add the [child-link] shortcode to the $footer_insert. */
	if ( STYLESHEETPATH !== TEMPLATEPATH )
		$settings['footer_insert'] = '<p class="copyright">' . __( 'Copyright &#169; [the-year] [site-link].', $domain ) . '</p>' . "\n\n" . '<p class="credit">' . __( 'Powered by [wp-link], [theme-link], and [child-link].', $domain ) . '</p>';

	return apply_filters( hybrid_get_prefix() . '_settings_args', $settings );
}

/**
 * Saves the default theme settings in the {$wpdb->prefix}_options if none have been added. The 
 * settings are given a unique name depending on the theme directory. They are always saved as 
 * {$prefix}_theme_settings in the database. It also fires the {$prefix}_update_settings_page 
 * hook for saving custom settings.
 *
 * @since 0.7
 */
function hybrid_load_settings_page() {
	$prefix = hybrid_get_prefix();

	/* Get theme settings from the database. */
	$settings = get_option( "{$prefix}_theme_settings" );

	/* If no settings are available, add the default settings to the database. */
	if ( empty( $settings ) ) {
		$settings = hybrid_theme_settings();
		add_option( "{$prefix}_theme_settings", $settings, '', 'yes' );

		/* Redirect the page so that the settings are reflected on the settings page. */
		wp_redirect( admin_url( 'themes.php?page=theme-settings' ) );
		exit;
	}

	/* If the form has been submitted, check the referer and execute available actions. */
	elseif ( 'Y' == $_POST["{$prefix}-settings-submit"] ) {

		/* Make sure the form is valid. */
		check_admin_referer( "{$prefix}-settings-page" );

		/* Available hook for saving settings. */
		do_action( "{$prefix}_update_settings_page" );

		/* Redirect the page so that the new settings are reflected on the settings page. */
		wp_redirect( admin_url( 'themes.php?page=theme-settings&updated=true' ) );
		exit;
	}
}

/**
 * Updates the default theme settings if the settings page has been updated. It validates the values
 * added through the default theme settings page meta boxes.  Only settings returned by the 
 * hybrid_theme_settings() function will be saved. Child themes and plugins should save their settings 
 * separately.
 *
 * @since 0.7
 */
function hybrid_save_theme_settings() {
	$prefix = hybrid_get_prefix();

	/* Get the current theme settings. */
	$settings = get_option( "{$prefix}_theme_settings" );

	/* Loop through each of the default settings and match them with the posted settings. */
	foreach ( hybrid_theme_settings() as $key => $value )
		$settings[$key] = $_POST[$key];

	/* Make sure users without the 'unfiltered_html' capability can't add HTML to the footer insert. */
	if ( $settings['footer_insert'] && !current_user_can( 'unfiltered_html' ) )
		$settings['footer_insert'] = stripslashes( wp_filter_post_kses( $settings['footer_insert'] ) );

	/* Escape the entered feed URL. */
	if ( $settings['feed_url'] )
		$settings['feed_url'] = esc_url( $settings['feed_url'] );

	/* Update the theme settings. */
	$updated = update_option( "{$prefix}_theme_settings", $settings );
}

/**
 * Creates the default meta boxes for the theme settings page. Child theme and plugin developers
 * should use add_meta_box() to create additional meta boxes.
 *
 * @since 0.7
 * @global string $hybrid The global theme object.
 */
function hybrid_create_settings_meta_boxes() {
	global $hybrid;

	/* Get theme information. */
	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain();
	$theme_data = get_theme_data( TEMPLATEPATH . '/style.css' );

	/* Adds the About box for the parent theme. */
	add_meta_box( "{$prefix}-about-theme-meta-box", sprintf( __( 'About %1$s', $domain ), $theme_data['Title'] ), 'hybrid_about_theme_meta_box', $hybrid->settings_page, 'normal', 'high' );
 
	/* If the user is using a child theme, add an About box for it. */
	if ( TEMPLATEPATH != STYLESHEETPATH ) {
		$child_data = get_theme_data( STYLESHEETPATH . '/style.css' );
		add_meta_box( "{$prefix}-about-child-meta-box", sprintf( __( 'About %1$s', $domain ), $child_data['Title'] ), 'hybrid_about_theme_meta_box', $hybrid->settings_page, 'normal', 'high' );
	}

	/* Creates a meta box for the general theme settings. */
	add_meta_box( "{$prefix}-general-settings-meta-box", __( 'General settings', $domain ), 'hybrid_general_settings_meta_box', $hybrid->settings_page, 'normal', 'high' );

	/* Creates a meta box for the footer settings. */
	add_meta_box( "{$prefix}-footer-settings-meta-box", __( 'Footer settings', $domain ), 'hybrid_footer_settings_meta_box', $hybrid->settings_page, 'normal', 'high' );
}

/**
 * Creates an information meta box with no settings about the theme. The meta box will display
 * information about both the parent theme and child theme. If a child theme is active, this function
 * will be called a second time.
 *
 * @since 0.7
 * @param $object Variable passed through the do_meta_boxes() call.
 * @param array $box Specific information about the meta box being loaded.
 */
function hybrid_about_theme_meta_box( $object, $box ) {

	/* Get theme information. */
	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain();

	/* Grab theme information based on the meta box being shown (parent or child theme). */
	if ( "{$prefix}-about-theme-meta-box" == $box['id'] )
		$theme_data = get_theme_data( TEMPLATEPATH . '/style.css' );

	elseif ( "{$prefix}-about-child-meta-box" == $box['id'] )
		$theme_data = get_theme_data( STYLESHEETPATH . '/style.css' ); ?>

	<table class="form-table">
		<tr>
			<th><?php _e( 'Theme:', $domain ); ?></th>
			<td><a href="<?php echo $theme_data['URI']; ?>" title="<?php echo $theme_data['Title']; ?>"><?php echo $theme_data['Title']; ?> <?php echo $theme_data['Version']; ?></a></td>
		</tr>
		<tr>
			<th><?php _e( 'Author:', $domain ); ?></th>
			<td><?php echo $theme_data['Author']; ?></td>
		</tr>
		<tr>
			<th><?php _e( 'Description:', $domain ); ?></th>
			<td><?php echo $theme_data['Description']; ?></td>
		</tr>
	</table><!-- .form-table --><?php
}

/**
 * Adds a general settings suite suitable for the average theme, which includes a print stylesheet,
 * drop-downs JavaScript option, and the ability to change the feed URL.
 *
 * @since 0.7
 */
function hybrid_general_settings_meta_box() {
	$domain = hybrid_get_textdomain(); ?>

	<table class="form-table">

		<tr>
			<th><label for="print_style"><?php _e( 'Stylesheets:', $domain ); ?></label></th>
			<td>
				<input id="print_style" name="print_style" type="checkbox" <?php if ( hybrid_get_setting( 'print_style' ) ) echo 'checked="checked"'; ?> value="true" /> 
				<label for="print_style"><?php _e( 'Select this to have the theme automatically include a print stylesheet.', $domain ); ?></label>
			</td>
		</tr>
		<tr>
			<th><label for="superfish_js"><?php _e( 'JavaScript:', $domain ); ?></label></th>
			<td>
				<input id="superfish_js" name="superfish_js" type="checkbox" <?php if ( hybrid_get_setting( 'superfish_js' ) ) echo 'checked="checked"'; ?> value="true" /> 
				<label for="superfish_js"><?php _e( 'Include the drop-down menu JavaScript.', $domain ); ?></label>
			</td>
		</tr>
		<tr>
			<th><label for="use_menus"><?php _e( 'Menus:', $domain ); ?></label></th>
			<td>
				<input id="use_menus" name="use_menus" type="checkbox" <?php if ( hybrid_get_setting( 'use_menus' ) ) echo 'checked="checked"'; ?> value="true" /> 
				<label for="use_menus"><?php _e( 'Use the WordPress 3.0+ menu system? Child themes built prior to <em>Hybrid</em> 0.8 may need to be updated to use this.', $domain ); ?></label>
			</td>
		</tr>
		<tr>
			<th><label for="feed_url"><?php _e( 'Feeds:', $domain ); ?></label></th>
			<td>
				<input id="feed_url" name="feed_url" type="text" value="<?php echo hybrid_get_setting( 'feed_url' ); ?>" size="30" /><br />
				<?php _e( 'If you have an alternate feed address, such as one from <a href="http://feedburner.com" title="Feedburner">Feedburner</a>, you can enter it here to have the theme redirect your feed links.', $domain ); ?><br /><br />
				<input id="feeds_redirect" name="feeds_redirect" type="checkbox" <?php if ( hybrid_get_setting( 'feeds_redirect' ) ) echo 'checked="checked"'; ?> value="true" /> 
				<label for="feeds_redirect"><?php _e( 'Direct category, tag, search, and author feeds to your alternate feed address?', $domain ); ?></label>
			</td>
		</tr>
		<tr>
			<th><label for="seo_plugin"><acronym title="<?php _e( 'Search Engine Optimization', $domain ); ?>"><?php _e( 'SEO:', $domain ); ?></acronym></label></th>
			<td>
				<input id="seo_plugin" name="seo_plugin" type="checkbox" <?php if ( hybrid_get_setting( 'seo_plugin' ) ) echo 'checked="checked"'; ?> value="true" /> 
				<label for="seo_plugin"><?php _e( 'Are you using an <acronym title="Search Engine Optimization">SEO</acronym> plugin? Select this to disable the theme\'s meta and indexing features.', $domain ); ?></label>
			</td>
			</tr>

	</table><!-- .form-table --><?php
}

/**
 * Creates a settings box that allows users to customize their footer. A basic textarea is given that
 * allows HTML and shortcodes to be input.
 *
 * @since 0.7
 */
function hybrid_footer_settings_meta_box() {
	$domain = hybrid_get_textdomain(); ?>

	<table class="form-table">
		<tr>
			<th><label for="footer_insert"><?php _e( 'Footer Insert:', $domain ); ?></label></th>
			<td>
				<?php _e( 'You can add custom <acronym title="Hypertext Markup Language">HTML</acronym> and/or shortcodes, which will be automatically inserted into your theme.', $domain ); ?><br />
				<textarea id="footer_insert" name="footer_insert" cols="60" rows="5" style="width: 98%;"><?php echo wp_specialchars( stripslashes( hybrid_get_setting( 'footer_insert' ) ), 1, 0, 1 ); ?></textarea><br />
				<?php _e( 'Shortcodes:', $domain ); ?> <code>[the-year]</code>, <code>[site-link]</code>, <code>[wp-link]</code>, <code>[theme-link]</code>, <code>[child-link]</code>, <code>[loginout-link]</code>, <code>[query-counter]</code>.
			</td>
		</tr>
	</table><!-- .form-table --><?php
}

/**
 * Displays the theme settings page and calls do_meta_boxes() to allow additional settings
 * meta boxes to be added to the page.
 *
 * @since 0.7
 * @global string $hybrid The global theme object.
 */
function hybrid_settings_page() {
	global $hybrid;

	/* Get the theme information. */
	$prefix = hybrid_get_prefix();
	$domain = hybrid_get_textdomain();
	$theme_data = get_theme_data( TEMPLATEPATH . '/style.css' ); ?>

	<div class="wrap">

		<h2><?php printf( __( '%1$s Theme Settings', $domain ), $theme_data['Name'] ); ?></h2>

		<?php if ( 'true' == esc_attr( $_GET['updated'] ) ) echo '<p class="updated fade below-h2" style="padding: 5px 10px;"><strong>' . __( 'Settings saved.', $domain ) . '</strong></p>'; ?>

		<div id="poststuff">

			<form method="post" action="<?php admin_url( 'themes.php?page=theme-settings' ); ?>">

				<?php wp_nonce_field( "{$prefix}-settings-page" ); ?>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

				<div class="metabox-holder">
					<div class="post-box-container column-1 normal"><?php do_meta_boxes( $hybrid->settings_page, 'normal', $theme_data ); ?></div>
					<div class="post-box-container column-2 advanced"><?php do_meta_boxes( $hybrid->settings_page, 'advanced', $theme_data ); ?></div>
					<div class="post-box-container column-3 side"><?php do_meta_boxes( $hybrid->settings_page, 'side', $theme_data ); ?></div>
				</div>

				<p class="submit" style="clear: both;">
					<input type="submit" name="Submit"  class="button-primary" value="<?php _e( 'Update Settings', $domain ); ?>" />
					<input type="hidden" name="<?php echo "{$prefix}-settings-submit"; ?>" value="Y" />
					<!-- deprecated --><input type="hidden" name="<?php echo "hybrid_submit_hidden"; ?>" value="Y" />
				</p><!-- .submit -->

				<?php do_action( "{$prefix}_child_settings" ); // Hook for child settings (deprecated). ?>

			</form>

		</div><!-- #poststuff -->

	</div><!-- .wrap --><?php
}

/**
 * Loads the JavaScript files required for managing the meta boxes on the theme settings
 * page, which allows users to arrange the boxes to their liking.
 *
 * @since 0.7
 */
function hybrid_settings_page_enqueue_script() {
	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'wp-lists' );
	wp_enqueue_script( 'postbox' );
}

/**
 * Loads the admin.css stylesheet for the theme settings page.
 *
 * @since 0.7
 */
function hybrid_settings_page_enqueue_style() {
	wp_enqueue_style( hybrid_get_prefix() . '-admin', THEME_CSS . '/admin.css', false, 0.7, 'screen' );
}

/**
 * Loads the JavaScript required for toggling the meta boxes on the theme settings page.
 *
 * @since 0.7
 * @global string $hybrid The global theme object.
 */
function hybrid_settings_page_load_scripts() {
	global $hybrid; ?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			postboxes.add_postbox_toggles( '<?php echo $hybrid->settings_page; ?>' );
		});
		//]]>
	</script><?php
}

?>