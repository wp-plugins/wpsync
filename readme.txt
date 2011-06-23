=== Plugin Name ===
Contributors: jmagnone
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VVE9SYHSM38FY
Tags: google, spreadsheets, import, synchronize, posts, edit
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 

WP Sync is a simple plugin that helps you to import Google Spreadsheet into WP posts.


== Description ==

WP Sync is a simple plugin that helps you to import Google Spreadsheet rows into WP posts.

This plugin is useful for example if you want to import lot of rows from a spreadsheet into separate posts and it is intended to be used for those who need a simple way to enter blog posts and don't like to use the Quick Post feature at WordPress.

Instead, you can just write your post drafts into a spreadsheet and configure the plugin to synchronize your rows with WordPress.

I coded this plugin for my own usage but after getting some user's feedback I decided to publish it on the directory.

Things that you can expect from this plugin:

* helps you importing rows from a Google Spreadsheet into separeted posts
* use the Google Spreadsheet to write down ideas and post drafts, for example if you are developing domain names with WordPress or niche websites, but also useful for directories (ie: with local info, phone numbers, custom fields, etc.)

Things that you won't find on this plugin:

* In the initial version the import is from Google Spreadsheets into WordPress but we'll try to develop the opposite direction (from WP to Google Spreadsheet) soon.
* This version don't writes back any change on the Google Spreadsheet so the Spreadsheet is only used as read onle, not write mode.
* Google Spreadsheets doesn't support rich text format, so if you need HTML for your content you can do that by using HTML tags in the cells



== Installation ==

1. Upload `wpsync` folder to `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to WPSync settings page and configure your Spreadsheet key

In order to use your Spreadsheet created in Google Docs, you need to:

1. Create a new spreadsheet
1. Create a few mandatory fields: id, title, content
1. Publish your spreadsheet and get the spreadsheet KEY from the shared URL
1. Copy the KEY and paste it in the WPSync settings page.




== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)
2. This is the second screen shot

== Changelog ==

= 1.0 =
* A change since the previous version.
* Another change.

= 0.5 =
* List versions from most recent at top to oldest at bottom.

== Upgrade Notice ==

= 1.0 =
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.

= 0.5 =
This version fixes a security related bug.  Upgrade immediately.

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`