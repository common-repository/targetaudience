=== TargetAudience ===
Contributors: marketerbase
Tags: segmentation, conversion, bouncing
Requires at least: 4.0.1
Tested up to: 5.0.3
Stable tag: 1.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

TargetAudience helps you to address your website audience more precisely.

== Description ==
TargetAudience helps you to address your website audience more precisely. By adding a "utm_content" parameter you can change the visual behaviour of your website to better address visitors who came from different places.

After configuring the plugin in the settings you can use a shortcode for displaying the name of your audience. For example place the shortcode:

`[audience default="Webdesigner" alternative="1"]`

on your page. By default "Webdesigner" is displayed.

But if you append an "utm_content" parameter like "?utm_content=1" in the address bar, the shortcode will display the first alternative of your set audience with ID 1 (maybe "Online-Marketers").

== Installation ==
1. Upload `targetaudience` to the `/wp-content/plugins/` directory
4. Activate the plugin through the 'Plugins' menu in WordPress
5. Configure the plugin in the settings by adding audiences
6. Place the shortcode on your page
7. Control the output by adding the equivalent "utm_content" to the address bar

== Frequently Asked Questions ==
= What is needed for setting up the plugin? =
No coding is needed for setting up the plugin. Just follow the screen instructions.

== Changelog ==

= 1.0 =
* Initial release

== Upgrade Notice ==
The latest version gets you new features and more stability.