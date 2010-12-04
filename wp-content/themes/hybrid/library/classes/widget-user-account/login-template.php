<div class="modal-content group" id="modal-two-col">
    <div class="modal-column" id="modal-column-left">
        <p class="modal-header"><?php _e('Log in');?> <span><?php _e('to');?> Centrix</span></p>
        <?php if(!empty($message)):?>
        <div class="message-dock error">
            <div class="message-sleeve group">
                <p>
                    <span class="message-text">
                    <?php echo $message;?>
                    </span>
                </p>
            </div>
        </div><br/>
        <?php endif;?>
        <form role="login" method="post" class="digg-form vertical-form group">
            <div class="row">
                <span class="placeholder">
                    <label for="user_login"><?php _e('Username');?></label>
                    <input type="text" name="user_login" id="user_login" value="<?php echo $_REQUEST['user_login'];?>" tabindex="1" class="text-input placeholder-input">
                </span>
            </div>
            <div class="row">
                <span class="placeholder float-left">
                    <label for="user_password"><?php _e('Password');?></label>
                    <input type="password" name="user_password" value="<?php echo $_REQUEST['user_password'];?>" class="text-input placeholder-input" tabindex="2" id="user_password">
                </span>
                <a role="forgot" href="javascript:;" class="inline-link" id="login-forgot-password"><?php _e('Forgot?');?></a>
            </div>
            <div class="row">
                <button role="login" type="submit" name="submit" class="btn btn-primary" id="login-button"><?php _e('Log in');?></button>
                <label class="remember-me"><input type="checkbox" name="remember" checked="checked" tabindex="3"> Keep me logged in</label>
            </div>
            <div class="bottom">
                <p>Don't have an account? <a href="javascript:;" role="register" class="auth-register">Create one.</a></p>
            </div>
        </form>
        <form id="forgot-password-form" method="post" class="invite-form group">
            <div class="row">
                <span class="placeholder">
                    <label for="forgot-password-email">Email Address</label>
                    <input type="text" class="text-input placeholder-input" id="forgot-password-email">
                </span>
            </div>
            <div class="row">
                <button type="submit" name="submit" class="btn btn-primary">Retrieve Password</button>
                <a class="inline-link auth-login">I got it now!</a>
            </div>
        </form>
    </div>
</div>