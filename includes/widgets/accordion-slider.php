<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;

require_once UPSITES_ADDONS_PATH . 'includes/controls/accordion-slider-controls.php';

class UpSites_Accordion_Slider_Widget extends Widget_Base {

	use UpSites_Accordion_Slider_Controls;

	public function get_name() {
		return 'upsites-accordion-slider';
	}

	public function get_title() {
		return __( 'Accordion Slider', 'upsites-addons' );
	}

	public function get_icon() {
		return 'eicon-accordion';
	}

	public function get_categories() {
		return [ 'upsites' ];
	}

	public function get_script_depends() {
		return [ 'upsites-accordion-slider' ];
	}

	public function get_style_depends() {
		return [ 'upsites-accordion-slider' ];
	}

	protected function render() {
		$settings       = $this->get_settings_for_display();
		$slides         = $settings['slides'];
		$default_active = intval( $settings['default_active'] );

		$grayscale     = ! empty( $settings['inactive_grayscale'] ) && 'yes' === $settings['inactive_grayscale'];

		if ( empty( $slides ) ) {
			return;
		}
		?>
		<div class="upsites-accordion-wrapper<?php echo $grayscale ? ' has-grayscale' : ''; ?>"
			data-default-active="<?php echo esc_attr( $default_active ); ?>">

			<?php /* ── Mobile tab bar ── */ ?>
			<div class="upsites-accordion-tabs">
				<?php foreach ( $slides as $index => $slide ) :
					$is_active    = ( $index === $default_active );
					$active_class = $is_active ? ' is-active' : '';
					$logo_url     = ! empty( $slide['slide_logo']['url'] ) ? $slide['slide_logo']['url'] : '';
					$logo_alt     = ! empty( $slide['slide_logo_alt'] ) ? $slide['slide_logo_alt'] : '';
					$logo_width_mb = ! empty( $slide['slide_logo_width_mobile']['size'] ) ? intval( $slide['slide_logo_width_mobile']['size'] ) : 80;
				?>
				<div class="upsites-accordion-tab<?php echo esc_attr( $active_class ); ?>"
					data-index="<?php echo esc_attr( $index ); ?>">
					<?php if ( $logo_url ) : ?>
					<img src="<?php echo esc_url( $logo_url ); ?>"
						alt="<?php echo esc_attr( $logo_alt ); ?>"
						style="max-width: <?php echo esc_attr( $logo_width_mb ); ?>px;">
					<?php else : ?>
					<span class="upsites-accordion-tab__num"><?php echo esc_html( $index + 1 ); ?></span>
					<?php endif; ?>
				</div>
				<?php endforeach; ?>
			</div>

			<?php /* ── Desktop accordion + Mobile card ── */ ?>
			<div class="upsites-accordion-slider">
				<?php foreach ( $slides as $index => $slide ) :
					$is_active     = ( $index === $default_active );
					$active_class  = $is_active ? ' is-active' : '';
					$bg_url        = ! empty( $slide['slide_bg']['url'] ) ? $slide['slide_bg']['url'] : '';

					$bg_x   = isset( $slide['slide_bg_pos_x']['size'] )        ? $slide['slide_bg_pos_x']['size']        : 50;
					$bg_xu  = isset( $slide['slide_bg_pos_x']['unit'] )        ? $slide['slide_bg_pos_x']['unit']        : '%';
					$bg_y   = isset( $slide['slide_bg_pos_y']['size'] )        ? $slide['slide_bg_pos_y']['size']        : 50;
					$bg_yu  = isset( $slide['slide_bg_pos_y']['unit'] )        ? $slide['slide_bg_pos_y']['unit']        : '%';
					$bg_pos = $bg_x . $bg_xu . ' ' . $bg_y . $bg_yu;

					$tb_x          = isset( $slide['slide_bg_pos_x_tablet']['size'] ) ? $slide['slide_bg_pos_x_tablet']['size'] : $bg_x;
					$tb_xu         = isset( $slide['slide_bg_pos_x_tablet']['unit'] ) ? $slide['slide_bg_pos_x_tablet']['unit'] : $bg_xu;
					$tb_y          = isset( $slide['slide_bg_pos_y_tablet']['size'] ) ? $slide['slide_bg_pos_y_tablet']['size'] : $bg_y;
					$tb_yu         = isset( $slide['slide_bg_pos_y_tablet']['unit'] ) ? $slide['slide_bg_pos_y_tablet']['unit'] : $bg_yu;
					$bg_pos_tablet = $tb_x . $tb_xu . ' ' . $tb_y . $tb_yu;

					$mb_x          = isset( $slide['slide_bg_pos_x_mobile']['size'] ) ? $slide['slide_bg_pos_x_mobile']['size'] : $tb_x;
					$mb_xu         = isset( $slide['slide_bg_pos_x_mobile']['unit'] ) ? $slide['slide_bg_pos_x_mobile']['unit'] : $tb_xu;
					$mb_y          = isset( $slide['slide_bg_pos_y_mobile']['size'] ) ? $slide['slide_bg_pos_y_mobile']['size'] : $tb_y;
					$mb_yu         = isset( $slide['slide_bg_pos_y_mobile']['unit'] ) ? $slide['slide_bg_pos_y_mobile']['unit'] : $tb_yu;
					$bg_pos_mobile = $mb_x . $mb_xu . ' ' . $mb_y . $mb_yu;

					$overlay_color        = ! empty( $slide['overlay_color'] ) ? $slide['overlay_color'] : 'rgba(77,45,120,0.7)';
					$overlay_color_tablet = ! empty( $slide['overlay_color_tablet'] ) ? $slide['overlay_color_tablet'] : $overlay_color;
					$overlay_color_mobile = ! empty( $slide['overlay_color_mobile'] ) ? $slide['overlay_color_mobile'] : $overlay_color_tablet;
					$logo_width    = ! empty( $slide['slide_logo_width']['size'] )        ? intval( $slide['slide_logo_width']['size'] )        : 160;
					$logo_width_tb = ! empty( $slide['slide_logo_width_tablet']['size'] ) ? intval( $slide['slide_logo_width_tablet']['size'] ) : $logo_width;
					$logo_width_mb = ! empty( $slide['slide_logo_width_mobile']['size'] ) ? intval( $slide['slide_logo_width_mobile']['size'] ) : $logo_width_tb;
					$link_url      = ! empty( $slide['slide_link']['url'] ) ? $slide['slide_link']['url'] : '';
					$link_target   = ! empty( $slide['slide_link']['is_external'] ) ? '_blank' : '_self';
					$link_nofollow = ! empty( $slide['slide_link']['nofollow'] ) ? ' rel="nofollow"' : '';
				?>
				<div class="upsites-accordion-slide<?php echo esc_attr( $active_class ); ?>"
					data-index="<?php echo esc_attr( $index ); ?>"
					data-overlay-active="<?php echo esc_attr( $overlay_color ); ?>"
					data-overlay-active-tablet="<?php echo esc_attr( $overlay_color_tablet ); ?>"
					data-overlay-active-mobile="<?php echo esc_attr( $overlay_color_mobile ); ?>"
					data-bg-pos-tablet="<?php echo esc_attr( $bg_pos_tablet ); ?>"
					data-bg-pos-mobile="<?php echo esc_attr( $bg_pos_mobile ); ?>">

					<div class="upsites-accordion-slide__bg"
						<?php if ( $bg_url ) : ?>
						style="background-image: url('<?php echo esc_url( $bg_url ); ?>'); background-position: <?php echo esc_attr( $bg_pos ); ?>;"
						<?php endif; ?>>
					</div>

					<div class="upsites-accordion-slide__overlay"></div>

					<div class="upsites-accordion-slide__content">
						<?php if ( ! empty( $slide['slide_title'] ) ) : ?>
						<h3 class="upsites-accordion-slide__title">
							<?php echo wp_kses_post( $slide['slide_title'] ); ?>
						</h3>
						<?php endif; ?>

						<?php if ( ! empty( $slide['slide_description'] ) ) : ?>
						<p class="upsites-accordion-slide__description">
							<?php echo wp_kses_post( $slide['slide_description'] ); ?>
						</p>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $slide['slide_logo']['url'] ) ) : ?>
					<div class="upsites-accordion-slide__logo"
						data-logo-width="<?php echo esc_attr( $logo_width ); ?>"
						data-logo-width-tablet="<?php echo esc_attr( $logo_width_tb ); ?>"
						data-logo-width-mobile="<?php echo esc_attr( $logo_width_mb ); ?>">
						<img src="<?php echo esc_url( $slide['slide_logo']['url'] ); ?>"
							alt="<?php echo esc_attr( $slide['slide_logo_alt'] ?? '' ); ?>"
							style="max-width: <?php echo esc_attr( $logo_width ); ?>px;">
					</div>
					<?php endif; ?>

					<?php if ( $link_url ) : ?>
					<a class="upsites-accordion-slide__link"
						href="<?php echo esc_url( $link_url ); ?>"
						target="<?php echo esc_attr( $link_target ); ?>"
						<?php echo $link_nofollow; ?>
						aria-label="<?php echo esc_attr( $slide['slide_title'] ?? '' ); ?>">
					</a>
					<?php endif; ?>

					<div class="upsites-accordion-slide__arrow">
						<?php /* Desktop arrow */ ?>
						<svg class="upsites-arrow-desktop" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
							<path d="M7 17L17 7M17 7H7M17 7V17" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
						<?php /* Mobile arrow */ ?>
						<svg class="upsites-arrow-mobile" width="51" height="51" viewBox="0 0 51 51" fill="none" xmlns="http://www.w3.org/2000/svg">
							<circle opacity="0.5" cx="25.3158" cy="25.3178" r="23.8705" fill="white"/>
							<path d="M17.9336 32.7031L32.7027 17.934" stroke="#F8F5FB" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M17.9336 17.9336H32.7027V32.7027" stroke="#F8F5FB" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</div>

				</div>

					<?php if ( ! empty( $slide['slide_description'] ) ) : ?>
					<p class="upsites-accordion-slide__description-mobile">
						<?php echo wp_kses_post( $slide['slide_description'] ); ?>
					</p>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>

		</div>
		<?php
	}
}
