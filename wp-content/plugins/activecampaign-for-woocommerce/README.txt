=== ActiveCampaign for WooCommerce ===
Contributors: acteamintegrations, bartboy011
Tags: marketing, ecommerce, woocommerce, email, activecampaign, abandoned cart
Requires at least: 4.7
Tested up to: 6.1
Stable tag: 1.9.6
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

= CUSTOMER EXPERIENCE AUTOMATION FOR WOOCOMMERCE =
Grow your ecommerce business when you connect ActiveCampaign to WooCommerce. Automate lead generations, email, and SMS marketing that can help you drive conversions and build customer loyalty.

= ENGAGE WITH YOUR CUSTOMERS =
Leverage advanced segmentation to build personalized emails based on your audience’s interest. Sync your WooCommerce data with product catalog to use filters or manually select the products that you want to use in a targeted campaign.

= DRIVE REPEAT PURCHASES =
Automate revenue-driving activities such as welcome series, abandoned cart, post-purchase product reviews, promote new product launch, cross-sell, upsell, and more!

= FOLLOW YOUR CUSTOMERS' JOURNEYS =
Automatically sync customer purchase information into ActiveCampaign and track purchases through your pipelines. Effectively engage and follow up with customers right after they purchase.

= WHAT YOU CAN DO WITH ACTIVECAMPAIGN AND WOOCOMMERCE =
Everything you need to create unique and scalable customer experiences, you can:
- Sync your WooCommerce data to filter or manually select products you want to highlight in your campaign
- Leverage a library of pre-built automation recipes to promote new product launch, cross-sell, and upsell revenue-driving activities.
- Use abandoned cart email automations to increase purchase-completion rate
- Create personalized emails to build customer relationships and increase brand loyalty.
- Apply conditional content to send shoppers product recommendations based on their past purchases and criteria set.
- Engage with customers through promos and special offers using SMS.
- Use ecommerce reporting to identify which email campaigns and automations are driving the most sales revenue.

= FREE AUTOMATION RECIPES FOR WOOCOMMERCE USERS =
- [Accessory Upsell After Purchase Recipe](https://www.activecampaign.com/marketplace/recipe/accessory-upsell-after-purchase?utm_source=unpaid_syndication_website&utm_medium=referral&utm_campaign=woocommerce_listing_june_2022)
- [Ecommerce Subscription and Welcome Recipe](https://www.activecampaign.com/marketplace/recipe/ecommerce-subscription-and-welcome?utm_source=unpaid_syndication_website&utm_medium=referral&utm_campaign=woocommerce_listing_june_2022)
- [Birthday and Anniversary Coupon Email Recipe](https://www.activecampaign.com/marketplace/recipe/birthday-anniversary-email?utm_source=unpaid_syndication_website&utm_medium=referral&utm_campaign=woocommerce_listing_june_2022)

= FREE TOOLS WOOCOMMERCE USERS =
- [Lead nurturing email templates](https://www.activecampaign.com/free-marketing-tools/lead-nurturing-email-templates?utm_source=unpaid_syndication_website&utm_medium=referral&utm_campaign=woocommerce_listing_june_2022)
- [Ecommerce integration and automation starter pack](https://www.activecampaign.com/free-marketing-tools/sample-ecommerce-integration-and-automation-starter-stack?utm_source=unpaid_syndication_website&utm_medium=referral&utm_campaign=woocommerce_listing_june_2022)

= ABOUT ACTIVECAMPAIGN =
ActiveCampaign's category-defining Customer Experience Automation Platform (CXA) helps over [180,000+ businesses](https://www.activecampaign.com/tomorrows-business?utm_source=unpaid_syndication_website&utm_medium=referral&utm_campaign=woocommerce_listing_june_2022) in 170 countries meaningfully engage with their customers. The platform gives businesses of all sizes access to hundreds of pre-built automations that combine transactional email and email marketing, marketing automation and CRM for powerful segmentation and personalization across social, email, messaging, chat and text. Over 70% of ActiveCampaign's customers use its 900+ integrations including Microsoft, Shopify, Square, Facebook and Salesforce. ActiveCampaign scores higher in customer satisfaction than any other solution in Marketing Automation, CRM and E-Commerce Personalization on [G2.com](https://www.g2.com/products/activecampaign/reviews) and is the Top Rated Email Marketing Software on TrustRadius. Start a free trial at [ActiveCampaign.com](https://www.activecampaign.com/?utm_source=unpaid_syndication_website&utm_medium=referral&utm_campaign=woocommerce_listing_june_2022).

== Installation ==

= WooCommerce Compatibility =
* Tested up to version: 7.2.0
* Minimal version requirement: 3.6.0

= Minimum Requirements =
* Wordpress supported PHP version (PHP 7.4 or greater is recommended)
* Latest release versions of WordPress and WooCommerce are recommended
* MySQL version 5.6 or greater

= Before You Start =
- Our plugin requires you to have the WooCommerce plugin installed and activated in WordPress.
- Your hosting environment should meet WooCommerce's minimum requirements, including PHP 7.0 or greater.

= Installation Steps =
1. In your ActiveCampaign account, navigate to Settings.
2. Click the Integrations tab.
3. If your WooCommerce store is already listed here, skip to step 7. Otherwise, continue to step 4.
4. Click the "Add Integration" button.
5. Enter the URL of your WooCommerce site.
6. Follow the connection process that appears in WooCommerce.
7. In your WooCommerce store, install the "ActiveCampaign for WooCommerce" plugin and activate it.
8. Navigate to the plugin settings page (Settings > ActiveCampaign for WooCommerce)
9. Enter your ActiveCampaign API URL and API Key in the provided boxes.
10. Click "Update Settings".

== Changelog ==

= 1.9.6 2022-12-07 =
* Bugfix for abandoned cart sync, duplicates, & add to cart customerid errors
* Bugfix for products not syncing due to product attribute naming
* AC Cronjobs will now be removed and re-scheduled on settings save

= 1.9.5 2022-12-02 =
* Bugfixes for 400 errors in historical sync
* Bugfix for connection issues and improvements to repair
* Bugfix for product variations

= 1.9.4 2022-11-21 =
* Bugfixes for historical sync 400 errors

= 1.9.3 2022-11-18 =
* Products will now sync to ActiveCampaign on creation, update, or stock change
* Removing HTML and limiting product descriptions when syncing products

= 1.9.2 2022-11-07 =
* Various fixes for product sync

= 1.9.1 2022-11-02 =
* Fix for product sync status

= 1.9.0 2022-11-02 =
* Adding product sync feature

= 1.8.1 2022-10-31 =
* Various bugfixes for abandoned cart, customer data, and order syncing.
* Adding a retry for timeouts and connection failures.
* Adds a version number to logging to track which version threw specific errors.

= 1.8.0 2022-07-27 =
* Adds a status for cron jobs
* Vendor file updates
* Bugfix for null orders
* Bugfix for conversion to cents issue
* Bugfix for order processing hooks not having a session available
* Bugfix for contacts not syncing

= 1.7.14 2022-09-07 =
* Bugfix for some accounts not syncing live orders

= 1.7.13 2022-08-31 =
* Limits how often background services are run
* Adds a foreground job to trigger functions that are not happening in cron
* Adding more clarity to abandoned cart status
* Fixes the source status for sync
* Cleanup and bug fixes for syncing new orders
* Bugfixes for abandoned carts not syncing
* Bugfix for coupon codes
* Adds simple report info to the status page
* Add transparency to order sync and health check

= 1.7.12 2022-08-09 =
* Hotfix for new orders wrongly syncing as historical sync
* Fixes inconsistent automation triggers

= 1.7.11 2022-08-04 =
* Bugfix for when historical sync is run background without contacts box checked

= 1.7.10 2022-08-03 =
* Added a cache flush and time extension to historical sync to keep the process from crashing
* Resolved a bug with the contact sync and phone number lookup
* Added more detail to the historical sync page

= 1.7.9 2022-07-18 =
* Added contact syncing to historical sync for all contacts regardless of order status
* Reverted live order syncing to single record create/update to resolve various bugs

= 1.7.8 2022-06-22 =
* Bugfix for abandoned carts not syncing

= 1.7.7 2022-06-09 =
* Bugfix for order totals syncing with the incorrect value
* Bugfix for abandoned cart date check issues

= 1.7.6 2022-05-25 =
* Bugfix for serialization error and guzzle handling

== Screenshots ==

1. ActiveCampaign for WooCommerce
2. Post-purchase thank you and product suggestion ActiveCampaign for WooCommerce automation workflow
3. WooCommerce store purchase history on an ActiveCampaign contact
4. Accessory upsell after purchase ActiveCampaign automation recipe for WooCommerce stores
5. Ecommerce subscription and welcome ActiveCampaign automation recipe for WooCommerce stores
6. Birthday and anniversary coupon email ActiveCampaign automation recipe for WooCommerce store
