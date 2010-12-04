<?php
$wp_default_secret_key = 'впишите сюда уникальную фразу';

// Make the menu wider and correct some overlapping issues
function ru_accomodate_markup() {
	global $locale, $wp_styles;

	wp_enqueue_style($locale, WP_CONTENT_URL . "/languages/$locale.css", array(), '20100615', 'all');
	wp_enqueue_style("$locale-ie", WP_CONTENT_URL . "/languages/$locale-ie.css", array(), '20100615', 'all');
	if ( is_multisite() )
		wp_enqueue_style("ms-$locale", WP_CONTENT_URL . "/languages/ms-$locale.css", array(), '20100615', 'all');
	$wp_styles->add_data("$locale-ie", 'conditional', 'IE');

	wp_print_styles();
}
add_action('admin_head', 'ru_accomodate_markup');

function ru_populate_options() {
	add_option('rss_language', 'ru');
}
add_action('populate_options', 'ru_populate_options');

function ru_restore_scripts_l10n() {
	global $wp_scripts;

	if ( is_a($wp_scripts, 'WP_Scripts') ) {
		do_action_ref_array('wp_default_scripts', array(&$wp_scripts));
	}
}
add_action('init', 'ru_restore_scripts_l10n');
?>