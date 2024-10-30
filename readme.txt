=== Plugin Name ===
Contributors: avenger339, Everyblock
Tags: widgets, everyblock, local info, local, info, chicago, philly, philadelphia, illinois, pennsylvania, every, block
Tested up to: 3.6.1
Requires at least: 3.6.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a Local Info widget to a post, page, or sidebar.

== Description ==

Add a Local Info widget to a post, page, or sidebar.  Currently supports Chicago and Philadelphia.

== Installation ==

1. Add the folder to wp-content/plugins.
2. Navigate into the settings.php, located in the plugin directory, inside the "everyblock" folder.
3. Replace "API_KEY_HERE" with your API key (See FAQ's for info on how to get a key).
4. If you want to add as a widget, drag the widget labeled "Local Info Powered by Everyblock" to the appropriate location.  You can change the height, width, metro, and schema.
5. If you want to embed in a post, use the shortcode [display_local_info_widget].  You must specify a metro and a schema (example: [display_local_info_widget metro=chicago schema=food-inspections].  This will add a 300x500 widget.  You can specify sizes by adding the attributes width and height (example: [display_local_info_widget width=400 height=600 metro=chicago schema=crime]).

== Frequently Asked Questions ==

= How do I get an Everyblock API Key? =

First, you will need an Everyblock Account.

Navigate your browser window to https://chicago.everyblock.com/account/register/.  Select a username and password, and enter your zip code.

Next, navigate to http://www.everyblock.com/developers/.  Click "Create Developer Profile".  Enter your First Name, Last Name, and if applicable, your company or organization.  Next, click "Create New Project".  Enter a Project Name, the URL to your site, and a brief description.  Next, click "Save Project".  Click "Create Key".  A new API key will generate.  Enter this key in settings.php file, located in the "everyblock" folder, where it says "API_KEY_HERE".

= Why are no entries or neighborhoods loading in my widget? Why can't I set any properties in the widget itself? =

First, make sure you set an API Key the settings.php file (located in the plugin directory, inside the "everyblock" folder).  Second, make sure the API Key is valid.  If it is still not working, please check the current running status of the Everyblock API.

= How do I add the local info widget to a post or page? =

After activating the plugin, add the shortcode [display_local_info_widget] to any page or post.  You need to specify shortcode attributes metro and a schema.  These are slugs provided by the Everyblock API.  Example: [display_local_info_widget metro=chicago schema=food-inspections]

= How do I change the width and height of the widget while it is embedded in a page or post? =

Add the shortcode attributes width and height.  Example: [display_local_info_widget metro=chicago schema=food-inspections width=400 height=600].

= How do I add the local info widget to a sidebar, header, or footer? =

After activating the plugin, go to Appearance -> Settings, and drag the "Local Info Powered By EveryBlock" to the appropriate area. Set a Metro and Schema from the appropriate dropdown boxes.

== Changelog ==

= 1.0 =
* First release.