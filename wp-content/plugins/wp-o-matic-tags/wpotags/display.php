<style type="text/css">
    <?php echo readfile(dirname(__FILE__).'/wpotags.css') ?>
</style>

<script type="text/javascript">
<!-- Begin
function checkall(obj) {
    var array = obj.form[obj.name];
    for (var i = 0; i < array.length; i += 1) {
        array[i].checked = obj.checked;
    }
}
//  End -->
</script>


<div class="wrap">
    <h2><?php _e('Tags Management', 'wpotags') ?></h2>

            <?php
            if ($transformInit) :
            ?>
            <form action="" method="post" accept-charset="utf-8">
                <input type="hidden" name="wpot_transform_2" value="1" />
                <table class="list">
                    <th colspan="2"><?php _e('Tags to transfer', 'wpotags'); ?></th>
                    <input type="hidden" name="transferred_tags" value="<?=join(',', $arrayToCheck)?>" />
                    <?php
                        foreach ($arrayToCheck as $id) :
                            $tag = $this->getOwnTag($id);?>
                    <tr><td><?php echo $tag->name; ?></td><td><input type="text" name="transfer_for_<?= $tag->id ?>" value="<?= $tag->name ?>" /></td></tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2">
                            <input type="submit"/>
                        </td>
                    </tr>
                </table>
            </form>
            <?php endif; ?>

    <table class="container">
        <tr>
            <td>
                <form action="" method="post" accept-charset="utf-8" id="pendingform">
                    <input type="hidden" name="wpot_pending" value="1" />
                    <table class="list">
                        <th><?php _e('Pending tags', 'wpotags'); ?></th><th><input type="checkbox" name="pending_tag[]" onclick="checkall(this)" /></th>
                        <?php
                            $tags = $this->getPendingTags();

                            foreach ($tags as $tag) : ?>
                        <tr><td><?php echo $tag->name; ?></td><td><input type="checkbox" name="pending_tag[]" value="<?= $tag->id ?>" /></td></tr>
                        <?php endforeach; ?>
                        <tr>
                            <td>
                                <select name="wpot_bulk_action">
                                    <option value="add"><?php _e('Add tags to current tags', 'wpotags') ?></option>
                                    <option value="ignore"><?php _e('Add tags to ignored tags', 'wpotags') ?></option>
                                    <option value="transform"><?php _e('Tranform tags to another tag', 'wpotags') ?></option>
                                </select>
                                <input type="submit" />
                            </td>
                        </tr>
                    </table>
                </form>
            </td>

            <td>
                <form action="" method="post" accept-charset="utf-8">
                    <input type="hidden" name="wpot_existing" value="1" />
                    <table class="list">
                        <th><?php _e('Current tags', 'wpotags'); ?></th><th><input type="checkbox" name="existing_tag[]" onclick="checkall(this)" /></th>
                        <?php
                            $tags = get_tags();

                            foreach ($tags as $tag) : ?>
                        <tr>
                            <td>
                                <?php echo $tag->name; ?>
                            </td>
                            <td>
                                <input type="checkbox" name="existing_tag[]" value="<?= $tag->term_id ?>" />
                            </td></tr>
                        <?php endforeach; ?>
                        <tr>
                            <td>
                                <select name="wpot_bulk_action">
                                    <option value="ignore"><?php _e('Ignore tags', 'wpotags') ?></option>
                                    <option value="transform"><?php _e('Tranform tags', 'wpotags') ?></option>
                                </select>
                                <input type="submit" />
                            </td>
                        </tr>
                    </table>
                </form>
            </td>
        </tr>

        <tr>
            <td>
                <form action="" method="post" accept-charset="utf-8">
                    <input type="hidden" name="wpot_ignored" value="1" />
                    <table class="list">
                        <th><?php _e('Ignored tags', 'wpotags'); ?></th><th><input type="checkbox" name="ignored_tag[]" onclick="checkall(this)" /></th>
                        <?php
                            $tags = $this->getIgnoredTags();

                            foreach ($tags as $tag) : ?>
                        <tr><td><?php echo $tag->name; ?></td><td><input type="checkbox" name="ignored_tag[]" value="<?= $tag->id ?>" /></td></tr>
                        <?php endforeach; ?>
                        <tr>
                            <td>
                                <select name="wpot_bulk_action">
                                    <option value="add"><?php _e('Add tag to current tags', 'wpotags'); ?></option>
                                    <option value="transform"><?php _e('Tranform tags to another tag', 'wpotags'); ?></option>
                                    <option value="delete"><?php _e('Delete permanently', 'wpotags');?></option>
                                </select>
                                <input type="submit" />
                            </td>
                        </tr>
                    </table>
                </form>
            </td>

            <td>
                <form action="" method="post" accept-charset="utf-8">
                    <input type="hidden" name="wpot_transform" value="1" />
                    <table class="list">
                        <th colspan="2"><?php _e('Transformed tags', 'wpotags'); ?></th><th><input type="checkbox" name="transformed_tag[]" onclick="checkall(this)" /></th>
                        <?php
                            $tags = $this->getTransformedTags();

                            foreach ($tags as $tag) : ?>
                        <tr><td><?php echo $tag->name; ?></td><td><?php echo $tag->transfer_name; ?></td><td><input type="checkbox" name="transformed_tag[]" value="<?= $tag->id ?>" /></td></tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="2">
                                <select name="wpot_bulk_action">
                                    <option value="add"><?php _e('Add to current tags', 'wpotags'); ?></option>
                                    <option value="ignore"><?php _e('Ignore tags', 'wpotags'); ?></option>
                                </select>
                                <input type="submit" />
                            </td>
                        </tr>
                    </table>
                </form>
            </td>
        </tr>
    </table>


	<h2><?php _e('Process WP-o-Matic posts', 'wpotags'); ?></h2>
	<?php if(!get_option('wpo_version')): ?>
	<p><?php _e ('WP-o-Matic is not installed', 'wpotags'); ?></p>
	<?php else: ?>
	<p><?php _e('Tags imported by wpomatic can be registered in a database, so
                that you are able to manage them with this plugin.', 'wpotags'); ?></p>
	<form action="" method="post" accept-charset="utf-8">
		<p><input type="checkbox" name="process_wpomatic_posts[]" value="all" /><?php _e('Process all
			existing posts. Warning, this may take time. This will overwrite any prior processing.', 'wpotags'); ?>
		</p>
		<p><input type="checkbox" name="process_wpomatic_posts[]" value="new" <?= get_option('wpot_processwpoposts') ? 'checked' : ''?>/>
			<?php _e('Process posts when they are imported by WP-o-matic.', 'wpotags'); ?>
		</p>
		<input type="hidden" name="wpot_wpomatic" value="true" />
		<input type="submit" />
	</form>
	<?php endif; ?>

</div>