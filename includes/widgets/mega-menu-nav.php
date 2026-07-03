<?php
if (! defined('ABSPATH')) {
	exit;
}

use Elementor\Widget_Base;

require_once UPSITES_ADDONS_PATH . 'includes/controls/mega-menu-nav-controls.php';

class UpSites_Mega_Menu_Nav_Widget extends Widget_Base
{

	use UpSites_Mega_Menu_Nav_Controls;

	public function get_name()
	{
		return 'upsites-mega-menu-nav';
	}

	public function get_title()
	{
		return __('Mega Menu', 'upsites-addons');
	}

	public function get_icon()
	{
		return 'eicon-nav-menu';
	}

	public function get_categories()
	{
		return ['upsites'];
	}

	public function get_script_depends()
	{
		return ['upsites-mega-menu-nav'];
	}

	public function get_style_depends()
	{
		return ['upsites-mega-menu-nav'];
	}

	/**
	 * Tabs grouped by their originating nav_items row index (menu_tabs.tab_nav_index).
	 * Columns grouped by their originating tab's global position in menu_tabs
	 * (menu_columns.column_tab_index). Both populated once per render() call.
	 */
	private $tabs_by_nav_index     = [];
	private $columns_by_tab_index  = [];

	/**
	 * Builds nav_index => [tabs] and tab_position => [columns] maps from the
	 * flat "menu_tabs"/"menu_columns"/"menu_links" repeaters. Each level is
	 * linked to the previous one by a numeric index field instead of nested
	 * repeaters, since Elementor's nested-repeater UI has a bug where the
	 * "add row" button can trigger the wrong (parent) repeater.
	 */
	private function build_index_maps($menu_tabs, $menu_columns, $menu_links)
	{
		$links_by_column = [];
		foreach ($menu_links as $link) {
			$col_index = isset($link['link_column_index']) ? intval($link['link_column_index']) : 0;
			$links_by_column[$col_index][] = $link;
		}

		$columns_by_tab = [];
		foreach ($menu_columns as $col_position => $column) {
			$tab_index = isset($column['column_tab_index']) ? intval($column['column_tab_index']) : 0;
			$column['_links'] = isset($links_by_column[$col_position]) ? $links_by_column[$col_position] : [];
			$columns_by_tab[$tab_index][] = $column;
		}

		$tabs_by_nav = [];
		foreach ($menu_tabs as $tab_position => $tab) {
			$nav_index = isset($tab['tab_nav_index']) ? intval($tab['tab_nav_index']) : 0;
			$tab['_columns'] = isset($columns_by_tab[$tab_position]) ? $columns_by_tab[$tab_position] : [];
			$tabs_by_nav[$nav_index][] = $tab;
		}

		$this->tabs_by_nav_index = $tabs_by_nav;
	}

	private function render_icon($media, $alt = '')
	{
		if (empty($media['url'])) {
			return;
		}
		printf(
			'<img class="upsites-mega-nav__icon" src="%s" alt="%s">',
			esc_url($media['url']),
			esc_attr($alt)
		);
	}

	private function render_columns($columns)
	{
		if (empty($columns)) {
			return;
		}
		?>
		<div class="upsites-mega-nav__columns">
			<?php foreach ($columns as $column) : ?>
				<div class="upsites-mega-nav__column">
					<?php if (! empty($column['column_title'])) : ?>
						<div class="upsites-mega-nav__column-title"><?php echo esc_html($column['column_title']); ?></div>
					<?php endif; ?>
					<div class="upsites-mega-nav__links">
						<?php foreach ($column['_links'] as $link) :
							$url    = ! empty($link['link_url']['url']) ? $link['link_url']['url'] : '#';
							$target = ! empty($link['link_url']['is_external']) ? ' target="_blank"' : '';
							$rel    = ! empty($link['link_url']['nofollow']) ? ' rel="nofollow"' : '';
							$highlighted = ! empty($link['is_highlighted']) && 'yes' === $link['is_highlighted'];
						?>
							<a class="upsites-mega-nav__link<?php echo $highlighted ? ' is-highlighted' : ''; ?>"
								href="<?php echo esc_url($url); ?>"<?php echo $target . $rel; ?>>
								<span class="upsites-mega-nav__link-icon" style="background-color:<?php echo esc_attr($link['link_icon_bg_color']); ?>">
									<?php $this->render_icon($link['link_icon'], $link['link_label']); ?>
								</span>
								<span class="upsites-mega-nav__link-text">
									<span class="upsites-mega-nav__link-label"><?php echo esc_html($link['link_label']); ?></span>
									<?php if (! empty($link['link_description'])) : ?>
										<span class="upsites-mega-nav__link-desc"><?php echo esc_html($link['link_description']); ?></span>
									<?php endif; ?>
								</span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	private function render_promo($tab)
	{
		if (empty($tab['promo_enabled']) || 'yes' !== $tab['promo_enabled']) {
			return;
		}
		$image_url = ! empty($tab['promo_image']['url']) ? $tab['promo_image']['url'] : '';
		$link_url  = ! empty($tab['promo_link_url']['url']) ? $tab['promo_link_url']['url'] : '#';
		?>
		<div class="upsites-mega-nav__promo">
			<?php if ($image_url) : ?>
				<div class="upsites-mega-nav__promo-image">
					<img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($tab['promo_title']); ?>">
				</div>
			<?php endif; ?>
			<?php if (! empty($tab['promo_title'])) : ?>
				<div class="upsites-mega-nav__promo-title"><?php echo esc_html($tab['promo_title']); ?></div>
			<?php endif; ?>
			<?php if (! empty($tab['promo_text'])) : ?>
				<div class="upsites-mega-nav__promo-text"><?php echo esc_html($tab['promo_text']); ?></div>
			<?php endif; ?>
			<?php if (! empty($tab['promo_link_label'])) : ?>
				<a class="upsites-mega-nav__promo-link" href="<?php echo esc_url($link_url); ?>">
					<?php echo esc_html($tab['promo_link_label']); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Global footer link settings (not per-tab), shared by every mega menu
	 * panel and mobile drill-down screen. Populated once per render() call.
	 */
	private $panel_footer = [];

	private function render_panel_footer()
	{
		if (empty($this->panel_footer['enabled'])) {
			return;
		}
		?>
		<div class="upsites-mega-nav__panel-footer">
			<a class="upsites-mega-nav__panel-footer-link" href="<?php echo esc_url($this->panel_footer['url']); ?>">
				<?php echo esc_html($this->panel_footer['text']); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Renders one mega menu panel (tabs header when there's more than one tab
	 * for this nav item, plus each tab's columns/promo). Shared between the
	 * desktop dropdown and the mobile drill-down level-2 screen.
	 */
	private function render_panel($tabs, $group_index)
	{
		if (empty($tabs)) {
			return;
		}
		$has_tabs = count($tabs) > 1;
		?>
		<div class="upsites-mega-nav__panel<?php echo $has_tabs ? '' : ' upsites-mega-nav__panel--no-tabs'; ?>" data-group="<?php echo esc_attr($group_index); ?>">
			<?php if ($has_tabs) : ?>
				<div class="upsites-mega-nav__tabs" role="tablist">
					<?php foreach ($tabs as $tab_index => $tab) :
						$is_active = (0 === $tab_index);
						$icon_url  = ! empty($tab['tab_icon']['url']) ? $tab['tab_icon']['url'] : '';
					?>
						<button type="button"
							class="upsites-mega-nav__tab<?php echo $is_active ? ' is-active' : ''; ?>"
							role="tab"
							aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
							data-tab="<?php echo esc_attr($tab_index); ?>"
							style="--tab-accent: <?php echo esc_attr($tab['tab_accent_color']); ?>">
							<?php if ($icon_url) : ?>
								<span class="upsites-mega-nav__tab-icon-wrap" style="background-color: <?php echo esc_attr($tab['tab_icon_bg_color']); ?>">
									<img class="upsites-mega-nav__tab-icon" src="<?php echo esc_url($icon_url); ?>" alt="">
								</span>
							<?php endif; ?>
							<span class="upsites-mega-nav__tab-text">
								<span class="upsites-mega-nav__tab-title"><?php echo esc_html($tab['tab_title']); ?></span>
								<?php if (! empty($tab['tab_description'])) : ?>
									<span class="upsites-mega-nav__tab-desc"><?php echo esc_html($tab['tab_description']); ?></span>
								<?php endif; ?>
							</span>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="upsites-mega-nav__panel-body">
				<?php foreach ($tabs as $tab_index => $tab) :
					$is_active = (0 === $tab_index);
					$style     = ! empty($tab['submenu_style']) ? $tab['submenu_style'] : 'columns_simple';
				?>
					<div class="upsites-mega-nav__tabpanel<?php echo $is_active ? ' is-active' : ''; ?> upsites-mega-nav__tabpanel--<?php echo esc_attr($style); ?>"
						role="tabpanel"
						data-tabpanel="<?php echo esc_attr($tab_index); ?>">
						<?php $this->render_columns($tab['_columns']); ?>
						<?php if ('columns_promo' === $style && ! empty($tab['promo_enabled']) && 'yes' === $tab['promo_enabled']) : ?>
							<div class="upsites-mega-nav__divider" aria-hidden="true"></div>
							<?php $this->render_promo($tab); ?>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
				<?php $this->render_panel_footer(); ?>
			</div>
		</div>
		<?php
	}

	protected function render()
	{
		$settings  = $this->get_settings_for_display();
		$nav_items = ! empty($settings['nav_items']) ? $settings['nav_items'] : [];

		if (empty($nav_items)) {
			return;
		}

		$this->build_index_maps(
			! empty($settings['menu_tabs']) ? $settings['menu_tabs'] : [],
			! empty($settings['menu_columns']) ? $settings['menu_columns'] : [],
			! empty($settings['menu_links']) ? $settings['menu_links'] : []
		);

		$this->panel_footer = [
			'enabled' => ! empty($settings['panel_footer_enabled']) && 'yes' === $settings['panel_footer_enabled'],
			'text'    => ! empty($settings['panel_footer_text']) ? $settings['panel_footer_text'] : '',
			'url'     => ! empty($settings['panel_footer_url']['url']) ? $settings['panel_footer_url']['url'] : '#',
		];
		?>
		<div class="upsites-mega-nav">

			<?php /* ── Desktop bar ── */ ?>
			<div class="upsites-mega-nav__desktop">
				<ul class="upsites-mega-nav__top-list" role="menubar">
					<?php foreach ($nav_items as $nav_index => $item) :
						$has_submenu = ! empty($item['has_submenu']) && 'yes' === $item['has_submenu'];
						$item_url    = ! empty($item['item_link']['url']) ? $item['item_link']['url'] : '#';
						$tabs        = isset($this->tabs_by_nav_index[$nav_index]) ? $this->tabs_by_nav_index[$nav_index] : [];
					?>
						<li class="upsites-mega-nav__top-item<?php echo ($has_submenu && ! empty($tabs)) ? ' has-submenu' : ''; ?>" data-group="<?php echo esc_attr($nav_index); ?>">
							<?php if ($has_submenu && ! empty($tabs)) : ?>
								<button type="button" class="upsites-mega-nav__top-link" aria-expanded="false" aria-haspopup="true">
									<?php echo esc_html($item['item_label']); ?>
								</button>
								<?php $this->render_panel($tabs, $nav_index); ?>
							<?php else : ?>
								<a class="upsites-mega-nav__top-link" href="<?php echo esc_url($item_url); ?>">
									<?php echo esc_html($item['item_label']); ?>
								</a>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>

				<button type="button" class="upsites-mega-nav__burger" aria-label="<?php esc_attr_e('Abrir menu', 'upsites-addons'); ?>" aria-expanded="false">
					<span></span><span></span><span></span>
				</button>
			</div>

			<?php /* ── Mobile off-canvas ── */ ?>
			<div class="upsites-mega-nav__mobile" aria-hidden="true">
				<div class="upsites-mega-nav__mobile-header">
					<button type="button" class="upsites-mega-nav__close" aria-label="<?php esc_attr_e('Fechar menu', 'upsites-addons'); ?>">&times;</button>
				</div>

				<div class="upsites-mega-nav__mobile-screens">

					<?php /* Level 1 */ ?>
					<div class="upsites-mega-nav__screen upsites-mega-nav__screen--level1 is-active" data-level="1">
						<ul class="upsites-mega-nav__mobile-list">
							<?php foreach ($nav_items as $nav_index => $item) :
								$has_submenu = ! empty($item['has_submenu']) && 'yes' === $item['has_submenu'];
								$item_url    = ! empty($item['item_link']['url']) ? $item['item_link']['url'] : '#';
								$tabs        = isset($this->tabs_by_nav_index[$nav_index]) ? $this->tabs_by_nav_index[$nav_index] : [];
							?>
								<li>
									<?php if ($has_submenu && ! empty($tabs)) : ?>
										<button type="button" class="upsites-mega-nav__mobile-link" data-open-group="<?php echo esc_attr($nav_index); ?>">
											<?php echo esc_html($item['item_label']); ?>
											<span class="upsites-mega-nav__chevron" aria-hidden="true"></span>
										</button>
									<?php else : ?>
										<a class="upsites-mega-nav__mobile-link" href="<?php echo esc_url($item_url); ?>">
											<?php echo esc_html($item['item_label']); ?>
										</a>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>

						</ul>
					</div>

					<?php /* Level 2 — one screen per nav item with a submenu */ ?>
					<?php foreach ($nav_items as $nav_index => $item) :
						$has_submenu = ! empty($item['has_submenu']) && 'yes' === $item['has_submenu'];
						$tabs        = isset($this->tabs_by_nav_index[$nav_index]) ? $this->tabs_by_nav_index[$nav_index] : [];
						if (! $has_submenu || empty($tabs)) {
							continue;
						}
					?>
						<div class="upsites-mega-nav__screen upsites-mega-nav__screen--level2" data-level="2" data-group="<?php echo esc_attr($nav_index); ?>">
							<button type="button" class="upsites-mega-nav__back" data-back>
								<span class="upsites-mega-nav__chevron upsites-mega-nav__chevron--back" aria-hidden="true"></span>
								<?php esc_html_e('Voltar', 'upsites-addons'); ?>
							</button>

							<?php foreach ($tabs as $tab_position => $tab) : ?>
								<?php if ('columns_promo' === $tab['submenu_style']) : ?>
									<?php $this->render_promo($tab); ?>
								<?php endif; ?>

								<div class="upsites-mega-nav__mobile-accordion-group">
									<?php if (! empty($tab['tab_title'])) : ?>
										<div class="upsites-mega-nav__mobile-group-label"><?php echo esc_html($tab['tab_title']); ?></div>
									<?php endif; ?>
									<?php foreach ($tab['_columns'] as $col_index => $column) :
										$panel_id = 'mm-' . $nav_index . '-' . $tab_position . '-' . $col_index;
									?>
										<div class="upsites-mega-nav__accordion">
											<button type="button" class="upsites-mega-nav__accordion-trigger" aria-expanded="false" aria-controls="<?php echo esc_attr($panel_id); ?>">
												<?php echo esc_html($column['column_title']); ?>
												<span class="upsites-mega-nav__chevron" aria-hidden="true"></span>
											</button>
											<div class="upsites-mega-nav__accordion-panel" id="<?php echo esc_attr($panel_id); ?>">
												<?php foreach ($column['_links'] as $link) :
													$url = ! empty($link['link_url']['url']) ? $link['link_url']['url'] : '#';
												?>
													<a class="upsites-mega-nav__link" href="<?php echo esc_url($url); ?>">
														<span class="upsites-mega-nav__link-icon" style="background-color:<?php echo esc_attr($link['link_icon_bg_color']); ?>">
															<?php $this->render_icon($link['link_icon'], $link['link_label']); ?>
														</span>
														<span class="upsites-mega-nav__link-label"><?php echo esc_html($link['link_label']); ?></span>
													</a>
												<?php endforeach; ?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endforeach; ?>
							<?php $this->render_panel_footer(); ?>
						</div>
					<?php endforeach; ?>

				</div>
			</div>

		</div>
		<?php
	}
}
