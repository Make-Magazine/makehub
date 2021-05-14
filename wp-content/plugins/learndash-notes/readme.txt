=== LearnDash Notes ===
Contributors: Ross Johnson
Tags: LearnDash
Requires at least: 5.0
Tested up to: 5.3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Give users their own notepad on LearnDash courses, lessons, topics or assignments.

== Description ==

LearnDash notes gives users the ability to take notes on courses, lessons, topics or assignments. Notes are saved through the front end through a draggable and resizable window. Once
saved, users can choose to download or print their notes. Use convenient shortcodes to display a list of a users notes for easy future reference.

= Website =
http://snaporbital.com/downloads/LearnDash-notes/

= Documentation =
http://snaporbital.com/docs/LearnDash-notes/

= Bug Submission and Forum Support =
http://snaporbital.com/support

== Installation ==

1. Upload 'learndash-notes' folder to the '/wp-content/plugins/' directory or upload the learndash-notes.zip through the WordPress admin
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Under "Settings" you will have a new tab called "LearnDash Notes"
4. Activate your license
5. Choose which content types you'd like the notepad to appear on (courses, lessons, topics or assignments)
6. Choose if you want access to notes through the WordPress admin
7. Choose if you want note links to open in a new window
8. Go to individual courses, lessons, topics or assignments and set default note titles and bodies, or optionally disable notes for specific pages.

= Changelog =

1.6.7.2
* Adds nt_user_note_body class so you can filter note content before it's loaded
* More checks for BBPress and BuddyPress pages

1.6.7.1
* Fixes debug notice on views with no page ID or page content
* Prevents notes from loading on any BBPress page

1.6.7
* Larger close button on mobile for easier clicking
* Fixes issue where custom single note template saved parent title rather than note title

1.6.6
* Group notes shortcode now outputs nice_name rather than display_name
* Prevents notes from showing up on BBPress pages
* Adds body class of has-notepad when notepad is present (for easier styling)
* Adds new placement of fixed to the right

1.6.5.3
* BUGFIX: for notes template page

1.6.5.2
* Taller editor on single note page
* Additional TinyMCE buttons

1.6.5.1
* Fixed issues with saving and printing on single note page

1.6.5
* Mobile UI improvements
* Fixed double confirmation message on delete
* Enhanced security
* More robust data validation on save
* Fixed: after you save, the button text reverts to english
* Improved single note template experience

1.6.4
* Adds ability to [note_editor id=""] and have multiple notepads on a given page

1.6.3
* Allows the forcing of the note editor to display with [note_editor force_display="true" ]
* Detects Elementor and adds CPT support if exists
* Adds ability to set global default title / body

1.6.2
* Adds filter ldnt_notes_per_page for options on number of visible notes per page
* Fixes gap below take notes tab at bottom
* Fixes issue caused by resizing textarea but not notepad

1.6.1
* Corrects redirect issues for reused lessons/topics

1.6
* Revised settings experience
* Tweaks to mobile experience

1.5.2
* Checks for LearnDash stepped permalinks

1.5.1
* Corrected cases where new color pickers weren't extending

1.5
* Reworked mobile UI
* Native responsive tables and table realignment
* You can now select a specific page to use as the single notes template
* New take notes button option

1.4.3
* Added jQuery UI support touch events on draggable and resizable
* Massaged support for iPhone X
* Adds support to configure how far from the right you'd like the tab to display
* Added autosave feature
* Added ability to save as basic RTF

1.4.1
* Fixes issue where note indicators showed up even when logged out (or in doubles)

1.4
* Better support for when TinyMCE doesn't load properly (not ideal but it works)
* Adds link to LD30 content listings if note exists
* More standard mobile support
* Added ability to control who can take notes based on user role or LearnDash group
* Improved styling on note table

1.3.7.5
* Minor logic and conditional updates

1.3.7.2
* Prevents the notepad shortcode or widget from appearing on coursenotes pages

1.3.7.1
* Misc minor bug fixes

1.3.7
* Misc minor bug fixes

1.3.6.2
* More support for edge cases where users don't save a title

1.3.6.1
* Changed logic on notepad initialization for better backwards compatibility
* Prevent the ability to support notes on notes (noteception)

1.3.6
* Saved note defaults to page name if no note title is created
* Downloaded note fallback if note doesn't have a title
* Interface overhaul
* Added attribute of posts_per_page="" to set the number of notes to display before pagination
* Users notes get deleted when you delete all LearnDash user data

1.3.4
* If you have new window set the all notes link opens in a new window as well
* Improved overall user experience
* Added pagination to notes
* Improved search capabilities
* Added a back to all notes link from individual note pages

1.3.3
* More logic around access and saving of other users notes

1.3.2
* Fixes issues with content being output as escaped HTML
* Reworks how and when scripts are enqueued
* Adds translation for delete confirmation

1.3
* Added new permissions 'read_others_nt_notes', 'edit_others_nt_notes', 'delete_others_nt_notes'
* Group leaders can now edit and read others notes
* Added new shortcode [nt_my_groups_notes] for group leaders to navigate users notes within their groups

1.2.7.1
* Different save routine

1.2.7
* Better support for multilingual characters in exported doc files
* Fixed issue with attempting to download notes on iOS devices

1.2.6
* If notepad was left outside the viewport dimensions will automatically reset

1.2.5
* Fixed bug with hiding notepad on mobile
* Added ability to turn on notepad for any page, post or custom post type
* If no custom title is set, the post title will be used
* Added live search on note listing shortcode

1.2.3
* Added support for special characters in word downloads

1.2.2
* You can now use [note_editor] shortcode to put the note editor right in the page

1.2.1
* You can now use [learndash_course_notes display="user"] so administrators only see their own notes

1.2
* Added option to place editor using a shortcode
* Ensured WP Media file enqueuing for compatibility

1.1.12
* Added listing of users notes to the user profile page for editors / administrators
* Added button to bulk download notes with the [learndash_my_notes] shortcode

1.1.11
* Added attribute of order to note listing shortcode

1.1.9.7
* Renamed print function for compatibility reasons

1.1.9.6
* Change DOM placement to improve compatibility with overlapping
* Added check activation message option on license registration

1.1.9.1
* Fixed downloading with permalinks disabled

1.1.9
* Added ability to select page with the all notes shortcode
* Adds link to all notes in notepad

1.1.8.3.1
* Better handling of fixed positioning on some themes
* Fixes JS errors

1.1.8.2
* Misc bug fixes

1.1.8.1
* Added support for Quizes

1.1.7.2
* Added the ability to edit a note on the notes page
* Added some missing localization
* Better support for iPhone 5

1.1.7.1
* Adjusted saving method for settings

1.1.7
* Fixed issues with older PHP versions
* Added ability to customize colors
* Moved note taking icon default placement to the bottom of the screen
* Added option to move notes icon before or after the content
* Added option to move notes icon fixed to the top of the screen
* Added option to hide on mobile
* Better mobile support

1.1.5
* Added delete icon / option
* Changed method of including main icons
* Better mobile support
* Fixed issues with icons loading on individual note pages
* Added options to enabled / disabled notes on courses, lessons, topics or assignments
* Added option to open note links in a new window or not
* Added option on individual courses, lessons and topics to disable note taking
* Added option for a default note and title on individual courses, lessons and topics
* Basic implementation of new shortcode that outputs course hierarchy with associated notes [learndash_course_notes]

1.0
* Initial Release
