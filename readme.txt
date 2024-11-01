=== WP-Announcements ===
Contributors: messenlehner
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3084056
Tags:  posts, plugin, widget, announcements, notices, alerts, popups, marquee, shadowbox, thickbox, ads, splash screen
Requires at least: 2.6
Tested up to: 2.8.2
Stable tag: 1.8

Display a post as a site wide announcement, notice, ad or emergency alert on your website via a marquee or popup window using ShadowBox-JS or ThickBox.

== Description ==
The WP-Announcements plugin is ideal for websites or blogs that would like to display a site wide announcement, featured post, important message, community notice, advertisement, survey form, email subscription form, emergency alert or anything you can publish in a post as a marquee or popup window using ShadowBox-JS or ThickBox.
 

WP-Announcements provides two announcement options:

1. A customizable marquee that displays an assigned post title linked to that actual post. The marquee can be displayed by calling the php function wp&#95;announce&#95;marquee('') anywhere in your theme, using the WP-Announce-Marquee Widget or using the Shortcode [wp&#95;announce&#95;marquee] anywhere in your post or page content. 

2. A popup window using ShadowBox-JS or ThickBox that displays the title and content of an assigned announcement post. The popup will only show up once for each website visitor per active popup post as a cookie is created. You can also set a popup window for each time a website member logs into the admin dashboard.

WP-Announcements activates checkboxes on the add/edit post page to allow easy assignment of announcement posts. An admin page under the settings tab is also included to toggle announcements on or off and to manage all posts saved as announcements for easy retrieval and reuse of reoccurring announcements in the future.

Works with WordPress and WordPress MU 2.8.1

For more information check out:

<a href=http://webdevstudios.com/support/wordpress-plugins/wp-announcements/>WP-Annoucements Page</a>

<a href=http://webdevstudios.com/blog/wp-announcements-wordpress-announcement-plugin/>WP-Annoucements Post</a>

<a href=http://webdevstudios.com/support/forum/wp-announcements/>WP-Announcements Support Forum</a>

== Screenshots ==

1. ThickBox or ShadowBox Popup of Announcements.
2. WP-Announcements Admin & Settings Page. "Settings>WP-Announcements"
3. Add new Announcements to a Post.

== Changelog ==

= 1.8 =
* fixed bugs with displaying shadowbox and thickbox in some cases.

= 1.7 =
* locked down adding/editing announcements for administrators only

= 1.6 =
* added member popup post so announcements can also be displayed the 1st time a member logs into the admin dashboard

= 1.5 =
* wrapped &lt;div id='wpa_marquee'&gt; around marquee content so can be easily styled with #wpa_marquee{args} CSS

= 1.4 =
* added link love switch in admin settings page

= 1.3 =
* fixed permalinks on all links

= 1.2 =
* added option to delete all announcement visitor cookies (can't really delete visitor cookies so just adds a random suffix to the cookie name)

= 1.1 =
* fixed problem with short codes breaking on popups using echo apply_filters('the_content', $post->post_content);
* on plugin activation checks if thickbox or shadowbox installed and sets to default popup
* fixed bug with adding new posts as announcements 

= 1.0 =
* First official release

== Installation ==

1. Upload the wp-announcements folder to the plugins directory in your WordPress or WPMU installation.
2. Activate the plugin.
3. To display the marquee in your website: Call the php function wp&#95;announce&#95;marquee('') anywhere in your theme, use the WP-Announce-Marquee Widget or use the Shortcode [wp&#95;announce&#95;marquee] anywhere in your post or page content. 
4. For posts checked off as popup announcements: The Popup will popup a new browser window by default unless you specify otherwise, some visitors may receive a popup blocker alert from their browser. Using the Shadowbox JS or ThickBox WordPress plugins are recommended, just upload and activate either plugin and save your popup type.
5. Check marquee or/and popup announcement on any post add/edit page on the bottom left under categories.
6. Manage announcements from the admin page located under "Settings>WP-Announcements".

That's it! A Marquee or Popup will be displayed promoting your announcement.

<a href=http://webdevstudios.com/support/forum/wp-announcements/>WP-Announcements Support Forum</a>

== Frequently Asked Questions ==

= Does this plugin work with WordPress MU? =

Absolutely!  This plugin has been tested and verified to work on the most current version of WordPress MU

<a href=http://webdevstudios.com/support/forum/wp-announcements/>WP-Announcements Support Forum</a>

== Plugin Support ==
[WP-Announcements Support Forum](http://webdevstudios.com/support/forum/wp-announcements/ "WordPress Plugins and Support Forum")