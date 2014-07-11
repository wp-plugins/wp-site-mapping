=== WP Site Mapping ===
Contributors: benohead, amazingweb-gmbh
Donate link: http://benohead.com/donate/
Tags: html sitemap, map, menu, navigation, page sitemap, pages, posts, posts list, posts sitemap, seo, shortcode, simple sitemap, sitemap, sitemap shortcode
Requires at least: 3.0.1
Tested up to: 3.9.1
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add one or multiple HTML sitemaps to your site by using a shortcode, calling a PHP function or using a widget with this flexible and easy to use plugin.

== Description ==

WP Site Mapping allows to add a sitemap to your WordPress site using a shortcode, calling a PHP function or using one of the two provided widgets.

Using the shortcode doesn't mean you need to write code. Just press the sitemap button in your visual post editor and all options available for the shortcode will be displayed. Press OK when you are done and the shortcode will be inserted with the appropriate parameters.

Of course you can also add the shortcode yourself using the following syntax:

[showsitemap] for the default sitemap or [showsitemap arg1 arg2...] using the following arguments:

* depth: defines how many levels of posts will be shown e.g. depth=1 means that only parent posts will be shown, depth=2 means that parent posts as well as one level of children posts will be displayed.

* exclude=1 means that all posts/pages will be displayed in the sitemap except the ones matching one of the defined criteria. If not present, only the posts/pages matching at least one of the defined criteria will be displayed.

* post_id: List of post IDs to include or exclude e.g. post_id="32,9-11,33" will include/exclude all posts with the IDs 9, 10, 11, 32 and 33.

* cat: List of category IDs to include or exclude e.g. cat="32,11,33" will include/exclude all posts of the categories with the IDs 11, 32 and 33.

* tag: List of tag IDs to include or exclude e.g. tag="32,11,33" will include/exclude all posts with the tags with the IDs 11, 32 and 33.

* fmt: List of post formats to include or exclude e.g. fmt="post-format-aside,post-format-image"

* type: List of post types to include or exclude e.g. type="post,page"

* aut: List of post authors to include or exclude e.g. aut="1,2" will include/exclude posts which author is the user with the user ID 1 or 2

* group: Defines how entries in the sitemap will be grouped e.g.:

    * group=title means that the list will be sorted alphabetically without any grouping
    * group=date means that the list will be sorted by date without any grouping
    * group=author means that the list will be grouped by author name
    * group=category means that the list will be grouped by category
    * group=tag means that the list will be grouped by tag
or
* link: defines a template for displaying the link to posts/pages. Note that you need to properly escape the value of this attribute e.g. link=&quot;&amp;lt;a title=&amp;amp;quot;%title%&amp;amp;quot; href=&amp;amp;quot;%permalink%&amp;amp;quot;&amp;gt;%title%&amp;lt;/a&amp;gt;&quot;. You can use the following variables in the template:

    * %title%: The title of the post
    * %permalink%: The URL of the post
    * %year%: The year of the post, four digits, for example 2004
    * %monthnum%: Month of the year, for example 05
    * %day%: Day of the month, for example 28
    * %hour%: Hour of the day, for example 15
    * %minute%: Minute of the hour, for example 43
    * %second%: Second of the minute, for example 33
    * %post_id%: The unique ID # of the post, for example 423
    * %category%: A comma separated list of the categories for this post
    * %author%: The author name

* class: Class to be applied to the containing DIV to be used to CSS styling and for use in JavaScript.

* id: The ID to be used for the containing DIV to be used to CSS styling and for use in JavaScript.

But the easiest way to learn how to use the shortcode is to use the dialog displayed when clicking the button in the visual editor. It also is easier to handle escaping when you use the dialog.

If you need to have the sitemap in a sidebar, you should use on the two widgets provided by this plugin.

The first widget is called "Site Map" and will provide you with the same possibilities as the shortcode but in a widget. This widget will show a sitemap in an HTML5 navigation tag (&lt;nav&gt;).

The second widget is called "Menu Site Map" and will allow you to show the entries defined in a navigation menu. Define a menu in Appearance &gt; Menus and select it in this widget to have them displayed.

== Installation ==

1. Upload the folder `wp-site-mapping` to the `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How can I contact you with a complaint, a question or a suggestion? =
Send an email to henri.benoit@gmail.com

== Screenshots ==

1. Site Map Widget settings

2. Site Map Widget

3. Menu Site Map Widget settings

4. Menu Structure for Menu Site Map Widget

5. Menu Site Map Widget

6. Short code editor

== Changelog ==

= 0.1 =

* First version.

== Upgrade Notice ==

n.a.
