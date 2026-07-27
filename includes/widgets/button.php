<?php
if (! defined('ABSPATH')) {
	exit;
}

use Elementor\Widget_Base;

require_once UPSITES_ADDONS_PATH . 'includes/controls/button-controls.php';

class UpSites_Button_Widget extends Widget_Base
{

	use UpSites_Button_Controls;

	public function get_name()
	{
		return 'upsites-button';
	}

	public function get_title()
	{
		return __('Botão', 'upsites-addons');
	}

	public function get_icon()
	{
		return 'eicon-button';
	}

	public function get_categories()
	{
		return ['upsites'];
	}

	public function get_script_depends()
	{
		return ['upsites-button'];
	}

	public function get_style_depends()
	{
		return ['upsites-button'];
	}

	/**
	 * Returns the bundled default arrow SVG — a plain arrow for
	 * Primário/Secundário, or the "bullet" (arrow inside a ball) for Link —
	 * inlined so their colors read from the --upsites-btn-icon-ball /
	 * --upsites-btn-icon-arrow CSS custom properties set by the panel, and
	 * the hover swap animation can move it via CSS transforms.
	 */
	private function get_default_arrow_svg($style)
	{
		static $cache = [];
		$file = 'link' === $style ? 'button-arrow-bullet.svg' : 'button-arrow.svg';
		if (! isset($cache[$file])) {
			$path         = UPSITES_ADDONS_PATH . 'assets/images/' . $file;
			$cache[$file] = file_exists($path) ? file_get_contents($path) : '';
		}
		return $cache[$file];
	}

	private function render_icon_markup($custom_icon_url, $animate, $style)
	{
		$inner = $custom_icon_url
			? '<img src="' . esc_url($custom_icon_url) . '" alt="">'
			: $this->get_default_arrow_svg($style);

		if (! $animate) {
			printf('<span class="upsites-btn__icon upsites-btn__icon--static">%s</span>', $inner);
			return;
		}
		?>
		<span class="upsites-btn__icon">
			<span class="upsites-btn__icon-arrow upsites-btn__icon-arrow--a"><?php echo $inner; ?></span>
			<span class="upsites-btn__icon-arrow upsites-btn__icon-arrow--b"><?php echo $inner; ?></span>
		</span>
		<?php
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$text          = ! empty($settings['button_text']) ? $settings['button_text'] : '';
		$url           = ! empty($settings['button_link']['url']) ? $settings['button_link']['url'] : '#';
		$target        = ! empty($settings['button_link']['is_external']) ? ' target="_blank"' : '';
		$rel           = ! empty($settings['button_link']['nofollow']) ? ' rel="nofollow"' : '';
		$style         = ! empty($settings['button_style']) ? $settings['button_style'] : 'primary';
		$show_icon     = ! empty($settings['show_icon']) && 'yes' === $settings['show_icon'];
		$icon_position = ! empty($settings['icon_position']) ? $settings['icon_position'] : 'after';
		$animate       = $show_icon && ! empty($settings['icon_hover_animation']) && 'yes' === $settings['icon_hover_animation'];
		$custom_icon   = ('custom' === $settings['icon_source'] && ! empty($settings['icon']['url'])) ? $settings['icon']['url'] : '';
		$button_size   = ! empty($settings['button_size']) ? $settings['button_size'] : 'md';

		$classes = ['upsites-btn', 'upsites-btn--' . $style, 'upsites-btn--size-' . $button_size];
		if ('link' === $style) {
			$link_variant = ! empty($settings['link_variant']) ? $settings['link_variant'] : 'light';
			$classes[]    = 'upsites-btn--link-' . $link_variant;
		}
		if ($show_icon) {
			$classes[] = 'upsites-btn--icon-' . $icon_position;
		}
		if (! empty($settings['hover_animation'])) {
			$classes[] = 'elementor-animation-' . $settings['hover_animation'];
		}
		?>
		<div class="upsites-btn-wrap">
			<a class="<?php echo esc_attr(implode(' ', $classes)); ?>" href="<?php echo esc_url($url); ?>"<?php echo $target . $rel; ?>>
				<?php if ($show_icon && 'before' === $icon_position) : ?>
					<?php $this->render_icon_markup($custom_icon, $animate, $style); ?>
				<?php endif; ?>
				<span class="upsites-btn__text"><?php echo esc_html($text); ?></span>
				<?php if ($show_icon && 'after' === $icon_position) : ?>
					<?php $this->render_icon_markup($custom_icon, $animate, $style); ?>
				<?php endif; ?>
			</a>
		</div>
		<?php
	}
}
