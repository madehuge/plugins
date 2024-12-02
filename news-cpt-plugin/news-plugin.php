<?php
/*
Plugin Name: News Plugin
Description: A plugin to manage custom post types for news and log user activities using Eloquent ORM.
Version: 1.0
Author: Your Name
*/


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Autoload required classes
require_once plugin_dir_path( __FILE__ ) . 'includes/class-news-post-type.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-news-taxonomies.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-activity-logger.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-news-shortcode.php';
// Include Composer autoload
// Example for including Composer autoload safely
if (file_exists(plugin_dir_path(__FILE__) . 'vendor/autoload.php')) {
    require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
} else {
    wp_die(__('Composer dependencies are missing. Please run `composer install`.', 'news-plugin'));
}

// Hook for logging user login (persistent)
add_action('wp_login', 'log_user_login_activity', 10, 2);

// Plugin Activation Hook
function news_plugin_activation() {
    global $wpdb;
    // Create the custom table for user activity logging
    $table_name = $wpdb->prefix . 'user_activity_logs';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        activity VARCHAR(255) NOT NULL,
        activity_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id),
        INDEX user_activity_idx (user_id, activity_time)
    ) ENGINE=InnoDB $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

}

register_activation_hook(__FILE__, 'news_plugin_activation');

// Plugin Deactivation Hook
function news_plugin_deactivation() {
    // Cleanup operations on deactivation
    remove_action('wp_login', 'log_user_login_activity', 10);
}
register_deactivation_hook(__FILE__, 'news_plugin_deactivation');

// Uninstall script
if (defined('WP_UNINSTALL_PLUGIN')) {
    // Cleanup custom tables or options
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_activity_logs';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

// Log User Login Activity
function log_user_login_activity($user_login, $user) {
    // Use the logger to record activity
    $logger = new Activity_Logger($user->ID, 'User logged in');
    $logger->logActivity();
}

//Guternberg Block Learning

// Enqueue block scripts and styles
function news_plugin_register_block_assets() {
    wp_register_script(
        'news-plugin-block-js',
        plugins_url( '/blocks/news-block/index.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-editor' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'blocks/news-block/index.js' )
    );

    wp_register_style(
        'news-plugin-block-style',
        plugins_url( '/blocks/news-block/style.css', __FILE__ ),
        array(),
        filemtime( plugin_dir_path( __FILE__ ) . 'blocks/news-block/style.css' )
    );

    register_block_type( 'news-plugin/news-block', array(
        'editor_script' => 'news-plugin-block-js',
        'editor_style'  => 'news-plugin-block-style',
    ) );
}

add_action( 'init', 'news_plugin_register_block_assets' );


// Initialize the plugin
function initialize_news_plugin() {
    new News_Post_Type();
    new News_Taxonomies();
    new News_Shortcode();
}

add_action('plugins_loaded', 'initialize_news_plugin');
