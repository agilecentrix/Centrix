<?php
/**
 * Login/Register Widget Class
 *
 * The Login/Register widget replaces the default WordPress Login/Register widget. This version gives total
 * control over the output to the user by allowing the input of all the arguments
 * 
 * @package Hybrid
 * @subpackage Classes
 */

class Widget_User_Account extends WP_Widget {

	var $prefix;
	var $textdomain;
        var $folder;
	var $login_template;
	var $register_template;
	var $forgot_template;
	var $user_account_template;
        var $js_folder;

        /**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 0.6
	 */
	function Widget_User_Account() {
		$this->prefix = hybrid_get_prefix();
		$this->textdomain = hybrid_get_textdomain();
                $this->folder = THEME_CLASSES.'/widget-user-account/';
                $this->login_template = $this->folder.'login-template.php';
                $this->register_template = $this->folder.'register-template.php';
                $this->user_account_template = $this->folder.'user-account-template.php';
                $this->forgot_template = $this->folder.'forgot-template.php';
                $this->js_folder = THEME_URI.'/library/classes/widget-user-account/js/';

                $widget_ops = array( 'classname' => 'login_register', 'description' => __( 'An advanced widget that gives you total control over the output of login and registration.', $this->textdomain ) );
		$control_ops = array( 'id_base' => "{$this->prefix}-user-account" );
		$this->WP_Widget( "{$this->prefix}-user-account", __( 'User Account', $this->textdomain ), $widget_ops, $control_ops );

                wp_register_script('user-account-script', $this->js_folder.'script.js');

                add_action('init', array(&$this, 'process_request'), 10);
                require_once(ABSPATH . WPINC . '/registration.php');
        }

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 0.6
	 */
	function widget( $args, $instance ) {
            wp_print_scripts('user-account-script');
            ?>
            <div role="user-account">
            <?php
            if ( !is_user_logged_in() )
            {
                include ($this->login_template);
            }
            else
            {
                include ($this->user_account_template);
            }
            ?>
            </div>
            <?php
	}

	/**
	 * processes the request
	 * @since 0.6
	 */
	function process_request() {
            if(isset($_POST['action'])
               && ('user-account' == $_POST['action']))
            {
                $this->ajax_callback();
            }
	}

	/**
	 * ajax callback
	 * @since 0.6
	 */
	function ajax_callback() 
        {
            $callback = $_REQUEST['callback'];
            switch($callback)
            {
                case 'login':
                    $creds = array();
                    $creds['user_login'] = $_REQUEST['user_login'];
                    $creds['user_password'] = $_REQUEST['user_password'];
                    $creds['remember'] = (empty($_REQUEST['remember'])||$_REQUEST['user_password']=='0')?false:true;

                    $user = wp_signon( $creds, false );
                    if ( is_wp_error($user) )
                    {
                        $message = __($user->get_error_message());
                        include($this->login_template);
                    }
                    else
                    {
                        include($this->user_account_template);
                    }
                break;
                case 'register_form':
                     include($this->register_template);
                    break;
                case 'login_form':
                     include($this->login_template);
                    break;
                    break;
                case 'forgot_form':
                     include($this->forgot_template);
                    break;
                case 'register':
                    $user_name = $_REQUEST['user_name'];
                    $user_password = $_REQUEST['user_password'];
                    $user_email = $_REQUEST['user_email'];

                    $user_id = username_exists( $user_name );
                    if ( !$user_id )
                    {
                        $user_id = wp_create_user( $user_name, $user_password, $user_email );
                        if ( !is_wp_error($user_id) )
                        {
                            $message = __('You have successfully registered to Centrix.');
                            include($this->login_template);
                        }
                        else
                        {
                            $message = __($user_id->get_error_message());
                            include($this->register_template);
                        }
                    }
                    else
                    {
                        $message = __('User already exists.');
                        include($this->register_template);
                    }
                    break;
                    case 'forgot':
                        $result = $this->retrieve_password();
                        if(is_wp_error($result))
                        {
                            $message = __($result->get_error_message());
                            include($this->forgot_template);
                        }
                        else
                        {
                            $message = __("dddd");
                            include($this->login_template);
                        }
                    break;
                    case 'logout':
                        wp_logout();
                        include($this->login_template);
                    break;
            }
            exit;
	}

        /**
         * Handles sending password retrieval email to user.
         *
         * @uses $wpdb WordPress Database object
         *
         * @return bool|WP_Error True: when finish. WP_Error on error
         */
        function retrieve_password() {
                global $wpdb, $current_site;

                $errors = new WP_Error();
                if(preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/', $_POST['user_login']))
                {
                    $_POST['user_email'] = $_POST['user_login'];
                    unset($_POST['user_login']);
                }
                
                if ( empty( $_POST['user_login'] ) && empty( $_POST['user_email'] ) )
                        $errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.'));

                if ( strpos($_POST['user_login'], '@') ) {
                        $user_data = get_user_by_email(trim($_POST['user_login']));
                        if ( empty($user_data) )
                                $errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.'));
                } else {
                        $login = trim($_POST['user_login']);
                        $user_data = get_userdatabylogin($login);
                }

                if ( $errors->get_error_code() )
                        return $errors;

                if ( !$user_data ) {
                        $errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or e-mail.'));
                        return $errors;
                }

                // redefining user_login ensures we return the right case in the email
                $user_login = $user_data->user_login;
                $user_email = $user_data->user_email;
                
                $allow = apply_filters('allow_password_reset', true, $user_data->ID);

                if ( ! $allow )
                        return new WP_Error('no_password_reset', __('Password reset is not allowed for this user'));
                else if ( is_wp_error($allow) )
                        return $allow;

                $key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
                if ( empty($key) ) {
                        // Generate something random for a key...
                        $key = wp_generate_password(20, false);
                        do_action('retrieve_password_key', $user_login, $key);
                        // Now insert the new md5 key into the db
                        $wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
                }
                $message = __('Someone has asked to reset the password for the following site and username.') . "\r\n\r\n";
                $message .= network_site_url() . "\r\n\r\n";
                $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
                $message .= __('To reset your password visit the following address, otherwise just ignore this email and nothing will happen.') . "\r\n\r\n";
                $message .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . "\r\n";

                if ( is_multisite() )
                        $blogname = $GLOBALS['current_site']->site_name;
                else
                        // The blogname option is escaped with esc_html on the way into the database in sanitize_option
                        // we want to reverse this for the plain text arena of emails.
                        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

                $title = sprintf( __('[%s] Password Reset'), $blogname );

                $title = apply_filters('retrieve_password_title', $title);
                $message = apply_filters('retrieve_password_message', $message, $key);

                //if ( $message && !wp_mail($user_email, $title, $message) )
                //        wp_die( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') );

                return true;
        }

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 * @since 0.6
	 */
	function update( $new_instance, $old_instance ) {

	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 0.6
	 */
	function form( $instance ) {

	}
}

?>