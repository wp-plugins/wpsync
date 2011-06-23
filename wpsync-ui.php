<?php


function wpsync_show_ui_settings_page()
{

	$categories = get_categories( array('hide_empty'=>0) );

	?>
		<div class="wrap">
			<h2>Magn WPSync</h2>
		
			<!-- 
			<div style="float:right; width: 300px; height:auto;"> 
				<h3>Support</h3>
				<p>If this plugin was helpful and saved your time, please consider donating in order to support further development.</p>
				<div>
				
				</div>
				Thanks!
			</div>
			-->
		
			<h3>Synchronization</h3>

			
			<form name="wpsync_preview" method="POST" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" style="">
				<input type="hidden" name="wpsync_form_action" value="preview">
				<p>See entries in Spreadsheet</p>
				<p class="submit">
				<input type="submit" class="button-primary" name="submit" value="Preview" />
				</p>
			</form>
			
			<form name="wpsync_run" method="POST" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" style="">
				<input type="hidden" name="wpsync_form_action" value="run">
				<p>Press the button when you are ready to run the synchronization </p>
				<p class="submit">
				<input type="submit" class="button-primary" name="submit" value="Run Synchronization" />
				</p>
			</form>
			
			<br/>
			
			<h3>Settings</h3>
		
			<form name="wpsync_options" method="POST" action="options.php">
				<?php //wp_nonce_field('update-options'); ?>
				<?php settings_fields( 'wpsync-settings-group' ); ?>
				<input type="hidden" name="wpsync_form_action" value="save">
				
				<div>
					<label for="wpsync_spreadsheet_key">Spreadsheet KEY</label>
					<input type="text" name="wpsync_spreadsheet_key" value="<?php echo get_option('wpsync_spreadsheet_key'); ?>" style="width: 400px;" />
				</div>
				
				<div>
					<label for="wpsync_spreadsheet_key">Spreadsheet Sheet (1,2,3...)</label>
					<input type="text" name="wpsync_spreadsheet_sheet" value="<?php echo get_option('wpsync_spreadsheet_sheet', "1"); ?>" style="width: 200px;" />
					<span><small>for now, you can use 1 for first spreadsheet, 2 for second one, etc. We plan to show a list of spreadsheets soon</small></span>
				</div>
				
				<div>
					<input type="checkbox" name="wpsync_allow_delete_from_spreadsheet" value="1" <?php echo (get_option('wpsync_allow_delete_from_spreadsheet')=='1'?'checked':''); ?> />
					Allow delete from spreadsheet? <span>Specifying "delete" status on spreadsheet</span>
				</div>
		
				<div>
					<input type="checkbox" name="wpsync_allow_update_from_spreadsheet" value="1" <?php echo (get_option('wpsync_allow_update_from_spreadsheet')=='1'?'checked':''); ?> />
					Check if entries are updated in the original spreadsheet and then update the posts in WordPress
					<span></span>
				</div>
				
				<div>
					<input type="checkbox" name="wpsync_debug_mode" value="1" <?php echo (get_option('wpsync_debug_mode')=='1'?'checked':''); ?> />
					Debug mode enabled
					<span></span>
				</div>
				
				<div>
					<input type="checkbox" name="wpsync_create_categories_if_not_exist" value="1" <?php echo (get_option('wpsync_create_categories_if_not_exist')=='1'?'checked':''); ?> />
					Should we create categories automatically if not exist?
					<span></span>
				</div>
				
				<div>
					<input type="checkbox" name="wpsync_create_tags_if_not_exist" value="1" <?php echo (get_option('wpsync_create_tags_if_not_exist')=='1'?'checked':''); ?> />
					Should we create tags automatically if not exist?
					<span></span>
				</div>
				
				<div>
					<p>Should we assign a default category if not exist?</p>
					<select name="wpsync_default_category">
						<!-- <?php echo get_option('wpsync_default_category') ?> -->
						<option name="" <?php echo (get_option('wpsync_default_category')==''?'selected':''); ?>  ></option>
						<?php foreach($categories as $cat): ?>
						<option  value="<?= $cat->cat_ID ?>"  <?php echo (get_option('wpsync_default_category')==$cat->cat_ID ?'selected':''); ?> ><?= $cat->cat_name ?></option>
						<?php endforeach; ?>
					</select>
					<span></span>
				</div>
				
				<div>
					<label for="wpsync_default_tags">Default tags (separated by comma)</label>
					<input type="text" name="wpsync_default_tags" value="<?php echo get_option('wpsync_default_tags', "1"); ?>" style="width: 400px;" />
					<span><small></small></span>
				</div>
				
				<hr/>
				
				<div>
					<p>What should be the default status if not set?</p>
					<select name="wpsync_default_status">
						<option name="" <?php echo (get_option('wpsync_default_status')==''?'selected':''); ?>  ></option>
						<option name="draft" <?php echo (get_option('wpsync_default_status')=='draft'?'selected':''); ?> >draft</option>
						<option name="publish" <?php echo (get_option('wpsync_default_status')=='publish'?'selected':''); ?> >publish</option>
						<option name="private" <?php echo (get_option('wpsync_default_status')=='private'?'selected':''); ?> >private</option>
						<option name="future" <?php echo (get_option('wpsync_default_status')=='future'?'selected':''); ?> >future</option>
						<option name="pending" <?php echo (get_option('wpsync_default_status')=='pending'?'selected':''); ?> >pending</option>
					</select>
					<span></span>
				</div>
				
				
				
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
			</form>
		</div><!-- end wrap-->
	<?
} // end wpsync_show_ui_settings_page


function wpsync_show_preview($rows)
{
	
	?>
		<table class="wp-list-table widefat fixed posts" cellspacing="0"> 
			<thead> 
			<tr> 
				<th width="50">Id</th>
				<th>Title</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach($rows as $row): ?>
			<?php
			
			?>
				<tr>
				<td><?= $row['id'] ?></td>
				<td><?= $row['title'] ?></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	<?
	
}


function wpsync_show_message($message)
{
?>
	<div id="message" class="updated below-h2 fade" style="margin-top:30px; margin-left:5px; width:600px; cursor:pointer;" onclick="jQuery('div#message').css('display','none');">
    <p style="float:right; font-size:10px; font-variant:small-caps; color:#600000; padding-top:4px;">(close)</p>
    <p><b><?= $message ?></b></p>
	</div>
	<script type="text/javascript">
		//jQuery(document).ready(function($) {$(".fade").fadeTo(5000,1).fadeOut(3000);});
	</script>
<?php
}

function wpsync_show_error($message)
{
?>
	<div id="message" class="error below-h2 fade" style="margin-top:30px; margin-left:5px; width:600px; cursor:pointer;" onclick="jQuery('div#message').css('display','none');">
    <p style="float:right; font-size:10px; font-variant:small-caps; color:#600000; padding-top:4px;">(close)</p>
    <p><b><?= $message ?></b></p>
	</div>
	<script type="text/javascript">
		//jQuery(document).ready(function($) {$(".fade").fadeTo(5000,1).fadeOut(3000);});
	</script>
<?php
}