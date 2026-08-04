<?php
if (! defined('ABSPATH')) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Icons_Manager;

require_once UPSITES_CARROUSEL_PATH . 'includes/controls/carousel-controls.php';

class UpSites_Carousel_Widget extends Widget_Base
{
	use UpSites_Carousel_Controls;

	public function get_name()
	{
		return 'upsites-carousel';
	}

	public function get_title()
	{
		return __('Carrossel', 'upsites-addons');
	}

	public function get_icon()
	{
		return 'eicon-carousel';
	}

	public function get_categories()
	{
		return ['upsites'];
	}

	public function get_script_depends()
	{
		return ['upsites-carousel'];
	}

	public function get_style_depends()
	{
		return ['upsites-carousel'];
	}

	/**
	 * Splide reads perPage/autoScroll etc. through its own `breakpoints` option,
	 * so the responsive values chosen in the panel are collected here into a
	 * plain array and handed to the frontend JS via a data-settings attribute
	 * instead of being resolved server-side (there is no viewport at render time).
	 */
	private function build_splide_settings($settings)
	{
		$active_breakpoints = \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints();

		$breakpoints = [];
		if (! empty($active_breakpoints['tablet'])) {
			$breakpoints[ $active_breakpoints['tablet']->get_value() ] = [
				'perPage' => ! empty($settings['slides_per_view_tablet']) ? (int) $settings['slides_per_view_tablet'] : 2,
				'gap'     => ! empty($settings['slides_gap_tablet']['size']) ? $settings['slides_gap_tablet']['size'] . $settings['slides_gap_tablet']['unit'] : '16px',
			];
		}
		if (! empty($active_breakpoints['mobile'])) {
			$breakpoints[ $active_breakpoints['mobile']->get_value() ] = [
				'perPage' => ! empty($settings['slides_per_view_mobile']) ? (int) $settings['slides_per_view_mobile'] : 1,
				'gap'     => ! empty($settings['slides_gap_mobile']['size']) ? $settings['slides_gap_mobile']['size'] . $settings['slides_gap_mobile']['unit'] : '12px',
			];
		}

		$auto_scroll = ! empty($settings['auto_scroll']) && 'yes' === $settings['auto_scroll'];
		$autoplay    = ! $auto_scroll && ! empty($settings['autoplay']) && 'yes' === $settings['autoplay'];
		$speed       = ! empty($settings['auto_scroll_speed']) ? (float) $settings['auto_scroll_speed'] : 1;
		if ('backward' === ($settings['auto_scroll_direction'] ?? 'forward')) {
			$speed *= -1;
		}

		return [
			'type'          => (! empty($settings['infinite_loop']) && 'yes' === $settings['infinite_loop']) ? 'loop' : 'slide',
			'perPage'       => ! empty($settings['slides_per_view']) ? (int) $settings['slides_per_view'] : 4,
			'gap'           => ! empty($settings['slides_gap']['size']) ? $settings['slides_gap']['size'] . $settings['slides_gap']['unit'] : '24px',
			'arrows'        => ! empty($settings['show_arrows']) && 'yes' === $settings['show_arrows'],
			'pagination'    => ! empty($settings['show_bullets']) && 'yes' === $settings['show_bullets'],
			'autoplay'      => $autoplay,
			'interval'      => ! empty($settings['autoplay_interval']) ? (int) $settings['autoplay_interval'] : 3000,
			'speed'         => ! empty($settings['transition_speed']) ? (int) $settings['transition_speed'] : 400,
			'breakpoints'   => $breakpoints,
			'autoScroll'    => $auto_scroll ? [
				'speed'         => $speed,
				'pauseOnHover'  => ! empty($settings['pause_on_hover']) && 'yes' === $settings['pause_on_hover'],
				'pauseOnFocus'  => ! empty($settings['pause_on_hover']) && 'yes' === $settings['pause_on_hover'],
			] : false,
		];
	}

	private function render_button_icon($slide)
	{
		if (empty($slide['button_icon']['value'])) {
			return;
		}
		?>
		<span class="upsites-carousel__button-icon">
			<?php Icons_Manager::render_icon($slide['button_icon'], ['aria-hidden' => 'true']); ?>
		</span>
		<?php
	}

	private function render_card($slide, $card_style, $content_alignment = 'left')
	{
		$image        = ! empty($slide['image']['url']) ? $slide['image']['url'] : '';
		$eyebrow      = ! empty($slide['eyebrow']) ? $slide['eyebrow'] : '';
		$title        = ! empty($slide['title']) ? $slide['title'] : '';
		$description  = ! empty($slide['description']) ? $slide['description'] : '';
		$show_button  = ! empty($slide['show_button']) && 'yes' === $slide['show_button'];
		$button_text  = ! empty($slide['button_text']) ? $slide['button_text'] : '';
		$button_url   = ! empty($slide['button_link']['url']) ? $slide['button_link']['url'] : '#';
		$target       = ! empty($slide['button_link']['is_external']) ? ' target="_blank"' : '';
		$rel          = ! empty($slide['button_link']['nofollow']) ? ' rel="nofollow"' : '';
		$has_icon     = ! empty($slide['button_icon']['value']);
		$icon_position = ! empty($slide['button_icon_position']) ? $slide['button_icon_position'] : 'after';
		?>
		<div class="upsites-carousel__card upsites-carousel__card--<?php echo esc_attr($card_style); ?>">
			<?php if ($image) : ?>
				<div class="upsites-carousel__media">
					<img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
				</div>
			<?php endif; ?>
			<div class="upsites-carousel__card-body">
				<div class="upsites-carousel__content upsites-carousel__content--align-<?php echo esc_attr($content_alignment); ?>">
					<?php if ($eyebrow) : ?>
						<span class="upsites-carousel__eyebrow"><?php echo esc_html($eyebrow); ?></span>
					<?php endif; ?>
					<?php if ($title) : ?>
						<h3 class="upsites-carousel__title"><?php echo esc_html($title); ?></h3>
					<?php endif; ?>
					<?php if ($description && 'minimal' !== $card_style) : ?>
						<p class="upsites-carousel__description"><?php echo esc_html($description); ?></p>
					<?php endif; ?>
					<?php if ($show_button && $button_text) : ?>
						<a class="upsites-carousel__button<?php echo $has_icon ? ' upsites-carousel__button--has-icon' : ''; ?>" href="<?php echo esc_url($button_url); ?>"<?php echo $target . $rel; ?>>
							<?php if ($has_icon && 'before' === $icon_position) : ?>
								<?php $this->render_button_icon($slide); ?>
							<?php endif; ?>
							<span class="upsites-carousel__button-text"><?php echo esc_html($button_text); ?></span>
							<?php if ($has_icon && 'after' === $icon_position) : ?>
								<?php $this->render_button_icon($slide); ?>
							<?php endif; ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Splide auto-detects hand-authored `.splide__arrow--prev/--next` elements
	 * anywhere inside the root `.splide` node and wires its click handlers to
	 * them instead of generating its own — this lets the panel swap the icon
	 * without touching the library's arrow markup.
	 */
	private function render_arrows($icon)
	{
		if (empty($icon['value'])) {
			return;
		}
		?>
		<div class="splide__arrows">
			<button class="splide__arrow splide__arrow--prev upsites-carousel__custom-arrow" type="button" aria-label="<?php esc_attr_e('Anterior', 'upsites-addons'); ?>">
				<?php Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']); ?>
			</button>
			<button class="splide__arrow splide__arrow--next upsites-carousel__custom-arrow" type="button" aria-label="<?php esc_attr_e('Próximo', 'upsites-addons'); ?>">
				<?php Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']); ?>
			</button>
		</div>
		<?php
	}

	protected function render()
	{
		$settings   = $this->get_settings_for_display();
		$slides     = ! empty($settings['slides']) ? $settings['slides'] : [];
		$card_style = ! empty($settings['card_style']) ? $settings['card_style'] : 'default';
		$content_alignment = ! empty($settings['content_alignment']) ? $settings['content_alignment'] : 'left';
		$show_arrows = ! empty($settings['show_arrows']) && 'yes' === $settings['show_arrows'];

		if (empty($slides)) {
			return;
		}

		$frontend_settings = $this->build_splide_settings($settings);
		?>
		<div class="upsites-carousel splide" data-settings="<?php echo esc_attr(wp_json_encode($frontend_settings)); ?>">
			<div class="splide__track">
				<ul class="splide__list">
					<?php foreach ($slides as $slide) : ?>
						<li class="splide__slide">
							<?php $this->render_card($slide, $card_style, $content_alignment); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php if ($show_arrows) : ?>
				<?php $this->render_arrows($settings['custom_arrow_icon'] ?? []); ?>
			<?php endif; ?>
		</div>
		<?php
	}
}
