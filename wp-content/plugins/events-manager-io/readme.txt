=== Events Manager I/O ===
Contributors: netweblogic
Tags: events, event, event import, event export
Requires at least: 4.8
Tested up to: 5.2.2
Stable tag: 1.2
Requires PHP: 5.3

== Description ==

This is an importer/exporter plugin for Events Manager, currently in closed beta for Pro customers.

Events Manager I/O supports syncing import sources from all sources, and can also sync to export destinations that allow it (Google Calendar/Sheets and Meetup.com)

Upon installation, you can import and export to CSV, Excel, and iCal. You can also download the additional plugins to add importing for

* Google Calendar and Sheets
* Meetup.com
* Facebook.com (only supports importing events you, the user, interact with or pages you are an admin of)

For these additional plugins, you will also need to create corresponding developer accounts on their site to enable oAuth login, so that you can connect Events Manager I/O with your account on these sites.

== Installation ==

Please visit http://wp-events-plugin.com/documentation/installation/ and follow the same instructions as for Pro.

== Changelog ==
= 1.2 =
* fixed spreadsheet formats not exporting images
* fixed export file name default prefix starting with emio-csv- instead of format prefix
* fixed event creation issues via cron when privacy settings require consent for event submissions
* fixed import history logging not showingg failed attempts
* added support for individual custom meta fields for import mapping in spreadsheets,
* added JSON meta (custom fields and attributes) columnt to spreadsheet exports
* fixed custom field JSON and serialized formats not getting recognized
* added location type import options
* fixed bug in admin checkboxes function for settings page
* fixed inability to remove all tags or categories from import/export filter input boxes
* added category/tag import support from source for existing categories/tag names slugs or term ids
* fixed batch deleting import/export job not working if format is not active aymore
* added option to create new tags/categories upon import
* fixed categories and tags not getting created
* fixed location_id not linking to current location

= 1.1.1 =
* fixed PHP warning when importing ical for some PHP versions
* fixed location advanced settings and source ID checking option for imports not showing up anymore

= 1.1 =
* fixed ical importer not parsing all-day events if no times supplied in start/end dates
* added tolerance to ical files with empty white lines
* fixed downloadable file issues when switching between url and file types
* updated external libraries
* fixed various PHP warning/errors
* fixed endless loop bug when adding 0 limit to exports
* fixed headers not being added to CSV exports
* fixed google server key not being retrieved for usage in address prediction
* fixed settings page meta box CSS size issues in recent WP versions
* improved VTIMEZONE support including RRULE compatibility
* added event location support
* added merging of $options array with a master $options_default array
* added option to hide attachments setting field in imports
* added minimum version checks of EMIO for format add-ons and also invers format API check
* added taxonomy filtering for exports
* unified taxonomy meta key value and get_post() functions for imports/exports to 'taxonomies' with backwards compat for EMIO_Imports
* fixed export public link changing upon each export job save
* fixed PHP error when accessing non-existent public export link

= 1.0 =
* Initial public release