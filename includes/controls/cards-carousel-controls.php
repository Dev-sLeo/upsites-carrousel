<?php
if (! defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

trait UpSites_Cards_Carousel_Controls
{

	protected function register_controls()
	{

		// ── Content Tab — Cards ───────────────────────────────────────
		$this->start_controls_section(
			'section_cards',
			[
				'label' => __('Cards', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'content_source',
			[
				'label'   => __('Origem do conteúdo', 'upsites-addons'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => [
					'manual' => __('Manual', 'upsites-addons'),
					'query'  => __('Dinâmico (CPT / Crocoblock)', 'upsites-addons'),
				],
			]
		);

		$post_type_options = [];
		foreach (get_post_types(['public' => true], 'objects') as $pt) {
			$post_type_options[$pt->name] = $pt->label;
		}

		$query_scope_conditions = [
			'relation' => 'and',
			'terms'    => [
				['name' => 'content_source', 'operator' => '===', 'value' => 'query'],
				[
					'relation' => 'or',
					'terms'    => [
						['name' => 'query_card_source', 'operator' => '!==', 'value' => 'repeater'],
						['name' => 'query_repeater_scope', 'operator' => '!==', 'value' => 'current_post'],
					],
				],
			],
		];

		$this->add_control(
			'query_post_type',
			[
				'label'      => __('Post Type', 'upsites-addons'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'post',
				'options'    => $post_type_options,
				'conditions' => $query_scope_conditions,
			]
		);

		$this->add_control(
			'query_posts_per_page',
			[
				'label'      => __('Quantidade de cards', 'upsites-addons'),
				'type'       => Controls_Manager::NUMBER,
				'default'    => 5,
				'min'        => 1,
				'max'        => 50,
				'conditions' => $query_scope_conditions,
			]
		);

		$this->add_control(
			'query_orderby',
			[
				'label'      => __('Ordenar por', 'upsites-addons'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'date',
				'options'    => [
					'date'  => __('Data', 'upsites-addons'),
					'title' => __('Título', 'upsites-addons'),
					'menu_order' => __('Ordem do menu', 'upsites-addons'),
					'rand'  => __('Aleatório', 'upsites-addons'),
				],
				'conditions' => $query_scope_conditions,
			]
		);

		$this->add_control(
			'query_order',
			[
				'label'      => __('Direção', 'upsites-addons'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'DESC',
				'options'    => [
					'DESC' => __('Decrescente', 'upsites-addons'),
					'ASC'  => __('Crescente', 'upsites-addons'),
				],
				'conditions' => $query_scope_conditions,
			]
		);

		$this->add_control(
			'query_desc_source',
			[
				'label'       => __('Campo da descrição', 'upsites-addons'),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'excerpt',
				'options'     => [
					'excerpt'  => __('Resumo (excerpt)', 'upsites-addons'),
					'meta'     => __('Campo personalizado (meta) — ex: Crocoblock/JetEngine', 'upsites-addons'),
				],
				'condition'   => ['content_source' => 'query', 'query_card_source' => 'flat'],
			]
		);

		$this->add_control(
			'query_desc_meta_key',
			[
				'label'       => __('Meta key da descrição', 'upsites-addons'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => __('Nome do campo personalizado (ex: criado no JetEngine).', 'upsites-addons'),
				'condition'   => ['content_source' => 'query', 'query_card_source' => 'flat', 'query_desc_source' => 'meta'],
			]
		);

		$this->add_control(
			'query_icon_meta_key',
			[
				'label'       => __('Meta key do ícone (imagem)', 'upsites-addons'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => __('Opcional. Meta key de um campo de imagem (ID ou URL) usado como ícone.', 'upsites-addons'),
				'condition'   => ['content_source' => 'query', 'query_card_source' => 'flat'],
			]
		);

		$this->add_control(
			'query_image_source',
			[
				'label'     => __('Campo da imagem', 'upsites-addons'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'featured',
				'options'   => [
					'featured' => __('Imagem destacada', 'upsites-addons'),
					'meta'     => __('Campo personalizado (meta)', 'upsites-addons'),
				],
				'condition' => ['content_source' => 'query', 'query_card_source' => 'flat'],
			]
		);

		$this->add_control(
			'query_image_meta_key',
			[
				'label'       => __('Meta key da imagem', 'upsites-addons'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => __('Meta key de um campo de imagem (ID ou URL), ex: criado no JetEngine/Crocoblock.', 'upsites-addons'),
				'condition'   => ['content_source' => 'query', 'query_card_source' => 'flat', 'query_image_source' => 'meta'],
			]
		);

		// ── Fonte dos cards: campos simples ou repeater do JetEngine ──
		$this->add_control(
			'query_card_source',
			[
				'label'     => __('Origem dos cards no CPT', 'upsites-addons'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'flat',
				'options'   => [
					'flat'     => __('Campos individuais (padrão)', 'upsites-addons'),
					'repeater' => __('Repeater do JetEngine / Crocoblock', 'upsites-addons'),
				],
				'separator' => 'before',
				'condition' => ['content_source' => 'query'],
			]
		);

		$this->add_control(
			'query_repeater_scope',
			[
				'label'       => __('Onde está o repeater?', 'upsites-addons'),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'current_post',
				'options'     => [
					'current_post' => __('Na página/post atual (onde o widget está)', 'upsites-addons'),
					'other_cpt'    => __('Buscar de outros posts do CPT', 'upsites-addons'),
				],
				'description' => __('"Na página/post atual" lê o repeater diretamente do post onde o widget foi colocado, sem buscar outros posts. "Buscar de outros posts do CPT" consulta o Post Type selecionado abaixo.', 'upsites-addons'),
				'condition'   => ['content_source' => 'query', 'query_card_source' => 'repeater'],
			]
		);

		$this->add_control(
			'query_repeater_meta_key',
			[
				'label'       => __('Meta key do repeater', 'upsites-addons'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => __('Nome do campo repeater no JetEngine (ex: card_repeater). Cada post pode gerar vários cards.', 'upsites-addons'),
				'condition'   => ['content_source' => 'query', 'query_card_source' => 'repeater'],
			]
		);

		$this->add_control(
			'query_repeater_title_key',
			[
				'label'       => __('Sub-campo: Título', 'upsites-addons'),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'titulo',
				'description' => __('Nome do sub-campo de título dentro de cada row do repeater.', 'upsites-addons'),
				'condition'   => ['content_source' => 'query', 'query_card_source' => 'repeater'],
			]
		);

		$this->add_control(
			'query_repeater_desc_key',
			[
				'label'       => __('Sub-campo: Descrição', 'upsites-addons'),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'descricao',
				'description' => __('Nome do sub-campo de descrição dentro de cada row do repeater.', 'upsites-addons'),
				'condition'   => ['content_source' => 'query', 'query_card_source' => 'repeater'],
			]
		);

		$this->add_control(
			'query_repeater_image_key',
			[
				'label'       => __('Sub-campo: Imagem', 'upsites-addons'),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'imagem',
				'description' => __('Nome do sub-campo de imagem (ID ou URL) dentro de cada row do repeater.', 'upsites-addons'),
				'condition'   => ['content_source' => 'query', 'query_card_source' => 'repeater'],
			]
		);

		$this->add_control(
			'query_repeater_icon_key',
			[
				'label'       => __('Sub-campo: Ícone', 'upsites-addons'),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'icone',
				'description' => __('Nome do sub-campo de ícone (ID ou URL) dentro de cada row do repeater. Deixe vazio para ignorar.', 'upsites-addons'),
				'condition'   => ['content_source' => 'query', 'query_card_source' => 'repeater'],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'card_icon',
			[
				'label'   => __('Ícone', 'upsites-addons'),
				'type'    => Controls_Manager::MEDIA,
				'default' => ['url' => ''],
			]
		);

		$repeater->add_control(
			'card_icon_alt',
			[
				'label'     => __('Alt do Ícone', 'upsites-addons'),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => ['card_icon[url]!' => ''],
			]
		);

		$repeater->add_control(
			'card_title',
			[
				'label'       => __('Título', 'upsites-addons'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __('Título do card', 'upsites-addons'),
				'separator'   => 'before',
			]
		);

		$repeater->add_control(
			'card_description',
			[
				'label'   => __('Descrição', 'upsites-addons'),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __('Descrição do card aqui.', 'upsites-addons'),
				'rows'    => 4,
			]
		);

		$repeater->add_control(
			'card_image',
			[
				'label'     => __('Imagem', 'upsites-addons'),
				'type'      => Controls_Manager::MEDIA,
				'default'   => ['url' => ''],
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'card_image_alt',
			[
				'label'     => __('Alt da Imagem', 'upsites-addons'),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => ['card_image[url]!' => ''],
			]
		);

		$this->add_control(
			'cards',
			[
				'label'       => __('Cards', 'upsites-addons'),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'card_title'       => __('Esteja nos canais certos e aumente sua rentabilidade', 'upsites-addons'),
						'card_description' => __('Conecte-se a +750 canais e gerencie tarifas, disponibilidade e inventário deles em uma única plataforma!', 'upsites-addons'),
					],
					[
						'card_title'       => __('Segundo card de exemplo', 'upsites-addons'),
						'card_description' => __('Descrição do segundo card aqui.', 'upsites-addons'),
					],
					[
						'card_title'       => __('Terceiro card de exemplo', 'upsites-addons'),
						'card_description' => __('Descrição do terceiro card aqui.', 'upsites-addons'),
					],
				],
				'title_field' => '{{{ card_title }}}',
				'condition'   => ['content_source' => 'manual'],
			]
		);

		$this->add_control(
			'scroll_top_offset',
			[
				'label'       => __('Offset do topo — Desktop (px)', 'upsites-addons'),
				'description' => __('Compensar header sticky. O scroll inicia X px abaixo do topo da viewport.', 'upsites-addons'),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => ['px'],
				'range'       => ['px' => ['min' => 0, 'max' => 300]],
				'default'     => ['unit' => 'px', 'size' => 0],
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'scroll_top_offset_tablet',
			[
				'label'      => __('Offset do topo — Tablet (px)', 'upsites-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => ['px' => ['min' => 0, 'max' => 300]],
				'default'    => ['unit' => 'px', 'size' => 0],
			]
		);

		$this->add_control(
			'scroll_top_offset_mobile',
			[
				'label'      => __('Offset do topo — Mobile (px)', 'upsites-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => ['px' => ['min' => 0, 'max' => 300]],
				'default'    => ['unit' => 'px', 'size' => 0],
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Card ─────────────────────────────────────────
		$this->start_controls_section(
			'section_style_card',
			[
				'label' => __('Card', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'card_height',
			[
				'label'          => __('Altura do card', 'upsites-addons'),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => ['px'],
				'range'          => ['px' => ['min' => 300, 'max' => 1000, 'step' => 10]],
				'default'        => ['unit' => 'px', 'size' => 580],
				'tablet_default' => ['unit' => 'px', 'size' => 402],
				'mobile_default' => ['unit' => 'px', 'size' => 602],
				'selectors'      => [
					'{{WRAPPER}} .upsites-cc-card' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_border_radius',
			[
				'label'          => __('Border Radius', 'upsites-addons'),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => ['px'],
				'range'          => ['px' => ['min' => 0, 'max' => 60]],
				'default'        => ['unit' => 'px', 'size' => 30],
				'tablet_default' => ['unit' => 'px', 'size' => 30],
				'mobile_default' => ['unit' => 'px', 'size' => 30],
				'selectors'      => [
					'{{WRAPPER}} .upsites-cc-card' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_bg_type',
			[
				'label'     => __('Tipo de fundo', 'upsites-addons'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'gradient',
				'options'   => [
					'gradient' => __('Gradiente', 'upsites-addons'),
					'solid'    => __('Cor sólida', 'upsites-addons'),
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'card_bg_color',
			[
				'label'     => __('Cor', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8E52DE',
				'condition' => ['card_bg_type' => 'solid'],
				'selectors' => [
					'{{WRAPPER}} .upsites-cc-card' => '--cc-bg-solid: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'card_bg_start',
			[
				'label'     => __('Gradiente (início)', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8E52DE',
				'condition' => ['card_bg_type' => 'gradient'],
				'selectors' => [
					'{{WRAPPER}} .upsites-cc-card' => '--cc-bg-start: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'card_bg_end',
			[
				'label'     => __('Gradiente (fim)', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4E2E79',
				'condition' => ['card_bg_type' => 'gradient'],
				'selectors' => [
					'{{WRAPPER}} .upsites-cc-card' => '--cc-bg-end: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'card_bg_angle',
			[
				'label'     => __('Ângulo do gradiente', 'upsites-addons'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => ['px' => ['min' => 0, 'max' => 360]],
				'default'   => ['size' => 114],
				'condition' => ['card_bg_type' => 'gradient'],
				'selectors' => [
					'{{WRAPPER}} .upsites-cc-card' => '--cc-bg-angle: {{SIZE}}deg;',
				],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'          => __('Padding interno', 'upsites-addons'),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => ['px'],
				'default'        => ['top' => '52', 'right' => '52', 'bottom' => '52', 'left' => '52', 'unit' => 'px'],
				'tablet_default' => ['top' => '32', 'right' => '32', 'bottom' => '32', 'left' => '32', 'unit' => 'px'],
				'mobile_default' => ['top' => '20', 'right' => '20', 'bottom' => '0',  'left' => '20', 'unit' => 'px'],
				'selectors'      => [
					'{{WRAPPER}} .upsites-cc-card__left' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'      => 'before',
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'          => __('Espaço entre colunas', 'upsites-addons'),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => ['px'],
				'range'          => ['px' => ['min' => 0, 'max' => 120]],
				'default'        => ['unit' => 'px', 'size' => 0],
				'tablet_default' => ['unit' => 'px', 'size' => 0],
				'mobile_default' => ['unit' => 'px', 'size' => 31],
				'selectors'      => [
					'{{WRAPPER}} .upsites-cc-card__inner' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'left_column_width',
			[
				'label'          => __('Largura da coluna de texto', 'upsites-addons'),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => ['%', 'px'],
				'range'          => [
					'%'  => ['min' => 20, 'max' => 80],
					'px' => ['min' => 100, 'max' => 800],
				],
				'default'        => ['unit' => '%', 'size' => 46],
				'tablet_default' => ['unit' => '%', 'size' => 46],
				'mobile_default' => ['unit' => '%', 'size' => 100],
				'selectors'      => [
					'{{WRAPPER}} .upsites-cc-card__left' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Ícone ─────────────────────────────────────────
		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => __('Ícone', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_container_size',
			[
				'label'          => __('Tamanho do container', 'upsites-addons'),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => ['px'],
				'range'          => ['px' => ['min' => 32, 'max' => 120]],
				'default'        => ['unit' => 'px', 'size' => 72],
				'tablet_default' => ['unit' => 'px', 'size' => 56],
				'mobile_default' => ['unit' => 'px', 'size' => 48],
				'selectors'      => [
					'{{WRAPPER}} .upsites-cc-card__icon-wrap' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'          => __('Tamanho do ícone', 'upsites-addons'),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => ['px'],
				'range'          => ['px' => ['min' => 12, 'max' => 80]],
				'default'        => ['unit' => 'px', 'size' => 32],
				'tablet_default' => ['unit' => 'px', 'size' => 24],
				'mobile_default' => ['unit' => 'px', 'size' => 21],
				'selectors'      => [
					'{{WRAPPER}} .upsites-cc-card__icon-wrap img'         => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .upsites-cc-card__icon-wrap--empty svg'  => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_container_color',
			[
				'label'     => __('Cor do container', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.1)',
				'selectors' => [
					'{{WRAPPER}} .upsites-cc-card__icon-wrap' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_border_radius',
			[
				'label'          => __('Border Radius', 'upsites-addons'),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => ['px'],
				'range'          => ['px' => ['min' => 0, 'max' => 40]],
				'default'        => ['unit' => 'px', 'size' => 10],
				'tablet_default' => ['unit' => 'px', 'size' => 10],
				'mobile_default' => ['unit' => 'px', 'size' => 10],
				'selectors'      => [
					'{{WRAPPER}} .upsites-cc-card__icon-wrap' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Tipografia ────────────────────────────────────
		$this->start_controls_section(
			'section_style_typography',
			[
				'label' => __('Tipografia', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __('Cor do Título', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .upsites-cc-card__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => __('Tipografia do Título', 'upsites-addons'),
				'selector' => '{{WRAPPER}} .upsites-cc-card__title',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => __('Cor da Descrição', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .upsites-cc-card__desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'label'    => __('Tipografia da Descrição', 'upsites-addons'),
				'selector' => '{{WRAPPER}} .upsites-cc-card__desc',
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Imagem ────────────────────────────────────────
		$this->start_controls_section(
			'section_style_image',
			[
				'label' => __('Imagem', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'image_area_bg',
			[
				'label'     => __('Cor de fundo da área', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.1)',
				'selectors' => [
					'{{WRAPPER}} .upsites-cc-card__image-wrap' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'          => __('Border Radius da imagem', 'upsites-addons'),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => ['px'],
				'range'          => ['px' => ['min' => 0, 'max' => 60]],
				'default'        => ['unit' => 'px', 'size' => 25],
				'tablet_default' => ['unit' => 'px', 'size' => 25],
				'mobile_default' => ['unit' => 'px', 'size' => 25],
				'selectors'      => [
					'{{WRAPPER}} .upsites-cc-card__image-wrap' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_margin',
			[
				'label'          => __('Padding da área da imagem', 'upsites-addons'),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => ['px'],
				'default'        => ['top' => '52', 'right' => '52', 'bottom' => '52', 'left' => '0',  'unit' => 'px'],
				'tablet_default' => ['top' => '32', 'right' => '32', 'bottom' => '32', 'left' => '0',  'unit' => 'px'],
				'mobile_default' => ['top' => '0',  'right' => '20', 'bottom' => '20', 'left' => '20', 'unit' => 'px'],
				'selectors'      => [
					'{{WRAPPER}} .upsites-cc-card__right' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}
}
