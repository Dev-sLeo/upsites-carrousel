<?php
/**
 * Plugin Name: UpSites Add-ons
 * Description: Add-ons para Elementor desenvolvidos pela UpSites.
 * Version: 1.0.0
 * Author: UpSites
 * Text Domain: upsites-addons
 * Requires Plugins: elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'UPSITES_ADDONS_VERSION', '1.0.0' );
define( 'UPSITES_ADDONS_PATH', plugin_dir_path( __FILE__ ) );
define( 'UPSITES_ADDONS_URL', plugin_dir_url( __FILE__ ) );

final class UpSites_Addons {

	private static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	public function init() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_elementor_missing' ] );
			return;
		}

		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_styles' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );
	}

	public function register_widgets( $widgets_manager ) {
		require_once UPSITES_ADDONS_PATH . 'widgets/accordion-slider/accordion-slider.php';
		$widgets_manager->register( new \UpSites_Accordion_Slider_Widget() );
	}

	public function enqueue_styles() {
		wp_enqueue_style(
			'upsites-accordion-slider',
			UPSITES_ADDONS_URL . 'widgets/accordion-slider/accordion-slider.css',
			[],
			UPSITES_ADDONS_VERSION
		);
	}

	public function register_scripts() {
		wp_register_script(
			'gsap',
			'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js',
			[],
			'3.12.5',
			true
		);
		wp_register_script(
			'upsites-accordion-slider',
			UPSITES_ADDONS_URL . 'widgets/accordion-slider/accordion-slider.js',
			[ 'jquery', 'gsap' ],
			UPSITES_ADDONS_VERSION,
			true
		);
	}

	public function notice_elementor_missing() {
		$message = sprintf(
			'<strong>%s</strong> requer o <strong>Elementor</strong> instalado e ativado.',
			'UpSites Add-ons'
		);
		echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
	}
}

UpSites_Addons::instance();
