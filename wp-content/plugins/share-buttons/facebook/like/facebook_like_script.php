<?php

    global $post;

    $url    = get_permalink($post->ID);
    $layout = get_option('facebook_like_layout');
    $verb   = get_option('facebook_like_verb');
    $width  = get_option('facebook_like_width');
    $height = get_option('facebook_like_height');
    $color  = get_option('facebook_like_color');
    $faces  = get_option('facebook_like_faces');

    if($faces==1) { $faces='true'; } else { $faces='false'; }
    
    $button_code .= '<div style="clear:both;"></div>';
    $button_code .= "\r\n";    
    $button_code .= '<div>';
    $button_code .= "\r\n";    
    $button_code .= '<iframe src="http://www.facebook.com/plugins/like.php?href='.$url.'&amp;layout='.$layout.'&amp;show_faces='.$faces.'&amp;width='.$width.'&amp;action='.$verb.'&amp;font=tahoma&amp;colorscheme='.$color.'&amp;height='.$height.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'.$width.'px; height:'.$height.'px;" allowTransparency="true"></iframe>';
    $button_code .= "\r\n";    
    $button_code .= '</div>';
    $button_code .= "\r\n";    
?>