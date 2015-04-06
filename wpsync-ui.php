<?php


function wpsync_show_ui_settings_page()
{

	$categories = get_categories( array('hide_empty'=>0) );

	?>
		<div class="wrap metabox-holder has-right-sidebar" id="poststuff">

			<div class="inner-sidebar">
					<div id="side-sortables" class="meta-box-sortables ui-sortable"><div id="metabox_like" class="postbox ">
						<div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Like this plugin?</span></h3>
						<div class="inside">
							<ul>
								<li>Please take a moment to support future development:</li><li>* Rate it <a href="http://wordpress.org/extend/plugins/wpsync/" target="_blank">5 Stars on WordPress.org</a></li>
								<li>* Visit <a href="http://magn.com/?utm_source=wp&utm_medium=plugin&utm_campaign=wpsync" target="_blank">Author's Homepage http://magn.com</a></li>
								<li>* <a href="https://plus.google.com/109045091422552341246" target="_blank">Google Plus</a></li>
								<li>* <a href="http://twitter.com/jmagnone" target="_blank">@jmagnone Twitter</a></li>
								<li>* Donate: </li>
							</ul>
							<div>
								<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
								<input type="hidden" name="cmd" value="_s-xclick">
								<input type="hidden" name="hosted_button_id" value="VVE9SYHSM38FY">
								<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
								<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
								</form>

								<p>If you enjoyed this plugin and helped to saved your time, please consider donating below in order to support further development.</p>
							</div>
						</div>
						</div>



						</div>
			</div>

			<div id="post-body">

				<div id="post-body-content">

					<h2>Magn WPSync</h2>


					<form name="wpsync_preview" method="POST" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" style="">
						<input type="hidden" name="wpsync_form_action" value="preview">
						<p class="submit">
						<input type="submit" class="button-primary" name="submit" value="Preview" /> Click here to see a preview.</p>
						</p>
					</form>

					<form name="wpsync_run" method="POST" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" style="" onclick="return confirm('Did you run Preview button first? Please make sure to preview it first to make sure we are able to connect to the spreadsheet.')">
						<input type="hidden" name="wpsync_form_action" value="run">
						<p class="submit">
						<input type="submit" class="button-primary" name="submit" value="Run Synchronization" /> Press this button when you are ready to run the synchronization
						</p>
					</form>

					<br/>

					<div id="wpsync-settings" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Settings</span></h3>
					<div class="inside">
					<form name="wpsync_options" method="POST" action="options.php">


						<?php //wp_nonce_field('update-options'); ?>
						<?php settings_fields( 'wpsync-settings-group' ); ?>
						<input type="hidden" name="wpsync_form_action" value="save" />

						<table>
						<tr>
							<td scope="row">Spreadsheet Key</td>
							<td><input type="text" name="wpsync_spreadsheet_key" value="<?php echo get_option('wpsync_spreadsheet_key'); ?>" style="width: 400px;" /></td>
						</tr>
						<tr>
							<td scope="row">Spreadsheet Sheet (1,2,3...)</td>
							<td><input type="text" name="wpsync_spreadsheet_sheet" value="<?php echo get_option('wpsync_spreadsheet_sheet', "1"); ?>" style="width: 200px;" />
								<span><small>for now, you can use 1 for first spreadsheet, 2 for second one, etc. We plan to show a list of spreadsheets soon</small></span>
							</td>
						</tr>

						<tr>
							<td scope="row">Enable delete from spreadsheet? </td>
							<td><input disabled="disabled" type="checkbox" name="wpsync_allow_delete_from_spreadsheet" value="1" <?php echo (get_option('wpsync_allow_delete_from_spreadsheet')=='1'?'checked':''); ?> /> <span>Specifying "delete" status on spreadsheet</span></td>
						</tr>

						<tr>
							<td scope="row">Enable Update from spreadsheet? </td>
							<td><input type="checkbox" name="wpsync_allow_update_from_spreadsheet" value="1" <?php echo (get_option('wpsync_allow_update_from_spreadsheet')=='1'?'checked':''); ?> /> Which columns you want to update from the spreadsheet if post already exists? <input type="text" name="wpsync_allow_update_fields" value="<?php echo get_option('wpsync_allow_update_fields', ""); ?>" style="width: 250px;" /> ie: "post_content, post_tags"
							</td>
						</tr>
						<tr>
							<td scope="row">Debug Mode</td>
							<td><input type="checkbox" name="wpsync_debug_mode" value="1" <?php echo (get_option('wpsync_debug_mode')=='1'?'checked':''); ?> /> Check if you want to see debug messages
							</td>
						</tr>

						<tr>
							<td scope="row">Default Tags</td>
							<td><input type="text" name="wpsync_default_tags" value="<?php echo get_option('wpsync_default_tags', "1"); ?>" style="width: 400px;" />
							</td>
						</tr>
						<tr>
							<td scope="row">Default Post Type </td>
							<td><input type="text" name="wpsync_default_post_type" value="<?php echo get_option('wpsync_default_post_type', "post"); ?>" style="width: 200px;" />
								('post', 'page', or any other custom post type)
							</td>
						</tr>
						</table>

						<?php /*

						<div>
							<input disabled="disabled" type="checkbox" name="wpsync_create_categories_if_not_exist" value="1" <?php echo (get_option('wpsync_create_categories_if_not_exist')=='1'?'checked':''); ?> />
							Should we create categories automatically if not exist?
							<span></span>
						</div>

						<div>
							<input disabled="disabled" type="checkbox" name="wpsync_create_tags_if_not_exist" value="1" <?php echo (get_option('wpsync_create_tags_if_not_exist')=='1'?'checked':''); ?> />
							Should we create tags automatically if not exist?
							<span></span>
						</div>

						<div>
							<p>Should we assign a default category if not exist?</p>
							<select name="wpsync_default_category">
								<option name="" <?php echo (get_option('wpsync_default_category')==''?'selected':''); ?>  ></option>
								<?php foreach($categories as $cat): ?>
								<option  value="<?= $cat->cat_ID ?>"  <?php echo (get_option('wpsync_default_category')==$cat->cat_ID ?'selected':''); ?> ><?= $cat->cat_name ?></option>
								<?php endforeach; ?>
							</select>
							<span></span>
						</div>
						*/ ?>

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
					</div>
					</div>



					<div id="wpsync-help" class="postbox">
					<div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Resources & Help</span></h3>
					<div class="inside">
						<!-- <div style="float:right; width: 300px; height:auto; background-color: #fafafa; padding: 20px;"> -->

						<div>
							<p>Learn how to prepare your spreadsheet from this template: <a href="https://docs.google.com/spreadsheet/ccc?key=0As5DEk6l4HCodHU3MThwNmVocUVoUHlHVF9DYXBMeXc" target="_blank">Google Spreadsheet Template</a></p>
						</div>

						<div>
							<p>Your spreadsheet should contain the following columns: <strong>id, post_title, post_content, post_category, post_type, post_tags </strong></p>

							<p>Additionally you can add any other column and will be imported as a custom field. You can also add custom taxonomies.</p>
						</div>




						<?php
						$message = "Please notice this plugin is still under development. If you have questions, suggestions or any other comment kindly write to <a href=mailto:julianmagnone@gmail.com>julianmagnone@gmail.com</a> and I will try to answer your questions.";
						wpsync_show_message($message);
						?>

					</div>
					</div>

				</div> <!-- end post body content -->
			</div>

		</div><!-- end wrap-->
	<?php
} // end wpsync_show_ui_settings_page


function wpsync_show_preview($rows, $cols = array('id', 'title'), $notices = null )
{
	?>
		<a class='button-secondary' href='javascript:history.back()'>Back</a>

		<h3>The following entries will be imported as single posts</h3>
		<table class="wp-list-table widefat " cellspacing="0">
			<thead>
			<tr>
				<!-- <th width="50">Id</th> -->
				<!-- <th>Title</th> -->
				<?php if (!empty($cols) AND is_array($cols)): ?>
					<?php foreach( $cols as $col ): ?>
						<th><?= $col ?></th>
					<?php endforeach; ?>
				<?php endif; ?>
			</tr>
			</thead>
			<tbody>
			<?php if (!empty($rows) AND is_array($rows)): ?>
				<?php foreach($rows as $row): ?>
					<?php if (!empty($row['id']) AND !empty($notices[$row['id']])): ?>
						<tr/>
						<td colspan="<?= count($cols) ?>">
						<?php echo implode(',' , $notices[$row['id']]) ?>
						</td>
						</tr>
					<?php continue;
						endif;
					?>
				<tr>
					<?php foreach($row as $cell): ?>
						<?php if (!is_array($cell)): ?>
						<td><?= $cell ?></td>
						<?php else: ?>
						<?php wpsync_show_cell_array($cell) ?>
						<?php endif; ?>
					<?php endforeach; ?>
				</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
			<tfoot>
			<tr>
				<!-- <th width="50">Id</th> -->
				<!-- <th>Title</th> -->
				<?php if (!empty($cols) AND is_array($cols)): ?>
					<?php foreach( $cols as $col ): ?>
						<th><?= $col ?></th>
					<?php endforeach; ?>
				<?php endif; ?>
			</tr>
			</tfoot>
			<tbody>
		</table>

		<p>Important! Make sure to backup your database before perform the synchronization.</p>

	<?php
}

function wpsync_show_cell_array($row)
{
	?>

	<?php if (!empty($row) AND is_array($row)): ?>
	<?php foreach($row as $cell): ?>
			<?php if (!is_array($cell)): ?>
			<td><?= $cell ?></td>
			<?php else: ?>
			<td><?php print_r($cell) ?></td>
			<?php endif; ?>
	<?php endforeach; ?>
	<?php endif; ?>

	<?php
	return;
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

