<?php

/**
 * Plugin Name: UpSites Add-ons
 * Description: Add-ons para Elementor desenvolvidos pela UpSites.
 * Version: 1.0.3
 * Author: UpSites
 * Text Domain: upsites-addons
 * Requires Plugins: elementor
 */

if (! defined('ABSPATH')) {
	exit;
}

define('UPSITES_ADDONS_VERSION', '1.0.1');
define('UPSITES_ADDONS_PATH', plugin_dir_path(__FILE__));
define('UPSITES_ADDONS_URL', plugin_dir_url(__FILE__));

require_once UPSITES_ADDONS_PATH . 'includes/plugin.php';

UpSites_Addons::instance();
