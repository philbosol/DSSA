<?php
/**
 * Uninstall script for DSSA PMPro Helper
 * 
 * This file is called when the plugin is deleted from WordPress admin.
 * It cleans up all plugin data if the cleanup setting is enabled.
 *
 * @package DSSA_PMPro_Helper
 * @since 3.0.0
 */

// Prevent direct access
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Load WordPress functions
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

/**
 * Clean up plugin data
 *
 * Removes all plugin-related data from the database including:
 * - Plugin options
 * - Custom database tables
 * - User metadata
 * - Scheduled cron jobs
 * - Custom roles
 * - Transients
 *
 * Only runs if cleanup_on_delete setting is enabled.
 *
 * @return void
 */
function dssa_pmpro_helper_uninstall_cleanup() {
    global $wpdb;
    
    // Get cleanup setting
    $cleanup_on_delete = get_option('dssa_pmpro_helper_cleanup_on_delete', false);
    
    // Only proceed if cleanup is enabled
    if (!$cleanup_on_delete) {
        // Log that cleanup was skipped
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[DSSA PMPro Helper] Uninstall cleanup skipped - cleanup_on_delete setting is disabled.');
        }
        return;
    }
    
    try {
        // Remove all plugin options using prepared statement pattern
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                $wpdb->esc_like('dssa_pmpro_helper_') . '%'
            )
        );
        
        // Remove database tables
        $tables = [
            "{$wpdb->prefix}dssa_legacy_numbers",
            "{$wpdb->prefix}dssa_audit_log",
            "{$wpdb->prefix}dssa_branches",
        ];
        
        foreach ($tables as $table) {
            // Use proper escaping for table names
            $wpdb->query("DROP TABLE IF EXISTS `{$table}`");
        }
        
        // Remove user meta using prepared statement
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE %s",
                $wpdb->esc_like('dssa_') . '%'
            )
        );
        
        // Remove scheduled cron jobs with error handling
        $cron_hooks = [
            'dssa_pmpro_helper_daily_renewal_check',
            'dssa_pmpro_helper_daily_audit_cleanup',
        ];
        
        foreach ($cron_hooks as $hook) {
            $result = wp_clear_scheduled_hook($hook);
            if (defined('WP_DEBUG') && WP_DEBUG && $result !== false) {
                error_log("[DSSA PMPro Helper] Cleared scheduled hook: {$hook}");
            }
        }
        
        // Remove custom role (if no users have it)
        $users_with_role = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->usermeta} 
                WHERE meta_key = %s 
                AND meta_value LIKE %s",
                "{$wpdb->prefix}capabilities",
                '%' . $wpdb->esc_like('membership_manager') . '%'
            )
        );
        
        if ($users_with_role == 0) {
            remove_role('membership_manager');
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[DSSA PMPro Helper] Removed custom role: membership_manager');
            }
        }
        
        // Clear any transients using prepared statements
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                $wpdb->esc_like('_transient_dssa_') . '%'
            )
        );
        
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                $wpdb->esc_like('_transient_timeout_dssa_') . '%'
            )
        );
        
        // Log successful uninstall
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[DSSA PMPro Helper] Plugin uninstalled and all data cleaned up successfully.');
        }
        
    } catch (Exception $e) {
        // Log any errors that occur during cleanup
        error_log('[DSSA PMPro Helper] Error during uninstall cleanup: ' . $e->getMessage());
    }
}

// Run cleanup
dssa_pmpro_helper_uninstall_cleanup();