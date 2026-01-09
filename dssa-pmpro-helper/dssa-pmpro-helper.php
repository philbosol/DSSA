<?php
/**
 * Plugin Name: DSSA PMPro Helper
 * Plugin URI: https://dendro.co.za
 * Description: Custom membership management system for Dendrological Society of South Africa
 * Version: 3.0.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: Phil Meyer / RMM New Generation Marketing
 * Author URI: https://rmmm.co.za
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dssa-pmpro-helper
 * Domain Path: /languages
 * Tags: membership, paid memberships pro, south africa, dendrological society
 * Network: false
 */

// Prevent direct access to this file
defined('ABSPATH') || exit;

/* ============================================================
 * CONSTANTS
 * ============================================================ */

/**
 * Plugin version
 * Used for cache busting and database migrations
 */
define('DSSA_PMPRO_HELPER_VERSION', '3.0.0');

/**
 * Plugin directory path
 * Used for including files
 */
define('DSSA_PMPRO_HELPER_PATH', plugin_dir_path(__FILE__));

/**
 * Plugin directory URL
 * Used for enqueueing assets
 */
define('DSSA_PMPRO_HELPER_URL', plugin_dir_url(__FILE__));

/**
 * Plugin main file path
 * Used for activation/deactivation hooks
 */
define('DSSA_PMPRO_HELPER_FILE', __FILE__);

/* ============================================================
 * GLOBAL HELPERS
 * ============================================================ */

/**
 * Get a plugin setting value
 *
 * @param string $key     The setting key.
 * @param mixed  $default Default value if setting doesn't exist.
 * @return mixed The setting value.
 */
function dssa_pmpro_helper_get_setting($key, $default = '') {
	$value = get_option("dssa_pmpro_helper_{$key}", $default);
	return apply_filters("dssa_pmpro_helper_setting_{$key}", $value);
}

/**
 * Log a debug message if logging is enabled
 *
 * @param string $message The message to log.
 * @param mixed  $data    Optional data to include in the log.
 * @return void
 */
function dssa_pmpro_helper_log($message, $data = null) {
	if (!dssa_pmpro_helper_get_setting('enable_debug_logging')) {
		return;
	}

	$log = '[DSSA PMPro Helper] ' . $message;
	if ($data !== null) {
		$log .= ' | ' . print_r($data, true);
	}

	error_log($log);
}

/* ============================================================
 * REQUIREMENTS CHECK
 * ============================================================ */

/**
 * Check if plugin requirements are met
 *
 * Checks if the Paid Memberships Pro plugin is active.
 * Displays admin notices if requirements are not met but allows
 * partial plugin functionality to continue.
 *
 * @return bool True if requirements are met, false otherwise.
 */
function dssa_pmpro_helper_check_requirements() {

	if (!class_exists('PMPro_Membership_Level')) {
		add_action('admin_notices', function () {
			$message = sprintf(
				/* translators: %s: Plugin name */
				__('The DSSA PMPro Helper plugin requires %s to be installed and activated. Some features may not function correctly.', 'dssa-pmpro-helper'),
				'<strong>Paid Memberships Pro</strong>'
			);
			printf(
				'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
				wp_kses_post($message)
			);
		});
		
		// Log the warning
		dssa_pmpro_helper_log('Paid Memberships Pro dependency not met. Plugin running with limited functionality.');
		
		return false;
	}

	return true;
}

/* ============================================================
 * MAIN BOOTSTRAP (FRONTEND + ADMIN)
 * ============================================================ */

/**
 * Initialize the plugin
 *
 * This is the main entry point for the plugin. It:
 * - Checks if requirements are met
 * - Loads text domain for internationalization
 * - Includes required class files
 * - Initializes plugin classes
 *
 * Runs early on 'plugins_loaded' (priority 5) to ensure classes
 * are available before PMPro renders checkout pages.
 *
 * @return void
 */
function dssa_pmpro_helper_init() {

	// Check requirements but continue with partial functionality if not met
	$requirements_met = dssa_pmpro_helper_check_requirements();

	// Load text domain for South African English translations
	load_plugin_textdomain(
		'dssa-pmpro-helper',
		false,
		dirname(plugin_basename(__FILE__)) . '/languages'
	);

	// Define files to include
	$files = [
		'includes/class-database.php',
		'includes/class-settings.php',
		'includes/class-checkout-fields.php',
		'includes/class-audit-log.php',
		'includes/class-security.php',
		'includes/class-legacy-members.php',
		'includes/class-membership-levels.php',
		'includes/class-registration.php',
		'includes/class-branch-management.php',
		'includes/class-login-system.php',
	];

	// Include each file with error handling
	foreach ($files as $file) {
		$filepath = DSSA_PMPRO_HELPER_PATH . $file;
		
		if (file_exists($filepath)) {
			require_once $filepath;
		} else {
			// Log missing file error
			dssa_pmpro_helper_log("Required file not found: {$file}");
			
			// Show admin notice for critical errors
			if (is_admin()) {
				add_action('admin_notices', function () use ($file) {
					printf(
						'<div class="notice notice-error"><p>%s</p></div>',
						sprintf(
							/* translators: %s: File path */
							esc_html__('DSSA PMPro Helper: Required file not found: %s', 'dssa-pmpro-helper'),
							esc_html($file)
						)
					);
				});
			}
		}
	}

	// Define classes to initialize
	$classes = [
		'DSSA_PMPro_Helper_Database',
		'DSSA_PMPro_Helper_Settings',
		'DSSA_PMPro_Helper_Checkout_Fields',
		'DSSA_PMPro_Helper_Legacy_Members',
		'DSSA_PMPro_Helper_Security',
		'DSSA_PMPro_Helper_Audit_Log',
		'DSSA_PMPro_Helper_Registration',
		'DSSA_PMPro_Helper_Membership_Levels',
		'DSSA_PMPro_Helper_Login_System',
		'DSSA_PMPro_Helper_Branch_Management',
	];

	// Initialize each class with dynamic checking
	foreach ($classes as $class) {
		// Check if class exists
		if (!class_exists($class)) {
			dssa_pmpro_helper_log("Class not found: {$class}");
			continue;
		}
		
		// Check if init method exists before calling
		if (method_exists($class, 'init')) {
			try {
				$class::init();
				dssa_pmpro_helper_log("Initialized class: {$class}");
			} catch (Exception $e) {
				// Log initialization errors
				dssa_pmpro_helper_log("Error initializing {$class}: " . $e->getMessage());
			}
		} else {
			dssa_pmpro_helper_log("Init method not found for: {$class}");
		}
	}
}

/**
 * Initialize plugin
 *
 * IMPORTANT: Must run on frontend BEFORE PMPro renders checkout
 * to ensure custom fields are properly registered.
 */
add_action('plugins_loaded', 'dssa_pmpro_helper_init', 5);

/* ============================================================
 * ADMIN-ONLY INTERFACE
 * ============================================================ */

/**
 * Load admin interface
 *
 * Conditionally loads the admin interface class only in the
 * WordPress admin area to optimize frontend performance.
 */
add_action('plugins_loaded', function () {

	// Only load admin interface in admin area
	if (!is_admin()) {
		return;
	}

	$admin_file = DSSA_PMPRO_HELPER_PATH . 'includes/class-admin-interface.php';

	// Check if file exists before including
	if (file_exists($admin_file)) {
		require_once $admin_file;

		// Initialize admin interface if class exists
		if (class_exists('DSSA_PMPro_Helper_Admin_Interface')) {
			try {
				DSSA_PMPro_Helper_Admin_Interface::init();
				dssa_pmpro_helper_log('Admin interface initialized');
			} catch (Exception $e) {
				dssa_pmpro_helper_log('Error initializing admin interface: ' . $e->getMessage());
			}
		} else {
			dssa_pmpro_helper_log('Admin interface class not found');
		}
	} else {
		dssa_pmpro_helper_log('Admin interface file not found');
	}
});

/* ============================================================
 * ACTIVATION / DEACTIVATION
 * ============================================================ */

/**
 * Plugin activation callback
 *
 * Runs when the plugin is activated. Creates database tables and
 * verifies that required dependencies are present.
 *
 * @return void
 */
register_activation_hook(__FILE__, function () {

	// Check for Paid Memberships Pro
	if (!class_exists('PMPro_Membership_Level')) {
		// Deactivate this plugin
		deactivate_plugins(plugin_basename(__FILE__));
		
		// Display error message
		wp_die(
			sprintf(
				/* translators: %s: Required plugin name */
				esc_html__('%s must be installed and activated before activating this plugin.', 'dssa-pmpro-helper'),
				'<strong>Paid Memberships Pro</strong>'
			),
			esc_html__('Plugin Activation Error', 'dssa-pmpro-helper'),
			array('back_link' => true)
		);
	}

	// Include database class
	$database_file = DSSA_PMPRO_HELPER_PATH . 'includes/class-database.php';
	
	if (file_exists($database_file)) {
		require_once $database_file;
		
		// Create database tables
		if (class_exists('DSSA_PMPro_Helper_Database')) {
			try {
				DSSA_PMPro_Helper_Database::create_tables();
				
				// Log successful activation
				if (defined('WP_DEBUG') && WP_DEBUG) {
					error_log('[DSSA PMPro Helper] Plugin activated and database tables created.');
				}
			} catch (Exception $e) {
				// Log database creation error
				error_log('[DSSA PMPro Helper] Database table creation failed: ' . $e->getMessage());
				
				wp_die(
					sprintf(
						/* translators: %s: Error message */
						esc_html__('Failed to create database tables: %s', 'dssa-pmpro-helper'),
						esc_html($e->getMessage())
					),
					esc_html__('Database Error', 'dssa-pmpro-helper'),
					array('back_link' => true)
				);
			}
		} else {
			error_log('[DSSA PMPro Helper] Database class not found during activation.');
		}
	} else {
		error_log('[DSSA PMPro Helper] Database file not found during activation.');
	}
});

/**
 * Plugin deactivation callback
 *
 * Runs when the plugin is deactivated. Cleans up scheduled tasks
 * and logs the deactivation.
 *
 * @return void
 */
register_deactivation_hook(__FILE__, function () {
	
	// Clear scheduled cron jobs with error handling
	$hooks = [
		'dssa_pmpro_helper_daily_renewal_check',
		'dssa_pmpro_helper_daily_audit_cleanup',
	];
	
	foreach ($hooks as $hook) {
		$result = wp_clear_scheduled_hook($hook);
		
		// Log the result
		if (defined('WP_DEBUG') && WP_DEBUG) {
			if ($result !== false) {
				error_log("[DSSA PMPro Helper] Cleared scheduled hook: {$hook}");
			} else {
				error_log("[DSSA PMPro Helper] No scheduled events found for hook: {$hook}");
			}
		}
	}
	
	// Log deactivation
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log('[DSSA PMPro Helper] Plugin deactivated successfully.');
	}
});
