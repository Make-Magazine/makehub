=== Gravity Press ===
Author URI: http://ristrettoapps.com
Plugin URI: https://ristrettoapps.com/downloads/gravity-press/
Requires at least Wordpress: 3.7
Tested up to: 5.7
License: GNU Version 2
Description:
Integrate Gravity Forms and MemberPress: utilize Gravity Forms to take payment and registration and add new or existing users to MemberPress Levels

### Need Help? ###

http://docs.ristrettoapps.com/


### Change Log ###

== 3.4.5 ==
- Fix: Disable presslog.txt debug file from being generated to reduce security vulnerabilies
- Fix: Defined the variable with default value 0
- Fix: Fixed the warnings with optional parameters
- Fix: Fixed missing 'get_default_card' error

== 3.4.3 ==
-Fix: Double Stripe calculation issue in MemberPress reporting
-Fix: Membership status not showing Complete when this is setup in feed settings for offline payments and/ free membership registration
-Feature: Allow existing MP customers with existing subscription/s to sign up for other subscription without expiring their initial subscription/s
-Feature: Ability to pass additional information (custom data) from GF fields to MemberPress by using GF user reg add-on's user meta option


== 3.4.2 ==
-Update for all users

== 3.4.1 ==
-Added support for PayPal Checkout
-Added support for PayPal Standard
-Added support for MemberPress Corporate Accounts
-Added support for Stripe Payment Form

== 3.4.0 ==
-Feature: Ability to login newly created user after successful PayPal payment
-Feature: Gravity Press enabled Gravity Forms forms can now be submitted by logged-in users
-Bug: Fixed transactions not showing on MemberPress for some Stripe Payments
-Bug: Fixed duplicate recurring subscription email from being sent out
-Bug: Fixed error after deactivating plugin
-Bug: Fixed plan price amount in multiple membership when using one form
-Redesigned settings page

== 3.3.1 ==
-Updated to be compatible with Gravity Forms 2.5

== 3.3.0 ==
-[Pro] Fixed issue where members could not pause subscriptions created by Gravity Press using MemberPress account page 
-[Pro] Added support for different currencies (especially useful for offline payments) -Reimplemented Auto Login after form submission

== 3.2.3 ==
-Fixed issue with Auto-Login setting not showing up and functioning properly
-[Pro] Fixed issue where members could not pause subscriptions created by Gravity Press using MemberPress account page
-[Pro] Added support for different currencies (especially useful for offline payments)


== 3.2.3 ==
-[Pro} Fixed issue with upgrading/downgrading using MemberPress Groups after registration with GP
-Fixed issue where uninstall button did not delete plugin settings

== 3.2.1 ==
-Fixed issue for those upgrading from GP2: legacy subscriptions did not create new MP transactions if they were created in GP2
-Removed previous admin message asking to setup Stripe webhook as it's no longer critical for V3.X
-Added admin message about not supporting Stripe Payment Form setting
-Added new admin message about not supporting Stripe Payment Form 

== 3.2.0 ==
-Various event-driven bug fixes with Stripe checkout
-Support added for GF User Registration ad-on "Enable User Activation" integrated for Stripe
-Support added for Offline Payments (whereby users can be manually approved by Admin)

== 3.1.2 ==
-Added preliminary support for Authorize.net gateway

== 3.1.1 ==
-Fixed issue where double transactions were being created with Stripe.

== 3.1 ==
-Added ability to allow free/no-charge signups for MemberPress membership levels
-Fixed issue where other Gravity Forms Addons may interfere with ability for plugin to activate if using EDD for licensing

== 3.0 ==
-Major rewrite of codebase
-Subscriptions are now initiated by Gravity Forms Stripe but completed by and handed over completely to MemberPress, thus managed by MemberPress going forward, allowing support for all MemberPress account changes natively
-Reverted "memberpress_selectLevel_field" back to "mappedFields" db key to ensure smoother upgrade from 2.x to 3.x 

== 2.5.3 ==
-Feature: Override renewal link on MP account page (under GF Settings->Gravity Press)
-Bug Fix / Improvement: Disable MemberPress Level Fields that are already in use by other Gravity Press feeds to prevent members not being added in some cases

== 2.5.2 ==
-Fixed bug that prevented new members from being added to MP level in some cases

== 2.5 ==
-Added Autologin feature for new users (Excluding PayPal Standard)
-Improved admin notices to be more informative
-Added setting to make new transactions(subscriptions if Pro) Complete/Pending
-Added support for MemberPress Corporate Sub-accounts Add-on (Pro)
-Added feature to set a different form filed for MP membership level price besides form total (Pro)
-Improved cancellation of GF subscriptions function (Pro)

== 2.4 ==
-Fixed bulk memberships limit issue

== 2.3 ==
-Updated settings area to be read more accurately

== 2.2 ==
-Fixed Authorize.net Subscription Cancellation function
-Updated MemberPress Account templates

== 2.1 ==
-Fixed PayPal Subscription Cancellation function

== 2.0 ==
-Added improved support for subscription management
-If GF Subscription created, a MemberPress subscription will be created linked to GF Entry
-Each successful GF subscription payment from Stripe, PP, Authorize.net creates a MP transaction which continues member’s access to their MP levels
-Allows members to cancel their own MP subscriptions on MP Account page
-Cancelled membership subscriptions will cancel MP subscription but allow user to retain access to their level until next scheduled date, at which point their membership will terminate

== 1.5.0 ==
-Relocated Plugin Version Constant
-Increased timeout for activating/checking license

== 1.4.7 ==
-Removed redundant bulk MemberPress Levels field add feature.

== 1.4.6 ==
-Updated Licensing Activation files to latest versions in order to match license system

== 1.4.5 ==
-Fixed incompatability with other EDD License Plugins

== 1.4.4 ==
-Fixed issue in which Gravity Press disrupted normal form validation process if using multi-page forms

== 1.4.3 ==
-Fixed issue which threw error on activation on certain servers

== 1.4.2 ==
-Fixed issue which prevented existing users from being added if PayPal addon was installed but not active
-Fixed issue which prevented Conditional Logic on feed from working properly

== 1.4.1 ==
-Removed legacy calls to previous plugin internal styling which are no longer relevant 

== 1.4.0 ==
-Moved GravityPress Subscription Support into its own separate plugin for easier updating for Pro and Developer customers

== 1.3.5 ==
-Fixed bug where menus weren’t showing unless Gravity Forms Addons were enabled

== 1.3.4 ==
-Fixed critical error when specific, required Gravity Forms add-on were not enabled.
-Removed plugin specific administration-only styling to rely fully on Gravity Forms admin styling

== 1.3.3 ==
-Added Gravity Forms Subscription Cancellation Support for Pro and Developer versions: automatically expires MemberPress member transactions immediately upon cancellation or failed payments setup by Gravity Member in various payment gateways.

== 1.3.2 ==
-Fixed bug that prevented functionality for non-product drop-down/radio fields

== 1.3.1 ==
-Included “Amount” to MemberPress transactions coming through Gravity Press

== 1.3 ==
-Fix to updater

== 1.2 ==
-Fix for form validation errors on multi-page forms

== 1.1 ==
-Bug fixes to add user to level when offline/online

== 1.0 ==
-First version
