<link href="<?php echo $this->plugin_url;?>upload/css/styles.css" rel="stylesheet" type="text/css" media="all" />
<link href="<?php echo $this->plugin_url;?>share-buttons.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="<?php echo $this->plugin_url;?>upload/scripts/ajaxupload.js"></script>
<script src="http://code.jquery.com/jquery-latest.min.js"></script>

<script>
jQuery(document).ready(function($){
        url = "<?php echo $this->plugin_url;?>";
        if($("input[name='vkontakte_button_show']").attr('checked')) {
            $("#vkontakte_sample_buttons").show();
        } else {
            $("#vkontakte_sample_buttons").hide();
        }
        
        if($("input[name='vkontakte_like_button_show']").attr('checked')) {
            $("#vkontakte_like_sample_buttons").show();
        } else {
            $("#vkontakte_like_sample_buttons").hide();
        }
        
        
        if($("input[name='odnoklassniki_button_show']").attr('checked')) {
            $("#odnoklassniki_sample_buttons").show();
        } else {
            $("#odnoklassniki_sample_buttons").hide();
        }
        
        if($("input[name='mailru_button_show']").attr('checked')) {
            $("#mailru_sample_buttons").show();
        } else {
            $("#mailru_sample_buttons").hide();
        }        
        
        if($("input[name='facebook_like_button_show']").attr('checked')) {
            $("#facebook_like_button").show();
        } else {
            $("#facebook_like_button").hide();
        }
        
        if($("input[name='facebook_share_button_show']").attr('checked')) {
            $("#facebook_share_button").show();
        } else {
            $("#facebook_share_button").hide();
        }
        
        if($("input[name='twitter_button_show']").attr('checked')) {
            $("#twitter_sample_buttons").show();
        } else {
            $("#twitter_sample_buttons").hide();
        }                    
        
        $("input[name='vkontakte_button_show']").change(function(){
        if (this.checked) {
		  $("#vkontakte_sample_buttons").fadeIn();
        } else {
		  $("#vkontakte_sample_buttons").fadeOut();
        }});

        $("input[name='vkontakte_like_button_show']").change(function(){
        if (this.checked) {
		  $("#vkontakte_like_sample_buttons").fadeIn();
        } else {
		  $("#vkontakte_like_sample_buttons").fadeOut();
        }});        

        $("input[name='odnoklassniki_button_show']").change(function(){
        if (this.checked) {
		  $("#odnoklassniki_sample_buttons").fadeIn();
        } else {
		  $("#odnoklassniki_sample_buttons").fadeOut();
        }});
        
        $("input[name='mailru_button_show']").change(function(){
        if (this.checked) {
		  $("#mailru_sample_buttons").fadeIn();
        } else {
		  $("#mailru_sample_buttons").fadeOut();
        }}); 
        
        $("input[name='facebook_like_button_show']").change(function(){
        if (this.checked) {
		  $("#facebook_like_button").fadeIn();
        } else {
		  $("#facebook_like_button").fadeOut();
        }});
        
        $("input[name='facebook_share_button_show']").change(function(){
        if (this.checked) {
		  $("#facebook_share_button").fadeIn();
        } else {
		  $("#facebook_share_button").fadeOut();
        }});
        
        $("input[name='twitter_button_show']").change(function(){
        if (this.checked) {
		  $("#twitter_sample_buttons").fadeIn();
        } else {
		  $("#twitter_sample_buttons").fadeOut();
        }});                          
       
        $("#pic").each(function(){
            layout = $("#facebook_like_layout option:selected").val();
            verb = $("#facebook_like_verb option:selected").val();
            color = $("#facebook_like_color option:selected").val();
            url = "<?php echo $this->plugin_url;?>";
            image_url = url+'facebook/like/sample_images/'+layout+'-'+verb+'-'+color+'.png';
            this.src = image_url;
        });
        
        $("#vk_pic_like").each(function(){
            type = $("#vkontakte_like_type:checked").val();
            verb = $("#vkontakte_like_verb option:selected").val();
            url = "<?php echo $this->plugin_url;?>";
            image_url = url+'vkontakte/like/sample_images/'+type+'-'+verb+'.png';
            this.src = image_url;
        });        
        
        $("#facebook_like_layout, #facebook_like_verb, #facebook_like_color").change(function(){
            var src = $("#pic").attr('src');
            
            url = "<?php echo $this->plugin_url;?>";
            layout = $("#facebook_like_layout option:selected").val();
            verb = $("#facebook_like_verb option:selected").val();
            color = $("#facebook_like_color option:selected").val();
            image_url = url+'facebook/like/sample_images/'+layout+'-'+verb+'-'+color+'.png';
            var tmp = url+'facebook/like/sample_images/'+layout+'-'+verb+'-'+color+'.png';
            $("#pic").fadeOut("slow", function () {
            $("#pic").attr("src", tmp);
            });
            $("#pic").fadeIn("slow");
            $("#tmp").val(src);
            //$("#pic").attr('src',image_url).fadeIn('slow');            
        });
        
        $("#vkontakte_like_verb, #vkontakte_like_type").change(function(){
            var src = $("#vk_pic_like").attr('src');
            
            url = "<?php echo $this->plugin_url;?>";
            type = $('#vkontakte_like_type:checked').val();
            verb = $("#vkontakte_like_verb option:selected").val();
            image_url = url+'vkontakte/like/sample_images/'+type+'-'+verb+'.png';
            var tmp = url+'vkontakte/like/sample_images/'+type+'-'+verb+'.png';
            $("#vk_pic_like").fadeOut("slow", function () {
            $("#vk_pic_like").attr("src", tmp);
            });
            $("#vk_pic_like").fadeIn("slow");
            $("#tmp").val(src);
            //$("#pic").attr('src',image_url).fadeIn('slow');            
        });        

 });
</script>
<div>
    <div class="wrap">
        <div style="margin-right: 160px;">
            <h2><?php _e('Share Buttons Settings', $this->plugin_domain) ?></h2>
            <div id="container">
                <br />
                <fieldset class="fieldset_image">
                    <legend><?php _e('Upload picture for your site-logo', $this->plugin_domain) ?></legend>
                    <div id="left_col">
                        <form action="<?php echo $this->plugin_url;?>upload/scripts/ajaxupload.php" method="post" name="sleeker" id="sleeker" enctype="multipart/form-data">
                            <input type="hidden" name="maxSize" value="7291456" />
                            <input type="hidden" name="maxW" value="150" />
                            <input type="hidden" name="fullPath" value="<?php echo $this->plugin_url;?>upload/uploads/" />
                            <input type="hidden" name="relPath" value="<?php echo dirname(__FILE__);?>/upload/uploads/" />
                            <input type="hidden" name="colorR" value="255" />
                            <input type="hidden" name="colorG" value="255" />
                            <input type="hidden" name="colorB" value="255" />
                            <input type="hidden" name="maxH" value="150" />
                            <input type="hidden" name="filename" value="filename" />
                            <input type="file"  size="40" id="file_input" name="filename" onchange="ajaxUpload(this.form,'<?php echo $this->plugin_url;?>upload/scripts/ajaxupload.php?filename=name&amp;maxSize=9999999999&amp;maxW=200&amp;fullPath=<?php echo $this->plugin_url;?>upload/uploads/&amp;relPath=../uploads/&amp;colorR=255&amp;colorG=255&amp;colorB=255&amp;maxH=300','upload_area','File Uploading Please Wait...&lt;br /&gt;&lt;img src=\'<?php echo $this->plugin_url;?>upload/images/loader_light_blue.gif\' width=\'128\' height=\'15\' border=\'0\' /&gt;','&lt;img src=\'upload/images/error.gif\' width=\'16\' height=\'16\' border=\'0\' /&gt; Error in Upload, check settings and path info in source code.'); return false;" />
                        </form>
                        <div style="width: 300px;"><small><?php _e('Files must be <b>.jpg, .gif, .png</b> extension, the desired size of <b>100x100 pixels</b>.',$this->plugin_domain);?></small></div>
                    </div>
                    <div id="right_col">
                        <?php if(file_exists(dirname(__FILE__).'/upload/uploads/logo.png')) { ?>
                            <div id="upload_area"><img src="<?php echo $this->plugin_url;?>upload/uploads/logo.png" /></div>
                        <?php } else { ?>
                            <div id="upload_area"><img src="<?php echo $this->plugin_url;?>images/other/trans.png" width="150px" height="150px"/></div>
                        <?php } ?>
                    </div>
                    <div class="clear"></div>
                    <br />
                </fieldset>
            </div>            

            <div class="for_example">
                <div><?php _e('Please use the buttons similar to each other, for example:',$this->plugin_domain);?></div>
                <div class="one_num">1.</div>
                <div class="one_img"><img src="<?php echo $this->plugin_url;?>images/example/countable.png" /></div>
                <div style="clear: both;"></div>
                <br />
                <div class="two_num">2.</div>
                <div class="two_img"><img src="<?php echo $this->plugin_url;?>images/example/modern_icon.png" /></div>
                <div style="clear: both;"></div>
                <br />
                <div class="three_num">3.</div>
                <div class="three_img"><img src="<?php echo $this->plugin_url;?>images/example/mini_icon.png" /></div>
                <div style="clear: both;"></div>                                
            </div>
            <form method="post" action="options.php" name="change_image" id="change_image">
            <?php settings_fields( 'sb-settings-group' ); ?>
            <fieldset class="fieldset_position">
                <legend><?php _e('Position Share Buttons', $this->plugin_domain);?></legend>
                <table class="form-table">
                    <tr valign="top">
                    <th scope="row"><label for="share_buttons_position"><?php _e('Button horizontal position', $this->plugin_domain) ?></label></th>
                        <td>
                            <select name="share_buttons_position" id="share_buttons_position" value="<?php echo $sb_pos; ?>">
                                <option <?php if($sb_pos == 'right') echo("selected=\"selected\""); ?> value="right"><?php _e('Right', $this->plugin_domain) ?></option>
                                <option <?php if($sb_pos == 'left') echo("selected=\"selected\""); ?> value="left"><?php _e('Left', $this->plugin_domain) ?></option>
                            </select>
                            <span class="description"><?php _e('Select which side you want to display the button: right or left', $this->plugin_domain) ?></p></span>
                        </td>
                    </tr>
                    <tr valign="top">
                    <th scope="row"><label for="share_buttons_vposition"><?php _e('Button vertical position', $this->plugin_domain) ?></label></th>
                        <td>
                            <select name="share_buttons_vposition" id="share_buttons_vposition" value="<?php echo $sb_vpos; ?>">
                                <option <?php if($sb_vpos == 'top') echo("selected=\"selected\""); ?> value="top"><?php _e('On top of post', $this->plugin_domain) ?></option>
                                <option <?php if($sb_vpos == 'bottom') echo("selected=\"selected\""); ?> value="bottom"><?php _e('On bottom of post', $this->plugin_domain) ?></option>
                            </select>
                            <span class="description"><?php _e('Sets up before or after post/page button are shown', $this->plugin_domain) ?></p></span>
                        </td>
                    </tr>
                    <tr valign="top">
                    <th scope="row"><?php _e('The Button displays on', $this->plugin_domain) ?></th>
                        <td>
                            <label for="share_buttons_show_on_posts">
                                <input name="share_buttons_show_on_posts" type="checkbox" id="share_buttons_show_on_posts" value="1" <?php checked(TRUE, $this->show_on_post); ?> />
                                <?php _e('Posts', $this->plugin_domain) ?>
                            </label>
                            <br />
                            <label for="share_buttons_show_on_pages">
                                <input name="share_buttons_show_on_pages" type="checkbox" id="share_buttons_show_on_pages" value="1" <?php checked(TRUE, $this->show_on_page); ?> />
                                <?php _e('Pages', $this->plugin_domain) ?>
                            </label>
                            <br />
                        </td>
                    </tr>
                    <tr valign="top">
                    <th scope="row"><label for="share_buttons_exclude"><?php _e('Exclude pages and posts with IDs', $this->plugin_domain) ?></label></th>
                        <td>
                            <input type="text" name="share_buttons_exclude" value="<?php echo esc_attr($this->exclude); ?>" class="regular-text" />
                            <span class="description"><?php _e('Specify IDs of pages and posts which should stay without buttons (separated by commas, eg <code>4, 8, 15, 16, 23, 42</code>)', $this->plugin_domain) ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <br />

<!-- Vkontakte.ru Share Buttons -->
            <fieldset class="fieldset_social">
                <legend><?php _e('Vkontakte.ru Share Button', $this->plugin_domain) ?></legend>
<!-- Vkontakte.ru Like Button -->
                <div class="head_social">
                    <input name="vkontakte_like_button_show" type="checkbox" id="vkontakte_like_show" value="1" <?php checked(TRUE, $this->vkontakte_like_show); ?> />
                    <?php _e('Show Vkontakte.ru Like Button', $this->plugin_domain) ?>
                </div>

                <div class="body_social" id="vkontakte_like_sample_buttons">

                    <div>
                        <img name="vk_pic_like" id="vk_pic_like" src="<?php echo $this->plugin_url;?>/vkontakte/like/sample_images/full-like.png" />
                    </div>
                    <br />
                    <div>
                        <div><?php _e('<b>API ID:</b>', $this->plugin_domain); ?>&nbsp;<span><?php _e('You can get your <b>"api_id"</b> on this <b><a href="http://vkontakte.ru/apps.php?act=add&site=1">link</a></b>',$this->plugin_domain);?></span></div>
                        <div><input type="text" name="vkontakte_like_api" value="<?php echo esc_attr($this->vkontakte_like_api);?>" class="regular-text" />&nbsp;<span style="color: red;"><?php _e('<b>Required Field</b>', $this->plugin_domain);?></span></div>
                    </div>
                    <br />                   
                    <div>
                        <div style="float: left; width:25px;"><input type="radio" name="vkontakte_like_type" id="vkontakte_like_type" value="full" <?php echo (get_option('vkontakte_like_type') == 'full' ? 'checked' : ''); ?> /></div>
                        <div style="float: left;"><?php _e('Button with textable counter', $this->plugin_domain);?></div>
                        <div style="clear: both;"></div>
                    </div>
                    <div>
                        <div style="float: left; width:25px;"><input type="radio" name="vkontakte_like_type" id="vkontakte_like_type" value="button" <?php echo (get_option('vkontakte_like_type') == 'button' ? 'checked' : ''); ?> /></div>
                        <div style="float: left;"><?php _e('Button with mini counter',$this->plugin_domain);?></div>
                        <div style="clear: both;"></div>
                    </div>
                    <div>
                        <div style="float: left; width:25px;"><input type="radio" name="vkontakte_like_type" id="vkontakte_like_type" value="mini" <?php echo (get_option('vkontakte_like_type') == 'mini' ? 'checked' : ''); ?> /></div>
                        <div style="float: left;"><?php _e('Mini button',$this->plugin_domain);?></div>
                        <div style="clear: both;"></div>
                    </div>
                    <div>
                        <div style="float: left; width:25px;"><input type="radio" name="vkontakte_like_type" id="vkontakte_like_type" value="vertical" <?php echo (get_option('vkontakte_like_type') == 'vertical' ? 'checked' : ''); ?> /></div>
                        <div style="float: left;"><?php _e('Mini button and counter bottom',$this->plugin_domain);?></div>
                        <div style="clear: both;"></div>
                    </div>
                    <br />
                    <div>
                        <div><?php _e('Verb to display', $this->plugin_domain) ?></div>
                        <div>
                            <select name="vkontakte_like_verb" id="vkontakte_like_verb" value="<?php echo $vk_like_verb; ?>">
                                <option <?php if($vk_like_verb == 'like') echo("selected=\"selected\""); ?> value="like"><?php _e('Like', $this->plugin_domain) ?></option>
                                <option <?php if($vk_like_verb == 'recommend') echo("selected=\"selected\""); ?> value="recommend"><?php _e('Interesting', $this->plugin_domain) ?></option>
                            </select>           
                        </div>    
                    </div>
                </div>
                <br />

<!-- Vkontakte.ru Share Button -->
                <div class="head_social">
                    <input name="vkontakte_button_show" type="checkbox" id="vkontakte_show" value="1" <?php checked(TRUE, $this->vkontakte_show); ?> />
                    <?php _e('Show Vkontakte.ru Share Button', $this->plugin_domain) ?>
                </div>

                <div class="body_social" id="vkontakte_sample_buttons">
                    <div>
                        <div style="margin-bottom:5px;"><?php _e("Modern Icon:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px;margin-top:8px;"><input type="radio" name="vkontakte_button_type" value="myicon" <?php echo (get_option('vkontakte_button_type') == 'myicon' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>images/social/32px/vkontakte.png" /></div>
                        <div style="clear: both;"></div>
                    </div>
                    <div>
                        <div style="margin-bottom:5px; margin-top:10px;"><?php _e("Mini Icon:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px;margin-top: 3px;"><input type="radio" name="vkontakte_button_type" value="custom" <?php echo (get_option('vkontakte_button_type') == 'custom' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>images/social/20px/vkontakte.png" /></div>
                        <div style="clear: both;"></div>
                    </div>                    
                    <div>
                        <div style="margin-bottom:5px;margin-top:10px;"><?php _e("Round button with counter:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px; margin-top:2px;"><input type="radio" name="vkontakte_button_type" value="round" <?php echo (get_option('vkontakte_button_type') == 'round' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>/vkontakte/sample_images/round.png" /></div>
                        <div style="clear: both;"></div>
                    </div>
                    <div>
                        <div style="margin-bottom:5px;margin-top:10px;"><?php _e("Round button without counter:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px; margin-top:2px;"><input type="radio" name="vkontakte_button_type" value="round_nocount" <?php echo (get_option('vkontakte_button_type') == 'round_nocount' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>/vkontakte/sample_images/round_nocount.png" /></div>
                        <div style="clear: both;"></div>
                    </div>
                    <div>
                        <div style="margin-bottom:5px; margin-top:10px;"><?php _e("Rectangle button with counter:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px; margin-top:3px;"><input type="radio" name="vkontakte_button_type" value="button" <?php echo (get_option('vkontakte_button_type') == 'button' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>/vkontakte/sample_images/button.png" /></div>
                        <div style="clear: both;"></div>
                    </div>
                    <div>
                        <div style="margin-bottom:5px; margin-top:10px;"><?php _e("Rectangle button without counter:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px;margin-top:3px;"><input type="radio" name="vkontakte_button_type" value="button_nocount" <?php echo (get_option('vkontakte_button_type') == 'button_nocount' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>/vkontakte/sample_images/button_nocount.png" /></div>
                        <div style="clear: both;"></div>
                    </div>
                </div>
                <br />
            </fieldset>
            <br />

<!-- Odnoklassniki.ru Share Buttons -->
            <fieldset class="fieldset_social">
                <legend><?php _e('Odnoklassniki Share Button', $this->plugin_domain) ?></legend>
                <div class="head_social">
                    <input name="odnoklassniki_button_show" type="checkbox" id="odnoklassniki_show" value="1" <?php checked(TRUE, $this->odnoklassniki_show); ?> />
                    <?php _e('Show Odnoklassniki.ru Share Button', $this->plugin_domain) ?>
                </div>
                <div class="body_social" id="odnoklassniki_sample_buttons">
                    <div>
                        <div style="margin-bottom:5px;"><?php _e("Modern Icon:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px; margin-top:8px;"><input type="radio" name="odnoklassniki_button_type" value="myicon" <?php echo (get_option('odnoklassniki_button_type') == 'myicon' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>images/social/32px/odnoklassniki.png" /></div>
                        <div style="clear: both;"></div>
                    </div>
                    <div>
                        <div style="margin-bottom:5px; margin-top:10px;"><?php _e("Mini Icon:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px; margin-top:3px;"><input type="radio" name="odnoklassniki_button_type" value="icon" <?php echo (get_option('odnoklassniki_button_type') == 'icon' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>images/social/20px/odnoklassniki.png" /></div>
                        <div style="clear: both;"></div>
                    </div>                    
                    <div>
                        <div style="margin-bottom:5px;margin-top:10px;"><?php _e("Button with counter:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px; margin-top:2px;"><input type="radio" name="odnoklassniki_button_type" value="button" <?php echo (get_option('odnoklassniki_button_type') == 'button' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>/odkl/sample_images/button.png" /></div>
                        <div style="clear: both;"></div>
                    </div>
                    <div>
                        <div style="margin-bottom:5px;margin-top:10px;"><?php _e("Button without counter:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px; margin-top:2px;"><input type="radio" name="odnoklassniki_button_type" value="button_nocount" <?php echo (get_option('odnoklassniki_button_type') == 'button_nocount' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>/odkl/sample_images/button_nocount.png" /></div>
                        <div style="clear: both;"></div>
                    </div>
                </div>
                <br />
            </fieldset>
            <br />

<!-- Mail.ru Share Buttons -->
            <fieldset class="fieldset_social">
                <legend><?php _e('Mail.ru Share Button', $this->plugin_domain) ?></legend>
                <div class="head_social">
                    <input name="mailru_button_show" type="checkbox" id="mailru_show" value="1" <?php checked(TRUE, $this->mailru_show); ?> />
	               <?php _e('Show Mail.ru Share Button', $this->plugin_domain) ?>
                </div>

                <div class="body_social" id="mailru_sample_buttons">
                    <div>
                        <div style="margin-bottom:5px;"><?php _e("Modern Icon:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px;margin-top:8px;"><input type="radio" name="mailru_button_type" value="myicon" <?php echo (get_option('mailru_button_type') == 'myicon' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>images/social/32px/mailru.png" /></div>
                        <div style="clear: both;"></div>
                    </div>
                    <div>
                        <div style="margin-bottom:5px;margin-top:10px;"><?php _e("Mini Icon:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px; margin-top:2px;"><input type="radio" name="mailru_button_type" value="link_notext" <?php echo (get_option('mailru_button_type') == 'link_notext' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>images/social/20px/mailru.png" /></div>
                        <div style="clear: both;"></div>
                    </div>                     
                    <div>
                        <div style="margin-bottom:5px;margin-top:10px;"><?php _e("Button with counter:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px; margin-top:2px;"><input type="radio" name="mailru_button_type" value="button" <?php echo (get_option('mailru_button_type') == 'button' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>/mailru/sample_images/button.png" /></div>
                        <div style="clear: both;"></div>
                    </div>
                    <div>
                        <div style="margin-bottom:5px;margin-top:10px;"><?php _e("Button without counter:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px; margin-top:2px;"><input type="radio" name="mailru_button_type" value="button_nocount" <?php echo (get_option('mailru_button_type') == 'button_nocount' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>/mailru/sample_images/button_nocount.png" /></div>
                        <div style="clear: both;"></div>
                    </div>   
                </div>
                <br />
            </fieldset>
            <br />

<!-- Facebook.com Share Buttons -->

            <fieldset class="fieldset_social">
                <legend><?php _e('Facebook Share Button', $this->plugin_domain) ?></legend>
                <div class="head_social">
                    <input name="facebook_like_button_show" type="checkbox" id="facebook_like_show" value="1" <?php checked(TRUE, $this->facebook_like_show); ?> />
                    <?php _e('Show Facebook Like Button', $this->plugin_domain) ?>
                </div>
                <br />
<!-- Facebook.com Like Button -->
                <div class="body_social" id="facebook_like_button">
                    <div>
                        <img name="pic" id="pic" src="<?php echo $this->plugin_url;?>/facebook/like/sample_images/standart-like-light.png" />
                    </div>
                    <br />
                    <div><?php _e('Layout style', $this->plugin_domain) ?>&nbsp;<span class="description">(<?php _e('Determines the size and amount of social context next to the button', $this->plugin_domain) ?>)</span></div>
                    <div>
                        <select name="facebook_like_layout" id="facebook_like_layout" value="<?php echo $fb_like_layout; ?>">
                            <option <?php if($fb_like_layout == 'standart') echo("selected=\"selected\""); ?> value="standart"><?php _e('standart', $this->plugin_domain) ?></option>
                            <option <?php if($fb_like_layout == 'button_count') echo("selected=\"selected\""); ?> value="button_count"><?php _e('button_count', $this->plugin_domain) ?></option>
                            <option <?php if($fb_like_layout == 'box_count') echo("selected=\"selected\""); ?> value="box_count"><?php _e('box_count', $this->plugin_domain) ?></option>
                        </select>           
                    </div>
                    <br />
                    <div>
                        <div><?php _e('Show Faces');?>&nbsp;<span class="description">(<?php _e('Show profile pictures below the button');?>)</span></div>
                        <div>
                            <input name="facebook_like_faces" type="checkbox" id="facebook_like_faces" value="1" <?php checked(TRUE, $this->facebook_like_faces); ?> />
                            <?php _e('Show faces', $this->plugin_domain) ?>
                        </div>    
                    </div>
                    <br />
                    <div>
                        <div><?php _e('Width', $this->plugin_domain); ?>&nbsp;<span class="description">(<?php _e('The width of the plugin, in pixels', $this->plugin_domain);?>)</span></div>
                        <div><input type="text" name="facebook_like_width" value="<?php echo esc_attr($this->facebook_like_width);?>" class="regular-text" /></div>
                    </div>
                    <br />
                    <div>
                        <div><?php _e('Height', $this->plugin_domain); ?>&nbsp;<span class="description">(<?php _e('The height of the plugin, in pixels', $this->plugin_domain);?>)</span></div>
                        <div><input type="text" name="facebook_like_height" value="<?php echo esc_attr($this->facebook_like_height);?>" class="regular-text" /></div>
                    </div>
                    <br />    
                    <div>
                        <div><?php _e('Verb to display', $this->plugin_domain) ?>&nbsp;<span class="description">(<?php _e('The verb to display in the button. Currently only Like and Recommend are supported', $this->plugin_domain) ?>)</span></div>
                        <div>
                            <select name="facebook_like_verb" id="facebook_like_verb" value="<?php echo $fb_like_verb; ?>">
                                <option <?php if($fb_like_verb == 'like') echo("selected=\"selected\""); ?> value="like"><?php _e('like', $this->plugin_domain) ?></option>
                                <option <?php if($fb_like_verb == 'recommend') echo("selected=\"selected\""); ?> value="recommend"><?php _e('recommend', $this->plugin_domain) ?></option>
                            </select>           
                        </div>    
                    </div>
                    <br />
                    <div>
                        <div><?php _e('Color scheme', $this->plugin_domain) ?>&nbsp;<span class="description">(<?php _e('The color scheme of the plugin', $this->plugin_domain) ?>)</span></div>
                        <div>
                            <select name="facebook_like_color" id="facebook_like_color" value="<?php echo $fb_like_color; ?>">
                                <option <?php if($fb_like_color == 'light') echo("selected=\"selected\""); ?> value="light"><?php _e('light', $this->plugin_domain) ?></option>
                                <option <?php if($fb_like_color == 'dark') echo("selected=\"selected\""); ?> value="dark"><?php _e('dark', $this->plugin_domain) ?></option>
                            </select>            
                        </div>
                    </div>
                </div>

<!-- Facebook.com Share Button -->
                    <div class="head_social">
                        <input name="facebook_share_button_show" type="checkbox" id="facebook_share_show" value="1" <?php checked(TRUE, $this->facebook_share_show); ?> />
                        <?php _e('Show Facebook Share Button', $this->plugin_domain) ?>
                    </div>
                    <div class="body_social" id="facebook_share_button">
                        <div>
                            <div style="margin-bottom:5px;"><?php _e("Modern Icon:", $this->plugin_domain ); ?></div>
                            <div style="float: left; width:25px;margin-top:8px;"><input type="radio" name="facebook_share_button_type" value="myicon" <?php echo (get_option('facebook_share_button_type') == 'myicon' ? 'checked' : ''); ?> /></div>
                            <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>images/social/32px/facebook.png" /></div>
                            <div style="clear: both;"></div>
                        </div>
                        <div>
                            <div style="margin-bottom:5px; margin-top:10px;"><?php _e("Mini Icon:", $this->plugin_domain ); ?></div>
                            <div style="float: left; width:25px;margin-top:3px;"><input type="radio" name="facebook_share_button_type" value="icon" <?php echo (get_option('facebook_share_button_type') == 'icon' ? 'checked' : ''); ?> /></div>
                            <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>images/social/20px/facebook.png" /></div>
                            <div style="clear: both;"></div>
                        </div>                        
                        <div>
                            <div style="margin-bottom:5px;margin-top:10px;"><?php _e("Button:", $this->plugin_domain ); ?></div>
                            <div style="float: left; width:25px;"><input type="radio" name="facebook_share_button_type" value="button" <?php echo (get_option('facebook_share_button_type') == 'button' ? 'checked' : ''); ?> /></div>
                            <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>/facebook/share/sample_images/button.png" /></div>
                            <div style="clear: both;"></div>
                        </div>
                    </div>
                    <br />
                </div>
            </fieldset>
            <br />

<!-- Twitter Button Share -->
            <fieldset class="fieldset_social">
                <legend><?php _e('Twitter Share Button', $this->plugin_domain) ?></legend>
                <div class="head_social">
                    <input name="twitter_button_show" type="checkbox" id="twitter_show" value="1" <?php checked(TRUE, $this->twitter_show); ?> />
	               <?php _e('Show Twitter Share Button', $this->plugin_domain) ?>
                </div>
                <div class="body_social" id="twitter_sample_buttons">
                    <div>
                        <div><?php _e('Twitter via', $this->plugin_domain); ?>&nbsp;<span class="description">(<?php _e('Your Nickname without "@"', $this->plugin_domain);?>)</span></div>
                        <div><input type="text" name="twitter_via" value="<?php echo esc_attr($this->twitter_via);?>" class="regular-text" /></div>
                    </div>
                    <div>
                        <div style="margin-bottom:5px;"><?php _e("Modern Icon:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px;margin-top:8px;"><input type="radio" name="twitter_button_type" value="myicon" <?php echo (get_option('twitter_button_type') == 'myicon' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>images/social/32px/twitter.png" /></div>
                        <div style="clear: both;"></div>
                    </div>
                    <div>
                        <div style="margin-bottom:5px;margin-top:10px;"><?php _e("Mini Icon:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px;margin-top:3px;"><input type="radio" name="twitter_button_type" value="mini" <?php echo (get_option('twitter_button_type') == 'mini' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>images/social/20px/twitter.png" /></div>
                        <div style="clear: both;"></div>
                    </div>                      
                    <div>
                        <div style="margin-bottom:5px;margin-top:10px;"><?php _e("Button with counter:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px;margin-top:2px;"><input type="radio" name="twitter_button_type" value="horizontal" <?php echo (get_option('twitter_button_type') == 'horizontal' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>/twitter/sample_images/horizontal.png" /></div>
                        <div style="clear: both;"></div>
                    </div>
                    <div>
                        <div style="margin-bottom:5px;margin-top:10px;"><?php _e("Button without counter:", $this->plugin_domain ); ?></div>
                        <div style="float: left; width:25px;margin-top:3px;"><input type="radio" name="twitter_button_type" value="none" <?php echo (get_option('twitter_button_type') == 'none' ? 'checked' : ''); ?> /></div>
                        <div style="float: left; width:140px;"><img src="<?php echo $this->plugin_url;?>/twitter/sample_images/none.png" /></div>
                        <div style="clear: both;"></div>
                    </div>                      
                </div>
                <br />
            </fieldset>

            <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes', $this->plugin_domain) ?>" />
            </p>
            </form>
        </div>
    </div>
</div>