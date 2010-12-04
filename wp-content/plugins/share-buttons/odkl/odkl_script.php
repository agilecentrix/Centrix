<?php

    global $post;
    $type = get_option('odnoklassniki_button_type');
    $url = get_permalink($post->ID);        
    if($type=='myicon') {

        $button_code .= '<div class="odkl-myicon">';
        $button_code .= "<a href=\"#odnoklassniki\" name=\"odnoklassniki\" onclick=\"new_window('http://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1&st._surl=$url');\">";
        $button_code .= '<img src="'.$this->plugin_url.'images/social/32px/odnoklassniki.png" /></a></div>';
        $button_code .= "\r\n";
                        
    } else if($type=='button') {

        $button_code .= '<div class="odkl-button"><a class="odkl-klass-stat" href="'.$url.'" onclick="ODKL.Share(this);return false;" ><span>0</span></a></div>';
        $button_code .= "\r\n";        
                        
    } else if($type=='button_nocount') {

        $button_code .= '<div class="odkl-button-count"><a class="odkl-klass" href="'.$url.'" onclick="ODKL.Share(this);return false;" >Класс!</a></div>';
        $button_code .= "\r\n";        
            
    } else if($type=='icon') {

        //$button_code .= '<div class="odkl-icon"><a class="odkl-klass-s" href="'.$url.'" onclick="ODKL.Share(this);return false;" ></a></div>';
        $button_code .= '<div class="odkl-myicon">';
        $button_code .= "<a href=\"#odnoklassniki\" name=\"odnoklassniki\" onclick=\"new_window('http://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1&st._surl=$url');\">";
        $button_code .= '<img src="'.$this->plugin_url.'images/social/20px/odnoklassniki.png" /></a></div>';
        $button_code .= "\r\n";                
            
    }


?>