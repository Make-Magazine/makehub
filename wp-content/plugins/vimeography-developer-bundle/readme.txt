=== Vimeography Developer Bundle ===
Contributors: iamdavekiss
Tags: vimeo
Requires at least: 3.8
Tested up to: 4.9.4
Stable tag: 2.0.6
License: GPL3

The easiest way to create beautiful Vimeo galleries on your Wordpress blog.

== Description ==

The Developer Bundle is a collection of all of the themes sold on Vimeography.com

Make your gallery stand out with our custom themes!
[http://vimeography.com/themes](http://vimeography.com/themes "vimeography.com/themes")

For the latest updates, follow us!
[http://twitter.com/vimeography](http://twitter.com/vimeography "twitter.com/vimeography")

== Installation ==

1. Upload `vimeography-developer-bundle.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==
= Help! My theme doesn't look right! =

Okay, deep breath. More than likely, it is another plugin causing this issue. See if you can pinpoint which one by disabling your plugins, one by one, and really determining if you need it. If that task sounds daunting, try disabling plugins that are used for photo galleries, minifying scripts, widgets, or otherwise alter your blog's appearance.

= Can I change the look of my Vimeography theme? =

Heck yeah! Use the appearance editor to change your theme's style so that it matches your site perfectly.

== Changelog ==

= 2.0.6 =
* [All Themes] Introduce fallback for when source video downloads are unavailable
* [All Themes] Navigate to the current window pathname instead of root
* [All Themes] Allow player settings to be configured via Vimeography Pro
* [All Themes] Upgrade to Vimeo.js 2.6.x
* [Hero] Ensure scroll to top targets current gallery player

= 2.0.5 =
* [All Themes] Mangle double let declaration errors in Safari 10
* [Lightbox Themes] Reset gallery route whenever the lightbox is closed
* [Playlister] Change overflow: none rule to overflow: hidden

= 2.0.4 =
* [All Themes] Update Vimeography Blueprint helpers for player padding
* [Playlister] Move autoscrolling capability to use the vue-scroll library
* [Playlister] Improve styles in IE11
* [Playlister] Update Vimeography Blueprint helpers for player padding
* [Journey] Rebuild js bundle for IE11 compatibility

= 2.0.3 =
* [All Applicable Themes] Theme lightbox and search component can now be overridden
* [All Applicable Themes] Add placeholder text to searchbox
* [All Applicable Themes] Polyfill Object.assign for vue-js-modal compatibility with IE11
* [All Applicable Themes] Add fallback flexbox support for IE11
* [All Themes] Adds postcss-cssnext to theme build process
* [Timber] Add PRO control to hide title
* [Timber] Ensure figcaption text is correct color
* [Playlister] Removes potential box-shadow from thumbnail
* [Playlister] Prevents browser from jumping right to gallery on pageload
* [Bridge] Allow for separate spacing controls for thumbnail grid

= 2.0.2 =
* [All Themes] Add rendering compatibility for Microsoft Edge 16
* [All Applicable Themes] Add better responsive display for pop-up window on smaller screens

= 2.0.1 =
* [All themes] Switch to new Download Link component from Vimeography Blueprint
* [All themes] Switch to new Thumbnail Mixin from Vimeography Blueprint
* [All themes] Adds an :alt tag to thumbnail images
* [Squares] Rewrite grid CSS for more predictable square images.

= 2.0.0 =
* New! Vimeography Developer Bundle 2.0
* All themes have been updated to be compatible with Vimeography 2
* Most themes now support gallery search and direct links to videos within your gallery
* Every theme has been rewritten from scratch for better performance and compatibility
* Please report any unexpected issues to support@vimeography.com - thanks!

= 1.2.3 =
* [All Themes] Ensure themes are loaded at the correct priority for your site.
* [Timber] Fonts are now loaded over your site's protocol.
* [Journey] Ensure the player exits fullscreen when playlisting
* [Squares] Update loading technique for newer version of jQuery
* [Aloha] Sort direction is now honored appropriately during paging requests
* [Circles] Rewrite the margin expression for resizing circle width.

= 1.2.2 =
* [Aloha] You can now control thumbnail width and height with separate appearance sliders.
* [Journey] Added schema.org markup for enhanced SEO
* [Playlister] Add compatibility with Vimeography PRO download links.
* [Playlister] Fixed a bug where exiting fullscreen could cause a layout display issue.
* [All themes] Fixed a bug where sort direction wasn't honored with multiple pages of videos.
* This update also helps make sure your site doesn't run into errors if the Vimeography Developer Bundle plugin is deactivated.

= 1.2.1 =
* [New] Now including: Playlister! Our new responsive gallery theme that shows a list of videos in a scrolling sidebar that advances automatically with Vimeography Pro!
* [Squares] Fixed an appearance editor calculation bug on the margin of images.
* [Journey] Rewrote javascript to allow for multiple galleries per page and minified CSS and JS for faster pageloading.
* [6up] Load a local copy of Vimeo's Froogaloop library
* [Covers] Update Video Title height CSS

= 1.2 =
* [New] Allow video downloads for Vimeo Pro members using Vimeography Pro
* Not a Vimeo Pro member? You're missing out. Learn more at http://vimeography.com/vimeo-pro
* Check out all Vimeography Pro features at http://vimeography.com/pro

= 1.1 =
* [New] Added support for Vimeography Pro Playlists!
* [New] Say hello to Timber, the latest portfolio theme for Vimeography!
* [Greyscale] Added pagination support with Vimeography Pro
* [Bridge, Covers, Circles] Fancybox content is now opened manually on each thumbnail
* [Squares, Strips] Fancybox content is now set manually instead of using an iframe helper
* [Journey] Show the thumbnail overlay using pure CSS instead of jQuery animations
* [Journey] Add an appearance control for the playbar visibility [Vimeography Pro required]

= 1.0.10 =
* [Journey] Fixed an issue with thumbnail overlay resizing
* [Journey] Ensure that there is no bottom margin on thumbnails
* [Squares] Fixed an issue with thumbnail overlay resizing

= 1.0.9 =
* Add a license notification for the developer bundle on the product page
* [Journey] Added a fix where theme customizations would not load properly
* [Aloha, Squares, Strips] Fancybox will now open in the parent window if the gallery is being loaded in an iframe

= 1.0.8 =
* Moved the Fancybox script to use cdnjs for all Fancybox themes.
* [Journey] Improved responsive layout for mobile devices
* [Ballistic] Fixed an issue that caused Ballistic to fail with Vimeography Pro

= 1.0.7 =
* [Journey] Fixed an issue that caused double spacing on the first video description
* [Journey] Corrected the aspect ratio for video thumbnails

= 1.0.6 =
* Updated Bugsauce for Pro Compatibility
* Added thumbnail sizing controls to Journey

= 1.0.5 =
* Added the Greyscale theme
* Updates all themes for Pro support.

= 1.0.4 =
* Updated 8 themes for Vimeography Pro support.

= 1.0.3 =
* Ballistic now supports unlimited videos.
* Aloha, Squares, and Strips now autoplay videos once clicked.

= 1.0.2 =
* Updates to Aloha, Bridge, Circles, Squares, and Strips
* Fixed a bug where you might see inconsistent rendering.

= 1.0.1 =
* Fixed an issue in Bridge where the image path was incorrect.
* Updated 6up to have a fluid iframe width and height.

= 1.0 =
* Converted to plugin.

== Upgrade Notice ==
= 1.0 =
This update prevents your purchased themes from being publically accessible.