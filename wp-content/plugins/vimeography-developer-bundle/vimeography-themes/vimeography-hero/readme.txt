=== Vimeography Hero ===
Tags: vimeo
Requires at least: 3.8
Tested up to: 5.5
Stable tag: 2.1.1
License: GPL-2.0

The easiest way to create beautiful Vimeo galleries on your WordPress site.

== Description ==

Hero displays your videos in a modern gallery layout for your portfolio or membership site.

Make your gallery stand out with our custom themes!
[http://vimeography.com/themes](http://vimeography.com/themes "vimeography.com/themes")

For the latest updates, follow us!
[http://twitter.com/vimeography](http://twitter.com/vimeography "twitter.com/vimeography")

== Installation ==

1. Upload `vimeography-hero.zip` to the `/wp-content/plugins/` directory
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
* Bump compatibility to WordPress 5.5

= 2.1 =
* [New] Show number of search results in message after query is performed.
* Update theme dependencies to latest versions
* Add `fitvidsignore` attribute to player to prevent fitvids intervention
* Add support for loading "unlisted" videos in Pro
* Improve error message with link to docs on video load error
* Bump compatibility to WordPress 5.4

= 2.0.7 =
* [Tweak] Thumbnails are now lazy loaded by default
* [Tweak] Hide the spinner if a search returns no results
* [Tweak] Unload the video player before loading a new video in it

= 2.0.6 =
* [Fix] Ensure scroll to top targets current gallery player
* [Fix] Introduce fallback for when source video downloads are unavailable
* [Fix] Navigate to the current window pathname instead of root
* [Tweak] Allow player settings to be configured via Vimeography Pro
* [Tweak] Upgrade to Vimeo.js 2.6.x

= 2.0.5 =
* [Fix] Mangle double let declaration errors in Safari 10

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

= 2.0.1 =
* Switch to new Download Link component from Vimeography Blueprint
* Switch to new Thumbnail Mixin from Vimeography Blueprint
* Adds an :alt tag to thumbnail images

= 2.0 =
* This is the first public release of the Hero theme.

== Upgrade Notice ==
= 2.0 =
This is the first public release of the Hero theme.
