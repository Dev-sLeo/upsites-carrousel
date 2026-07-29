<?php
if (! defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

trait UpSites_Carousel_Controls
{

	protected function register_controls()
	{

		// ── Content Tab — Slides ────────────────────────────────────────
		$this->start_controls_section('section_slides', [
			'label' => __('Slides', 'upsites-addons'),
			'tab'   => Controls_Manager::TAB_CONTENT,
		]);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label'   => __('Imagem', 'upsites-addons'),
				'type'    => Controls_Manager::MEDIA,
				'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => __('Título', 'upsites-addons'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __('Título do slide', 'upsites-addons'),
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->add_control(
			'description',
			[
				'label'       => __('Descrição', 'upsites-addons'),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'default'     => __('Descrição breve do slide.', 'upsites-addons'),
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->add_control(
			'show_button',
			[
				'label'        => __('Mostrar botão', 'upsites-addons'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Sim', 'upsites-addons'),
				'label_off'    => __('Não', 'upsites-addons'),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label'       => __('Texto do botão', 'upsites-addons'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __('Saiba mais', 'upsites-addons'),
				'dynamic'     => ['active' => true],
				'condition'   => ['show_button' => 'yes'],
			]
		);

		$repeater->add_control(
			'button_link',
			[
				'label'       => __('Link do botão', 'upsites-addons'),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'default'     => ['url' => '#'],
				'dynamic'     => ['active' => true],
				'condition'   => ['show_button' => 'yes'],
			]
		);

		$this->add_control(
			'slides',
			[
				'label'       => __('Slides', 'upsites-addons'),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					['title' => __('Slide 1', 'upsites-addons')],
					['title' => __('Slide 2', 'upsites-addons')],
					['title' => __('Slide 3', 'upsites-addons')],
					['title' => __('Slide 4', 'upsites-addons')],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		// ── Content Tab — Layout ────────────────────────────────────────
		$this->start_controls_section('section_layout', [
			'label' => __('Layout', 'upsites-addons'),
			'tab'   => Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control(
			'card_style',
			[
				'label'   => __('Estilo do card', 'upsites-addons'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => __('Padrão — imagem, título e texto', 'upsites-addons'),
					'overlay'  => __('Overlay — texto sobre a imagem', 'upsites-addons'),
					'minimal'  => __('Minimalista — só título e botão', 'upsites-addons'),
				],
			]
		);

		$this->add_responsive_control(
			'slides_per_view',
			[
				'label'          => __('Slides visíveis', 'upsites-addons'),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 8,
				'step'           => 1,
				'default'        => 4,
				'tablet_default' => 2,
				'mobile_default' => 1,
			]
		);

		$this->add_responsive_control(
			'slides_gap',
			[
				'label'      => __('Espaçamento entre slides', 'upsites-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'range'      => [
					'px' => ['min' => 0, 'max' => 100],
					'em' => ['min' => 0, 'max' => 5],
				],
				'default'    => ['unit' => 'px', 'size' => 24],
			]
		);

		$this->add_responsive_control(
			'carousel_height',
			[
				'label'       => __('Altura do carrossel', 'upsites-addons'),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => ['px', 'vh'],
				'range'       => [
					'px' => ['min' => 100, 'max' => 900],
					'vh' => ['min' => 10, 'max' => 100],
				],
				'description' => __('Deixe em branco para a altura se ajustar automaticamente ao conteúdo. Ao definir um valor, a imagem preenche o espaço disponível cortando as bordas (cover).', 'upsites-addons'),
				'selectors'   => [
					'{{WRAPPER}} .upsites-carousel__card'      => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .upsites-carousel__media'     => 'flex: 1 1 auto; overflow: hidden;',
					'{{WRAPPER}} .upsites-carousel__media img' => 'height: 100%; object-fit: cover;',
				],
			]
		);

		$this->end_controls_section();

		// ── Content Tab — Comportamento do carrossel ────────────────────
		$this->start_controls_section('section_carousel_behavior', [
			'label' => __('Comportamento', 'upsites-addons'),
			'tab'   => Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control(
			'infinite_loop',
			[
				'label'        => __('Loop infinito', 'upsites-addons'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Sim', 'upsites-addons'),
				'label_off'    => __('Não', 'upsites-addons'),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __('Os slides se repetem continuamente, sem começo ou fim.', 'upsites-addons'),
			]
		);

		$this->add_control(
			'auto_scroll',
			[
				'label'        => __('Auto scroll infinito', 'upsites-addons'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Sim', 'upsites-addons'),
				'label_off'    => __('Não', 'upsites-addons'),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
				'description'  => __('Rolagem contínua e automática (estilo "marquee"), em vez de avançar slide a slide. Requer Loop infinito.', 'upsites-addons'),
			]
		);

		$this->add_control(
			'auto_scroll_speed',
			[
				'label'       => __('Velocidade do auto scroll', 'upsites-addons'),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 20,
				'step'        => 1,
				'default'     => 1,
				'description' => __('Pixels por frame. Quanto maior, mais rápido.', 'upsites-addons'),
				'condition'   => ['auto_scroll' => 'yes'],
			]
		);

		$this->add_control(
			'auto_scroll_direction',
			[
				'label'     => __('Direção do auto scroll', 'upsites-addons'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'forward',
				'options'   => [
					'forward'  => __('Para frente', 'upsites-addons'),
					'backward' => __('Para trás', 'upsites-addons'),
				],
				'condition' => ['auto_scroll' => 'yes'],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'        => __('Pausar ao passar o mouse', 'upsites-addons'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Sim', 'upsites-addons'),
				'label_off'    => __('Não', 'upsites-addons'),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => ['auto_scroll' => 'yes'],
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => __('Autoplay (slide a slide)', 'upsites-addons'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Sim', 'upsites-addons'),
				'label_off'    => __('Não', 'upsites-addons'),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
				'condition'    => ['auto_scroll!' => 'yes'],
				'description'  => __('Avança automaticamente para o próximo slide em intervalos regulares.', 'upsites-addons'),
			]
		);

		$this->add_control(
			'autoplay_interval',
			[
				'label'     => __('Intervalo do autoplay (ms)', 'upsites-addons'),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1000,
				'max'       => 15000,
				'step'      => 500,
				'default'   => 3000,
				'condition' => ['autoplay' => 'yes', 'auto_scroll!' => 'yes'],
			]
		);

		$this->add_control(
			'transition_speed',
			[
				'label'     => __('Velocidade da transição (ms)', 'upsites-addons'),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 100,
				'max'       => 3000,
				'step'      => 50,
				'default'   => 400,
				'condition' => ['auto_scroll!' => 'yes'],
			]
		);

		$this->end_controls_section();

		// ── Content Tab — Navegação (arrows/bullets — vale para todos os estilos) ──
		$this->start_controls_section('section_navigation', [
			'label' => __('Navegação', 'upsites-addons'),
			'tab'   => Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control(
			'show_arrows',
			[
				'label'        => __('Mostrar setas', 'upsites-addons'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Sim', 'upsites-addons'),
				'label_off'    => __('Não', 'upsites-addons'),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'custom_arrow_icon',
			[
				'label'       => __('Ícone da seta', 'upsites-addons'),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
				'default'     => [
					'value'   => 'eicon-chevron-right',
					'library' => 'eicons',
				],
				'condition'   => ['show_arrows' => 'yes'],
				'description' => __('Troca a seta padrão do carrossel por outro ícone (a mesma seta é espelhada para "anterior").', 'upsites-addons'),
			]
		);

		$this->add_control(
			'show_bullets',
			[
				'label'        => __('Mostrar bullets', 'upsites-addons'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Sim', 'upsites-addons'),
				'label_off'    => __('Não', 'upsites-addons'),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Card ─────────────────────────────────────────────
		$this->start_controls_section('section_style_card', [
			'label' => __('Card', 'upsites-addons'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control(
			'card_background_color',
			[
				'label'     => __('Cor de fundo', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => ['{{WRAPPER}} .upsites-carousel__card' => 'background-color: {{VALUE}}'],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .upsites-carousel__card',
			]
		);

		$this->add_control(
			'card_border_radius',
			[
				'label'      => __('Arredondamento', 'upsites-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upsites-carousel__card' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_box_shadow',
				'selector' => '{{WRAPPER}} .upsites-carousel__card',
			]
		);

		$this->add_control(
			'card_padding',
			[
				'label'      => __('Espaçamento interno', 'upsites-addons'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .upsites-carousel__card-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Título ────────────────────────────────────────────
		$this->start_controls_section('section_style_title', [
			'label' => __('Título', 'upsites-addons'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control(
			'title_color',
			[
				'label'     => __('Cor', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => ['{{WRAPPER}} .upsites-carousel__title' => 'color: {{VALUE}}'],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .upsites-carousel__title',
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Descrição ─────────────────────────────────────────
		$this->start_controls_section('section_style_description', [
			'label' => __('Descrição', 'upsites-addons'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control(
			'description_color',
			[
				'label'     => __('Cor', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => ['{{WRAPPER}} .upsites-carousel__description' => 'color: {{VALUE}}'],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .upsites-carousel__description',
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Botão ─────────────────────────────────────────────
		$this->start_controls_section('section_style_button', [
			'label' => __('Botão', 'upsites-addons'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control(
			'button_text_color',
			[
				'label'     => __('Cor do texto', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => ['{{WRAPPER}} .upsites-carousel__button' => 'color: {{VALUE}}'],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label'     => __('Cor de fundo', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => ['{{WRAPPER}} .upsites-carousel__button' => 'background-color: {{VALUE}}'],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .upsites-carousel__button',
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Setas ─────────────────────────────────────────────
		$this->start_controls_section('section_style_arrows', [
			'label'     => __('Setas', 'upsites-addons'),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => ['show_arrows' => 'yes'],
		]);

		$this->add_control(
			'arrows_color',
			[
				'label'     => __('Cor', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .splide__arrow svg'                    => 'fill: {{VALUE}}',
					'{{WRAPPER}} .upsites-carousel__custom-arrow svg'   => 'fill: {{VALUE}}',
					'{{WRAPPER}} .upsites-carousel__custom-arrow i'     => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'arrows_background_color',
			[
				'label'     => __('Cor de fundo', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => ['{{WRAPPER}} .splide__arrow' => 'background-color: {{VALUE}}'],
			]
		);

		$this->add_responsive_control(
			'arrows_size',
			[
				'label'      => __('Tamanho', 'upsites-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => ['px' => ['min' => 20, 'max' => 80]],
				'selectors'  => [
					'{{WRAPPER}} .splide__arrow, {{WRAPPER}} .upsites-carousel__custom-arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Bullets ──────────────────────────────────────────
		$this->start_controls_section('section_style_bullets', [
			'label'     => __('Bullets', 'upsites-addons'),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => ['show_bullets' => 'yes'],
		]);

		$this->add_control(
			'bullets_color',
			[
				'label'     => __('Cor', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => ['{{WRAPPER}} .splide__pagination__page' => 'background-color: {{VALUE}}'],
			]
		);

		$this->add_control(
			'bullets_active_color',
			[
				'label'     => __('Cor ativa', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => ['{{WRAPPER}} .splide__pagination__page.is-active' => 'background-color: {{VALUE}}'],
			]
		);

		$this->add_responsive_control(
			'bullets_size',
			[
				'label'      => __('Tamanho', 'upsites-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => ['px' => ['min' => 4, 'max' => 30]],
				'default'    => ['unit' => 'px', 'size' => 8],
				'selectors'  => [
					'{{WRAPPER}} .splide__pagination__page' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'bullets_border_radius',
			[
				'label'      => __('Arredondamento', 'upsites-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => ['min' => 0, 'max' => 30],
					'%'  => ['min' => 0, 'max' => 50],
				],
				'default'    => ['unit' => '%', 'size' => 50],
				'selectors'  => [
					'{{WRAPPER}} .splide__pagination__page' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'bullets_gap',
			[
				'label'      => __('Espaçamento entre bullets', 'upsites-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => ['px' => ['min' => 0, 'max' => 30]],
				'default'    => ['unit' => 'px', 'size' => 6],
				'selectors'  => [
					'{{WRAPPER}} .splide__pagination__page' => 'margin: 0 calc({{SIZE}}{{UNIT}} / 2)',
				],
			]
		);

		$this->add_responsive_control(
			'bullets_spacing_top',
			[
				'label'      => __('Distância do carrossel', 'upsites-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => ['px' => ['min' => 0, 'max' => 60]],
				'default'    => ['unit' => 'px', 'size' => 16],
				'selectors'  => [
					'{{WRAPPER}} .splide__pagination' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}
}
