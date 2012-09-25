=== FBF Facebook page Feed Widget ===
Contributors: lakshmananphp
Donate link: http://lakshmania.wordpress.com/
Tags: facebook, facebook sidebar, sidebar, social sidebar, widget, plugin, posts, links, facebook widget, facebook page,facebook page feed,facebook feed
Requires at least: 3.3+
Tested up to: 3.4.2
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Shows the latest updates from one or multiple Facebook page(s) in a sidebar widget with short code feature.

== Description ==
This plugin displays the latest posts from one or multiple Facebook page(s) in a sidebar widget. 
Facebook page ID is enough to configure this widget.

= Note =
* FBF Facebook page Feed Widget requires PHP5
* You can display the feeds which are publicly accessible
* Provide multiple pages id sepearated by commas e.g id1,id2,id3,id4

== Installation ==
1. Upload the `FBF Facebook page Feed Widget` directory into the `/wp-content/plugins/` directory
2. Activate the plugin through the `Plugins` menu in WordPress
3. Inside the `Themes->Widget` menu, place the FBF Facebook page Feed Widget inside a sidebar, customize the settings and save
4. Enter facebook page id(s)
5. Enjoy!

Multiple Page Feed

* Provide multiple pages id sepearated by commas
e.g id1,id2,id3,id4


Short code

[fbf_page_feed pageID="33138223345" num="2" update="true" show_avatar="true" avatar_size="square" link_target_blank="true" show_description="true" feed_title => "true"]

* update="true" => shows timestamp
* avatar_size="square" => avatar size, you can choose square,small,large and normal
* feed_title => "true" to show feed title
* feed_title => "" to hide feed title

To display feeds in Template (page templates - in themes)

You can use the following code to to display Facebook page feed in any part of template

* <?php echo do_shortcode('[fbf_page_feed pageID="33138223345" num="2" show_description="true" update="true" show_avatar="true" avatar_size="square" link_target_blank="true"]');
// we are using shortcode function to render the widget
// do_shortcode will convert that shortcode to output, and we echo the output.
?>

== Frequently Asked Questions == 

= I dont know my facebook page ID, How can i find it? =

To find your Page ID, simply go to your "Facebook Page", Click "Edit Page" button.
Then go to your browser address bar and copy your Page ID. (example:http://www.facebook.com/pages/edit/id?XXXXXXXXXX)
Your Id will be the number next to question mark.

== Credits ==

http://wordpress.org/support/profile/lakshmananphp


== Screenshots ==
1. Configuring widget in admin panel  
2. Example feed in fron end sidebar
2. Example feed in fron end within content of a post (using shortcode)

== Changelog ==

= 1.2 =

* simplexml_load_file() issues fixed.
* Added option to show multiple page feed.
* Broken links in feed description fixed
* Added option to show/hide facebook feed title.

= 1.1 =

* Added option to show/hide facebook avatar.
* Added stylesheet with simple predefined styles.
* Shortcode feature added to support facebook page feed widget in post/page content.

= 1.0 =
* Initial version


== Upcoming features ==

* showing User Profile Picture and changing styles

 == Upgrade Notice ==
* version 1.2 - simplexml_load_file() issues fixed, Added option to show multiple page feed, Broken links in feed description fixed
* version 1.1 - Added option to show/hide facebook avatar.
