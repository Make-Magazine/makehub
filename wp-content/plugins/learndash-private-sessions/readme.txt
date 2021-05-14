=== LearnDash Private Sessions ===
Contributors: John Wright, Ross Johnson
Tags: LearnDash
Requires at least: 3.5.0
Tested up to: 4.7.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Have one on one private coaching sessions with users

== Description ==

Through this add-on group leaders can start one on one private discussion sessions with any of their students, allowing you to provide more value and discuss topics you wouldn’t otherwise do in an open setting.
= Website =

https://snaporbital.com/downloads/learndash-private-sessions/

= Documentation =
https://docs.snaporbital.com/

= Bug Submission and Forum Support =
https://docs.snaporbital.com/

== Installation ==

1. Upload 'learndash-private-sessions' folder to the '/wp-content/plugins/' directory or upload the learndash-private-sessions.zip through the WordPress admin
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You can configure it at LearnDash > Settings > Private Sessions
4. Activate your license

= Changelog =

1.3.8.2
* Checks for comment attachment class before moving forward
* Fixes tab button styling

1.3.8.1
* Fixes register activation hook issue

1.3.8
* Adds separate files tab to consolidate all files uploaded
* Adds integration support to Simply Schedule Appointments - https://simplyscheduleappointments.com/
* Fixes some logic around who can see the start session view tab
* UI enhancements and refinements
* Will output a notice if users have permission to start sessions but no one to start a session with
* Performance optimization on session list

1.3.7.1
* Added missing translation strings

1.3.7
* Added support for PGN file type
* Added search on message listing
* Added ability to archive sessions

1.3.5
* [message_count] shortcode
* More filters

1.3.1
* Adds support for custom fields on admin edit

1.2.3
* Added support for target="new" for [sessions_widget] to open in a new window

1.2.2
* Added filter for changing notification name and email address
* Fixed bug with inaccurate unread message highlights
* Fixed issue where sometimes themes would display more than one login form
* Fixed issue where the [session_widget] wouldn't display a create message button even when the user had permission

1.2.1
* Sessions are sorted by last updated / responded
* Ability to star messages to keep them at the top

1.2
* Adds support for personal data export / erase
* Hashed private session URLs
* Open a session now uses select2 for easier finding of students when you have a lot
* Now have the ability to grant session ability by LearnDash group

1.1
* Misc updates and fixes

1.0.6
* Better handling of starting sessions if you're not a group leader or admin

1.0.5.4
* Adds support for title in editor
* Adds export ability
* Allows group leaders to start sessions with other group leaders

1.0.5.3
* Fixes bug related to starting sessions from non-subscriber accounts

1.0.5.2
* Fixes bug related to deleting users and GDPR data

1.0.5.1
* Hooks into LearnDash delete learner data for GDPR
* Re-adds ability to click a session row to navigate to it

1.0.5
* Switches session start to non-ajax for reliability

1.0.4.1
* Adds option to enable a title for newly created sessions

1.0.4
* Adds option to create moderator accounts that can see all messages

1.0.3.10
* Adds filters
* Adds option to enable / disable e-mail notifications

1.0.3.8
* Resolves translation strings

1.0.3.7
* Adds support for SketchUp files

1.0.3.5
* Feature: Support for access="all" on sessions_widget shortcode, allows anyone to create a private session
* Feature: Option to enable creation of private sessions to any user role

1.0.3.3
* Resolves attachment errors

1.0.3.2
* Resolves WP Pro Quiz JS errors

1.0.3.1
* Runs without LearnDash installed

1.0.3
* Option to send plain text e-mails

1.0.2.4
* Different method of grabbing TinyMCE content

1.0.2.3
* Fixes issue with Yoast SEO causing white screens
* Added loading indicator for new messages

1.0.2.1
* Forces comments open on message pages
* Reworks the permalink saving

1.0.1
* Adds missing language folder and POT file

1.0
* Initial Release
