# UpSites Add-ons

Plugin de widgets customizados para o Elementor, desenvolvido pela UpSites.

## Requisitos

- WordPress 6.0+
- Elementor (versão gratuita ou Pro)
- PHP 7.4+

## Estrutura

```
upsites-addons/
│
├── upsites-addons.php          # Bootstrap: define constantes e carrega o plugin
│
├── assets/
│   ├── css/                    # Folhas de estilo dos widgets
│   └── js/                     # Scripts dos widgets
│
└── includes/
    ├── plugin.php              # Classe principal — registra widgets, scripts e estilos
    ├── controls/               # Controles customizados do Elementor (se houver)
    └── widgets/                # Um arquivo PHP por widget
```

### Como o plugin inicializa

1. `upsites-addons.php` define as constantes globais (`UPSITES_ADDONS_PATH`, `UPSITES_ADDONS_URL`, `UPSITES_ADDONS_VERSION`) e carrega `includes/plugin.php`.
2. `includes/plugin.php` contém a classe `UpSites_Addons` (singleton) que se conecta aos hooks do Elementor:
   - `elementor/widgets/register` — registra cada widget da pasta `includes/widgets/`
   - `elementor/frontend/after_enqueue_styles` — enfileira os CSS
   - `elementor/frontend/after_register_scripts` — registra os JS (carregados sob demanda pelo widget)
3. Se o Elementor não estiver ativo, um aviso é exibido no painel do WordPress.

### Adicionando um novo widget

1. Crie `includes/widgets/meu-widget.php` com uma classe que estenda `\Elementor\Widget_Base`.
2. Em `includes/plugin.php`, dentro de `register_widgets()`, adicione:
   ```php
   require_once UPSITES_ADDONS_PATH . 'includes/widgets/meu-widget.php';
   $widgets_manager->register( new \MeuWidget() );
   ```
3. Coloque os assets em `assets/css/meu-widget.css` e `assets/js/meu-widget.js` e enfileire-os nos métodos `enqueue_styles()` e `register_scripts()`.

### Adicionando um controle customizado

1. Crie `includes/controls/meu-controle.php` com uma classe que estenda `\Elementor\Base_Control`.
2. Registre-o em `includes/plugin.php` via `elementor/controls/register`.
