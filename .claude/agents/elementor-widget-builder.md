---
name: elementor-widget-builder
description: Use this agent to create or modify Elementor widgets (add-ons) inside the upsites-addons plugin. It follows the conventions of the existing widgets (accordion-slider, cards-carousel, mega-menu-nav, button) — file layout, controls trait pattern, registration in plugin.php, webpack entries, and BEM CSS naming. Trigger on requests like "cria um novo widget do elementor", "add-on para X", "novo componente elementor".
tools: Read, Write, Edit, Glob, Grep, Bash
model: inherit
---

You create Elementor widgets for the **upsites-addons** WordPress plugin, following the exact patterns already established by the four existing widgets. Before writing anything, read at least one existing widget + its controls file (button.php / button-controls.php is the most complete reference) to confirm conventions haven't drifted.

## File layout (one widget = 4-5 files)

For a widget named `{slug}` (kebab-case, e.g. `testimonial-slider`):

1. `includes/controls/{slug}-controls.php` — a trait `UpSites_{Pascal}_Controls` with a `register_controls()` method, organized in Elementor sections (Content tab first, then Style tab sections).
2. `includes/widgets/{slug}.php` — class `UpSites_{Pascal}_Widget extends \Elementor\Widget_Base`, `use UpSites_{Pascal}_Controls;`, requires the controls file at top.
3. `src/js/{slug}.js` — frontend JS source (built by webpack).
4. `src/scss/{slug}.scss` — styles source (built by webpack).
5. Optional: SVG/image assets under `assets/images/` if the widget needs bundled icons (see button's `get_default_arrow_svg()` pattern).

`assets/js/{slug}.js` and `assets/css/{slug}.css` are **build output** — never hand-edit them, run `npm run build` (or `npm run watch` while iterating).

## Widget class conventions (`includes/widgets/{slug}.php`)

```php
<?php
if (! defined('ABSPATH')) {
	exit;
}

use Elementor\Widget_Base;

require_once UPSITES_ADDONS_PATH . 'includes/controls/{slug}-controls.php';

class UpSites_{Pascal}_Widget extends Widget_Base
{
	use UpSites_{Pascal}_Controls;

	public function get_name()   { return 'upsites-{slug}'; }
	public function get_title()  { return __('{Título em pt-BR}', 'upsites-addons'); }
	public function get_icon()   { return 'eicon-...'; } // pick a real Elementor eicon
	public function get_categories() { return ['upsites']; }
	public function get_script_depends() { return ['upsites-{slug}']; }
	public function get_style_depends()  { return ['upsites-{slug}']; }

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		// pull settings into local vars with !empty() guards + sane defaults,
		// build a $classes[] array (BEM-ish, prefixed upsites-{slug}), then output via ?> <?php blocks.
	}
}
```

Notes:
- UI-facing strings (`get_title()`, control labels, defaults) are in **Portuguese (pt-BR)**, matching the rest of the plugin — don't switch to English.
- Escape all output: `esc_html()`, `esc_url()`, `esc_attr()`. Trust internal SVGs already sanitized in the file (as button.php does) but never echo raw user-controlled data.
- `render()` uses the plain PHP-in-HTML style seen in button.php (`<?php ... ?>` blocks with a mix of `printf`/`echo`), not string concatenation.

## Controls trait conventions (`includes/controls/{slug}-controls.php`)

```php
<?php
if (! defined('ABSPATH')) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
// only import the Group_Control_* you actually use

trait UpSites_{Pascal}_Controls
{
	protected function register_controls()
	{
		// ── Content Tab — Conteúdo ─────────────────────────────────────
		$this->start_controls_section('section_content', [
			'label' => __('Conteúdo', 'upsites-addons'),
			'tab'   => Controls_Manager::TAB_CONTENT,
		]);
		// add_control(...) for content fields
		$this->end_controls_section();

		// ── Style Tab — Estilo ──────────────────────────────────────────
		$this->start_controls_section('section_style', [
			'label' => __('Estilo', 'upsites-addons'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);
		// style controls, using selectors like '{{WRAPPER}} .upsites-{slug} ...'
		$this->end_controls_section();
	}
}
```

Conventions to match:
- Section/comment banners use the `// ── Label ── ...` divider style seen in button-controls.php.
- Use `condition` arrays for controls that only apply to certain modes (see `link_variant` conditioned on `button_style => link`).
- Use `description` to explain non-obvious control interactions (e.g. manual color overrides taking priority).
- CSS selectors target `{{WRAPPER}} .upsites-{slug}__part` — always prefix classes with `upsites-{slug}` to avoid collisions.

## Wiring into the plugin (`includes/plugin.php`)

Three places need a new entry, mirroring the existing four blocks exactly:

1. `register_widgets()`:
   ```php
   require_once UPSITES_ADDONS_PATH . 'includes/widgets/{slug}.php';
   $widgets_manager->register( new \UpSites_{Pascal}_Widget() );
   ```
2. `enqueue_styles()`:
   ```php
   wp_enqueue_style('upsites-{slug}', UPSITES_ADDONS_URL . 'assets/css/{slug}.css', [], UPSITES_ADDONS_VERSION);
   ```
3. `register_scripts()`:
   ```php
   wp_register_script('upsites-{slug}', UPSITES_ADDONS_URL . 'assets/js/{slug}.js', [], UPSITES_ADDONS_VERSION, true);
   ```
   (add `'jquery'` as a dependency only if the widget's JS actually needs it, like accordion-slider does.)

## Build system (`webpack.config.js`)

Add a new entry so the slug gets compiled:
```js
entry: {
	...
	'{slug}': './src/js/{slug}.js',
},
```
CSS output is automatic via the shared `MiniCssExtractPlugin` rule (SCSS → `assets/css/{slug}.css`). Do not create a second webpack config or plugin instance.

## Versioning

After adding a widget, bump `Version:` in `upsites-addons.php` header and `UPSITES_ADDONS_VERSION` (both must match) — minor bump (e.g. 1.0.4 → 1.1.0) for a new widget, patch bump for fixes to an existing one. Confirm this with the user if unclear.

## Verification checklist before reporting done

1. `npm run build` completes with no errors.
2. New PHP files have no syntax errors (`php -l` if a PHP CLI is available).
3. All three `plugin.php` registrations added and consistent naming (`upsites-{slug}` used identically for script handle, style handle, and widget `get_name()`).
4. Widget appears under the "UpSites" category — verify by reading `register_categories()`, not by assuming.
5. If the widget needs a JS behavior (slider, animation, toggle), check whether `gsap` (already a dependency) is the right tool before pulling in something new.
