=== Plugin Name ===
Contributors: jmagnone
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VVE9SYHSM38FY
Tags: google, spreadsheets, import, synchronize, posts, edit, convert, google docs, excel, csv
Requires at least: 3.5
Tested up to: 4.1.1
Stable tag: trunk

WP Sync is a very simple plugin for WordPress that helps you to import Google Sheets into individual WP posts. You can use this plugin to import a Google Sheets spreadsheet into individual blog posts for example if you are preparing a business directory or importing a large spreadsheet with data into your blog or website. In recent versions of the plugin it also supports to import Custom Fields or meta values, so you can easily prepare a spreadsheet in Google Docs with all the information to be importer and the plugin will do it.

== Description ==

WP Sync is a simple plugin that helps you to import Google Spreadsheet rows into WP posts. This plugin is useful for example if you want to import lot of rows from a spreadsheet into separate posts and it is intended to be used for those who need a simple way to enter blog posts and don't like to use the Quick Post feature at WordPress.

Instead, you can just write your post drafts into a spreadsheet and configure the plugin to synchronize your rows with WordPress.

I coded this plugin for my personal use but after getting some user's feedback I decided to publish it on the WP plugin's directory. You can use this plugin to import a Google Spreadsheet as individual blog posts. The plugin also will help you to import custom post types for example if you are preparing a business directory or event website.

What you can expect from this plugin:

* Helps you importing rows from a Google Spreadsheet into individual posts in WordPress.
* Use the Google Spreadsheet to enter ideas and post drafts, for example if you are developing domain names with WordPress or niche websites, but also useful for directories (ie: with local info, phone numbers, custom fields, etc.)
* Import spreadsheet rows as individual posts or custom post types.
* Support custom fields and taxonomies (categories, tags, etc.)

What you shouldn't expect from this plugin (at least in the initial releases):

* In the initial version the utility works from Google Spreadsheets into WordPress but not viceversa. We'll eventually add the opposite direction soon.
* This version doesn't writes back any change on the Google Spreadsheet so the Spreadsheet is only used as read only.
* Google Spreadsheets doesn't support rich text format, so if you need HTML for your content you can do that by using HTML tags in the cells

Link to [Magn](http://magn.com/ "Simple but useful plugins") and author's profile [Julian Magnone](https://plus.google.com/109045091422552341246)


== Installation ==

1. Upload `wpsync` folder to `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to WPSync settings page and configure your Spreadsheet key

In order to use your Spreadsheet created in Google Docs, you need to:

1. Create a new spreadsheet in Google Docs
1. Create a few mandatory fields (column names): id, post_title, post_content (see the Plugin Settings page for more info and to find a template)
1. Publish your spreadsheet and get the spreadsheet KEY from the shared URL (use the share button in Google Spreadsheet)
1. Copy the KEY and paste it in the WPSync settings page.



== Frequently Asked Questions ==

= Plugin is not working, nothing is synchronized. What to do next? =

1. Google Sheets have changed lot of things since the first version of this plugin was developed. Even the product name was changed
and renamed to Google Sheets. However, it is possible to make the plugin work and here are a few things you can check:
1.1. Go to the spreadsheet in Google Sheets that you want to import into WordPress and then go to File -> Publish option.
1.2. Click Publish Entire Document (or any particular sheet).
1.3. Click Start Publishing button under "Published content & settings" section. Make sure Automatically republish when changes are made is checked.
1.4. Copy the key and paste it in the WP Magn Sync plugin settings page. Now click Preview and see if the rows are shown in the screen.

2. Another reason that could be preventing the plugin to work is if you are using custom post types. If you are using the column post_type in
the spreadsheet, make sure the post_type is registered in your WordPress installation. The plugin uses get_post_types function in WP to get
a list of post types registered.

= Does the plugin overwrites the spreadsheet? =

No, it doesn't writes the spreadsheet. It is only accessed read only mode (at least for now)

= Is this plugin free? =

Yes, it is free.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)
2. This is the second screen shot

== Changelog ==

= 1.0.10 =
* Changed query to include registered post types instead of 'any'
* Small bug fixes

= 1.0.7 =
* Fix the trim bug in the list of custom values to update

= 1.0.6 =
* Major upgrade to support new Google Docs API
* Now the required fields are id, post_title, post_content
* Custom post types and taxonomies are supported
* Update from spreadsheet is supported

== Upgrade Notice ==

= 1.0.6 =
Important update with new features. If you was using this plugin please make sure to adapt your spreadsheet header columns. There are some required fields like id, post_title, post_content that you should respect. Additionally, in this version meta values should not use meta_ prefix. Contact us if you need free support for the upgrade.

== Nothing here yet ==

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`