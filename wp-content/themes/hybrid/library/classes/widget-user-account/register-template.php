
<div class="modal-content group" id="modal-two-col">

    <div class="modal-column" id="modal-column-left">
        <p class="modal-header"><?php _e('Register');?></p>
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
        <form method="post" role="register" class="digg-form group vertical-form">
            <div id="reg-step-one">
                <div class="row">
                    <span class="placeholder">
                        <label for="user_email">Email Address</label>
                        <input type="text" name="user_email" id="user_email" value="<?php echo $_REQUEST['user_email'];?>" class="text-input placeholder-input" tabindex="1">
                    </span>
                </div>
                <div class="row">
                    <span class="placeholder">
                        <label for="user_name">Username</label>
                        <input type="text" name="user_name" id="user_name" value="<?php echo $_REQUEST['user_name'];?>" maxlength="30" class="text-input placeholder-input" tabindex="2">
                    </span>
                </div>
                <div class="row">
                    <span class="placeholder">
                        <label for="user_password">Password</label>
                        <input type="password" name="user_password" id="user_password" value="<?php echo $_REQUEST['user_password'];?>" class="text-input placeholder-input" tabindex="3">
                    </span>
                </div>
            </div>

            <button type="button" role="login" class="btn btn-primary">Back</button>
            <button type="button" role="register" class="btn btn-primary">Register</button>
            <p class="tos">By creating an account you agree to the
                <a href="http://about.digg.com/terms-use">Terms of Service</a>
                &amp; <a href="http://about.digg.com/privacy">Privacy Policy</a></p>
        </form>
    </div>

</div>