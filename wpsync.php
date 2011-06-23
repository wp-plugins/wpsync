<?php
/* 
Plugin Name: Magn WPSync
Plugin URI: http://netvivs.com/wpsync/
Description: WP Sync is a simple plugin that helps you to import Google Spreadsheet rows into WP posts.
Version: 0.1
Author: Julian Magnone (julianmagnone@gmail.com)
Author URI: http://jmagnone.com/

Please see WordPress.org default GPL licenses to know more about the license applied for this plugin.

Here are some references that were used in order to build this plugin:

* Github gist code https://gist.github.com/770584
* Google Spreadsheet Documentation API 3.0 http://code.google.com/intl/es/apis/documents/docs/3.0/developers_guide_protocol.html
* Code for listing feeds http://code.google.com/intl/es/apis/spreadsheets/data/3.0/developers_guide.html#ListFeeds

*/ 

require_once(dirname(__FILE__) . '/wpsync-ui.php');

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
		register_setting( 'wpsync-settings-group', 'wpsync_allow_delete_from_spreadsheet' );
		
		// for categories and other options
		register_setting( 'wpsync-settings-group', 'wpsync_create_categories_if_not_exist' );
		register_setting( 'wpsync-settings-group', 'wpsync_create_tags_if_not_exist' );
		register_setting( 'wpsync-settings-group', 'wpsync_default_category' );
		register_setting( 'wpsync-settings-group', 'wpsync_default_tags' );
		
		register_setting( 'wpsync-settings-group', 'wpsync_debug_mode' );
		
		// for default status and more
		register_setting( 'wpsync-settings-group', 'wpsync_default_status' );		
	}
	
	function wpsync_settings_page()
	{
		$wpsync_form_action = $_POST['wpsync_form_action'];
		
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
	
	/*
	function wpsync_connect_google()
	{
//		https://gist.github.com/770584		 
		wpsync_parse_spreadsheet($key);
	}*/
	
	function wpsync_parse_spreadsheet($key, $only_preview = TRUE)
	{
	
		$sheet = trim(get_option('wpsync_spreadsheet_sheet'));
		if (empty($sheet))
		{
			$sheet = "1";
		}
		
	
		// Parsing this spreadsheet
		$url = "http://spreadsheets.google.com/feeds/list/{$key}/{$sheet}/public/values?alt=json";
		echo 'Connecting to '.$url.'<br/>';
		
		$file= file_get_contents($url);
		$json = json_decode($file);
		//var_dump($json);		
		$posts_in_spreadsheet = array(); //array with external ids
		$indexes_ss_updated = array(); // used to keep a track about posts indexes in ss that were updated or not
		
		$i = 0;
		$rows = $json->{'feed'}->{'entry'};

		if (empty($rows))
		{
			$message = 'Failed to retrieve spreadsheet. Please check key is valid and spreadsheet is published to the web';
			wpsync_show_error( $message );
			return FALSE;
		}
		
		$cols = array();
		foreach($rows as $row) {
			
			if ($i==0)
			{
				// First row; Get columns
				$cell = (array) $row;
				foreach($cell as $key => $val)
				{
					// Check titles
					if ( substr($key, 0, 4) == 'gsx$')
					{
						//echo 'colname:['. substr($key, 4).']<br/>';
						$cols[] = substr($key, 4);
					}
				}
				
				// $cols contains a list of columns from the spreadsheet
				// the first iteration is on charge of getting these values then we'll need to see what values to insert
				// in the post or update
			}
			$i++;

			$values = array();
			$values_meta = array();
			foreach($cols as $col)
			{
				//if ($col == 'id') $col = 'external_id'; // If id then use external_id
				
				if (substr($col,0,4)=='meta')
				{
					// meta field
					$values_meta[substr($col,4)] = $row->{'gsx$'.$col}->{'$t'};
				}else{
					// normal field 
					$values[$col] = $row->{'gsx$'.$col}->{'$t'};
				}
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
			if (!empty($values['id']))
			{
				// Assign meta values to values
				$values['meta'] = $values_meta;
			
				//$posts_in_spreadsheet[] = array('title'=>$title, 'content'=>$content, 'external_id'=>$id);
				$posts_in_spreadsheet[] = $values;
			}
		}
		
		$args = array(
			//'orderby' => 'title',
			//'order' => 'ASC',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => 'wpsync_external_id',
					'value' => '',
					'compare' => '!='
				)
			),
			//'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash')   
			//'post_status' => 'draft'
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


		echo '<br/>Processing Posts in spreadsheet source: ';
		echo count($posts_in_spreadsheet).' were found.<br/><br/>';
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
					echo 'Processing post post_id='.$postwp['id'].' spreadsheet row id='.$postss['external_id'].' - entry already exist in WP!<br/>';
					$indexes_ss_updated[] = $i;  // indexes in spreadsheet that were updated (if not here, then we'll insert new entries later)
				}
			}
		}
		
		echo '<br/>Posts in spreadsheet<br/>';
		echo count($posts_in_spreadsheet). ' posts found in the spreadsheet<br/><br/>';
		//print_r($posts_in_spreadsheet);
		
		//
		// process again to see what posts to insert
		//
		//
		
		$categories = get_categories();
		$categories_map = array();
		foreach($categories as $cat)
		{
			$categories_map[strtolower($cat->cat_name)] = $cat->cat_id;
		}
		
		if (!$only_preview)
		{
			$default_category = get_option('wpsync_default_category', null);
			
			$config = array(
						'default_category' => array($default_category),
						'default_tags' => get_option('wpsync_default_tags', null),
						'default_status' => get_option('wpsync_default_status', 'draft' )
					);
			
			
			// Iterate on orignal spreadsheet 
			for($i=0;$i<count($posts_in_spreadsheet);$i++)
			{
				$postss = $posts_in_spreadsheet[$i];
				if (in_array($i, $indexes_ss_updated))
				{

				}else
				{
					$category = $postss['category'];
					if (!empty($category) AND !empty($categories_map[$category]))
					{
						$postss['post_category'] = array( $categories_map[$category] );
						//echo 'category : '.print_r($postss['post_category'], TRUE);
					}
					
					$tags = $postss['tags'];
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
			
		} else {
		
			wpsync_show_preview( $posts_in_spreadsheet );
		
			echo 'Preview is done!';
		}
		
	}
	
	function wpsync_sync_post($values, $meta = null, $config = null)
	{
		
		if (!empty($config['default_category'])) $post_category = $config['default_category'];
		if (!empty($config['default_tags'])) $tags_input = $config['default_tags'];
		if (!empty($config['default_status'])) $status = $config['default_status'];

		extract( $values );

	
		// Create post object
		$my_post = array(
			'post_title' => $title,
			'post_content' => $content,
			'post_status' => $status,
			'post_author' => 1,
			'post_category' => $post_category,
			'tags_input'=> $tags_input ,
		);
		
		//var_dump($my_post);

		// Insert the post into the database
		$res = wp_insert_post( $my_post, TRUE ); // return WP_Error
	  
		if (is_object($res))
		{
			echo 'Error inserting post: ';
			var_dump($res);
			echo '<br/>';
		} else {
		
			$post_id = (int)$res;
			
			echo "Inserting new post in WordPress ".$post_id." Title:{$title} Status:{$status}   <br/>";
			
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
				add_post_meta($post_id, $meta_key, $meta_value, TRUE);
			}
		}
	}
	
	
	
	
	
	function wpsync_auth_google()
	{
		include_once('c:\\xampp\\htdocs\\include3.php');
	
		// Construct an HTTP POST request
		$clientlogin_url = "https://www.google.com/accounts/ClientLogin";
		$clientlogin_post = array(
			"accountType" => "HOSTED_OR_GOOGLE",
			"Email" => "julianmagnone@gmail.com",
			"Passwd" => $pp,
			"service" => "writely",
			"source" => "WPSync"
		);

		// Initialize the curl object
		$curl = curl_init($clientlogin_url);

		// Set some options (some for SHTTP)
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $clientlogin_post);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		// Execute
		$response = curl_exec($curl);

		// Get the Auth string and save it
		preg_match("/Auth=([a-z0-9_\-]+)/i", $response, $matches);
		$auth = $matches[1];

		echo "The auth string is: " . $auth;
	
		return $auth;
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

// Run our code later in case this loads prior to any required plugins.
add_action('plugins_loaded', 'widget_wpsync_init');


