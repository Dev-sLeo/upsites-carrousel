<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UpSites_Carrousel_Plugin {

	private static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->init_update_checker();
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	private function init_update_checker() {
		$update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
			'https://github.com/Dev-sLeo/upsites-carrousel/',
			UPSITES_CARROUSEL_PATH . 'upsites-addons.php',
			'upsites-carrousel'
		);

		if ( defined( 'UPSITES_CARROUSEL_GITHUB_TOKEN' ) && UPSITES_CARROUSEL_GITHUB_TOKEN ) {
			$update_checker->setAuthentication( UPSITES_CARROUSEL_GITHUB_TOKEN );
		}
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
		require_once UPSITES_CARROUSEL_PATH . 'includes/widgets/carousel.php';
		$widgets_manager->register( new \UpSites_Carousel_Widget() );
	}

	public function enqueue_styles() {
		wp_enqueue_style(
			'upsites-carousel',
			UPSITES_CARROUSEL_URL . 'assets/css/carousel.css',
			[],
			UPSITES_CARROUSEL_VERSION
		);
	}

	public function register_scripts() {
		wp_register_script(
			'upsites-carousel',
			UPSITES_CARROUSEL_URL . 'assets/js/carousel.js',
			['elementor-frontend'],
			UPSITES_CARROUSEL_VERSION,
			true
		);
	}

	public function notice_elementor_missing() {
		$message = sprintf(
			'<strong>%s</strong> requer o <strong>Elementor</strong> instalado e ativado.',
			'Upsites Carrousel'
		);
		echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
	}
}
