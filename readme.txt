=== Plugin Name ===
Contributors: jmagnone
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VVE9SYHSM38FY
Tags: google, spreadsheets, import, synchronize, posts, edit
Requires at least: 3.1
Tested up to: 3.4.2
Stable tag: 1.0.6

WP Sync is a simple plugin that helps you to import Google Spreadsheet into WP posts. You can use this plugin to import a Google Spreadsheet as individual blog posts. The plugin also will help you to import custom post types for example if you are preparing a business directory or event website.

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

= Does the plugin overwrites the spreadsheet? =

No, it doesn't writes the spreadsheet. It is only accessed read only mode (at least for now)

= Is this plugin free? =

Yes, it is free.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)
2. This is the second screen shot

== Changelog ==

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