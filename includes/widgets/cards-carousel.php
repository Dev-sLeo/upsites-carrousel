<?php
if (! defined('ABSPATH')) {
	exit;
}

use Elementor\Widget_Base;

require_once UPSITES_ADDONS_PATH . 'includes/controls/cards-carousel-controls.php';

class UpSites_Cards_Carousel_Widget extends Widget_Base
{

	use UpSites_Cards_Carousel_Controls;

	public function get_name()
	{
		return 'upsites-cards-carousel';
	}

	public function get_title()
	{
		return __('Carrossel Cards', 'upsites-addons');
	}

	public function get_icon()
	{
		return 'eicon-slides';
	}

	public function get_categories()
	{
		return ['upsites'];
	}

	public function get_script_depends()
	{
		return ['upsites-cards-carousel'];
	}

	public function get_style_depends()
	{
		return ['upsites-cards-carousel'];
	}

	/**
	 * Sanitizes description HTML, stripping inline style/color/class attributes
	 * so the panel's color/typography controls always win. Repeater/meta fields
	 * (JetEngine rich text, pasted content) often carry inline "style" attrs that
	 * wp_kses_post() preserves for users with unfiltered_html, overriding the widget CSS.
	 *
	 * Also unwraps a single outer <p> tag: JetEngine rich text fields already
	 * return content wrapped in <p>...</p>, and re-wrapping that in the widget's
	 * own <p class="upsites-cc-card__desc"> produces nested <p> tags. Browsers
	 * auto-close the outer <p> when they hit the inner one, so the text ends up
	 * in an unclassed <p> that ignores the color/typography controls entirely.
	 */
	private function sanitize_card_desc($html)
	{
		if (empty($html)) {
			return '';
		}
		$html = wp_kses_post($html);
		$html = preg_replace('/\s(style|class)=("[^"]*"|\'[^\']*\')/i', '', $html);
		$html = trim($html);
		if (preg_match('/^<p>(.*)<\/p>$/is', $html, $matches) && stripos($matches[1], '<p>') === false) {
			$html = $matches[1];
		}
		return $html;
	}

	/**
	 * Resolves a meta value that may hold an attachment ID or a raw URL
	 * (covers JetEngine/Crocoblock image fields).
	 */
	private function resolve_meta_image_url($meta_value)
	{
		if (empty($meta_value)) {
			return '';
		}
		if (is_numeric($meta_value)) {
			$url = wp_get_attachment_image_url((int) $meta_value, 'full');
			if ($url) {
				return $url;
			}
		}
		return is_string($meta_value) ? $meta_value : '';
	}

	/**
	 * Builds cards from a JetEngine/Crocoblock repeater field stored in post meta.
	 * Each post can contribute N rows → N cards.
	 *
	 * The repeater value is usually a serialized array of associative arrays:
	 *   [ ['titulo' => '...', 'descricao' => '...', 'imagem' => 123, 'icone' => 456], ... ]
	 */
	private function get_query_cards_from_repeater($settings)
	{
		$repeater_key = ! empty($settings['query_repeater_meta_key']) ? $settings['query_repeater_meta_key'] : '';
		$title_key    = ! empty($settings['query_repeater_title_key']) ? $settings['query_repeater_title_key'] : 'titulo';
		$desc_key     = ! empty($settings['query_repeater_desc_key'])  ? $settings['query_repeater_desc_key']  : 'descricao';
		$image_key    = ! empty($settings['query_repeater_image_key']) ? $settings['query_repeater_image_key'] : 'imagem';
		$icon_key     = ! empty($settings['query_repeater_icon_key'])  ? $settings['query_repeater_icon_key']  : 'icone';

		if (empty($repeater_key)) {
			return [];
		}

		$scope = ! empty($settings['query_repeater_scope']) ? $settings['query_repeater_scope'] : 'current_post';

		// "Página/post atual": lê o repeater direto do post onde o widget está, sem consultar outros posts.
		if ('current_post' === $scope) {
			$post_ids = [get_the_ID()];
		} else {
			$query = new \WP_Query([
				'post_type'      => ! empty($settings['query_post_type'])      ? $settings['query_post_type']               : 'post',
				'posts_per_page' => ! empty($settings['query_posts_per_page']) ? intval($settings['query_posts_per_page']) : 5,
				'orderby'        => ! empty($settings['query_orderby'])        ? $settings['query_orderby']                 : 'date',
				'order'          => ! empty($settings['query_order'])          ? $settings['query_order']                   : 'DESC',
				'post_status'    => 'publish',
				'no_found_rows'  => true,
				'fields'         => 'ids',
			]);

			$post_ids = $query->posts;
			wp_reset_postdata();
		}

		$cards = [];

		foreach ($post_ids as $post_id) {
			if (empty($post_id)) {
				continue;
			}

			$raw = get_post_meta($post_id, $repeater_key, true);

			// JetEngine armazena o repeater serializado; pode já vir como array em alguns casos.
			if (is_string($raw)) {
				$raw = maybe_unserialize($raw);
			}

			if (empty($raw) || ! is_array($raw)) {
				continue;
			}

			foreach ($raw as $row) {
				if (! is_array($row)) {
					continue;
				}

				$title     = ! empty($row[$title_key]) ? sanitize_text_field($row[$title_key]) : '';
				$desc      = ! empty($row[$desc_key])  ? $this->sanitize_card_desc($row[$desc_key]) : '';
				$image_url = ! empty($row[$image_key]) ? $this->resolve_meta_image_url($row[$image_key]) : '';
				$icon_url  = ($icon_key && ! empty($row[$icon_key])) ? $this->resolve_meta_image_url($row[$icon_key]) : '';

				$cards[] = [
					'card_icon'        => ['url' => $icon_url],
					'card_icon_alt'    => '',
					'card_title'       => $title,
					'card_description' => $desc,
					'card_image'       => ['url' => $image_url],
					'card_image_alt'   => $title,
				];
			}
		}

		return $cards;
	}

	/**
	 * Builds the cards array from a WP_Query when content source is "dynamic",
	 * keeping the same shape as the manual repeater so render markup is reused.
	 */
	private function get_query_cards($settings)
	{
		$query = new \WP_Query([
			'post_type'      => ! empty($settings['query_post_type']) ? $settings['query_post_type'] : 'post',
			'posts_per_page' => ! empty($settings['query_posts_per_page']) ? intval($settings['query_posts_per_page']) : 5,
			'orderby'        => ! empty($settings['query_orderby']) ? $settings['query_orderby'] : 'date',
			'order'          => ! empty($settings['query_order']) ? $settings['query_order'] : 'DESC',
			'post_status'    => 'publish',
			'no_found_rows'  => true,
		]);

		$cards = [];

		foreach ($query->posts as $post) {
			$desc = '';
			if ('meta' === $settings['query_desc_source'] && ! empty($settings['query_desc_meta_key'])) {
				$desc = get_post_meta($post->ID, $settings['query_desc_meta_key'], true);
			} else {
				$desc = get_the_excerpt($post);
			}
			$desc = $this->sanitize_card_desc($desc);

			$image_url = '';
			if ('meta' === $settings['query_image_source'] && ! empty($settings['query_image_meta_key'])) {
				$image_url = $this->resolve_meta_image_url(get_post_meta($post->ID, $settings['query_image_meta_key'], true));
			} else {
				$image_url = get_the_post_thumbnail_url($post, 'full');
			}

			$icon_url = '';
			if (! empty($settings['query_icon_meta_key'])) {
				$icon_url = $this->resolve_meta_image_url(get_post_meta($post->ID, $settings['query_icon_meta_key'], true));
			}

			$cards[] = [
				'card_icon'         => ['url' => $icon_url],
				'card_icon_alt'     => '',
				'card_title'        => get_the_title($post),
				'card_description'  => $desc,
				'card_image'        => ['url' => $image_url ? $image_url : ''],
				'card_image_alt'    => get_the_title($post),
			];
		}

		wp_reset_postdata();

		return $cards;
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		if ('query' === $settings['content_source']) {
			$card_source = ! empty($settings['query_card_source']) ? $settings['query_card_source'] : 'flat';
			$cards = ('repeater' === $card_source)
				? $this->get_query_cards_from_repeater($settings)
				: $this->get_query_cards($settings);
		} else {
			$cards = $settings['cards'];
		}

		if (empty($cards)) {
			return;
		}

		$top_offset        = isset($settings['scroll_top_offset']['size'])        ? intval($settings['scroll_top_offset']['size'])        : 0;
		$top_offset_tablet = isset($settings['scroll_top_offset_tablet']['size']) ? intval($settings['scroll_top_offset_tablet']['size']) : $top_offset;
		$top_offset_mobile = isset($settings['scroll_top_offset_mobile']['size']) ? intval($settings['scroll_top_offset_mobile']['size']) : $top_offset_tablet;
?>
		<div class="upsites-cards-carousel"
			data-top-offset="<?php echo esc_attr($top_offset); ?>"
			data-top-offset-tablet="<?php echo esc_attr($top_offset_tablet); ?>"
			data-top-offset-mobile="<?php echo esc_attr($top_offset_mobile); ?>">

			<?php foreach ($cards as $card) :
				$icon_url  = ! empty($card['card_icon']['url'])  ? $card['card_icon']['url']  : '';
				$icon_alt  = ! empty($card['card_icon_alt'])     ? $card['card_icon_alt']     : '';
				$title     = ! empty($card['card_title'])        ? $card['card_title']        : '';
				$desc      = ! empty($card['card_description'])  ? $card['card_description']  : '';
				$image_url = ! empty($card['card_image']['url']) ? $card['card_image']['url'] : '';
				$image_alt = ! empty($card['card_image_alt'])    ? $card['card_image_alt']    : '';
			?>
				<div class="upsites-cc-card-wrapper">
					<div class="upsites-cc-card">

						<div class="upsites-cc-card__inner">

							<div class="upsites-cc-card__left">

								<?php if ($icon_url) : ?>
									<div class="upsites-cc-card__icon-wrap">
										<img src="<?php echo esc_url($icon_url); ?>"
											alt="<?php echo esc_attr($icon_alt); ?>">
									</div>
								<?php else : ?>
									<div class="upsites-cc-card__icon-wrap upsites-cc-card__icon-wrap--empty">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
											<circle cx="12" cy="12" r="10" />
											<circle cx="12" cy="12" r="3" />
											<line x1="12" y1="2" x2="12" y2="5" />
											<line x1="12" y1="19" x2="12" y2="22" />
											<line x1="2" y1="12" x2="5" y2="12" />
											<line x1="19" y1="12" x2="22" y2="12" />
										</svg>
									</div>
								<?php endif; ?>

								<div class="upsites-cc-card__text">
									<?php if ($title) : ?>
										<h3 class="upsites-cc-card__title">
											<?php echo wp_kses_post($title); ?>
										</h3>
									<?php endif; ?>

									<?php if ($desc) : ?>
										<div class="upsites-cc-card__desc">
											<?php echo $this->sanitize_card_desc($desc); ?>
										</div>
									<?php endif; ?>
								</div>

							</div><!-- .upsites-cc-card__left -->

							<div class="upsites-cc-card__right">
								<div class="upsites-cc-card__image-wrap">
									<?php if ($image_url) : ?>
										<img src="<?php echo esc_url($image_url); ?>"
											alt="<?php echo esc_attr($image_alt); ?>">
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
