<?php

    global $post;

    $url = get_permalink($post->ID); 
    $type = get_option('facebook_share_button_type');

    if ($type=='myicon') {
        $button_code .= '<div class="fb-myicon">';
        $button_code .= "<a href=\"#facebook\" name=\"facebook\" onclick=\"new_window('http://www.facebook.com/sharer.php?u=$url');\">";
        $button_code .= '<img src="'.$this->plugin_url.'images/social/32px/facebook.png" /></a></div>';
        $button_code .= "\r\n";                    
    } else if($type=='button') {
        $button_code .= '<div class="fb-share-button"><a name="fb_share" type="'.$type.'" share_url="'.$url.'">'.__('Share',$this->plugin_domain).'</a></div>';
        $button_code .= "\r\n";                  
    } else if($type=='icon') {
        $button_code .= '<div class="fb-myicon">';
        $button_code .= "\r\n";        
        $button_code .= "<a href=\"#facebook\" name=\"facebook\" onclick=\"new_window('http://www.facebook.com/sharer.php?u=$url');\">";
        $button_code .= "\r\n";        
        $button_code .= '<img src="'.$this->plugin_url.'images/social/20px/facebook.png" /></a></div>';
        $button_code .= "\r\n";                
    }
    
?>