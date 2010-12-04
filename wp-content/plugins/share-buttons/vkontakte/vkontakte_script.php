<?php
        global $post;
		
        $link =  esc_js(get_permalink($post->ID));
		
        $noparse = 'false';
        $text = __('Share',$this->plugin_domain);
        $thumb = get_post_meta($post->ID, 'Thumbnail', $single = true);
        
        $your_logo = $this->plugin_url.'upload/uploads/logo.png';
        $abs_path_logo = $this->plugin_path.'/upload/uploads/logo.png';
        
        $type = get_option('vkontakte_button_type');
        

       	$temp = substr(strip_shortcodes(strip_tags($post->post_content)), 0, 350);
	// Sometimes substr() returns substring with strange symbol in the end which crashes esc_js()
	while (esc_js($temp) == '' && $temp != '')
		$temp = substr($temp, 0, strlen($temp)-1);
	$descr = esc_js($temp);
				
	if (strlen($post->post_content) > 350 && $descr != '')
		$descr .= '...';

        $title = esc_js($post->post_title);

            
        if($type=='myicon' || $type=='custom') {
            $button_code .= "<div class=\"vk-myicon\">\r\n";            
        } else {
            $button_code .= "<div class=\"vk-button\">\r\n";
        }
        
        $button_code .="<script type=\"text/javascript\">\r\n<!--\r\ndocument.write(VK.Share.button(\r\n{\r\n";
		$button_code .= "  url: '$link',\r\n";
		$button_code .= "  title: '$title',\r\n";
		$button_code .= "  description: '$descr'";


        if($thumb!='') {
            $button_code .= ",\r\n image: '$thumb'";
        } else if(file_exists($abs_path_logo)) {
            $button_code .= ",\r\n image: '$your_logo'";
        }

        $button_code .= $noparse == 'true' ? ",\r\n  noparse: $noparse \r\n}, \r\n{\r\n" : "  \r\n}, \r\n{\r\n";
        
		if($type=='round') {
            $type = 'round';
        } else if($type=='round_nocount') {
            $type = 'round_nocount';
        } else if($type=='button') {
            $type = 'button';
        } else if($type=='button_nocount') {
            $type = 'button_nocount';        
        } else if($type=='custom') {
            $type = 'custom';
            $text = '<img src="'.$this->plugin_url.'images/social/20px/vkontakte.png" />';                        	
        } else if($type=='myicon') {
            $type = 'custom';
            $text = '<img src="'.$this->plugin_url.'images/social/32px/vkontakte.png" />';
        }
        $button_code .= "  type: '$type',\r\n";      
        $button_code .= "  text: '$text'\r\n}));";
        $button_code .= "\r\n-->\r\n</script></div>\r\n";

?>