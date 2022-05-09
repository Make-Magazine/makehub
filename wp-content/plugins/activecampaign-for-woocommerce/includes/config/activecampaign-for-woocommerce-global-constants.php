<?php

/**
 * A file containing all global constants for the plugin.
 *
 * @link       https://www.activecampaign.com/
 * @since      1.0.0
 *
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/includes
 */

/**
 * The plugin parent menu name.
 *
 * @since 1.5.0
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PARENT_MENU_NAME' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PARENT_MENU_NAME', 'activecampaign_plugins' );
}

/**
 * Current plugin version.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_VERSION' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_VERSION', '1.7.2' );
}

/**
 * Current Database Migration Version.
 * Update the version as any changes to our tables are made.
 *
 * @since 1.3.3
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_VERSION' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_VERSION', '1.1.0' );
}

/**
 * The abandoned cart table name.
 * Be sure to add the WordPress prefix to the usage.
 *
 * @since 1.3.3
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ABANDONED_CART_NAME', 'activecampaign_for_woocommerce_abandoned_cart' );
}

/**
 * The name of the plugin in kebab case (e.g., this-is-snake-case).
 *
 * @var string The name of the plugin in kebab case.
 * @since 1.0.0
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_KEBAB' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_KEBAB', 'activecampaign-for-woocommerce' );
}
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_ERR_KEBAB' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_ERR_KEBAB', 'activecampaign-for-woocommerce-errors' );
}
/**
 * The name of the plugin in snake case (e.g., this_is_snake_case).
 *
 * @var string The name of the plugin in snake case.
 * @since 1.0.0
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE', 'activecampaign_for_woocommerce' );
}

/**
 * Properly formatted name of plugin.
 *
 * @var string The name of the plugin as a properly formatted string.
 * @since 1.0.0
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_STANDARD' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_STANDARD', 'ActiveCampaign for WooCommerce' );
}

/**
 * Localization domain string.
 *
 * @var string The name of the plugins domain within the localization system.
 * @since 1.0.0
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN', 'activecampaign-for-woocommerce' );
}

/**
 * DB option name.
 *
 * @var  string  The name of the option saved to the WordPress database (wp_options.option_name).
 * @since 1.0.0
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_OPTION_NAME' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_OPTION_NAME', 'activecampaign_for_woocommerce_settings' );
}

/**
 * DB storage name.
 *
 * @var  string  The name of local storage row in the database (wp_options.storage_name).
 * @since 1.0.0
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_STORAGE_NAME' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_DB_STORAGE_NAME', 'activecampaign_for_woocommerce_storage' );
}

/**
 * The name of the persistent cart id row.
 *
 * @var  string  The name of persistent cart row in the database (wp_usermeta.storage_name).
 * @since 1.0.0
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PERSISTENT_CART_ID_NAME' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PERSISTENT_CART_ID_NAME', 'activecampaign_for_woocommerce_external_checkout_id' );
}

/**
 * Path to the log file.
 *
 * @var string The path to our plugin-specific log file.
 * @since 1.2.0
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOG_PATH' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOG_PATH', WP_CONTENT_DIR . '/uploads/wc-logs/ac-debug.log' );
}

/**
 * The accepts marketing common name.
 *
 * @var string The accepts marketing common name.
 * @since 1.5.0
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ACCEPTS_MARKETING_NAME' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_ACCEPTS_MARKETING_NAME', 'activecampaign_for_woocommerce_accepts_marketing' );
}

/**
 * The sync running status common name.
 *
 * @var string The sync running status common name.
 * @since 1.5.0
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME', 'activecampaign_for_woocommerce_historical_sync_running_status' );
}

/**
 * The historical sync last status common name.
 *
 * @var string The historical sync last status common name.
 * @since 1.5.0
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_LAST_STATUS_NAME' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_LAST_STATUS_NAME', 'activecampaign_for_woocommerce_historical_sync_last_status' );
}

/**
 * The run historical sync common name.
 *
 * @var string The run historical sync common name.
 * @since 1.5.0
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_RUN_SYNC_NAME' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_RUN_SYNC_NAME', 'activecampaign_for_woocommerce_run_historical_sync' );
}

/**
 * The sync scheduled status common name.
 *
 * @var string The sync scheduled status common name.
 * @since 1.5.0
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_SCHEDULED_STATUS_NAME' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_SCHEDULED_STATUS_NAME', 'activecampaign_for_woocommerce_historical_sync_scheduled_status' );
}

/**
 * The sync stop check common name.
 *
 * @var string The sync stop check common name.
 * @since 1.5.0
 */
if ( ! defined( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_STOP_CHECK_NAME' ) ) {
	define( 'ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_STOP_CHECK_NAME', 'activecampaign_for_woocommerce_historical_sync_stop' );
}
