<?php

    $type = get_option('vkontakte_like_type');
    $verb = get_option('vkontakte_like_verb');
    $api_id = get_option('vkontakte_like_api');

    if($verb=='like') { $verb=0; } else { $verb=1; }

    $button_code .= '<script type="text/javascript">';
    $button_code .= "\r\n";    
    $button_code .= "VK.init({apiId: \"$api_id\", onlyWidgets: true});";
    $button_code .= "\r\n";    
    $button_code .= '</script>';
    $button_code .= "\r\n";    

    $button_code .= '<div id="vk_like"></div>';
    $button_code .= "\r\n";    
    $button_code .= '<script type="text/javascript">';
    $button_code .= "\r\n";    
    $button_code .= "VK.Widgets.Like(\"vk_like\", {type: \"$type\", verb: $verb});";
    $button_code .= "\r\n";    
    $button_code .= '</script>';
    $button_code .= "\r\n";    
?>