<?php
    
    
    $type = get_option('mailru_button_type');
        
    if ($type=='myicon') {
            
        $button_code .= '<div class="mailru-myicon">';
        $button_code .= "<a href=\"#mailru\" name=\"mailru\" onclick=\"new_window('http://connect.mail.ru/share?share_url=$url');\">";
        $button_code .= '<img src="'.$this->plugin_url.'images/social/32px/mailru.png" /></a></div>';
        $button_code .= "\r\n";        
                      
    } else if($type=='button') {
            
        $button_code .= '<div class="mailru-button"><a class="mrc__share" type="button_count" href="http://connect.mail.ru/share">'.__('In My World',$this->plugin_domain).'</a></div>';
        $button_code .= "\r\n";        
            
    } else if($type=='button_nocount') {
            
        $button_code .= '<div class="mailru-button-nocount"><a class="mrc__share" type="button" href="http://connect.mail.ru/share">'.__('In My World',$this->plugin_domain).'</a></div>';
        $button_code .= "\r\n";        
            
    } else if($type=='link_notext') {
            
        //$button_code .= '<div class="mailru-link-notext"><a class="mrc__share" type="micro" href="http://connect.mail.ru/share"> </a></div>';
        $button_code .= '<div class="mailru-myicon">';
        $button_code .= "<a href=\"#mailru\" name=\"mailru\" onclick=\"new_window('http://connect.mail.ru/share?share_url=$url');\">";
        $button_code .= '<img src="'.$this->plugin_url.'images/social/20px/mailru.png" /></a></div>';
        $button_code .= "\r\n";
                       
    }        

?>