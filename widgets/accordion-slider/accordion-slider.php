<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

class UpSites_Accordion_Slider_Widget extends Widget_Base {

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
		return [ 'general' ];
	}

	public function get_script_depends() {
		return [ 'upsites-accordion-slider' ];
	}

	public function get_style_depends() {
		return [ 'upsites-accordion-slider' ];
	}

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
				'label'      => __( 'Opacidade da logo', 'upsites-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
				'default'    => [ 'size' => 0.75 ],
				'selectors'  => [
					'{{WRAPPER}} .upsites-accordion-slide__logo img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings       = $this->get_settings_for_display();
		$slides         = $settings['slides'];
		$default_active = intval( $settings['default_active'] );

		$overlay_start = ! empty( $settings['overlay_inactive_start'] ) ? $settings['overlay_inactive_start'] : 'rgba(77,45,120,0)';
		$overlay_end   = ! empty( $settings['overlay_inactive_end'] )   ? $settings['overlay_inactive_end']   : 'rgba(77,45,120,0.5)';

		if ( empty( $slides ) ) {
			return;
		}
		?>
		<div class="upsites-accordion-wrapper"
			data-default-active="<?php echo esc_attr( $default_active ); ?>"
			data-overlay-start="<?php echo esc_attr( $overlay_start ); ?>"
			data-overlay-end="<?php echo esc_attr( $overlay_end ); ?>">

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

					$mb_x          = isset( $slide['slide_bg_pos_x_mobile']['size'] ) ? $slide['slide_bg_pos_x_mobile']['size'] : 50;
					$mb_xu         = isset( $slide['slide_bg_pos_x_mobile']['unit'] ) ? $slide['slide_bg_pos_x_mobile']['unit'] : '%';
					$mb_y          = isset( $slide['slide_bg_pos_y_mobile']['size'] ) ? $slide['slide_bg_pos_y_mobile']['size'] : 50;
					$mb_yu         = isset( $slide['slide_bg_pos_y_mobile']['unit'] ) ? $slide['slide_bg_pos_y_mobile']['unit'] : '%';
					$bg_pos_mobile = $mb_x . $mb_xu . ' ' . $mb_y . $mb_yu;

					$overlay_color = ! empty( $slide['overlay_color'] ) ? $slide['overlay_color'] : 'rgba(77,45,120,0.7)';
					$logo_width    = ! empty( $slide['slide_logo_width']['size'] )        ? intval( $slide['slide_logo_width']['size'] )        : 160;
					$logo_width_mb = ! empty( $slide['slide_logo_width_mobile']['size'] ) ? intval( $slide['slide_logo_width_mobile']['size'] ) : 80;
				?>
				<div class="upsites-accordion-slide<?php echo esc_attr( $active_class ); ?>"
					data-index="<?php echo esc_attr( $index ); ?>"
					data-overlay-active="<?php echo esc_attr( $overlay_color ); ?>"
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
						data-logo-width-mobile="<?php echo esc_attr( $logo_width_mb ); ?>">
						<img src="<?php echo esc_url( $slide['slide_logo']['url'] ); ?>"
							alt="<?php echo esc_attr( $slide['slide_logo_alt'] ?? '' ); ?>"
							style="max-width: <?php echo esc_attr( $logo_width ); ?>px;">
					</div>
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
				<?php endforeach; ?>
			</div>

		</div>
		<?php
	}
}
