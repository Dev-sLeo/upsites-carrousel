<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

		add_action( 'elementor/elements/categories_registered', [ $this, 'register_categories' ] );
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_styles' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );
	}

	public function register_categories( $elements_manager ) {
		$elements_manager->add_category(
			'upsites',
			[
				'title' => __( 'UpSites', 'upsites-addons' ),
				'icon'  => 'eicon-font',
			]
		);
	}

	public function register_widgets( $widgets_manager ) {
		require_once UPSITES_ADDONS_PATH . 'includes/widgets/accordion-slider.php';
		$widgets_manager->register( new \UpSites_Accordion_Slider_Widget() );

		require_once UPSITES_ADDONS_PATH . 'includes/widgets/cards-carousel.php';
		$widgets_manager->register( new \UpSites_Cards_Carousel_Widget() );
	}

	public function enqueue_styles() {
		wp_enqueue_style(
			'upsites-accordion-slider',
			UPSITES_ADDONS_URL . 'assets/css/accordion-slider.css',
			[],
			UPSITES_ADDONS_VERSION
		);
		wp_enqueue_style(
			'upsites-cards-carousel',
			UPSITES_ADDONS_URL . 'assets/css/cards-carousel.css',
			[],
			UPSITES_ADDONS_VERSION
		);
	}

	public function register_scripts() {
		wp_register_script(
			'upsites-accordion-slider',
			UPSITES_ADDONS_URL . 'assets/js/accordion-slider.js',
			[ 'jquery' ],
			UPSITES_ADDONS_VERSION,
			true
		);
		wp_register_script(
			'upsites-cards-carousel',
			UPSITES_ADDONS_URL . 'assets/js/cards-carousel.js',
			[],
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
