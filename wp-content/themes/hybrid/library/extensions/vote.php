<?php
class Vote
{
    var $template_dir;
    var $default_template;

    function Vote()
    {
        add_action('init', array(&$this, init));
        add_action('vote', array(&$this, print_vote));
        $this->template_dir = THEME_EXTENSIONS.'/vote/';
        $this->default_template = $this->template_dir.'default-template.php';
    }

    function print_vote()
    {
        include($this->default_template);
    }

    function init()
    {
        wp_register_script('vote-script', THEME_URI.'/library/extensions/vote/js/script.js');
        wp_enqueue_script('vote-script');
    }
}

$vote = new Vote();
        