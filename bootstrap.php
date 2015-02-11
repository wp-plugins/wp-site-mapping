<?php
/*
Plugin Name: WP Site Mapping
Plugin URI:  https://wordpress.org/plugins/wp-site-mapping/
Description: Add one or multiple HTML sitemaps to your site by using a shortcode, calling a PHP function or using a widget with this flexible and easy to use plugin.
Version:     0.3
Author:      Henri Benoit
Author URI:  http://benohead.com
*/

/*
 * This plugin was built on top of WordPress-Plugin-Skeleton by Ian Dunn.
 * See https://github.com/iandunn/WordPress-Plugin-Skeleton for details.
 */

if (!defined('ABSPATH')) {
    die('Access denied.');
}

define('WPSM_NAME', 'WP Site Mapping');
define('WPSM_REQUIRED_PHP_VERSION', '5.3'); // because of get_called_class()
define('WPSM_REQUIRED_WP_VERSION', '3.9'); // because of TinyMCE 4

/**
 * Checks if the system requirements are met
 *
 * @return bool True if system requirements are met, false if not
 */
function wpsm_requirements_met()
{
    global $wp_version;

    if (version_compare(PHP_VERSION, WPSM_REQUIRED_PHP_VERSION, '<')) {
        return false;
    }

    if (version_compare($wp_version, WPSM_REQUIRED_WP_VERSION, '<')) {
        return false;
    }

    return true;
}

/**
 * Prints an error that the system requirements weren't met.
 */
function wpsm_requirements_error()
{
    global $wp_version;

    require_once(dirname(__FILE__) . '/views/requirements-error.php');
}

/*
 * Check requirements and load main class
 * The main program needs to be in a separate file that only gets loaded if the plugin requirements are met. Otherwise older PHP installations could crash when trying to parse it.
 */
if (wpsm_requirements_met()) {
    require_once(__DIR__ . '/classes/wpsm-module.php');
    require_once(__DIR__ . '/classes/wp-site-mapping.php');
    require_once(__DIR__ . '/includes/admin-notice-helper/admin-notice-helper.php');
    require_once(__DIR__ . '/classes/wpsm-settings.php');
    require_once(__DIR__ . '/classes/wpsm-widget.php');
    require_once(__DIR__ . '/classes/wpsm-menu-widget.php');

    if (class_exists('WordPress_Site_Mapping')) {
        $GLOBALS['wpsm'] = WordPress_Site_Mapping::get_instance();
        register_activation_hook(__FILE__, array($GLOBALS['wpsm'], 'activate'));
        register_deactivation_hook(__FILE__, array($GLOBALS['wpsm'], 'deactivate'));
    }
} else {
    add_action('admin_notices', 'wpsm_requirements_error');
}

if (isset($GLOBALS['wpsm'])) {
    function show_site_map($options)
    {
        echo $GLOBALS['wpsm']->get_site_map($options);
    }
}