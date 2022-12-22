=== LearnDash Visual Customizer ===
Contributors: Ross Johnson
Tags: LearnDash
Requires at least: 5.0
Tested up to: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Customize the look and feel of LearnDash with five custom skins, icons and color pickers.

== Description ==

LearnDash Visual Customizer gives you the ability to select from five custom skins each with their own unique look, feel and icons and customize colors using a set of custom color pickers. If you like the default LearnDash appearance but want new colors the LearnDash Visual Customizer lets you do that as well.

= Website =
http://snaporbital.com/downloads/LearnDash-visual-customizer/

= Documentation =
http://docs.snaporbital.com/

= Bug Submission and Forum Support =
http://docs.snaporbital.com/

== Installation ==

1. Upload 'ld-visual-customizer' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Under LearnDash > Settings you will have a new tab called "Appearance"
4. Under appearance you can switch between selecting your base theme, customizing colors and managing your license

== Changelog ==

= 2.3.7.5 =
* Fixes issue with icon picker on legacy
* Fixes some responsive issues with grid template

= 2.3.7.3 =
* Fixes issue with expanded navigation widget and grid
* Refinements on Focus Mode sidebar collapsed

= 2.3.7.2 =
* Adds new course navigation widget with expanded view

= 2.3.7.1 =
* Updates POT file
* Tweaks responsive breakpoints on grid

= 2.3.7 =
* Removed deprecated focus mode width (LD can handle this now)
* Better support for quiz questions
* Fixed issues with some buttons not getting styled
* Refinements on theme presets
* Refinements on templates
* Thumbnails on grid with banners are now relative for better responsive
* Fixed issue where course grid changes wouldn't display if added via a block
* Added view lesson / quiz / topic buttons in grid view
* Improved BuddyBoss Support
* Adds content type and duration to course grid if course grid add-on is installed

= 2.3.6.1 =
* Thumbnails on LearnDash Groups grid view
* Fixes attempt to load non-existent CSS file

= 2.3.6 =
* BuddyBoss compatibility improvements

= 2.3.5.4 =
* Visual tweaks and refinements
* New focus mode theme, sidebar only
* Fixed some breadcrumb display issues
* Enforces meta update on submitted post type only

= 2.3.5.3 =
* Cache busting

= 2.3.5.2 =
* Fixes CSS on screen

= 2.3.5.1 =
* Small tweak in breadcrumbs on mobile

= 2.3.5 =
* New minimal theme for focus mode
* Focus mode specific font sizing
* Focus mode specific color controls
* Time and content type appear in breadcrumbs

= 2.3.1 =
* Fixes issues with quiz answer styling getting overwritten

= 2.3 =
* Adds options to course grid add-on
* Relabeling and organization of settings for easier use
* Adds options to focus mode
* Another round of support for BuddyBoss
* Removes dependency on outside Google call for fonts
* Adds course icon and estimated time to course templates

= 2.2.10 =
* Checks to see if CURL is possible before making CURL call

= 2.2.9 =
* Individual ribbon color options

= 2.2.8 =
* Fix alignment on grid with less than full row
* New hover effects for course grid add-on
* Option to disable font awesome icons all together
* Misc styling tweaks and compatibility fixes

= 2.2.7 =
* Adds option for icon size
* Adds option for base font size
* UI improvements and theme compatibility optimizations
* Adds option to disable Font Awesome

= 2.2.6 =
* Design, spacing, theme compatibility optimizations
* Improved settings UI

= 2.2.5 =
* Misc optimizations

= 2.2 =
* Now compatible with BuddyPress!
* Added new options for focus mode
* Added new customizations for the course grid add-on

= 2.1.13 =
* Misc minor bug fixes and optimizations

= 2.1.9 =
* Misc minor bug fixes and optimizations

= 2.1.8 =
* New "stacked" template!
* Supports customizing LearnDash Course Grid
* Added price to LDS course grid

= 2.1.3 =
* Misc small display fixes

= 2.1 =
* Feature: Hide breadcrumbs
* Feature: Hide lesson / topic counts
* Feature: Have LD inherit theme font sizes
* Feature: Hide progress count on content tables
* Feature: Hide progress steps on progress bar
* Feature: Hide last activity on progress bar
* Feature: Control focus mode content width
* Feature: Adds a widget area to focus mode (below menu) (next to content)

= 2.0.6 =
* Misc consistency fixes
* Adds support for ld_course_list for LearnDash 3.0 theme

= 2.0.1 =
* Minor bug fix on shortcodes in legacy mode

= 2.0 =
* Massive overhaul for LearnDash 3.0! Supports the new modern theme

= 1.7.5.1 =
* Fontawesome version swaps iconpicker version in admin
* Updated version of Fontawesome icon picker for better gutenberg compatibility
* Typo on quiz incorrect background color

= 1.7.5 =
* Better support for drip feeding in the grid view
* Increased template include priority to override bundled themes

= 1.7.3 =
* Fixes bug where primary template overrides don't work
* Adds support to change number of columns when using the grid with banners template
* Changes ESC URL to ESC_ATTR

= 1.7.1 =
* Works on nested URLS with shortcodes
* Updated textdomain on some strings
* Adds ability to select between FontAwesome 4 and 5
* Fixes issue with stepped course links on shortcodes

= 1.7 =
* Adds FontAwesome 5 support
* You can now manage and preview all customizations through the WP Customizer
* New method of registering widgets
* Better consistency across themes

= 1.6.2.10 =
* Adds button to reset all colors
* Some styling consistency improvements
* Adds new hooks and filters from updated LearnDash templates

= 1.6.2.9 =
* Adds support for materials on lessons / topics inline new LearnDash capabilities

= 1.6.2.6 =
* Updated translation strings

= 1.6.2.5 =
* Added rustic theme back, new and improved

= 1.6.2.1 =
* Custom icon support in expanded widget
* Fixes compatibility issues with other plugins and saving settings

= 1.6.2 =
* Found issue with the_excerpt() causing infinite loop when using the page builder
* Some PHP backwards compatibility

= 1.6.1 =
* Misc bug fixes and optimizations

= 1.6 =
* More streamlined markup and templates.
* Added ability to override individual templates by copying them into /wp-content/themes/yourtheme/learndash/ldvc/*
* Added new template of grid style

= 1.5.3.5 =
* More strict styling on classic theme

= 1.5.3.3 =
* Extra conditionals around course materials output

= 1.5.3.2 =
* Restores [lds_lesson_list id="X"]

= 1.5.3.1 =
* Different enqueue method for dynamic file

= 1.5.3 =
* Expanded course list will only show an icon or a featured image, not both

= 1.5.2 =
* Fixed issue where templates wouldn't load in some hosting
* Added ability to pick your own icons if you don't like the default content type icons
* Added cat="" and tag="" support for [lds_expanded_course_list] and [lds_course_list]

= 1.5.1 =
* Added check for custom label class before use

= 1.5 =
* New expanded course navigation widget
* Added [lds_login] widget for a stylized login box
* Refined all themes!
* New course progress leaderboard in enhanced learndash template

= 1.4.5.1 =
* Fixes gap in expanded style listings in some situations

= 1.4.5 =
* Fixed mismatched translation slugs
* Added support for id="X" in [lds_expanded_course_list] shortcode

= 1.4.2.5 =
* Fixed bug with missing icon on default icon set

= 1.4.2.4 =
* Fixes translation string in english translation file

= 1.4.2.3 =
* Fixes PHP notices on profile pages with enhanced progress bar

= 1.4.2.2 =
* Changes to enhanced progress bar for recent version of LD
* Added support for quiz background and text color

= 1.4.2.1 =
* Hides steps in the progress bar for courses you have not enrolled in
* Updated translation source

= 1.4.2 =
* Another activation issue on old versions of PHP
* Fixes missing status icons on profile page
* Prevents errors if LearnDash is not enabled

= 1.4.1 =
* Fixed issue with missing 0 on enhanced progress widget
* Fixed activation issue on very old versions of PHP

= 1.4 =
* Added two new course listing shortcodes [lds_course_list cols="2" style="icon"] [lds_course_list cols="2/3" style="banner"] and [lds_expanded_course_list]
* Added shortcode for an enhanced progress bar [lds_progress]
* Added widget for an enhanced progress bar
* Added new course, lesson, topic view that can be set using LearnDash > Apperance > Settings > theme
* Added "enhanced" LearnDash setting that includes duration, content type / icon and short description to LearnDash Content


= 1.3.4.6 =
* Found another place where the print certificate button color was not being applied

= 1.3.4.5.1
* Fixed issue with styling the print certificate button

= 1.3.4.5
* Fixed course navigation widget compatibility with LearnDash release.

= 1.3.4.3
* Added option to deregister scripts for better compatibility with themes that have their own LD Styling
* Changed styling for drip feed courses

= 1.3.4
* Added ability to adjust text sizes
* Fixed issue with missing quiz icons

= 1.3.3
* Updated method of saving licenses to fix activation reset

= 1.3.2
* Switched default enqueue method to inline for compatibility

= 1.3.1
* Added more strict markup for better consistency

= 1.3
* Added ability to write out stylesheet for faster loading or embed in the head for compatibility
* Added ability to select the widget title selector for theme compatibility
* Bug fixes

= 1.2.2 =
* Bug fixes

= 1.2.1 =
* Bug fixes

= 1.2 =
* Combined stylesheets, preventing two color flashes
* Added animation effects option
* Added ability to select which icon set you'd like to use
* Added ability to customize border radius on buttons


= 1.1 =
* Moved activate button down on the page

= 1.0 =
* Initial Release!
