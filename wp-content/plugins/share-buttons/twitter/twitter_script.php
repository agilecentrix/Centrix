<?php

    global $post;
    
    $title = $post->post_title;

    $type=get_option('twitter_button_type');
    $via = get_option('twitter_via');
    
    if($via!='') { $via_new='&via='.$via; } else { $via_new = ''; }

    if($type=='myicon') {
        $button_code .= '<div class="twitter-myicon">';
        $button_code .= "<a href=\"#twitter\" name=\"twitter\" onclick=\"new_window('http://twitter.com/share?&text=$title%20-%20&url=$url$via_new');\">";
        $button_code .= '<img src="'.$this->plugin_url.'images/social/32px/twitter.png" /></a></div>';
        $button_code .= "\r\n";        
    } else if($type=='horizontal') {
        $button_code .= '<div class="twitter-horizontal"><a href="http://twitter.com/share" class="twitter-share-button" data-count="'.$type.'" data-via="'.$via.'">Tweet</a></div>';
        $button_code .= "\r\n";        
    } else if($type=='none') {
        $button_code .= '<div class="twitter-none"><a href="http://twitter.com/share" class="twitter-share-button" data-count="'.$type.'" data-via="'.$via.'">Tweet</a></div>';
        $button_code .= "\r\n";            
    } else if($type=='mini') {
        $button_code .= '<div class="twitter-myicon">';
        $button_code .= "<a href=\"#twitter\" name=\"twitter\" onclick=\"new_window('http://twitter.com/share?&text=$title%20-%20&url=$url$via_new');\">";
        $button_code .= '<img src="'.$this->plugin_url.'images/social/20px/twitter.png" /></a></div>';
        $button_code .= "\r\n";                
    }

    
?>