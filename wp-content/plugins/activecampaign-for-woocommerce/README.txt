=== ActiveCampaign for WooCommerce ===
Contributors: acteamintegrations, bartboy011
Tags: marketing, ecommerce, woocommerce, email, activecampaign, abandoned cart
Requires at least: 4.7
Tested up to: 6.1
Stable tag: 1.7.14
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

= Customer Experience Automation for WooCommerce =
Power your online sales thanks to ActiveCampaign and WooCommerce. Help your customers throughout their buying journey to turn them from new customers to regulars.

= Enhance customer loyalty =
When connected to ActiveCampaign, WooCommerce users can use purchase behavior to turn first-time buyers into repeat customers.

= Acquire more customers =
Enhance your relationship with existing customers, and gain new customers as well. Set up welcome emails and automation workflows to reach more potential customers with high-quality marketing automation and messaging.

= Follow your customers' journeys =
Automatically sync customer purchase information into ActiveCampaign and track purchases through your pipelines. Effectively engage and follow up with customers right after they purchase.

= What you can do with WooCommerce and ActiveCampaign =
- Segment email campaigns and automations by purchase behavior and shopper demographics
- Text customers with promos and special offers using SMS
- Use abandoned cart email automations to increase purchase-completion rate
- Understand the right time to reach out to customers on the right subjects.
- Use conditional content to send shoppers product recommendations based on their past purchases
- Build relationships and brand loyalty through personalized messaging.

= Free Automation Recipes for WooCommerce users =
- [Accessory Upsell After Purchase Recipe](https://www.activecampaign.com/marketplace/recipe/accessory-upsell-after-purchase?utm_source=unpaid_syndication_website&utm_medium=referral&utm_campaign=woocommerce_listing_june_2022)
- [Ecommerce Subscription and Welcome Recipe](https://www.activecampaign.com/marketplace/recipe/ecommerce-subscription-and-welcome?utm_source=unpaid_syndication_website&utm_medium=referral&utm_campaign=woocommerce_listing_june_2022)
- [Birthday and Anniversary Coupon Email Recipe](https://www.activecampaign.com/marketplace/recipe/birthday-anniversary-email?utm_source=unpaid_syndication_website&utm_medium=referral&utm_campaign=woocommerce_listing_june_2022)

= Free Tools WooCommerce users =
- [Lead nurturing email templates](https://www.activecampaign.com/free-marketing-tools/lead-nurturing-email-templates?utm_source=unpaid_syndication_website&utm_medium=referral&utm_campaign=woocommerce_listing_june_2022)
- [Ecommerce integration and automation starter pack](https://www.activecampaign.com/free-marketing-tools/sample-ecommerce-integration-and-automation-starter-stack?utm_source=unpaid_syndication_website&utm_medium=referral&utm_campaign=woocommerce_listing_june_2022)

= About ActiveCampaign =
ActiveCampaign's category-defining Customer Experience Automation Platform (CXA) helps over [180,000 businesses](https://www.activecampaign.com/tomorrows-business?utm_source=unpaid_syndication_website&utm_medium=referral&utm_campaign=woocommerce_listing_june_2022) in 170 countries meaningfully engage with their customers. The platform gives businesses of all sizes access to hundreds of pre-built automations that combine transactional email and email marketing, marketing automation and CRM for powerful segmentation and personalization across social, email, messaging, chat and text. Over 70% of ActiveCampaign's customers use its 880+ integrations including Microsoft, Shopify, Square, Facebook and Salesforce. ActiveCampaign scores higher in customer satisfaction than any other solution in Marketing Automation, CRM and E-Commerce Personalization on [G2.com](https://www.g2.com/products/activecampaign/reviews) and is the Top Rated Email Marketing Software on TrustRadius. Start a free trial at [ActiveCampaign.com](https://www.activecampaign.com/?utm_source=unpaid_syndication_website&utm_medium=referral&utm_campaign=woocommerce_listing_june_2022).

== Installation ==

= WooCommerce Compatibility =
* Tested up to version: 6.8.2
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
