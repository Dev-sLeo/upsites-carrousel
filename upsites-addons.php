<?php

/**
 * Plugin Name: Upsites Carrousel
 * Description: Add-ons para Elementor desenvolvidos pela UpSites.
 * Version: 2.7.1
 * Author: UpSites
 * Text Domain: upsites-addons
 * Requires Plugins: elementor
 */

if (! defined('ABSPATH')) {
	exit;
}

define('UPSITES_CARROUSEL_VERSION', '2.7.1');
define('UPSITES_CARROUSEL_PATH', plugin_dir_path(__FILE__));
define('UPSITES_CARROUSEL_URL', plugin_dir_url(__FILE__));

require_once UPSITES_CARROUSEL_PATH . 'includes/libs/plugin-update-checker/plugin-update-checker.php';
require_once UPSITES_CARROUSEL_PATH . 'includes/plugin.php';

UpSites_Carrousel_Plugin::instance();
