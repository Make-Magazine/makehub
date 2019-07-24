=== Vimeography Squares ===
Tags: vimeo
Requires at least: 3.8
Tested up to: 4.9
Stable tag: 2.0.6
License: GPL-2.0

The easiest way to create beautiful Vimeo galleries on your WordPress site.

== Description ==

Squares displays a thumbnail quilt containing details about your video.

Make your gallery stand out with our custom themes!
[http://vimeography.com/themes](http://vimeography.com/themes "vimeography.com/themes")

For the latest updates, follow us!
[http://twitter.com/vimeography](http://twitter.com/vimeography "twitter.com/vimeography")

== Installation ==

1. Upload `vimeography-squares.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==
= Help! My theme doesn't look right! =

Okay, deep breath. More than likely, it is another plugin causing this issue. See if you can pinpoint which one by disabling your plugins, one by one, and really determining if you need it. If that task sounds daunting, try disabling plugins that are used for photo galleries, minifying scripts, widgets, or otherwise alter your blog's appearance.

= Can I change the look of my Vimeography theme? =

Heck yeah! Use the appearance editor to change your theme's style so that it matches your site perfectly.

== Changelog ==
= 2.0.6 =
* [Fix] Introduce fallback for when source video downloads are unavailable
* [Fix] Navigate to the current window pathname instead of root
* [Tweak] Allow player settings to be configured via Vimeography Pro
* [Tweak] Upgrade to Vimeo.js 2.6.x

= 2.0.5 =
* [Fix] Mangle double let declaration errors in Safari 10
* [Fix] Reset gallery route whenever the lightbox is closed

= 2.0.4 =
* [Fix] Update Vimeography Blueprint helpers for player padding

= 2.0.3 =
* [New] Theme lightbox and search component can now be overridden
* [New] Add placeholder text to searchbox
* [Fix] Switch to use object-fit for displaying all thumbnails 1:1
* [Fix] Ensure thumbnails display correctly in Firefox
* [Fix] Polyfill Object.assign for vue-js-modal compatibility with IE11
* [Fix] Add fallback flexbox support for IE11
* [Fix] Adds postcss-cssnext to theme build process
* [Fix] Corrects videos per page bug when paging through search results

= 2.0.2 =
* [Fix] Add rendering compatibility for Microsoft Edge 16
* [Fix] Add better responsive display for pop-up window on smaller screens

= 2.0.1 =
* Switch to new Download Link component from Vimeography Blueprint
* Switch to new Thumbnail Mixin from Vimeography Blueprint
* Adds an :alt tag to thumbnail images

= 2.0 =
* Rewrote Squares gallery theme for Vimeography 2.0 compatibility.

= 1.2.3 =
* Theme is now loaded as soon as the plugin class is instantiated.
* Javascript now executes using jQuery.on

= 1.2.2 =
* Fixed a bug where sort direction wasn't honored with multiple pages of videos.
* This update also helps make sure your site doesn't run into errors if the Vimeography plugin is deactivated.

= 1.2.1 =
* Fixed a appearance calculation bug on the left margin of images

= 1.2 =
* Allow video downloads for Vimeo Pro members using Vimeography Pro
* Not a Vimeo Pro member? You're missing out. Learn more at http://vimeography.com/vimeo-pro
* Check out all Vimeography Pro features at http://vimeography.com/pro

= 1.1 =
* Added support for Vimeography Pro Playlists
* Set the fancybox content manually instead of an iframe helper

= 1.0.8 =
* Ensure overlay has same size as thumbnail when resizing

= 1.0.7 =
* [Fix] Open fancybox in parent window if being loaded in iframe

= 1.0.6 =
* Fancybox assets are now loaded from http://cdnjs.com/libraries/fancybox/

= 1.0.5 =
* Moved Fancybox dependency into the Squares theme
* Added CSS rules for proper box-sizing
* Updated assets organization

= 1.0.4 =
* Updated paging controls for Vimeography Pro
* Reduced filesize
* Updated testing stats

= 1.0.3 =
* Improved compatibility with Vimeography Pro.

= 1.0.2 =
* Now autoplays videos on square click.

= 1.0.1 =
* Fixed an issue where the layout might not center itself.
* Updated CSS styling.

= 1.0 =
* Converted to plugin.

== Upgrade Notice ==
= 2.0 =
Requires Vimeography 2.0
