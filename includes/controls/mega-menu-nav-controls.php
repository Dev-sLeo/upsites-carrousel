<?php
if (! defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

trait UpSites_Mega_Menu_Nav_Controls
{

	protected function register_controls()
	{

		// ── Content Tab — Rodapé do painel ────────────────────────────
		$this->start_controls_section(
			'section_panel_footer',
			[
				'label' => __('Rodapé do painel (mega menu)', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'panel_footer_help',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __('Linha + link exibidos no rodapé de todos os painéis de mega menu (desktop) e de cada tela de submenu (mobile), como no Figma.', 'upsites-addons'),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->add_control(
			'panel_footer_enabled',
			[
				'label'        => __('Mostrar rodapé', 'upsites-addons'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Sim', 'upsites-addons'),
				'label_off'    => __('Não', 'upsites-addons'),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'panel_footer_text',
			[
				'label'     => __('Texto do link', 'upsites-addons'),
				'type'      => Controls_Manager::TEXT,
				'default'   => __('Solicitar demonstração', 'upsites-addons'),
				'condition' => ['panel_footer_enabled' => 'yes'],
			]
		);

		$this->add_control(
			'panel_footer_url',
			[
				'label'       => __('Link', 'upsites-addons'),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'default'     => ['url' => '#'],
				'condition'   => ['panel_footer_enabled' => 'yes'],
			]
		);

		$this->end_controls_section();

		// ── Content Tab — Itens do menu ──────────────────────────────
		$this->start_controls_section(
			'section_nav_items',
			[
				'label' => __('Itens do menu', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'nav_items_help',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __('Cada linha aqui é um item da barra de topo (ex: "Produtos", "Soluções", "Preços"). As abas de cada mega menu são cadastradas na próxima seção ("Abas do mega menu"), ligadas pelo número da linha aqui embaixo (começando em 0).', 'upsites-addons'),
				'content_classes' => 'elementor-descriptor',
			]
		);

		// Repeater principal: itens de topo da barra
		$nav_repeater = new Repeater();

		$nav_repeater->add_control(
			'item_label',
			[
				'label'       => __('Rótulo do item', 'upsites-addons'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __('Menu', 'upsites-addons'),
			]
		);

		$nav_repeater->add_control(
			'item_link',
			[
				'label'       => __('Link (quando sem submenu)', 'upsites-addons'),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'default'     => ['url' => '#'],
				'condition'   => ['has_submenu!' => 'yes'],
			]
		);

		$nav_repeater->add_control(
			'has_submenu',
			[
				'label'        => __('Tem mega menu?', 'upsites-addons'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Sim', 'upsites-addons'),
				'label_off'    => __('Não', 'upsites-addons'),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'nav_items',
			[
				'label'       => __('1. Itens de topo', 'upsites-addons'),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $nav_repeater->get_controls(),
				'title_field' => '{{{ item_label }}}',
				'default'     => [
					['item_label' => __('Produtos', 'upsites-addons'), 'has_submenu' => 'yes'],
					['item_label' => __('Soluções', 'upsites-addons'), 'has_submenu' => 'yes'],
					['item_label' => __('Parceiros', 'upsites-addons'), 'has_submenu' => 'yes'],
					['item_label' => __('Recursos', 'upsites-addons'), 'has_submenu' => 'yes'],
					['item_label' => __('Preços', 'upsites-addons'), 'has_submenu' => ''],
				],
			]
		);

		$this->end_controls_section();

		// ── Content Tab — Abas ────────────────────────────────────────
		$this->start_controls_section(
			'section_menu_tabs',
			[
				'label' => __('2. Abas do mega menu', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'menu_tabs_help',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __('Cada mega menu precisa de pelo menos 1 aba aqui (mesmo que não apareça um cabeçalho de aba visível — ela é quem define o estilo e o conteúdo do painel). Para um mega menu com abas visíveis (ex: "Produtos" com "Hotéis" / "Buyers"), adicione 2 linhas com o mesmo "Pertence ao item #" e preencha o "Título da aba" em cada uma. Para um mega menu sem abas visíveis (ex: "Soluções", "Parceiros", "Recursos"), adicione só 1 linha e deixe o "Título da aba" em branco.', 'upsites-addons'),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$tab_repeater = new Repeater();

		$tab_repeater->add_control(
			'tab_nav_index',
			[
				'label'       => __('Pertence ao item #', 'upsites-addons'),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'description' => __('Número da linha em "Itens de topo" (começando em 0).', 'upsites-addons'),
			]
		);

		$tab_repeater->add_control(
			'submenu_style',
			[
				'label'     => __('Estilo do painel', 'upsites-addons'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'columns_simple',
				'options'   => [
					'tabs_columns'   => __('Colunas simples com abas visíveis (ex: Produtos)', 'upsites-addons'),
					'columns_promo'  => __('Colunas + card promocional (ex: Soluções)', 'upsites-addons'),
					'columns_cards'  => __('Colunas com cards (ex: Parceiros)', 'upsites-addons'),
					'columns_simple' => __('Colunas simples (ex: Recursos)', 'upsites-addons'),
				],
				'separator' => 'before',
			]
		);

		$tab_repeater->add_control(
			'tab_title',
			[
				'label'       => __('Título da aba', 'upsites-addons'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
				'description' => __('Deixe em branco se este mega menu não deve mostrar abas visíveis.', 'upsites-addons'),
			]
		);

		$tab_repeater->add_control(
			'tab_icon',
			[
				'label'     => __('Ícone da aba', 'upsites-addons'),
				'type'      => Controls_Manager::MEDIA,
				'default'   => ['url' => ''],
				'condition' => ['tab_title!' => ''],
			]
		);

		$tab_repeater->add_control(
			'tab_icon_bg_color',
			[
				'label'     => __('Cor de fundo do ícone da aba', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(254,196,55,0.1)',
				'condition' => ['tab_title!' => ''],
			]
		);

		$tab_repeater->add_control(
			'tab_description',
			[
				'label'     => __('Descrição da aba', 'upsites-addons'),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => ['tab_title!' => ''],
			]
		);

		$tab_repeater->add_control(
			'tab_accent_color',
			[
				'label'     => __('Cor de destaque da aba', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fec437',
				'condition' => ['tab_title!' => ''],
			]
		);

		$tab_repeater->add_control(
			'promo_enabled',
			[
				'label'        => __('Mostrar card promocional', 'upsites-addons'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Sim', 'upsites-addons'),
				'label_off'    => __('Não', 'upsites-addons'),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
				'condition'    => ['submenu_style' => 'columns_promo'],
			]
		);

		$tab_repeater->add_control(
			'promo_image',
			[
				'label'     => __('Imagem do card', 'upsites-addons'),
				'type'      => Controls_Manager::MEDIA,
				'default'   => ['url' => ''],
				'condition' => ['submenu_style' => 'columns_promo', 'promo_enabled' => 'yes'],
			]
		);

		$tab_repeater->add_control(
			'promo_title',
			[
				'label'     => __('Título do card', 'upsites-addons'),
				'type'      => Controls_Manager::TEXT,
				'default'   => __('Fale com um especialista', 'upsites-addons'),
				'condition' => ['submenu_style' => 'columns_promo', 'promo_enabled' => 'yes'],
			]
		);

		$tab_repeater->add_control(
			'promo_text',
			[
				'label'     => __('Texto do card', 'upsites-addons'),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 3,
				'default'   => '',
				'condition' => ['submenu_style' => 'columns_promo', 'promo_enabled' => 'yes'],
			]
		);

		$tab_repeater->add_control(
			'promo_link_label',
			[
				'label'     => __('Texto do link', 'upsites-addons'),
				'type'      => Controls_Manager::TEXT,
				'default'   => __('Entre em contato', 'upsites-addons'),
				'condition' => ['submenu_style' => 'columns_promo', 'promo_enabled' => 'yes'],
			]
		);

		$tab_repeater->add_control(
			'promo_link_url',
			[
				'label'       => __('Link', 'upsites-addons'),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'default'     => ['url' => '#'],
				'condition'   => ['submenu_style' => 'columns_promo', 'promo_enabled' => 'yes'],
			]
		);

		$this->add_control(
			'menu_tabs',
			[
				'label'       => __('Abas', 'upsites-addons'),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $tab_repeater->get_controls(),
				'title_field' => '#{{{ tab_nav_index }}} — {{{ tab_title || "(sem aba visível)" }}}',
				'default'     => [
					['tab_nav_index' => 0, 'submenu_style' => 'tabs_columns', 'tab_title' => __('Hotéis', 'upsites-addons')],
					['tab_nav_index' => 0, 'submenu_style' => 'tabs_columns', 'tab_title' => __('Buyers', 'upsites-addons')],
					['tab_nav_index' => 1, 'submenu_style' => 'columns_promo'],
					['tab_nav_index' => 2, 'submenu_style' => 'columns_cards'],
					['tab_nav_index' => 3, 'submenu_style' => 'columns_simple'],
				],
			]
		);

		$this->end_controls_section();

		// ── Content Tab — Colunas ─────────────────────────────────────
		$this->start_controls_section(
			'section_menu_columns',
			[
				'label' => __('3. Colunas do mega menu', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'menu_columns_help',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __('Cada coluna precisa informar a que aba ela pertence (a posição #0, #1, #2... da lista em "Abas do mega menu", na ordem em que foram cadastradas). Dica para o estilo "Colunas com cards" (ex: Parceiros): agrupe por categoria (ex: "Nossos Parceiros", "BeePartner") — cada coluna vira um grupo de largura igual, e os cards dentro dela se organizam em fileira (não empilhados).', 'upsites-addons'),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$column_repeater = new Repeater();

		$column_repeater->add_control(
			'column_tab_index',
			[
				'label'       => __('Pertence à aba #', 'upsites-addons'),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'description' => __('Posição da aba na lista "Abas do mega menu" (começando em 0).', 'upsites-addons'),
			]
		);

		$column_repeater->add_control(
			'column_title',
			[
				'label'       => __('Título da coluna', 'upsites-addons'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __('Categoria', 'upsites-addons'),
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'menu_columns',
			[
				'label'       => __('Colunas', 'upsites-addons'),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $column_repeater->get_controls(),
				'title_field' => '#{{{ column_tab_index }}} — {{{ column_title }}}',
			]
		);

		$this->end_controls_section();

		// ── Content Tab — Links ───────────────────────────────────────
		$this->start_controls_section(
			'section_menu_links',
			[
				'label' => __('4. Links das colunas', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'menu_links_help',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __('Cada link precisa informar a qual coluna pertence (a posição #0, #1, #2... da lista em "Colunas do mega menu", na ordem em que foram cadastradas).', 'upsites-addons'),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$link_repeater = new Repeater();

		$link_repeater->add_control(
			'link_column_index',
			[
				'label'       => __('Pertence à coluna #', 'upsites-addons'),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'description' => __('Posição da coluna na lista "Colunas do mega menu" (começando em 0).', 'upsites-addons'),
			]
		);

		$link_repeater->add_control(
			'link_icon',
			[
				'label'     => __('Ícone', 'upsites-addons'),
				'type'      => Controls_Manager::MEDIA,
				'default'   => ['url' => ''],
				'separator' => 'before',
			]
		);

		$link_repeater->add_control(
			'link_icon_bg_color',
			[
				'label'   => __('Cor de fundo do ícone', 'upsites-addons'),
				'type'    => Controls_Manager::COLOR,
				'default' => 'rgba(142,82,222,0.1)',
			]
		);

		$link_repeater->add_control(
			'link_label',
			[
				'label'       => __('Texto', 'upsites-addons'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __('Item do menu', 'upsites-addons'),
				'separator'   => 'before',
			]
		);

		$link_repeater->add_control(
			'link_description',
			[
				'label'       => __('Descrição', 'upsites-addons'),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => '',
				'description' => __('Opcional. Usado no estilo "Colunas com cards".', 'upsites-addons'),
			]
		);

		$link_repeater->add_control(
			'link_url',
			[
				'label'       => __('Link', 'upsites-addons'),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'default'     => ['url' => '#'],
			]
		);

		$link_repeater->add_control(
			'is_highlighted',
			[
				'label'        => __('Destacar item', 'upsites-addons'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Sim', 'upsites-addons'),
				'label_off'    => __('Não', 'upsites-addons'),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'menu_links',
			[
				'label'       => __('Links', 'upsites-addons'),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $link_repeater->get_controls(),
				'title_field' => '#{{{ link_column_index }}} — {{{ link_label }}}',
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Barra desktop ──────────────────────────────────
		$this->start_controls_section(
			'section_style_bar',
			[
				'label' => __('Barra (desktop)', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'bar_bg_color',
			[
				'label'     => __('Cor de fundo', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .upsites-mega-nav__desktop' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_color',
			[
				'label'     => __('Cor do item', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#262626',
				'selectors' => [
					'{{WRAPPER}} .upsites-mega-nav__top-link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'item_typography',
				'label'    => __('Tipografia do item', 'upsites-addons'),
				'selector' => '{{WRAPPER}} .upsites-mega-nav__top-link',
			]
		);

		$this->add_responsive_control(
			'item_active_color',
			[
				'label'       => __('Cor do item + seta (mega menu aberto)', 'upsites-addons'),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#e5b132',
				'separator'   => 'before',
				'description' => __('Aplicada ao texto e à seta do item de topo enquanto o painel dele está aberto.', 'upsites-addons'),
				'selectors'   => [
					'{{WRAPPER}} .upsites-mega-nav__top-item.is-open > .upsites-mega-nav__top-link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'bar_gap',
			[
				'label'      => __('Espaço entre itens', 'upsites-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => ['px' => ['min' => 0, 'max' => 80]],
				'default'    => ['unit' => 'px', 'size' => 24],
				'selectors'  => [
					'{{WRAPPER}} .upsites-mega-nav__top-list' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Painel do mega menu ─────────────────────────────
		$this->start_controls_section(
			'section_style_panel',
			[
				'label' => __('Painel do mega menu', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'panel_max_width',
			[
				'label'      => __('Largura máxima', 'upsites-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => ['px' => ['min' => 400, 'max' => 1920]],
				'default'    => ['unit' => 'px', 'size' => 1354],
				'selectors'  => [
					'{{WRAPPER}} .upsites-mega-nav__panel' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'panel_bg_color',
			[
				'label'     => __('Cor de fundo', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .upsites-mega-nav__panel' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'panel_column_gap',
			[
				'label'      => __('Espaço entre colunas', 'upsites-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => ['px' => ['min' => 0, 'max' => 100]],
				'default'    => ['unit' => 'px', 'size' => 50],
				'selectors'  => [
					'{{WRAPPER}} .upsites-mega-nav__columns' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab_title_color',
			[
				'label'     => __('Cor do título da aba', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#262626',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .upsites-mega-nav__tab-title, {{WRAPPER}} .upsites-mega-nav__mobile-group-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tab_title_typography',
				'label'    => __('Tipografia — Título da aba', 'upsites-addons'),
				'selector' => '{{WRAPPER}} .upsites-mega-nav__tab-title, {{WRAPPER}} .upsites-mega-nav__mobile-group-label',
			]
		);

		$this->add_responsive_control(
			'tab_desc_color',
			[
				'label'       => __('Cor da descrição da aba', 'upsites-addons'),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#262626',
				'separator'   => 'before',
				'description' => __('Descrição opcional exibida abaixo do título de cada aba (ex: "Transforme visitantes em hóspedes").', 'upsites-addons'),
				'selectors'   => [
					'{{WRAPPER}} .upsites-mega-nav__tab-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tab_desc_typography',
				'label'    => __('Tipografia — Descrição da aba', 'upsites-addons'),
				'selector' => '{{WRAPPER}} .upsites-mega-nav__tab-desc',
			]
		);

		$this->add_responsive_control(
			'column_title_color',
			[
				'label'     => __('Cor do título da coluna', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#a5a4a6',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .upsites-mega-nav__column-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'column_title_typography',
				'label'    => __('Tipografia — Título da coluna', 'upsites-addons'),
				'selector' => '{{WRAPPER}} .upsites-mega-nav__column-title, {{WRAPPER}} .upsites-mega-nav__accordion-trigger',
			]
		);

		$this->add_responsive_control(
			'link_label_color',
			[
				'label'     => __('Cor do item', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#262626',
				'selectors' => [
					'{{WRAPPER}} .upsites-mega-nav__link-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'link_label_typography',
				'label'    => __('Tipografia — Itens/links', 'upsites-addons'),
				'selector' => '{{WRAPPER}} .upsites-mega-nav__link-label',
			]
		);

		$this->add_responsive_control(
			'link_label_hover_color',
			[
				'label'     => __('Cor do item — hover', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e5b132',
				'selectors' => [
					'{{WRAPPER}} .upsites-mega-nav__link:hover .upsites-mega-nav__link-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'link_desc_color',
			[
				'label'       => __('Cor da descrição', 'upsites-addons'),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#262626',
				'separator'   => 'before',
				'description' => __('Usada no estilo "Colunas com cards" (ex: Parceiros), onde cada item tem uma descrição abaixo do título.', 'upsites-addons'),
				'selectors'   => [
					'{{WRAPPER}} .upsites-mega-nav__link-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'link_desc_typography',
				'label'    => __('Tipografia — Descrição', 'upsites-addons'),
				'selector' => '{{WRAPPER}} .upsites-mega-nav__link-desc',
			]
		);

		$this->add_responsive_control(
			'panel_footer_color',
			[
				'label'     => __('Cor do link do rodapé', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f62fe',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .upsites-mega-nav__panel-footer-link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'panel_footer_hover_color',
			[
				'label'     => __('Cor do link do rodapé — hover', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a4fcc',
				'selectors' => [
					'{{WRAPPER}} .upsites-mega-nav__panel-footer-link:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'panel_footer_typography',
				'label'    => __('Tipografia — Link do rodapé', 'upsites-addons'),
				'selector' => '{{WRAPPER}} .upsites-mega-nav__panel-footer-link',
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Card promocional (Soluções) ──────────────────────
		$this->start_controls_section(
			'section_style_promo',
			[
				'label' => __('Card promocional (ex: Soluções)', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'promo_title_color',
			[
				'label'     => __('Cor do título', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#262626',
				'selectors' => [
					'{{WRAPPER}} .upsites-mega-nav__promo-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'promo_title_typography',
				'label'    => __('Tipografia — Título', 'upsites-addons'),
				'selector' => '{{WRAPPER}} .upsites-mega-nav__promo-title',
			]
		);

		$this->add_responsive_control(
			'promo_text_color',
			[
				'label'     => __('Cor do texto', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#262626',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .upsites-mega-nav__promo-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'promo_text_typography',
				'label'    => __('Tipografia — Texto', 'upsites-addons'),
				'selector' => '{{WRAPPER}} .upsites-mega-nav__promo-text',
			]
		);

		$this->add_responsive_control(
			'promo_link_color',
			[
				'label'     => __('Cor do link', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f62fe',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .upsites-mega-nav__promo-link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'promo_link_hover_color',
			[
				'label'     => __('Cor do link — hover', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0a4fcc',
				'selectors' => [
					'{{WRAPPER}} .upsites-mega-nav__promo-link:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'promo_link_typography',
				'label'    => __('Tipografia — Link', 'upsites-addons'),
				'selector' => '{{WRAPPER}} .upsites-mega-nav__promo-link',
			]
		);

		$this->end_controls_section();
	}
}
