<?PHP
// don't load directly
if ( !defined('ABSPATH') )
	die('-1');

check_admin_referer('edit-job');
global $wpdb;	
$jobid = (int) $_REQUEST['jobid'];
?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br /></div>
<h2><?php _e('WPeMatico Campaign Settings', 'wpematico'); ?></h2>

<?PHP wpematico_option_submenues(); ?>
<div class="clear"></div>

<form method="post" action="">
<input type="hidden" name="subpage" value="edit" />
<input type="hidden" name="jobid" value="<?PHP echo $jobid;?>" />
<?php
wp_nonce_field('edit-job');
$jobs=get_option('wpematico_jobs');
$jobvalue=wpematico_check_job_vars($jobs[$jobid],$jobid);
?>

<div id="poststuff" class="metabox-holder has-right-sidebar">
	<div class="inner-sidebar">
		<div id="side-sortables" class="meta-box-sortables">

			<div id="posttype" class="postbox">
				<h3 class="hndle"><span><?PHP _e('Type of post to create','wpematico'); ?></span></h3>
				<div class="inside">
				<p><b><?PHP _e('Publish post as','wpematico'); ?></b></p>
				<?PHP
					echo '<input class="radio" type="radio"'.checked('publish',$jobvalue['campaign_posttype'],false).' name="campaign_posttype" value="publish" id="type_published" /> <label for="type_published">'.__('Published','wpematico').'</label><br />';
					echo '<input class="radio" type="radio"'.checked('private',$jobvalue['campaign_posttype'],false).' name="campaign_posttype" value="private" id="type_private" /> <label for="type_private">'.__('Private','wpematico').'</label><br />';
					echo '<input class="radio" type="radio"'.checked('draft',$jobvalue['campaign_posttype'],false).' name="campaign_posttype" value="draft" id="type_draft" /> <label for="type_draft">'.__('Draft','wpematico').'</label><br />';
//				_e('Example:','wpematico'); 
//				echo '<br /><i>'.$jobvalue['fileprefix'].date_i18n('Y-m-d_H-i-s').$jobvalue['fileformart'].'</i>';
				?>
				</div>
				<div id="major-publishing-actions">
					<div id="delete-action">
					<a class="submitdelete deletion" style="color:red" href="<?PHP echo wp_nonce_url('admin.php?page=WPeMatico&action=delete&jobid='.$jobid, 'delete-job_'.$jobid); ?>" onclick="if ( confirm('<?PHP echo esc_js(__("You are about to delete this Campaign. \n  'Cancel' to stop, 'OK' to delete.","wpematico")); ?>') ) { return true;}return false;"><?php _e('Delete', 'wpematico'); ?></a>
					</div>
					<div id="publishing-action">
						<input type="submit" name="submit" class="button-primary right" accesskey="s" value="<?php _e('Save Changes', 'wpematico'); ?>" />
					</div>
					<div class="clear"></div>
				</div>
			</div>

			<div id="jobschedule" class="postbox">
				<h3 class="hndle"><span><?PHP _e('Campaign Schedule','wpematico'); ?></span></h3>
				<div class="inside">
					<input class="checkbox" value="1" type="checkbox" <?php checked($jobvalue['activated'],true); ?> name="activated" /> <?PHP _e('Activate scheduling', 'wpematico'); ?><br />
					<?PHP list($cronstr['minutes'],$cronstr['hours'],$cronstr['mday'],$cronstr['mon'],$cronstr['wday'])=explode(' ',$jobvalue['cron'],5);    ?>
					<div style="width:85px; float: left;">
						<b><?PHP _e('Minutes: ','wpematico'); ?></b><br />
						<?PHP 
						if (strstr($cronstr['minutes'],'*/'))
							$minutes=explode('/',$cronstr['minutes']);
						else
							$minutes=explode(',',$cronstr['minutes']);
						?>
						<select name="cronminutes[]" id="cronminutes" style="height:65px;" multiple="multiple">
						<option value="*"<?PHP selected(in_array('*',$minutes,true),true,true); ?>><?PHP _e('Any (*)','wpematico'); ?></option>
						<?PHP
						for ($i=0;$i<60;$i=$i+5) {
							echo "<option value=\"".$i."\"".selected(in_array("$i",$minutes,true),true,false).">".$i."</option>";
						}
						?>
						</select>
					</div>
					<div style="width:85px; float: left;">
						<b><?PHP _e('Hours:','wpematico'); ?></b><br />
						<?PHP 
						if (strstr($cronstr['hours'],'*/'))
							$hours=explode('/',$cronstr['hours']);
						else
							$hours=explode(',',$cronstr['hours']);
						?>
						<select name="cronhours[]" id="cronhours" style="height:65px;" multiple="multiple">
						<option value="*"<?PHP selected(in_array('*',$hours,true),true,true); ?>><?PHP _e('Any (*)','wpematico'); ?></option>
						<?PHP
						for ($i=0;$i<24;$i++) {
							echo "<option value=\"".$i."\"".selected(in_array("$i",$hours,true),true,false).">".$i."</option>";
						}
						?>
						</select>
					</div>
					<div style="width:85px; float: right;">
						<b><?PHP _e('Days:','wpematico'); ?></b><br />
						<?PHP 
						if (strstr($cronstr['mday'],'*/'))
							$mday=explode('/',$cronstr['mday']);
						else
							$mday=explode(',',$cronstr['mday']);
						?>
						<select name="cronmday[]" id="cronmday" style="height:65px;" multiple="multiple">
						<option value="*"<?PHP selected(in_array('*',$mday,true),true,true); ?>><?PHP _e('Any (*)','wpematico'); ?></option>
						<?PHP
						for ($i=1;$i<=31;$i++) {
							echo "<option value=\"".$i."\"".selected(in_array("$i",$mday,true),true,false).">".$i."</option>";
						}
						?>
						</select>
					</div>
					<br class="clear" />
					<div style="width:130px; float: left;">
						<b><?PHP _e('Months:','wpematico'); ?></b><br />
						<?PHP 
						if (strstr($cronstr['mon'],'*/'))
							$mon=explode('/',$cronstr['mon']);
						else
							$mon=explode(',',$cronstr['mon']);
						?>
						<select name="cronmon[]" id="cronmon" style="height:65px;" multiple="multiple">
						<option value="*"<?PHP selected(in_array('*',$mon,true),true,true); ?>><?PHP _e('Any (*)','wpematico'); ?></option>
						<option value="1"<?PHP selected(in_array('1',$mon,true),true,true); ?>><?PHP _e('January'); ?></option>
						<option value="2"<?PHP selected(in_array('2',$mon,true),true,true); ?>><?PHP _e('February'); ?></option>
						<option value="3"<?PHP selected(in_array('3',$mon,true),true,true); ?>><?PHP _e('March'); ?></option>
						<option value="4"<?PHP selected(in_array('4',$mon,true),true,true); ?>><?PHP _e('April'); ?></option>
						<option value="5"<?PHP selected(in_array('5',$mon,true),true,true); ?>><?PHP _e('May'); ?></option>
						<option value="6"<?PHP selected(in_array('6',$mon,true),true,true); ?>><?PHP _e('June'); ?></option>
						<option value="7"<?PHP selected(in_array('7',$mon,true),true,true); ?>><?PHP _e('July'); ?></option>
						<option value="8"<?PHP selected(in_array('8',$mon,true),true,true); ?>><?PHP _e('Augest'); ?></option>
						<option value="9"<?PHP selected(in_array('9',$mon,true),true,true); ?>><?PHP _e('September'); ?></option>
						<option value="10"<?PHP selected(in_array('10',$mon,true),true,true); ?>><?PHP _e('October'); ?></option>
						<option value="11"<?PHP selected(in_array('11',$mon,true),true,true); ?>><?PHP _e('November'); ?></option>
						<option value="12"<?PHP selected(in_array('12',$mon,true),true,true); ?>><?PHP _e('December'); ?></option>
						</select>
					</div>
					<div style="width:130px; float: right;">
						<b><?PHP _e('Weekday:','wpematico'); ?></b><br />
						<select name="cronwday[]" id="cronwday" style="height:65px;" multiple="multiple">
						<?PHP 
						if (strstr($cronstr['wday'],'*/'))
							$wday=explode('/',$cronstr['wday']);
						else
							$wday=explode(',',$cronstr['wday']);
						?>
						<option value="*"<?PHP selected(in_array('*',$wday,true),true,true); ?>><?PHP _e('Any (*)','wpematico'); ?></option>
						<option value="0"<?PHP selected(in_array('0',$wday,true),true,true); ?>><?PHP _e('Sunday'); ?></option>
						<option value="1"<?PHP selected(in_array('1',$wday,true),true,true); ?>><?PHP _e('Monday'); ?></option>
						<option value="2"<?PHP selected(in_array('2',$wday,true),true,true); ?>><?PHP _e('Tuesday'); ?></option>
						<option value="3"<?PHP selected(in_array('3',$wday,true),true,true); ?>><?PHP _e('Wednesday'); ?></option>
						<option value="4"<?PHP selected(in_array('4',$wday,true),true,true); ?>><?PHP _e('Thursday'); ?></option>
						<option value="5"<?PHP selected(in_array('5',$wday,true),true,true); ?>><?PHP _e('Friday'); ?></option>
						<option value="6"<?PHP selected(in_array('6',$wday,true),true,true); ?>><?PHP _e('Saturday'); ?></option>
						</select>
					</div>
					<br class="clear" />
					<?PHP 
					_e('Working as <a href="http://wikipedia.org/wiki/Cron" target="_blank">Cron</a> job schedule:','wpematico'); echo ' <i>'.$jobvalue['cron'].'</i><br />'; 
					_e('Next runtime:'); echo ' '.date('D, j M Y H:i',wpematico_cron_next($jobvalue['cron']));
					?>
				</div>
			</div>
			
			<div id="campaign_cat" class="postbox">
				<h3 class="hndle"><span><?PHP _e('Campaign Categories','wpematico'); ?></span></h3>
				<div class="inside" style="overflow-y: scroll; overflow-x: hidden; max-height: 250px;">
				<ul id="categories" style="font-size: 11px;">
					<?php adminEditCategories($jobvalue['campaign_categories']) ?>
			  </ul> 
			  </div>
			  <div id="major-publishing-actions">
			  <a href="JavaScript:Void(0);" id="quick_add" onclick="arand=Math.floor(Math.random()*101);jQuery('#categories').append('&lt;li&gt;&lt;input type=&quot;checkbox&quot; name=&quot;campaign_newcat[]&quot; checked=&quot;checked&quot;&gt; &lt;input type=&quot;text&quot; id=&quot;campaign_newcatname'+arand+'&quot; class=&quot;input_text&quot; name=&quot;campaign_newcatname[]&quot;&gt;&lt;/li&gt;');jQuery('#campaign_newcatname'+arand).focus();" style="font-weight: bold; text-decoration: none;" ><?PHP _e('Quick add','wpematico'); ?>.</a>
			  </div>

			</div>	
          
			<div id="logmail" class="postbox">
				<h3 class="hndle"><span><?PHP _e('Send log','wpematico'); ?></span></h3>
				<div class="inside">
					<?PHP _e('E-Mail-Adress:','wpematico'); ?>
					<input name="mailaddresslog" id="mailaddresslog" type="text" value="<?PHP echo $jobvalue['mailaddresslog'];?>" class="large-text" /><br />
					<input class="checkbox" value="1" type="checkbox" <?php checked($jobvalue['mailerroronly'],true); ?> name="mailerroronly" /> <?PHP _e('Send only E-Mail on errors.','wpematico'); ?>
				</div>
			</div>


		</div>
	</div>
	<div class="has-sidebar" >
		<div id="post-body-content" class="has-sidebar-content">

			<div id="titlediv">
				<div id="titlewrap">
					<label class="hide-if-no-js" style="visibility:hidden" id="title-prompt-text" for="title"><?PHP __('Enter Campaign name here','wpematico'); ?></label>
					<input type="text" name="name" size="30" tabindex="1" value="<?PHP echo $jobvalue['name'];?>" id="title" autocomplete="off" />
				</div>
			</div>

			<div class="meta-box-sortables ui-sortable" id="advanced-sortables">
			<div id="feeds" class="postbox">
				<div title="Haz clic para cambiar" class="handlediv"><br></div>
				<h3 class="hndle"><span><?PHP _e('Feeds','wpematico'); ?></span></h3>
				<div class="inside">
					<p><?php _e('Please fill in at least one feed. If you\'re not sure about the exact feed url, just type in the domain name, and the feed will be autodetected.', 'wpematico') ?></p>
					<ul id="feeds_edit" class="inlinetext required">
					<?php if(isset($jobvalue['campaign_feeds'])): ?>
					  <?php foreach($jobvalue['campaign_feeds'] as $id => $feed): ?>
					  <?php echo '<li class="jobtype-select">
					<label for="feed_' . $id . '">' . __('Feed URL:','wpematico') . '</label>
					<label for="feed_'. $id .'" class="delete_label">' . __('Delete ?','wpematico') . '</label><input  class="large-text feedinput" type="text" value="' . $feed . '" id="feed_' . $id . '" name="campaign_feeds[]"></li>'; ?>
					  
					  <?php endforeach ?>
					<?php else: ?>
						<?php echo '<li class="jobtype-select">
					<label for="feed_new">' . __('Feed URL:','wpematico') . '</label>
					<input class="large-text feedinput" type="text" value="" id="feed_new" name="campaign_feeds[]"></li>'; ?>

					<?php endif ?>
				  </ul>
				  <div>
						<a href="JavaScript:Void(0);" class="button-primary right" id="addmore" onclick="jQuery('#feeds_edit').append('&lt;li class=&quot;jobtype-select&quot;&gt;&lt;label&gt;Feed URL:&lt;/label&gt;&lt;input  class=&quot;large-text feedinput&quot; type=&quot;text&quot; value=&quot;&quot; id=&quot;feed_new&quot; name=&quot;campaign_feeds[]&quot;&gt;&lt;/li&gt;');" style="font-weight: bold; text-decoration: none;" ><?PHP _e('Add more','wpematico'); ?>.</a>
						<span class="button-primary right" id="checkfeeds" style="font-weight: bold; text-decoration: none;" ><?PHP _e('Check all feeds','wpematico'); ?>.</span>
						
				  </div>
  				</div>

			</div>
			</div>
			
			<div class="meta-box-sortables ui-sortable" id="advanced-sortables">
			<div id="optionsjobs" class="postbox">
				<div title="Haz clic para cambiar" class="handlediv"><br></div>
				<h3 class="hndle"><span><?PHP _e('Campaign Options','wpematico'); ?></span></h3>
				<div class="inside">

					<p><b><?PHP echo '<label for="campaign_max">' . __('Max items to create on each fetch:','wpematico') . '</label>'; ?></b>
					<input name="campaign_max" type="text" size="3" value="<?PHP echo $jobvalue['campaign_max'];?>" class="small-text" id="campaign_max"/><br />
					<?php _e("Set it to 0 for unlimited. If set to a X value, only the last X items will be selected, ignoring the older ones.", 'wpematico') ?></p>

					<p><b><?PHP echo '<label for="campaign_imgcache">' . __('Cache Images for this campaign?','wpematico') . '</label>'; ?></b>
					<input name="campaign_imgcache" id="campaign_imgcache" class="checkbox" value="1" type="checkbox" <?php checked($jobvalue['campaign_imgcache'],true); ?> />
					&nbsp;&nbsp;<a href="JavaScript:Void(0);" style="font-weight: bold; text-decoration: none;" onclick="jQuery('#hlpimg').toggle();"><?PHP _e('Help','wpematico'); ?>.</a> <br />
					<div id="hlpimg" style="padding-left:20px;display:none;"><b><?php _e("Image Caching", 'wpematico') ?>:</b> <?php _e("When image caching is on, a copy of every image found (only in &lt;img&gt; tags) is downloaded to the specified directory, by default in Wordpress UPLOADS Dir (Highly recommended).", 'wpematico') ?><br />
					<?php _e("If not enabled all images will linked to the image owner's server, but also make your website faster for your visitors.", 'wpematico') ?><br />
					<b><?php _e("Note", 'wpematico') ?>:</b> <?php _e("If this featured is disabled the general Settings options for images caching is taken. Enabling this feature here will be overridden only for this campaign the general Settings options for images caching.", 'wpematico') ?></div></p>

					<p><b><?PHP echo '<label for="campaign_author">' . __('Author:','wpematico') . '</label>'; ?></b>
					<?php wp_dropdown_users(array('name' => 'campaign_author','selected' => $jobvalue['campaign_author'])); ?>
					<span class="note"><?php _e("The created posts will be assigned to this author.", 'wpematico') ?></span></p>

					<p><b><?PHP echo '<label for="campaign_linktosource">' . __('Post title links to source?','wpematico') . '</label>'; ?></b>
					<input class="checkbox" type="checkbox"<?php checked($jobvalue['campaign_linktosource'],true);?> name="campaign_linktosource" value="1" id="campaign_linktosource"/> <br />
					<?php _e("", 'wpematico') ?></p>

					<p><b><?PHP echo '<label for="ommentstatus">' . __('Discussion options:','wpematico') . '</label>'; ?></b>
					<?PHP //echo 'campaign_commentstatus = '.$jobvalue['campaign_commentstatus']; ?>
					<select id="campaign_commentstatus" name="campaign_commentstatus">
						<option value="open"<?PHP echo ($jobvalue['campaign_commentstatus']=="open" || $jobvalue['campaign_commentstatus']=="") ? 'SELECTED' : ''; ?> >Open
						<option value="closed" <?PHP echo ($jobvalue['campaign_commentstatus']=="closed") ? 'SELECTED' : ''; ?> >Closed
						<option value="registered_only" <?PHP echo ($jobvalue['campaign_commentstatus']=="registered_only") ? 'SELECTED' : ''; ?> >Registered only
					</select>
					<input class="checkbox" type="checkbox"<?php checked($jobvalue['campaign_allowpings'],true);?> name="campaign_allowpings" value="1" id="campaign_allowpings"/> <?PHP echo '<label for="campaign_allowpings">' . __('Allow pings?','wpematico') . '</label>'; ?><br />
					</p>
            
				</div>
			</div>
			</div>

			<div class="meta-box-sortables ui-sortable" id="advanced-sortables">
			<div class="postbox " id="rewrite">
				<div title="Haz clic para cambiar" class="handlediv"><br></div>
				<h3 class="hndle"><span><?PHP _e('Rewrite option','wpematico'); ?></span></h3>
				<div class="inside" <?PHP if (!isset($jobvalue['campaign_rewrites']['origin'])) echo 'style="display:none;"';?>>
					<p><?php _e('Want to transform a word into another? Or link a specific word to some website?', 'wpematico') ?>
					&nbsp;&nbsp;<a href="JavaScript:Void(0);" style="font-weight: bold; text-decoration: none;" onclick="jQuery('#hlprwg').toggle();"><?PHP _e('Help','wpematico'); ?>.</a> </p>
					<div id="hlprwg" style="padding-left:20px;display:none;"><b>Basics:</b>	The rewriting settings allow you to replace parts of the content with the text you specify.<br />
						<b>Basic rewriting</b><br />
						To replace all occurrences the word ass with butt, simply type ass in the "origin field", and butt in "rewrite to" <br />
						<b>Relinking</b><br />
						If you want to find all occurrences of google and make them link to Google, just type google in the "origin field" and http://google.com in the "relink to" field<br />
						<b>Regular expressions</b><br />
						For advanced users, regular expressions are supported. Using this will allow you to make more powerful replacements. Take multiple word replacements for example. Instead of using many rewriting boxes to replace ass and arse with butt, you can use the | operator: (ass|arse).
					</div><br />
				  <ul id="rewrites_edit" class="inlinetext">
					<?php if(isset($jobvalue['campaign_rewrites']['origin'])): ?>
						<?php for ($i = 0; $i < count($jobvalue['campaign_rewrites']['origin']); $i++) : ?>
							  <li class="jobtype-select" style="Background:#EEF1FF;border-color:#CEE1EF; border-style:solid; border-width:2px; width:80%; margin:5px 0px 5px 40px; padding:0.5em 0.5em;">
								<label for="campaign_word_origin_<?=$i ?>"><?=_e('Origin:','wpematico') ?></label>
								<textarea class="large-text" id="campaign_word_origin_<?=$i ?>" name="campaign_word_origin[<?=$i ?>]" /><?=$jobvalue['campaign_rewrites']['origin'][$i] ?></textarea>
								<input name="campaign_word_option_regex[<?=$i ?>]" id="campaign_word_option_regex_[<?=$i ?>]" class="checkbox" value="1" type="checkbox"<?=checked($jobvalue['campaign_rewrites']['regex'][$i],true) ?> />
								<label for="campaign_word_option_regex_<?=$i ?>"> <?=_e('RegEx','wpematico') ?></label>
								<hr style="border-color:#CEE1EF; border-style:solid; border-width:2px;">
								<label for="campaign_word_option_rewrite_<?=$i ?>"> <?=_e('Rewrite to:','wpematico') ?></label>
								<input name="campaign_word_option_rewrite[<?=$i ?>]" id="campaign_word_option_rewrite_<?=$i ?>" class="checkbox" value="1" type="checkbox"<?=checked(isset($jobvalue['campaign_rewrites']['rewrite'][$i]),true) ?> />
								<textarea class="large-text" id="campaign_word_rewrite_<?=$i ?>" name="campaign_word_rewrite[<?=$i ?>]" /><?=$jobvalue['campaign_rewrites']['rewrite'][$i] ?></textarea>
								<hr style="border-color:#CEE1EF; border-style:solid; border-width:2px;">
								<label for="campaign_word_option_relink_<?=$i ?>"> <?=_e('ReLink to:','wpematico') ?></label>
								<input name="campaign_word_option_relink[<?=$i ?>]" id="campaign_word_option_relink_[<?=$i ?>]" class="checkbox" value="1" type="checkbox"<?=checked(isset($jobvalue['campaign_rewrites']['relink'][$i]),true) ?> />
								<textarea class="large-text" id="campaign_word_relink_<?=$i ?>" name="campaign_word_relink[<?=$i ?>]" /><?=$jobvalue['campaign_rewrites']['relink'][$i] ?></textarea>
							  </li>
							<?php endfor ?>
							<input name="rewid" id="rewid" size="5" type="hidden" value="<?PHP echo $i+1;?>" class="small-text" />
					<?php else: ?>
						  <li class="jobtype-select" style="Background:#EEF1FF;border-color:#CEE1EF; border-style:solid; border-width:2px; width:80%; margin:5px 0px 5px 40px; padding:0.5em 0.5em;">
							<label for="campaign_word_origin_0"><?=_e('Origin:','wpematico') ?></label>
							<textarea class="large-text" id="campaign_word_origin_0" name="campaign_word_origin[0]" /></textarea>
							<input name="campaign_word_option_regex[0]" id="campaign_word_option_regex_0" class="checkbox" value="1" type="checkbox" />
							<label for="campaign_word_option_regex_0"> <?=_e('RegEx','wpematico') ?></label>
							<hr style="border-color:#CEE1EF; border-style:solid; border-width:2px;">
							<label for="campaign_word_option_rewrite_0"> <?=_e('Rewrite to:','wpematico') ?></label>
							<input name="campaign_word_option_rewrite[0]" id="campaign_word_option_rewrite_0" class="checkbox" value="1" type="checkbox" />
							<textarea class="large-text" id="campaign_word_rewrite_0" name="campaign_word_rewrite[0]" /></textarea>
							<hr style="border-color:#CEE1EF; border-style:solid; border-width:2px;">
							<label for="campaign_word_option_relink_0"> <?=_e('ReLink to:','wpematico') ?></label>
							<input name="campaign_word_option_relink[0]" id="campaign_word_option_relink_0" class="checkbox" value="1" type="checkbox" />
							<textarea class="large-text" id="campaign_word_relink_0" name="campaign_word_relink[0]" /></textarea>
  						  </li>
							<input name="rewid" id="rewid" size="5" type="hidden" value="1" class="small-text" />
					<?php endif ?>
				  </ul>
				  
				  <div>
						<a href="JavaScript:Void(0);" class="button-primary right" id="addrew" onclick="wpe_addrewrite('<?=_e('Origin:','wpematico') ?>','<?=_e('RegEx','wpematico') ?>','<?=_e('Rewrite to:','wpematico') ?>','<?=_e('ReLink to:','wpematico') ?>');" style="font-weight: bold; text-decoration: none;" ><?PHP _e('Add more','wpematico'); ?>.</a>
				  </div>
  				</div>

			</div>
			</div>

		</div>
	</div>
</div>

</form>
</div>