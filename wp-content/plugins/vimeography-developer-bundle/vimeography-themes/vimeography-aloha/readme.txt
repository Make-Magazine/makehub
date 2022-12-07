=== Vimeography Aloha ===
Tags: vimeo
Requires at least: 3.8
Tested up to: 5.5
Stable tag: 2.1.1
License: GPL-2.0

The easiest way to create beautiful Vimeo galleries on your WordPress site.

== Description ==

Aloha shows your video thumbnails with a title caption and popup video player.

Make your gallery stand out with our custom themes!
[http://vimeography.com/themes](http://vimeography.com/themes "vimeography.com/themes")

For the latest updates, follow us!
[http://twitter.com/vimeography](http://twitter.com/vimeography "twitter.com/vimeography")

== Installation ==

1. Upload `vimeography-aloha.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==
= Help! My theme doesn't look right! =

Okay, deep breath. More than likely, it is another plugin causing this issue. See if you can pinpoint which one by disabling your plugins, one by one, and really determining if you need it. If that task sounds daunting, try disabling plugins that are used for photo galleries, minifying scripts, widgets, or otherwise alter your blog's appearance.

= Can I change the look of my Vimeography theme? =

Heck yeah! Use the appearance editor to change your theme's style so that it matches your site perfectly.

== Changelog ==
= 2.1.1 =
* Fixes bug with background close and close button on all modals in Firefox
* Adds default font size to search box

= 2.1 =
* Update theme dependencies to latest versions
* Add `fitvidsignore` attribute to player to prevent fitvids intervention
* Lightbox templates can now be selected for overrides
* Add support for loading "unlisted" videos in Pro
* Improve error message with link to docs on video load error
* Added search results indicator to all themes
* Bump compatibility to WordPress 5.4

= 2.0.7 =
* [Tweak] Thumbnails are now lazy loaded by default
* [Tweak] Videos that appear in a lightbox will now autoplay when the thumbnail is clicked
* [Tweak] Hide the spinner if a search returns no results
* [Tweak] Change the close element in modal windows to an anchor tag
* [Tweak] Unload the video player before loading a new video in it

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
* Completely rewritten for Vimeography 2 compatibility

= 1.2.2 =
* Theme is now loaded as soon as the plugin class is instantiated.

= 1.2.1 =
* You can now control thumbnail width and height with separate appearance sliders.
* Fixed a bug where sort direction wasn't honored with multiple pages of videos.
* This update also helps make sure your site doesn't run into errors if the Vimeography plugin is deactivated.

= 1.2 =
* Allow video downloads for Vimeo Pro members using Vimeography Pro
* Not a Vimeo Pro member? You're missing out. Learn more at http://vimeography.com/vimeo-pro
* Check out all Vimeography Pro features at http://vimeography.com/pro

= 1.1 =
* Added support for playlists with Vimeography Pro

= 1.0.8 =
* [Fix] Open fancybox in parent window if being loaded in iframe

= 1.0.7 =
* Fancybox assets are now loaded from http://cdnjs.com/libraries/fancybox/

= 1.0.6 =
* Moved Fancybox dependency into the Aloha theme
* Updated assets organization

= 1.0.5 =
* Updated paging controls for Vimeography Pro
* Reduced filesize
* Updated testing stats

= 1.0.4 =
* Further compatibility with Vimeography Pro.

= 1.0.3 =
* Aloha now autoplays videos once clicked.

= 1.0.2 =
* Fixed an issue where the layout might not center itself.
* Updated CSS styling.

= 1.0.1 =
* Fixed an issue where the layout could render inconsistently.
* Sometimes the video wouldn't load. Now it will. This is good news.

= 1.0 =
* Converted to plugin.

== Upgrade Notice ==
= 2.0 =
Requires Vimeography 2.0

= 1.0 =
This update prevents your purchased themes from being publically accessible.
