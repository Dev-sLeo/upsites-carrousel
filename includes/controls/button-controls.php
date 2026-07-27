<?php
if (! defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;

trait UpSites_Button_Controls
{

	protected function register_controls()
	{

		// ── Content Tab — Conteúdo ─────────────────────────────────────
		$this->start_controls_section(
			'section_content',
			[
				'label' => __('Conteúdo', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => __('Texto', 'upsites-addons'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __('Solicitar demonstração', 'upsites-addons'),
				'dynamic'     => ['active' => true],
			]
		);

		$this->add_control(
			'button_link',
			[
				'label'       => __('Link', 'upsites-addons'),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'default'     => ['url' => '#'],
				'dynamic'     => ['active' => true],
			]
		);

		$this->add_control(
			'button_style',
			[
				'label'     => __('Estilo', 'upsites-addons'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'primary',
				'options'   => [
					'primary'   => __('Primário (preenchido)', 'upsites-addons'),
					'secondary' => __('Secundário (contorno)', 'upsites-addons'),
					'link'      => __('Link (texto)', 'upsites-addons'),
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'link_variant',
			[
				'label'     => __('Variação (Link)', 'upsites-addons'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'light',
				'options'   => [
					'light' => __('Clara — texto roxo (fundo claro)', 'upsites-addons'),
					'dark'  => __('Escura — texto branco (fundo escuro)', 'upsites-addons'),
				],
				'condition'   => ['button_style' => 'link'],
				'description' => __('Aplica a cor de texto/ícone de fábrica. Se você definir uma cor manual em Estilo → Cores, ela tem prioridade.', 'upsites-addons'),
			]
		);

		$this->add_control(
			'button_size',
			[
				'label'   => __('Tamanho', 'upsites-addons'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'md',
				'options' => [
					'xs' => __('Extra pequeno', 'upsites-addons'),
					'sm' => __('Pequeno', 'upsites-addons'),
					'md' => __('Médio', 'upsites-addons'),
					'lg' => __('Grande', 'upsites-addons'),
					'xl' => __('Extra grande', 'upsites-addons'),
				],
			]
		);

		$this->end_controls_section();

		// ── Content Tab — Ícone ────────────────────────────────────────
		$this->start_controls_section(
			'section_icon',
			[
				'label' => __('Ícone', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_icon',
			[
				'label'        => __('Mostrar ícone', 'upsites-addons'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Sim', 'upsites-addons'),
				'label_off'    => __('Não', 'upsites-addons'),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'icon_position',
			[
				'label'     => __('Posição', 'upsites-addons'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => [
					'after'  => __('Depois do texto', 'upsites-addons'),
					'before' => __('Antes do texto', 'upsites-addons'),
				],
				'condition' => ['show_icon' => 'yes'],
			]
		);

		$this->add_control(
			'icon_source',
			[
				'label'       => __('Origem do ícone', 'upsites-addons'),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'default',
				'options'     => [
					'default' => __('Padrão (seta com bola)', 'upsites-addons'),
					'custom'  => __('Personalizado (upload)', 'upsites-addons'),
				],
				'description' => __('A seta padrão tem cor de bola e de seta configuráveis abaixo, além da animação de hover.', 'upsites-addons'),
				'condition'   => ['show_icon' => 'yes'],
			]
		);

		$this->add_control(
			'icon',
			[
				'label'     => __('Imagem do ícone', 'upsites-addons'),
				'type'      => Controls_Manager::MEDIA,
				'default'   => ['url' => ''],
				'condition' => ['show_icon' => 'yes', 'icon_source' => 'custom'],
			]
		);

		$this->add_control(
			'icon_ball_color',
			[
				'label'       => __('Cor da bola', 'upsites-addons'),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#ffffff',
				'condition'   => ['show_icon' => 'yes', 'icon_source' => 'default', 'button_style' => 'link'],
				'description' => __('Só se aplica ao ícone padrão do estilo Link (seta com bola).', 'upsites-addons'),
				'selectors'   => [
					'{{WRAPPER}} .upsites-btn__icon, {{WRAPPER}} .upsites-btn__icon--static' => '--upsites-btn-icon-ball: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_arrow_color',
			[
				'label'       => __('Cor da seta', 'upsites-addons'),
				'type'        => Controls_Manager::COLOR,
				'condition'   => ['show_icon' => 'yes', 'icon_source' => 'default'],
				'description' => __('Deixe em branco para acompanhar a cor do texto do botão.', 'upsites-addons'),
				'selectors'   => [
					'{{WRAPPER}} .upsites-btn__icon, {{WRAPPER}} .upsites-btn__icon--static' => '--upsites-btn-icon-arrow: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_ball_hover_color',
			[
				'label'       => __('Cor da bola — hover', 'upsites-addons'),
				'type'        => Controls_Manager::COLOR,
				'condition'   => ['show_icon' => 'yes', 'icon_source' => 'default', 'button_style' => 'link'],
				'description' => __('Deixe em branco para manter a cor da bola igual ao estado normal.', 'upsites-addons'),
				'selectors'   => [
					'{{WRAPPER}} .upsites-btn:hover .upsites-btn__icon, {{WRAPPER}} .upsites-btn:hover .upsites-btn__icon--static' => '--upsites-btn-icon-ball: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_arrow_hover_color',
			[
				'label'       => __('Cor da seta — hover', 'upsites-addons'),
				'type'        => Controls_Manager::COLOR,
				'condition'   => ['show_icon' => 'yes', 'icon_source' => 'default'],
				'description' => __('Deixe em branco para manter a cor da seta igual ao estado normal.', 'upsites-addons'),
				'selectors'   => [
					'{{WRAPPER}} .upsites-btn:hover .upsites-btn__icon, {{WRAPPER}} .upsites-btn:hover .upsites-btn__icon--static' => '--upsites-btn-icon-arrow: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_hover_animation',
			[
				'label'        => __('Animação de troca no hover', 'upsites-addons'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Sim', 'upsites-addons'),
				'label_off'    => __('Não', 'upsites-addons'),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __('Duas setas se revezam com um leve deslocamento diagonal ao passar o mouse (Smart Animate / Ease Out).', 'upsites-addons'),
				'condition'    => ['show_icon' => 'yes'],
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Cores ────────────────────────────────────────────
		$this->start_controls_section(
			'section_style_colors',
			[
				'label' => __('Cores e fundo', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_button_colors');

		// ── Tab: Normal ─────────────────────────────────────────────
		$this->start_controls_tab(
			'tab_button_normal',
			['label' => __('Normal', 'upsites-addons')]
		);

		$this->add_responsive_control(
			'text_color',
			[
				'label'       => __('Cor do texto', 'upsites-addons'),
				'type'        => Controls_Manager::COLOR,
				'description' => __('Deixe em branco para usar a cor de fábrica (preto ou a "Variação" escolhida, no estilo Link).', 'upsites-addons'),
				'selectors'   => [
					'{{WRAPPER}} .upsites-btn' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'bg_color',
				'types'     => ['classic', 'gradient'],
				'condition' => ['button_style!' => 'link'],
				'selector'  => '{{WRAPPER}} .upsites-btn',
				'fields_options' => [
					'background' => ['default' => 'classic'],
					'color'      => ['default' => '#fec437'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'button_border',
				'condition' => ['button_style' => 'secondary'],
				'selector'  => '{{WRAPPER}} .upsites-btn',
				'fields_options' => [
					'border' => ['default' => 'solid'],
					'width'  => ['default' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'unit' => 'px']],
					'color'  => ['default' => '#262626'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .upsites-btn',
			]
		);

		$this->end_controls_tab();

		// ── Tab: Hover ──────────────────────────────────────────────
		$this->start_controls_tab(
			'tab_button_hover',
			['label' => __('Hover', 'upsites-addons')]
		);

		$this->add_responsive_control(
			'text_hover_color',
			[
				'label'       => __('Cor do texto', 'upsites-addons'),
				'type'        => Controls_Manager::COLOR,
				'description' => __('Deixe em branco para usar a cor de fábrica.', 'upsites-addons'),
				'selectors'   => [
					'{{WRAPPER}} .upsites-btn:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'bg_hover_color',
				'types'     => ['classic', 'gradient'],
				'condition' => ['button_style!' => 'link'],
				'selector'  => '{{WRAPPER}} .upsites-btn:hover',
				'fields_options' => [
					'background' => ['default' => 'classic'],
					'color'      => ['default' => '#e5b132'],
				],
			]
		);

		$this->add_control(
			'border_hover_color',
			[
				'label'     => __('Cor da borda', 'upsites-addons'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#262626',
				'condition' => ['button_style' => 'secondary'],
				'selectors' => [
					'{{WRAPPER}} .upsites-btn:hover' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .upsites-btn:hover',
			]
		);

		$this->add_control(
			'hover_transition_duration',
			[
				'label'     => __('Duração da transição (ms)', 'upsites-addons'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => ['px' => ['min' => 0, 'max' => 1000, 'step' => 10]],
				'default'   => ['unit' => 'px', 'size' => 200],
				'selectors' => [
					'{{WRAPPER}} .upsites-btn' => 'transition-duration: {{SIZE}}ms;',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'hover_animation',
			[
				'label'     => __('Animação de hover', 'upsites-addons'),
				'type'      => Controls_Manager::HOVER_ANIMATION,
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Tipografia ───────────────────────────────────────
		$this->start_controls_section(
			'section_style_typography',
			[
				'label' => __('Tipografia', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .upsites-btn',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'button_text_shadow',
				'selector' => '{{WRAPPER}} .upsites-btn',
			]
		);

		$this->add_control(
			'text_decoration',
			[
				'label'     => __('Sublinhado (normal)', 'upsites-addons'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => [
					'none'      => __('Nenhum', 'upsites-addons'),
					'underline' => __('Sublinhado', 'upsites-addons'),
				],
				'separator' => 'before',
				'condition' => ['button_style' => 'link'],
				'selectors' => [
					'{{WRAPPER}} .upsites-btn' => 'text-decoration: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_hover_decoration',
			[
				'label'     => __('Sublinhado (hover)', 'upsites-addons'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'underline',
				'options'   => [
					'none'      => __('Nenhum', 'upsites-addons'),
					'underline' => __('Sublinhado', 'upsites-addons'),
				],
				'condition' => ['button_style' => 'link'],
				'selectors' => [
					'{{WRAPPER}} .upsites-btn:hover' => 'text-decoration: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();

		// ── Style Tab — Dimensões ─────────────────────────────────────────
		$this->start_controls_section(
			'section_style_dimensions',
			[
				'label' => __('Dimensões', 'upsites-addons'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'button_position',
			[
				'label'     => __('Posição', 'upsites-addons'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'start'   => ['title' => __('Esquerda', 'upsites-addons'), 'icon' => 'eicon-h-align-left'],
					'center'  => ['title' => __('Centro', 'upsites-addons'), 'icon' => 'eicon-h-align-center'],
					'end'     => ['title' => __('Direita', 'upsites-addons'), 'icon' => 'eicon-h-align-right'],
					'stretch' => ['title' => __('Justificado', 'upsites-addons'), 'icon' => 'eicon-h-align-stretch'],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .upsites-btn-wrap' => 'justify-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'       => __('Espaçamento interno', 'upsites-addons'),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => ['px'],
				'description' => __('Deixe em branco para usar o espaçamento do "Tamanho" definido no Conteúdo.', 'upsites-addons'),
				'condition'   => ['button_style!' => 'link'],
				'selectors'   => [
					'{{WRAPPER}} .upsites-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_radius',
			[
				'label'      => __('Border radius', 'upsites-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => ['px' => ['min' => 0, 'max' => 60]],
				'default'    => ['unit' => 'px', 'size' => 12],
				'condition'  => ['button_style!' => 'link'],
				'selectors'  => [
					'{{WRAPPER}} .upsites-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_gap',
			[
				'label'      => __('Espaço entre texto e ícone', 'upsites-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => ['px' => ['min' => 0, 'max' => 40]],
				'default'    => ['unit' => 'px', 'size' => 8],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .upsites-btn' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => __('Tamanho do ícone', 'upsites-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => ['px' => ['min' => 12, 'max' => 48]],
				'default'    => ['unit' => 'px', 'size' => 24],
				'condition'  => ['show_icon' => 'yes'],
				'selectors'  => [
					'{{WRAPPER}} .upsites-btn__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'animation_duration',
			[
				'label'      => __('Duração da animação (ms)', 'upsites-addons'),
				'type'       => Controls_Manager::SLIDER,
				'range'      => ['px' => ['min' => 50, 'max' => 600, 'step' => 10]],
				'default'    => ['unit' => 'px', 'size' => 200],
				'condition'  => ['show_icon' => 'yes', 'icon_hover_animation' => 'yes'],
				'selectors'  => [
					'{{WRAPPER}} .upsites-btn__icon-arrow' => 'transition-duration: {{SIZE}}ms;',
				],
			]
		);

		$this->end_controls_section();
	}
}
