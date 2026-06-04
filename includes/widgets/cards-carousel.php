<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;

require_once UPSITES_ADDONS_PATH . 'includes/controls/cards-carousel-controls.php';

class UpSites_Cards_Carousel_Widget extends Widget_Base {

	use UpSites_Cards_Carousel_Controls;

	public function get_name() {
		return 'upsites-cards-carousel';
	}

	public function get_title() {
		return __( 'Carrossel Cards', 'upsites-addons' );
	}

	public function get_icon() {
		return 'eicon-slides';
	}

	public function get_categories() {
		return [ 'upsites' ];
	}

	public function get_script_depends() {
		return [ 'upsites-cards-carousel' ];
	}

	public function get_style_depends() {
		return [ 'upsites-cards-carousel' ];
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$cards    = $settings['cards'];

		if ( empty( $cards ) ) {
			return;
		}

		?>
		<div class="upsites-cards-carousel">

			<?php foreach ( $cards as $card ) :
				$icon_url  = ! empty( $card['card_icon']['url'] )  ? $card['card_icon']['url']  : '';
				$icon_alt  = ! empty( $card['card_icon_alt'] )     ? $card['card_icon_alt']     : '';
				$title     = ! empty( $card['card_title'] )        ? $card['card_title']        : '';
				$desc      = ! empty( $card['card_description'] )  ? $card['card_description']  : '';
				$image_url = ! empty( $card['card_image']['url'] ) ? $card['card_image']['url'] : '';
				$image_alt = ! empty( $card['card_image_alt'] )    ? $card['card_image_alt']    : '';
			?>
			<div class="upsites-cc-card-wrapper">
			<div class="upsites-cc-card">

				<div class="upsites-cc-card__inner">

					<div class="upsites-cc-card__left">

						<?php if ( $icon_url ) : ?>
						<div class="upsites-cc-card__icon-wrap">
							<img src="<?php echo esc_url( $icon_url ); ?>"
								alt="<?php echo esc_attr( $icon_alt ); ?>">
						</div>
						<?php else : ?>
						<div class="upsites-cc-card__icon-wrap upsites-cc-card__icon-wrap--empty">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
								<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/>
								<line x1="12" y1="2" x2="12" y2="5"/><line x1="12" y1="19" x2="12" y2="22"/>
								<line x1="2" y1="12" x2="5" y2="12"/><line x1="19" y1="12" x2="22" y2="12"/>
							</svg>
						</div>
						<?php endif; ?>

						<div class="upsites-cc-card__text">
							<?php if ( $title ) : ?>
							<h3 class="upsites-cc-card__title">
								<?php echo wp_kses_post( $title ); ?>
							</h3>
							<?php endif; ?>

							<?php if ( $desc ) : ?>
							<p class="upsites-cc-card__desc">
								<?php echo wp_kses_post( $desc ); ?>
							</p>
							<?php endif; ?>
						</div>

					</div><!-- .upsites-cc-card__left -->

					<div class="upsites-cc-card__right">
						<div class="upsites-cc-card__image-wrap">
							<?php if ( $image_url ) : ?>
							<img src="<?php echo esc_url( $image_url ); ?>"
								alt="<?php echo esc_attr( $image_alt ); ?>">
							<?php endif; ?>
						</div>
					</div><!-- .upsites-cc-card__right -->

				</div><!-- .upsites-cc-card__inner -->
			</div><!-- .upsites-cc-card -->
			</div><!-- .upsites-cc-card-wrapper -->
			<?php endforeach; ?>

		</div><!-- .upsites-cards-carousel -->
		<?php
	}
}
