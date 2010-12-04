<div class="modal-content group" id="modal-two-col">
    <div class="modal-column" id="modal-column-left">
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
        <form role="forgot" method="post" class="digg-form vertical-form group">
            <div class="row">
                <span class="placeholder">
                    <label for="user_login"><?php _e('Username');?></label>
                    <input type="text" name="user_login" id="user_login" value="" tabindex="1" class="text-input placeholder-input">
                </span>
            </div>
            <div class="row">
                <button role="login" type="submit" class="btn btn-primary"><?php _e('Back');?></button>
                <button role="get_new_password" type="submit" class="btn btn-primary"><?php _e('Get New Password');?></button>
            </div>
        </form>
    </div>
</div>