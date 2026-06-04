<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

trait UpSites_Accordion_Slider_Controls {

	protected function register_controls() {

		// ── Content Tab ──────────────────────────────────────────────
		$this->start_controls_section(
			'section_slides',
			[
				'label' => __( 'Slides', 'upsites-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		// ── Imagem ───────────────────────────────────────────────────
		$repeater->add_control(
			'slide_bg',
			[
				'label'   => __( 'Imagem de Fundo', 'upsites-addons' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [ 'url' => '' ],
			]
		);

		$repeater->add_control(
			'slide_bg_pos_x',
			[
				'label'      => __( 'Posição X — desktop', 'upsites-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'%'  => [ 'min' => 0, 'max' => 100 ],
					'px' => [ 'min' => -1000, 'max' => 1000 ],
				],
				'default'    => [ 'unit' => '%', 'size' => 50 ],
			]
		);

		$repeater->add_control(
			'slide_bg_pos_y',
			[
				'label'      => __( 'Posição Y — desktop', 'upsites-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'%'  => [ 'min' => 0, 'max' => 100 ],
					'px' => [ 'min' => -1000, 'max' => 1000 ],
				],
				'default'    => [ 'unit' => '%', 'size' => 50 ],
			]
		);

		$repeater->add_control(
			'slide_bg_pos_x_mobile',
			[
				'label'      => __( 'Posição X — mobile', 'upsites-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'%'  => [ 'min' => 0, 'max' => 100 ],
					'px' => [ 'min' => -1000, 'max' => 1000 ],
				],
				'default'    => [ 'unit' => '%', 'size' => 50 ],
				'separator'  => 'before',
			]
		);

		$repeater->add_control(
			'slide_bg_pos_y_mobile',
			[
				'label'      => __( 'Posição Y — mobile', 'upsites-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'%'  => [ 'min' => 0, 'max' => 100 ],
					'px' => [ 'min' => -1000, 'max' => 1000 ],
				],
				'default'    => [ 'unit' => '%', 'size' => 50 ],
			]
		);

		// ── Conteúdo ─────────────────────────────────────────────────
		$repeater->add_control(
			'slide_title',
			[
				'label'       => __( 'Título', 'upsites-addons' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'Título do slide', 'upsites-addons' ),
				'separator'   => 'before',
			]
		);

		$repeater->add_control(
			'slide_description',
			[
				'label'   => __( 'Descrição', 'upsites-addons' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Descrição do slide aqui.', 'upsites-addons' ),
			]
		);

		// ── Logo ─────────────────────────────────────────────────────
		$repeater->add_control(
			'slide_logo',
			[
				'label'     => __( 'Logo / Ícone', 'upsites-addons' ),
				'type'      => Controls_Manager::MEDIA,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'slide_logo_alt',
			[
				'label'     => __( 'Alt da Logo', 'upsites-addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => [ 'slide_logo[url]!' => '' ],
			]
		);

		$repeater->add_control(
			'slide_logo_width',
			[
				'label'      => __( 'Largura da logo', 'upsites-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 20, 'max' => 300 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 160 ],
				'condition'  => [ 'slide_logo[url]!' => '' ],
			]
		);

		$repeater->add_control(
			'slide_logo_width_mobile',
			[
				'label'      => __( 'Largura da logo (mobile)', 'upsites-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 20, 'max' => 200 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 80 ],
				'condition'  => [ 'slide_logo[url]!' => '' ],
			]
		);

		// ── Link ─────────────────────────────────────────────────────
		$repeater->add_control(
			'slide_link',
			[
				'label'         => __( 'Link do card', 'upsites-addons' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://',
				'show_external' => true,
				'separator'     => 'before',
			]
		);

		// ── Overlay ──────────────────────────────────────────────────
		$repeater->add_control(
			'overlay_color',
			[
				'label'     => __( 'Cor do Gradiente (ativo)', 'upsites-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(77,45,120,0.7)',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'slides',
			[
				'label'       => __( 'Slides', 'upsites-addons' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'slide_title'       => 'Reservas diretas triplicaram em apenas 3 meses.',
						'slide_description' => 'O Hotel Atlântico implementou o motor de reservas e passou de 12% para 38% de reservas diretas, com foco em estratégia tarifária e integração com o website.',
					],
					[
						'slide_title'       => 'Segundo slide de exemplo.',
						'slide_description' => 'Descrição do segundo slide aqui.',
					],
					[
						'slide_title'       => 'Terceiro slide de exemplo.',
						'slide_description' => 'Descrição do terceiro slide aqui.',
					],
				],
				'title_field' => '{{{ slide_title }}}',
			]
		);

		$this->add_control(
			'default_active',
			[
				'label'     => __( 'Item aberto por padrão', 'upsites-addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '0',
				'options'   => [
					'0' => __( 'Slide 1', 'upsites-addons' ),
					'1' => __( 'Slide 2', 'upsites-addons' ),
					'2' => __( 'Slide 3', 'upsites-addons' ),
					'3' => __( 'Slide 4', 'upsites-addons' ),
					'4' => __( 'Slide 5', 'upsites-addons' ),
					'5' => __( 'Slide 6', 'upsites-addons' ),
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Layout ────────────────────────────────────────
		$this->start_controls_section(
			'section_style_layout',
			[
				'label' => __( 'Layout', 'upsites-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'slider_height',
			[
				'label'      => __( 'Altura', 'upsites-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [ 'min' => 200, 'max' => 900, 'step' => 10 ],
					'vh' => [ 'min' => 20,  'max' => 100 ],
				],
				'default'    => [ 'unit' => 'px', 'size' => 460 ],
				'selectors'  => [
					'{{WRAPPER}} .upsites-accordion-slider' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .upsites-accordion-slide'  => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'slide_border_radius',
			[
				'label'      => __( 'Border Radius', 'upsites-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 30 ],
				'selectors'  => [
					'{{WRAPPER}} .upsites-accordion-slide' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'slide_gap',
			[
				'label'      => __( 'Espaço entre slides', 'upsites-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 20 ],
				'selectors'  => [
					'{{WRAPPER}} .upsites-accordion-slider' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'inactive_grayscale',
			[
				'label'        => __( 'Cinza nos slides inativos', 'upsites-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Sim', 'upsites-addons' ),
				'label_off'    => __( 'Não', 'upsites-addons' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'inactive_grayscale_color',
			[
				'label'       => __( 'Cor do filtro', 'upsites-addons' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#656565',
				'condition'   => [ 'inactive_grayscale' => 'yes' ],
				'description' => __( 'Cor usada como referência de cinza (afeta a intensidade do filtro)', 'upsites-addons' ),
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Tipografia ─────────────────────────────────────
		$this->start_controls_section(
			'section_style_typography',
			[
				'label' => __( 'Tipografia', 'upsites-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Cor do Título', 'upsites-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .upsites-accordion-slide__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => __( 'Tipografia do Título', 'upsites-addons' ),
				'selector' => '{{WRAPPER}} .upsites-accordion-slide__title',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => __( 'Cor da Descrição', 'upsites-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .upsites-accordion-slide__description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'label'    => __( 'Tipografia da Descrição', 'upsites-addons' ),
				'selector' => '{{WRAPPER}} .upsites-accordion-slide__description',
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Overlay ───────────────────────────────────────
		$this->start_controls_section(
			'section_style_overlay',
			[
				'label' => __( 'Overlay', 'upsites-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'overlay_inactive_start',
			[
				'label'   => __( 'Gradiente Inativo (início)', 'upsites-addons' ),
				'type'    => Controls_Manager::COLOR,
				'default' => 'rgba(77,45,120,0)',
			]
		);

		$this->add_control(
			'overlay_inactive_end',
			[
				'label'   => __( 'Gradiente Inativo (fim)', 'upsites-addons' ),
				'type'    => Controls_Manager::COLOR,
				'default' => 'rgba(77,45,120,0.5)',
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Logo ──────────────────────────────────────────
		$this->start_controls_section(
			'section_style_logo',
			[
				'label' => __( 'Logo (global)', 'upsites-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'logo_opacity',
			[
				'label'     => __( 'Opacidade da logo', 'upsites-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
				'default'   => [ 'size' => 0.75 ],
				'selectors' => [
					'{{WRAPPER}} .upsites-accordion-slide__logo img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_section();
	}
}
