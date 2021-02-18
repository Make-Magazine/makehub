=== Entry Revisions by GravityView ===
Tags: gravityview, gravity forms
Requires at least: 4.4
Tested up to: 5.3.2
Stable tag: trunk
Contributors: gravityview
License: GPL 3 or higher

Track changes to Gravity Forms entries and restore from previous revisions. Requires Gravity Forms 2.0 or higher.

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
2. Activate the plugin
3. Edit entries in Gravity Forms as normal
4. You'll see a "Revisions" meta box on the entries page. Click the link next to the revision to compare versions, and restore.

== Changelog ==

= 1.0.3 on February 19, 2020 =

* Fixed: Error when Gravity Forms is deactivated
* Fixed: Linking to entry revisions from GravityView and [Gravity Forms Calendar](https://gravityview.co/extensions/calendar/)
* Fixed: PHP warning in Gravity Forms Entry screen

__Developer Updates:__

* Added: `gravityview/entry-revisions/add-revision` Whether to add revisions for the entry

= 1.0.2 on February 6, 2019 =

* Fixed: Minor PHP warnings
* Updated: Translations!
    - Chinese by Edi Weigh
    - Turkish by Süha Karalar
    - Russian by Viktor S
    - Polish by Dariusz Zielonka

__Developer Updates:__

* Added: The `gravityview/entry-revisions/send-notifications` filter, which supplies the changed fields array ([see filter documentation](https://docs.gravityview.co/article/483-entry-revisions-hooks#gravityview-entry-revisions-send-notifications))

= 1.0.1 on September 17, 2018 =

* Fixed: `{all_fields}` Merge Tag was being replaced with "This entry has no revisions."
* Updated: Polish, Russian, and Turkish (Thank you, [@dariusz.zielonka](https://www.transifex.com/user/profile/dariusz.zielonka/), [@awsswa59](https://www.transifex.com/user/profile/awsswa59/), and [@suhakaralar](https://www.transifex.com/accounts/profile/suhakaralar/)!)
* Improved: Added an error message when trying to activate a GravityView license key that does not have access to Entry Revisions

= 1.0 =

* Launch!
