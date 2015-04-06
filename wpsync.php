<?php
/*
Plugin Name: Magn WPSync
Plugin URI: http://magn.com/wpsync/
Description: WP Sync is a very simple plugin that helps you to import Google Spreadsheet rows into individual WordPress posts. Currently it only supports one-way importing feature into WordPress (it won't update the spreadsheet)
Version: 1.0.10
Author: Julian Magnone (julianmagnone@gmail.com)
Author URI: http://magn.com

Please see WordPress.org default GPL licenses to know more about the license applied for this plugin.

Here are some references that were used in order to build this plugin:

* Github gist code https://gist.github.com/770584
* Google Spreadsheet Documentation API 3.0 http://code.google.com/intl/es/apis/documents/docs/3.0/developers_guide_protocol.html
* Code for listing feeds http://code.google.com/intl/es/apis/spreadsheets/data/3.0/developers_guide.html#ListFeeds
**
* Fields:
* post_category: array containing category ID
* date:
*
* Wish List
* ---------
* Give users an event-specific (each row) option to set Publish or Draft status on the spreadsheet, instead of all or nothing. For the events that were already sync'd as draft, I currently cannot change the status on the spreadsheet. I need to go to the Event WP Admin section and change them in bulk or each one.
*
*/

if ( ! defined( 'ABSPATH' ) ) exit;

if ( !defined( 'MAGN_WPSYNC_PLUGIN_DIR' ) ) {
	define( 'MAGN_WPSYNC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

include( MAGN_WPSYNC_PLUGIN_DIR . 'wpsync-ui.php' );

add_action('plugins_loaded', 'widget_wpsync_init');
function widget_wpsync_init() {

	// create custom plugin settings menu
	add_action('admin_menu', 'wpsync_create_menu');



	function wpsync_create_menu() {

		//create new top-level menu - maybe we should move this under settings later
		add_menu_page('WPSync Plugin Settings', 'WPSync Settings', 'administrator', __FILE__, 'wpsync_settings_page',plugins_url('/images/wpsyncicon.png', __FILE__));

		//call register settings function
		add_action( 'admin_init', 'register_wpsync_settings' );
	}

	function register_wpsync_settings() {

		//register important settings
		register_setting( 'wpsync-settings-group', 'wpsync_spreadsheet_key' );
		register_setting( 'wpsync-settings-group', 'wpsync_spreadsheet_sheet' );
		register_setting( 'wpsync-settings-group', 'wpsync_allow_delete_from_spreadsheet' );
		register_setting( 'wpsync-settings-group', 'wpsync_allow_update_from_spreadsheet' );
		register_setting( 'wpsync-settings-group', 'wpsync_allow_update_fields' );

		register_setting( 'wpsync-settings-group', 'wpsync_allow_delete_from_spreadsheet' );

		// for categories and other options
		register_setting( 'wpsync-settings-group', 'wpsync_create_categories_if_not_exist' );
		register_setting( 'wpsync-settings-group', 'wpsync_create_tags_if_not_exist' );
		register_setting( 'wpsync-settings-group', 'wpsync_default_category' );
		register_setting( 'wpsync-settings-group', 'wpsync_default_tags' );
		register_setting( 'wpsync-settings-group', 'wpsync_default_post_type' );

		register_setting( 'wpsync-settings-group', 'wpsync_debug_mode' );

		// for default status and more
		register_setting( 'wpsync-settings-group', 'wpsync_default_status' );
	}

	function wpsync_settings_page()
	{
		$wpsync_form_action = @$_POST['wpsync_form_action'];

		$message = "Please notice this plugin is still under development. If you have questions, suggestions or any other comment kindly write to <a href=mailto:julianmagnone@gmail.com>julianmagnone@gmail.com</a> ";
		wpsync_show_message($message);


		if (!empty($wpsync_form_action) AND $wpsync_form_action == 'save' )
		{
			// Update options
			wpsync_save_settings();
			wpsync_show_ui_settings_page();

		} else if (!empty($wpsync_form_action) AND $wpsync_form_action == 'run' )
		{
			// run sync (import rows from spreadsheets into posts)
			wpsync_run_sync();

		} else if (!empty($wpsync_form_action) AND $wpsync_form_action == 'preview' )
		{
			// run a simple preview
			wpsync_preview_sync();

		} else {
			// Display default settings page
			wpsync_show_ui_settings_page();
		}
	}

	function wpsync_save_settings()
	{
		// Hmm, this was not longer used in WP 3.x
		echo 'Options saved';
	}

	function wpsync_run_sync()
	{
		$key = get_option('wpsync_spreadsheet_key');
		if (empty($key))
		{
			echo 'Please enter a Spreadsheet KEY';
			return FALSE;
		}

		wpsync_parse_spreadsheet($key, FALSE);
	}

	function wpsync_preview_sync()
	{

		$key = trim( get_option('wpsync_spreadsheet_key') );
		if (empty($key))
		{
			$message = 'Please enter a Spreadsheet KEY';
			wpsync_show_error($message);
			return FALSE;
		}

		wpsync_parse_spreadsheet($key);
	}

	function wpsync_parse_spreadsheet($key, $only_preview = TRUE)
	{
		$wpsync_debug_mode = get_option('wpsync_debug_mode', FALSE);
		$wpsync_allow_update_from_spreadsheet = get_option('wpsync_allow_update_from_spreadsheet', FALSE);
		$wpsync_allow_update_fields = get_option('wpsync_allow_update_fields');

		$sheet = trim(get_option('wpsync_spreadsheet_sheet'));
		if (empty($sheet))
		{
			$sheet = "1";
		}

		// Parsing this spreadsheet
		$url = "http://spreadsheets.google.com/feeds/list/{$key}/{$sheet}/public/values?alt=json";
		//https://spreadsheets.google.com/feeds/worksheets/key/private/full

		echo '<div id="poststuff">';

		echo 'Connecting to '.$url.'<br/>';

		$url_cells = "http://spreadsheets.google.com/feeds/cells/{$key}/{$sheet}/public/values?alt=json";
		//$cells_file = @file_get_contents($url_cells);
		$cells_file = @wp_remote_fopen($url_cells);
		if (empty($cells_file))
		{
			$message = 'Failed to retrieve spreadsheet cells. Could not run wp_remote_fopen(). Please check key is valid, sheet ID and spreadsheet is published to the web';
			wpsync_show_error( $message );
			return FALSE;
		} else {
		}

		// get the titles
		$json_cells = json_decode($cells_file);
		$columns = array();
		$cols = array();
		foreach ($json_cells->feed->entry as $cellEntry)
		{
			$titlearr = (array)$cellEntry->title;
			$valuearr = (array)$cellEntry->content;
			//$text = ($objarr['$t']);

			$columnNumber  = $titlearr['$t'];
			$columnLabel  = $valuearr['$t'];
			//echo "Column $columnNumber is labeled $columnLabel<br>\n";
			$columns[$columnNumber] = $columnLabel;
			$cols[] = $columnLabel;
		}
		//var_dump($json_cells);
		//return FALSE;


		//$file= @file_get_contents($url);
		$file = @wp_remote_fopen($url);
		if (empty($file))
		{
			$message = 'Failed to retrieve spreadsheet. Could not run wp_remote_fopen(). Please check key is valid, sheet ID and spreadsheet is published to the web';
			wpsync_show_error( $message );
			return FALSE;
		}

		$json = json_decode($file);
		//var_dump($json);
		$posts_in_spreadsheet = array(); //array with external ids
		$indexes_ss_updated = array(); // used to keep a track about posts indexes in ss that were updated or not

		// ---

		$reserved_column_names = array('id', 'post_title', 'post_content', 'post_date', 'post_category', 'post_tags', 'post_type');
		$mandatory_column_names = array('post_title', 'post_content');

		if ($wpsync_allow_update_from_spreadsheet AND !empty($wpsync_allow_update_fields))
		{
			echo 'If a post already exist in WordPress, we will try to update it from spreadsheet. Only the following fields will be updated: '.$wpsync_allow_update_fields .'<br/>';
		} else {
			echo 'If a post is already in WordPress, then it would not be updated. If you need to update existing posts from your spreadsheet, you can change the Allow Update setting.';
		}

		$i = 0;
		$cells = $json_cells->{'feed'}->{'entry'};
		$prevrow = null;
		$values = array();
		$values_meta = array();
		$cols = array();
		$notices = array();
		foreach( $cells as $cell )
		{
			$x = (int)$cell->{'gs$cell'}->{'col'};
			$y = (int)$cell->{'gs$cell'}->{'row'};
			$celltext = $cell->{'gs$cell'}->{'$t'};
			$celltexti = strtolower($celltext);

			if ($y==1)
			{
				// First row; Get columns
				$cols[$x-1] = $celltext;

				$i++;
				continue;
			}

			if (!empty($prevrow) AND $y > $prevrow )
			{
				if (!empty($values['id']))
				{
					$final_values = array();
					$final_values_meta = array();
					foreach( $cols as $colname )
					{
						if (in_array($colname, $reserved_column_names))
						{
							if (empty($values[$colname]) OR $values[$colname] == NULL ) $values[$colname] = "";

							$final_values[$colname] = $values[$colname];
						} else {

							if (empty($values[$colname]) OR $values[$colname] == NULL ) $values_meta[$colname] = "";

							$final_values_meta[$colname] = $values_meta[$colname];
						}
					}

					foreach($mandatory_column_names as $mandatorycolname)
					{
						if (!array_key_exists ( $mandatorycolname , array_flip($cols) ))
						{
							$notices[$values['id']][] = "Required value for {$mandatorycolname} not present for this entry.";
						}
					}

					$final_values['meta'] = $final_values_meta;


					//$posts_in_spreadsheet[] = array('title'=>$title, 'content'=>$content, 'external_id'=>$id);
					// check mandatory fields
					/*foreach($mandatory_column_names as $mc)
					{

					}*/

					$posts_in_spreadsheet[] = $final_values;
					//var_dump($final_values);
					//var_dump($values['meta']);


				} else {

					// echo 'discard';
				}


				// Reset arrays
				$values = array();
				$values_meta = array();
			}

			//
			// if $y > 1, so... rows;
			//

			if (in_array( strtolower($cols[$x-1]), $reserved_column_names) )
			{
				//echo 'ok';
				$values[$cols[$x-1]] = $celltext;

			} else {

				$values_meta[$cols[$x-1]] = $celltext;
			}

			//echo 'values';
			//var_dump($values);
			//var_dump($values_meta);
			/*
			$id = $row->{'gsx$id'}->{'$t'};
			$title = $row->{'gsx$title'}->{'$t'};
			$content = $row->{'gsx$content'}->{'$t'};
			*/
			//echo $id . ' | ' .$title . ' | ' . $content . '<br>';
			echo '</p>';

			// If not empty id, then synchronize, otherwise skip (great if you plan to add drafts)

			$prevrow = $y;
		}

		//var_dump($notices);

		// Get registered post types
		$post_types = array_values( get_post_types( '', 'names' ) );

		$args = array(
			//'orderby' => 'title',
			//'order' => 'ASC',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => 'wpsync_external_id',
					//'value' => '',
					'compare' => 'EXISTS' // '!='
				)
			),
			//'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
			//'post_status' => 'draft',
			'post_type' => $post_types,
		);

		//$posts = get_posts($args);
		$my_query = new WP_Query( $args );

		echo $my_query->post_count . " posts found in WordPress already sync'ed";

		$posts_in_wordpress = array();

		while ($my_query->have_posts()): $my_query->the_post();
			$post = $my_query->post;
			//setup_postdata($post);
			$post_id = $post->ID;
			$external_id = get_post_meta($post_id, "wpsync_external_id", TRUE);
			if (!empty($external_id))
			{
				$posts_in_wordpress[] = array('id'=> $post_id, 'title'=> $post->title , 'external_id'=> $external_id );
			}
			//var_dump($post);echo '<br/>';
		endwhile;

		echo '<br/>';
		if ($wpsync_debug_mode) { "List of posts: "; var_dump($posts_in_wordpress); }


		echo '<br/>Processing Posts in spreadsheet source: ';
		echo count($posts_in_spreadsheet).' were found.';
		//print_r($posts_in_spreadsheet);

		echo '<br/>Processing Posts in Wordpress: ';
		echo count($posts_in_wordpress).' are already in WordPress<br/><br/>';
		//print_r($posts_in_wordpress);

		// filters['field'] = array("Aaaa", "Bbbb");
		//$recording = array_intersect_key( $recording , array_flip($filter['fields']) );

		for($i=0;$i<count($posts_in_spreadsheet);$i++)
		{
			$postss = $posts_in_spreadsheet[$i];

			for($k=0;$k<count($posts_in_wordpress);$k++)
			{
				$postwp = $posts_in_wordpress[$k];

				$postss['external_id'] = $postss['id'];
				if ($postwp['external_id']==$postss['id'])
				{
					echo 'Processing post_id='.$postwp['id'].' spreadsheet row id='.$postss['external_id'].'.';
					echo '<br/>';
					$indexes_ss_updated[] = $i;  // indexes in spreadsheet that were updated (if not here, then we'll insert new entries later)
				}
			}
		}

		echo '<br/><br/>';

		//echo '<br/>Posts in spreadsheet<br/>';
		//echo count($posts_in_spreadsheet). ' posts found in the spreadsheet<br/><br/>';
		//print_r($posts_in_spreadsheet);

		//
		// process again to see what posts to insert
		//
		//

		$categories = get_categories();
		$categories_map = array();
		foreach($categories as $cat)
		{
			$categories_map[strtolower($cat->cat_name)] = $cat->cat_ID;
		}

		$taxonomies = get_taxonomies();

		if (!$only_preview)
		{
			$default_category = get_option('wpsync_default_category', null);
			$existing_categories = get_categories();

			$config = array(
						'default_category' => array($default_category),
						'default_tags' => get_option('wpsync_default_tags', null),
						'default_status' => get_option('wpsync_default_status', 'draft' ),
						'existing_categories' => $existing_categories,
						'default_post_type' => get_option('wpsync_default_post_type', 'draft' ),
						'taxonomies' => $taxonomies,
					);


			// Iterate on orignal spreadsheet
			for($i=0;$i<count($posts_in_spreadsheet);$i++)
			{
				$postss = $posts_in_spreadsheet[$i];
				if (in_array($i, $indexes_ss_updated))
				{
					$allow_update = get_option('wpsync_allow_update_from_spreadsheet');
					if (!empty($allow_update) AND ($allow_update == TRUE))
					{
						$postss['external_id'] = $postss['id'];
						wpsync_sync_post_update( $postss, null, $config );
					}

				}else
				{
					$category = $postss['category'] OR $postss['post_category'];
					if (!empty($category) AND !empty($categories_map[$category]))
					{
						$postss['post_category'] = array( $categories_map[$category] );
						//echo 'category : '.print_r($postss['post_category'], TRUE);
					}

					$tags = $postss['tags'] OR $postss['post_tags'];
					if (!empty($tags))
					{
						$postss['tags_input'] = $tags;
					}

					$postss['external_id'] = $postss['id'];

					//echo "Inserting post in WordPress".print_r($postss, TRUE)."<br/>";
					wpsync_sync_post( $postss, null, $config );
				}
			}
			echo 'Done!';
			echo "<br/><br/><a class='button-secondary' href='javascript:history.back()'>Back</a>";


		} else {

			//print_r($posts_in_spreadsheet);
			wpsync_show_preview( $posts_in_spreadsheet, $cols, $notices );
			echo 'Preview is done!';
		}

		echo '</div>';

	}

	function wpsync_sync_post($values, $meta = null, $config = null)
	{
		$wpsync_debug_mode = get_option('wpsync_debug_mode', FALSE);

		if (!empty($config['default_category'])) $category = $config['default_category'];
		if (!empty($config['default_tags'])) $tags = $config['default_tags'];
		if (!empty($config['default_status'])) $status = $config['default_status'];
		if (!empty($config['default_post_type'])) $post_type = $config['default_post_type'];

		extract( $values );

		// Currently only supports Category ID but we should do something to support names, too
		if (!empty($category) AND !is_array($category)) $category = split(',',$category); // wp_insert expect an array instead of a single val

		if (!empty($post_category)) $category = split(',',$post_category);
		if (!empty($post_tags)) $tags = $post_tags;

		//var_dump($config['existing_categories'] );

		// Create post object
		$my_post = array(
			'post_title' => $post_title,
			'post_content' => $post_content,
			'post_status' => $status,
			'post_author' => 1,
			'post_category' => $category,
			'tags_input'=> $tags,
			'post_type' => $post_type,
		);

		if (empty($date)) $date = $post_date; // just to use date instead of post_date

		// Check if post is for the future
		if (!empty($date))
		{
			$date_ts = strtotime($date);
			$date  = date('Y-m-d h:i', $date_ts);

			$my_post['post_date'] = $date;
			$my_post['post_date_gmt'] = $date;
			if ($date_ts > time())
			{
				$my_post['post_status'] = 'future';
			}
		}

		// Insert Category

		if (TRUE AND is_array($category))
		{
			$new_cats = array();
			foreach($category as $cat)
			{
				if (is_numeric($cat))
				{
					$new_cats[] = $cat;
				}else{
					echo 'Inserting new category '.$cat.'<br/>';
					$new_cat_id = wp_create_category( $cat );
					if ($new_cat_id > 0) $new_cats[] = $new_cat_id;
				}
			}
			$my_post['post_category'] = $new_cats;
		}

		//var_dump($my_post);

		// Insert the post into the database
		$res = wp_insert_post( $my_post, TRUE ); // return WP_Error

		if (is_object($res))
		{
			echo 'Error inserting post: ';
			//var_dump($res);
			var_dump($res->errors);
			echo '<br/>';
		} else {

			$post_id = (int)$res;

			if ($wpsync_debug_mode) echo "Inserting new post in WordPress ".$post_id." Title:{$title} Status:{$status}   <br/>";

			var_dump($post_id);

			// Add external id
			$meta_key = "wpsync_external_id";
			$meta_value = $external_id;
			add_post_meta($post_id, $meta_key, $meta_value, TRUE);

			// Add Date
			$meta_key = "wpsync_created_on";
			$meta_value = date('Y-m-d H:i:s');
			add_post_meta($post_id, $meta_key, $meta_value, TRUE);

			foreach($meta as $meta_key => $meta_value)
			{
				$taxonomies = $config['taxonomies'];
				$taxonomy_values = array_keys($taxonomies);
				if (!empty($taxonomies) AND in_array($meta_key, $taxonomy_values))
				{
					// add a value for taxonomy
					$term_values = split(',', $meta_value);
					$res = wp_set_object_terms($post_id, $term_values, $meta_key);

					if ($wpsync_debug_mode) echo "Inserting new taxonomy value in post ". $post_id . print_r($meta_value, TRUE).$meta_key." <br/>";
					var_dump($res);

				} else {
					// add as meta tag
					add_post_meta($post_id, $meta_key, $meta_value, TRUE);

					if ($wpsync_debug_mode) echo "Inserting meta value in post ".$post_id." <br/>";
				}
			}
		}
	}

	function wpsync_sync_post_update($values, $meta = null, $config = null)
	{
		$wpsync_debug_mode = get_option('wpsync_debug_mode', FALSE);

		extract( $values );

		// Currently only supports Category ID but we should do something to support names, too
		if (!empty($category) AND !is_array($category)) $category = split(',',$category); // wp_insert expect an array instead of a single val

		if (!empty($post_category)) $category = split(',',$post_category);
		if (!empty($post_tags)) $tags = $post_tags;

		$fields_str = get_option('wpsync_allow_update_fields');
		$fields = split(',',$fields_str);
		$fields = array_map('trim', $fields);

		if (empty($fields))
		{
			echo 'Nothing to update (list of fields to update is empty) for post<br/>';
 			return false;
		}

		$post = get_posts( array(
				'numberposts' => 1,
				'meta_key' => 'wpsync_external_id',
				'meta_value' => $values['id'],
				'post_type' => 'any',
				) );

		$args = array(
			'posts_per_page' => 1,
			'meta_query' => array(
				array(
					'key' => 'wpsync_external_id',
					'value' => $values['id'],
				)
			),
			'post_type' => 'any',
		);
		$my_query = new WP_Query( $args );
		$post = null;
		while ($my_query->have_posts())
		{
			$my_query->the_post();
			$post = $my_query->post;
			break;
		}

		if (empty($post))
		{
			echo 'Error retrieving post with external_id = '.$values['id'];
			return false;
		}

		$my_post = array();
		$my_post['ID'] = $post->ID;

		//var_dump($fields);
		foreach($fields as $field)
		{
			$field = trim($field);
			$my_post[$field] = @$$field;
		}

		// Insert the post into the database
		if ($wpsync_debug_mode) echo 'Updating post '.$post->ID.'<br/>';
		$res = wp_update_post( $my_post );

		if (is_object($res))
		{
			echo 'Error inserting post: ';
			var_dump($res->errors);
			echo '<br/>';
		} else {

			$post_id = $post->ID;
			if ($wpsync_debug_mode) echo "Updating new post Meta Data  ".$post_id." Title:{$title} Status:{$status}   <br/>";

			//var_dump($post_id);

			// Add Updated Date
			$meta_key = "wpsync_updated_on";
			$meta_value = date('Y-m-d H:i:s');
			add_post_meta($post_id, $meta_key, $meta_value, TRUE);

			foreach($meta as $meta_key => $meta_value)
			{
				if (in_array($meta_key, $fields ))
				{
					$taxonomies = $config['taxonomies'];
					$taxonomy_values = array_keys($taxonomies);
					if (!empty($taxonomies) AND in_array($meta_key, $taxonomy_values))
					{
						// add a value for taxonomy
						$term_values = split(',', $meta_value);
						$res = wp_set_object_terms($post_id, $term_values, $meta_key);

						if ($wpsync_debug_mode ) echo "Updating new taxonomy value in post ". $post_id . print_r($meta_value, TRUE).$meta_key." <br/>";
						//var_dump($res);

					} else {
						// add as meta tag
						update_post_meta($post_id, $meta_key, $meta_value);

						if ($wpsync_debug_mode ) echo "Updating meta value '{$meta_value}' in post ".$post_id." <br/>";
					}
				}

			}
		}

	}



	function wpsync_open_spreadsheet($auth, $key)
	{

		echo '<br/>';

		// Include the Auth string in the headers
		// Together with the API version being used
		$headers = array(
			"Authorization: GoogleLogin auth=" . $auth,
			"GData-Version: 3.0",
		);

		// Make the request
		$curl = curl_init();
		//curl_setopt($curl, CURLOPT_URL, "http://docs.google.com/feeds/default/private/full");

		 $url = "https://spreadsheets.google.com/feeds/list/{$key}/1/private/full";
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_POST, false);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($curl);
		curl_close($curl);

		echo '<br/>';
		echo '<br/>';
		echo '<br/>';

		var_dump($url);
		var_dump($response);

		// Parse the response
		$response = simplexml_load_string($response);

		var_dump($response);
		echo '<br/>';

		// Output data
		foreach($response->entry as $file)
		{
			echo "File: " . $file->title . "<br />";
			echo "Type: " . $file->content["type"] . "<br />";
			echo "Author: " . $file->author->name . "<br /><br />";
		}

		echo '<br/>';
	}


}

