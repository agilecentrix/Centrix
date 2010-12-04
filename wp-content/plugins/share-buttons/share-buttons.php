<?php
/*
Plugin Name: Share Buttons
Plugin URI: http://artlosk.com/2010/10/social-share-buttons/
Description: The plugin implements the API function socials networks that adds the link share buttons.
Author: Loskutnikov Artem
Version: 1.2.1
Author URI: http://artlosk.com/
License: GPL2
*/

/*  Copyright 2010 Loskutnikov Artem (artlosk) (email: artlosk at gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

*/

?>
<?php
require_once('share-buttons-scripts.php');
if (!class_exists('ShareButtons')) :

class ShareButtons extends ButtonsScripts {
	var $plugin_url;
    var $plugin_path;
	var $plugin_domain = 'share_buttons';
	var $exclude; // IDs of excluding pages and posts
	var $show_on_post;
	var $show_on_page;
    
    var $logo_share;
    
    var $vkontakte_show; // Show Share Button Vkontakte
    var $vkontakte_button_type;
    
    var $vkontakte_like_api;
    var $vkontakte_like_show;
    var $vkontakte_like_verb;
    var $vkontakte_like_type;
    
    var $odnoklassniki_show;
    var $odnoklassniki_button_type;
    
    var $mailru_show;
    var $mailru_button_type;
    
    var $facebook_like_show;
    var $facebook_like_layout;
    var $facebook_like_color;
    var $facebook_like_faces;
    var $facebook_like_width;
    var $facebook_like_height;    
    var $facebook_like_verb;
    
    var $facebook_share_show;
    var $facebook_share_button_type;
    
    var $twitter_show;
    var $twitter_button_type;
    var $twitter_via;    
            
        

	function ShareButtons() {
        define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
        define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
        $this->plugin_path = WP_PLUGIN_DIR.'/'.dirname(plugin_basename(__FILE__));
        $this->plugin_url = trailingslashit(WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)));
		// Check version
		global $wp_version;
		
		// Load translation only on admin pages
		if (is_admin())
			$this->load_domain();

		$exit_msg = __('Share buttons plugin requires Wordpress 2.8 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>', $this->plugin_domain);

		if (version_compare($wp_version,"2.8","<"))
		{
			exit ($exit_msg);
		}
		// Installation
		register_activation_hook(__FILE__, array(&$this, 'install'));
		// Register options settings
		add_action('admin_init', array(&$this, 'register_settings'));
		// Create custom plugin settings menu
		add_action('admin_menu', array(&$this, 'create_menu'));
		// add vk api scripts to head
		add_action('wp_print_scripts', array(&$this, 'add_head'));
		// Filter for processing button placing
		add_filter('the_content', array(&$this, 'place_button'));
		// Shortcode
		add_shortcode('share-buttons', array(&$this, 'the_button'));
		
		$this->exclude = get_option('share_buttons_exclude');
		
		$this->show_on_post = get_option('share_buttons_show_on_posts');
		$this->show_on_page = get_option('share_buttons_show_on_pages');
        
        $this->vkontakte_show = get_option('vkontakte_button_show');
        $this->vkontakte_button_type = get_option('vkontakte_button_type');
        
        $this->vkontakte_like_api = get_option('vkontakte_like_api');
        $this->vkontakte_like_show = get_option('vkontakte_like_button_show');
        $this->vkontakte_like_type = get_option('vkontakte_like_type');
        $this->vkontakte_like_verb = get_option('vkontakte_like_verv');
        
        $this->odnoklassniki_show = get_option('odnoklassniki_button_show');
        $this->odnoklassniki_button_type = get_option('odnoklassniki_button_type');
        
        $this->mailru_show = get_option('mailru_button_show');
        $this->mailru_button_type = get_option('mailru_button_type');
        
        $this->facebook_like_show = get_option('facebook_like_button_show');
        $this->facebook_like_layout = get_option('facebook_like_layout');
        $this->facebook_like_color = get_option('facebook_like_color');
        $this->facebook_like_faces = get_option('facebook_like_faces');
        $this->facebook_like_width = get_option('facebook_like_width');
        $this->facebook_like_height = get_option('facebook_like_height');        
        $this->facebook_like_verb = get_option('facebook_like_verb');
        
        $this->facebook_share_show = get_option('facebook_share_button_show');
        $this->facebook_share_button_type = get_option('facebook_share_button_type');
        
        $this->twitter_show = get_option('twitter_button_show');
        $this->twitter_button_type = get_option('twitter_button_type');
        $this->twitter_via = get_option('twitter_via');                        
        
        $this->logo_share = get_option('logo_share');
        
        
	
	}
    
	function install() {
		//create options

        add_option('share_buttons_position', 'left');
        add_option('share_buttons_vposition', 'bottom');
		
        add_option('share_buttons_show_on_posts', TRUE);
        add_option('share_buttons_show_on_pages', TRUE);
	
        add_option('share_buttons_noparse', 'true');
        add_option('share_buttons_exclude', '');
        
        add_option('vkontakte_button_show', TRUE);
        add_option('vkontakte_button_type', 'button');
        
        add_option('vkontakte_like_api','');
        add_option('vkontakte_like_button_show', FALSE);
        add_option('vkontakte_like_type','full');
        add_option('vkontakte_like_verb', '��� ��������');
        
        add_option('odnoklassniki_button_show', TRUE);
        add_option('odnoklassniki_button_type', 'button');
        
        add_option('mailru_button_show', TRUE);
        add_option('mailru_button_type', 'button');

        add_option('facebook_like_button_show', TRUE);
        add_option('facebook_like_layout', 'standart');
        add_option('facebook_like_color', 'light');
        add_option('facebook_like_faces', FALSE);
        add_option('facebook_like_width', 450);
        add_option('facebook_like_height', 30);        
        add_option('facebook_like_verb', 'like');
        
        add_option('facebook_share_button_show', TRUE);
        add_option('facebook_share_button_type', 'button');
        
        add_option('twiteer_button_show', TRUE);
        add_option('twitter_button_type', 'horizontal');
        add_option('twitter_via','');                               
        
        add_option('logo_share', 'logo.png');                

	}

	function register_settings() {
		//register our settings

		register_setting( 'sb-settings-group', 'share_buttons_position' );
		register_setting( 'sb-settings-group', 'share_buttons_vposition' );
		
		register_setting( 'sb-settings-group', 'share_buttons_show_on_posts' );
		register_setting( 'sb-settings-group', 'share_buttons_show_on_pages' );
		
		register_setting( 'sb-settings-group', 'share_buttons_exclude' );
        
        register_setting( 'sb-settings-group', 'vkontakte_button_show' );
        register_setting( 'sb-settings-group', 'vkontakte_button_type');
        
        register_setting( 'sb-settings-group', 'vkontakte_like_api' );
        register_setting( 'sb-settings-group', 'vkontakte_like_button_show' );
        register_setting( 'sb-settings-group', 'vkontakte_like_type');
        register_setting( 'sb-settings-group', 'vkontakte_like_verb');                
        
        register_setting( 'sb-settings-group', 'odnoklassniki_button_show' );
        register_setting( 'sb-settings-group', 'odnoklassniki_button_type');
        
        register_setting( 'sb-settings-group', 'mailru_button_show' );
        register_setting( 'sb-settings-group', 'mailru_button_type');
        
        register_setting( 'sb-settings-group', 'facebook_like_button_show' );
        register_setting( 'sb-settings-group', 'facebook_like_layout');
        register_setting( 'sb-settings-group', 'facebook_like_color');
        register_setting( 'sb-settings-group', 'facebook_like_faces');
        register_setting( 'sb-settings-group', 'facebook_like_width');
        register_setting( 'sb-settings-group', 'facebook_like_height');        
        register_setting( 'sb-settings-group', 'facebook_like_verb');
        
        register_setting( 'sb-settings-group', 'facebook_share_button_show' );
        register_setting( 'sb-settings-group', 'facebook_share_button_type');
        
        register_setting( 'sb-settings-group', 'twitter_button_show' );
        register_setting( 'sb-settings-group', 'twitter_button_type' );
        register_setting( 'sb-settings-group', 'twitter_via');                                                        
        
        register_setting( 'sb-settings-group', 'logo_share');                
	}
	
	// Add options page
	function create_menu() 	{
		//create new menu in Settings section
		add_options_page(__('Share Buttons Plugin Settings', $this->plugin_domain), __('Share Buttons', $this->plugin_domain), 'administrator', __FILE__, array(&$this, 'settings_page'));	
	}
	
	// Settings page
	function settings_page() {
		$fb_like_layout = get_option('facebook_like_layout');
        $fb_like_color = get_option('facebook_like_color');
        $fb_like_verb = get_option('facebook_like_verb');
        $vk_like_verb = get_option('vkontakte_like_verb');                
        $sb_pos = get_option('share_buttons_position');
		$sb_vpos = get_option('share_buttons_vposition');
			
    	include('share-buttons-options.php');
	}
	

	function place_button($content) {
		// Here we place button on the page
		global $post;
		$exclude_ids = explode(",", $this->exclude);
		
		// Looking for exclusion
		foreach($exclude_ids as $id) 
			if ($post->ID == $id)
				return $content;
				
		$clear_button = $this->the_button();
        $like_button = $this->the_like_button();
		$pos = get_option('share_buttons_position');
		$vpos = get_option('share_buttons_vposition');
		if ($pos == 'right')
			// right alignment
            $the_button = "<div name=\"#\" style=\"float: $pos; margin: 0px 0px 0px 0px; \">\r\n$clear_button\r\n</div><div style=\"clear:both;\"></div>";
		else
			// left alignment
            $the_button = "<div name=\"#\" style=\"float: $pos; margin: 0px 0px 0px 0px;\">\r\n$clear_button\r\n</div><div style=\"clear:both;\"></div>";
            $the_button .= "<div name=\"#\" style=\"float: $pos; margin: 0px 0px 0px 0px;\">\r\n$like_button\r\n</div><div style=\"clear:both;\"></div>";            
		
		//@author: Eldar
		//@reason: enable it for all pages
		if (is_single() && $this->show_on_post || is_page() && $this->show_on_page) { 
			if ($vpos == 'top')
				// place button before post
				return $the_button . $content;
			else
				// after post                  				
                return $content . $the_button;
		}
		return $content;
	}
	
	// Localization support
	function load_domain()
	{
		$mofile = dirname(__FILE__) . '/lang/' . $this->plugin_domain . '-' . get_locale() . '.mo';
		
		load_textdomain($this->plugin_domain, $mofile);
	}
}
else :

	exit(__('Class Share Buttons already declared!', $this->plugin_domain));
	
endif;


if (class_exists('ShareButtons')) :
	
	$ShareButtons = new ShareButtons();

endif;