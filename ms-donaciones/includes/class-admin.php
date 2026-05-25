<?php

if (!defined('ABSPATH')) {
    exit;
}

class MS_Donaciones_Admin {

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu_page']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
    }

    public static function add_menu_page() {
        add_menu_page(
            'Donaciones MS',
            'Donaciones MS',
            'manage_options',
            'ms-donaciones',
            [__CLASS__, 'render_page'],
            'dashicons-heart',
            56
        );

        foreach (self::get_sections() as $slug => $section) {
            add_submenu_page(
                'ms-donaciones',
                'Donaciones MS - ' . $section['title'],
                $section['menu'],
                'manage_options',
                'ms-donaciones-' . $slug,
                [__CLASS__, 'render_page']
            );
        }
    }

    public static function register_settings() {
        register_setting(
            'ms_donaciones_settings',
            'ms_donaciones_labels',
            [
                'type'              => 'array',
                'sanitize_callback' => [__CLASS__, 'sanitize_labels'],
                'default'           => MS_Donaciones_Shortcodes::default_labels(),
            ]
        );
    }

    public static function sanitize_labels($input) {
        $defaults = MS_Donaciones_Shortcodes::default_labels();
        $input = is_array($input) ? $input : [];
        $output = array_merge(
            $defaults,
            get_option('ms_donaciones_labels', [])
        );

        foreach ($input as $key => $value) {
            if (!array_key_exists($key, $defaults) && !preg_match('/^impact_tier_\d+$/', $key)) {
                continue;
            }

            if (str_ends_with($key, '_url') || $key === 'foto_url') {
                $value = trim((string) $value);
                $output[$key] = str_starts_with($value, '/') || str_starts_with($value, '#')
                    ? sanitize_text_field($value)
                    : esc_url_raw($value);
                continue;
            }

            if ($key === 'bank_email') {
                $output[$key] = sanitize_email($value);
                continue;
            }

            if (in_array($key, ['default_amount', 'min_amount'], true)) {
                $output[$key] = (string) max(0, absint($value));
                continue;
            }

            if ($key === 'crm_enabled') {
                $output[$key] = !empty($value) ? '1' : '0';
                continue;
            }

            if ($key === 'amount_presets') {
                $amounts = array_filter(array_map('absint', preg_split('/[\s,;]+/', (string) $value)));
                $output[$key] = implode(',', $amounts);
                continue;
            }

            $output[$key] = sanitize_text_field($value);
        }

        return $output;
    }

    public static function render_page() {
        if (!current_user_can('manage_options')) {
            wp_die('No autorizado.');
        }

        $labels = array_merge(
            MS_Donaciones_Shortcodes::default_labels(),
            get_option('ms_donaciones_labels', [])
        );
        $sections = self::get_sections($labels);
        $current_slug = self::current_section_slug($sections);
        $section = $sections[$current_slug];
        $prev_next = self::get_prev_next($sections, $current_slug);
        ?>
        <div class="wrap ms-donaciones-admin">
            <h1>Donaciones MS</h1>

            <p>
                Configuración del formulario embebido con <code>[formulario_donacion]</code>.
                Cada página corresponde a una parte del recorrido del donante.
            </p>

            <?php settings_errors('ms_donaciones_settings'); ?>
            <?php self::render_styles(); ?>
            <?php self::render_tabs($sections, $current_slug); ?>

            <form method="post" action="options.php">
                <?php settings_fields('ms_donaciones_settings'); ?>

                <section class="ms-card">
                    <div class="ms-section-head">
                        <div>
                            <span class="ms-step-label"><?php echo esc_html($section['step']); ?></span>
                            <h2><?php echo esc_html($section['title']); ?></h2>
                            <p><?php echo esc_html($section['description']); ?></p>
                        </div>
                        <button type="submit" class="button button-primary button-hero">
                            Guardar esta sección
                        </button>
                    </div>

                    <table class="form-table" role="presentation">
                        <?php foreach ($section['fields'] as $key => $field) : ?>
                            <?php
                            $label = $field[0] ?? $key;
                            $type = $field[1] ?? 'text';
                            $description = $field[2] ?? '';
                            ?>
                            <tr>
                                <th scope="row">
                                    <label for="ms-donaciones-<?php echo esc_attr($key); ?>">
                                        <?php echo esc_html($label); ?>
                                    </label>
                                </th>
                                <td>
                                    <?php self::render_input($key, $type, $labels[$key] ?? '', $description); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <?php if (!empty($section['help'])) : ?>
                        <?php self::render_help_box($section['help']); ?>
                    <?php endif; ?>
                </section>

                <div class="ms-save-bar">
                    <div class="ms-step-nav">
                        <?php if ($prev_next['prev']) : ?>
                            <a class="button" href="<?php echo esc_url(self::section_url($prev_next['prev'])); ?>">
                                &larr; Anterior
                            </a>
                        <?php endif; ?>

                        <?php if ($prev_next['next']) : ?>
                            <a class="button" href="<?php echo esc_url(self::section_url($prev_next['next'])); ?>">
                                Siguiente &rarr;
                            </a>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="button button-primary button-hero">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
        <?php
    }

    private static function get_sections($labels = null) {
        $impact_fields = self::get_impact_fields($labels);

        return [
            'navegacion' => [
                'menu' => 'Navegación',
                'step' => 'General',
                'title' => 'Navegación y pasos',
                'description' => 'Textos superiores del formulario y labels del stepper.',
                'fields' => [
                    'site_back_url' => ['URL volver al sitio', 'text', 'Puede ser relativa, por ejemplo /inicio.'],
                    'site_back_label' => ['Texto volver al sitio'],
                    'stepper_1_label' => ['Paso 1'],
                    'stepper_2_label' => ['Paso 2'],
                    'stepper_3_label' => ['Paso 3'],
                ],
            ],
            'hero' => [
                'menu' => 'Hero lateral',
                'step' => 'Step 1',
                'title' => 'Hero lateral',
                'description' => 'Imagen, caption, métricas y cita que aparecen a la izquierda.',
                'fields' => [
                    'foto_url' => ['URL de foto principal', 'url'],
                    'hero_image_alt' => ['Alt de imagen'],
                    'hero_caption' => ['Texto sobre la foto principal'],
                    'hero_stat_1_number' => ['Metrica 1 - numero'],
                    'hero_stat_1_label' => ['Metrica 1 - texto'],
                    'hero_stat_2_number' => ['Metrica 2 - numero'],
                    'hero_stat_2_label' => ['Metrica 2 - texto'],
                    'hero_quote_text' => ['Cita'],
                    'hero_quote_author' => ['Autor de la cita'],
                ],
            ],
            'datos' => [
                'menu' => 'Datos personales',
                'step' => 'Step 1',
                'title' => 'Datos personales',
                'description' => 'Títulos, labels, ayudas y botones del primer paso.',
                'fields' => [
                    'step1_eyebrow' => ['Etiqueta superior'],
                    'step1_title_before' => ['Titulo antes del destacado'],
                    'step1_title_highlight' => ['Titulo destacado'],
                    'step1_title_after' => ['Titulo despues del destacado'],
                    'step1_lede' => ['Bajada'],
                    'saved_banner_text' => ['Banner datos guardados'],
                    'step1_impact_text' => ['Mensaje de impacto fijo'],
                    'nombre' => ['Label Nombre'],
                    'apellido' => ['Label Apellido'],
                    'email' => ['Label Email'],
                    'dni' => ['Label DNI'],
                    'telefono' => ['Label Telefono'],
                    'email_hint' => ['Ayuda Email'],
                    'dni_hint' => ['Ayuda DNI'],
                    'telefono_hint' => ['Ayuda Telefono'],
                    'step1_button' => ['Boton continuar'],
                    'step1_reassure' => ['Texto de seguridad'],
                ],
            ],
            'crm' => [
                'menu' => 'Datos personales a CRM',
                'step' => 'Step 1',
                'title' => 'Datos personales a CRM',
                'description' => 'Configuración del envío automático de los datos del primer paso.',
                'fields' => [
                    'crm_enabled' => ['Activar envío a Airtable', 'checkbox'],
                    'airtable_base_id' => ['Base ID', 'text', 'Ejemplo: appXXXXXXXXXXXXXX. Lo encontrás en la documentación de API de la base.'],
                    'airtable_table_name' => ['Tabla', 'text', 'Nombre o ID de la tabla donde se crearán los registros. Ejemplo: Donaciones.'],
                    'airtable_token' => ['Personal Access Token', 'password', 'Se envía server-side desde WordPress, no queda expuesto en el navegador.'],
                    'airtable_field_nombre' => ['Columna Nombre', 'text', 'Debe coincidir exactamente con Airtable.'],
                    'airtable_field_apellido' => ['Columna Apellido', 'text', 'Debe coincidir exactamente con Airtable.'],
                    'airtable_field_email' => ['Columna Email', 'text', 'Debe coincidir exactamente con Airtable.'],
                    'airtable_field_dni' => ['Columna DNI', 'text', 'Debe coincidir exactamente con Airtable.'],
                    'airtable_field_telefono' => ['Columna Telefono', 'text', 'Debe coincidir exactamente con Airtable. Dejalo vacio si no existe.'],
                    'airtable_field_origen' => ['Columna Origen opcional', 'text', 'Dejalo vacio si no queres enviar este dato.'],
                    'airtable_field_fecha' => ['Columna Fecha opcional', 'text', 'Dejalo vacio si no queres enviar este dato.'],
                ],
                'help' => [
                    'title' => 'Cómo generar el token de Airtable',
                    'items' => [
                        'Entrá al Developer hub de Airtable y creá un Personal Access Token.',
                        'Agregá el scope data.records:write.',
                        'En recursos, seleccioná la base donde está la tabla de donantes.',
                        'Los nombres de columnas deben coincidir exacto con Airtable, incluyendo tildes, espacios y mayusculas.',
                        'Si una columna opcional no existe en Airtable, dejala vacia para no enviarla.',
                        'Guardá el token, pegalo acá y completá el Base ID y la tabla.',
                    ],
                    'link' => 'https://support.airtable.com/docs/creating-personal-access-tokens',
                    'link_label' => 'Ver guía oficial de Airtable',
                ],
            ],
            'montos' => [
                'menu' => 'Montos',
                'step' => 'Step 2',
                'title' => 'Montos y frecuencia',
                'description' => 'Montos predefinidos, monto mínimo y textos de frecuencia.',
                'fields' => [
                    'amount_presets' => ['Montos predefinidos', 'text', 'Separar por coma. Ej: 1500,5000,15000,50000'],
                    'default_amount' => ['Monto inicial', 'number'],
                    'min_amount' => ['Monto minimo', 'number'],
                    'step2_back_label' => ['Boton volver'],
                    'step2_eyebrow' => ['Etiqueta superior'],
                    'step2_title' => ['Titulo'],
                    'step2_lede_before_name' => ['Bajada antes del nombre'],
                    'step2_lede_after_name' => ['Bajada despues del nombre'],
                    'anonymous_name' => ['Nombre fallback'],
                    'frequency_legend' => ['Titulo frecuencia'],
                    'frequency_once_label' => ['Frecuencia unica'],
                    'frequency_monthly_label' => ['Frecuencia mensual'],
                    'frequency_monthly_badge' => ['Badge mensual'],
                    'amount_legend' => ['Titulo montos'],
                    'amount_monthly_suffix' => ['Sufijo mensual'],
                    'custom_amount_placeholder' => ['Placeholder otro monto'],
                    'amount_error' => ['Error monto invalido'],
                    'methods_title' => ['Titulo metodos'],
                ],
            ],
            'impacto' => [
                'menu' => 'Impacto',
                'step' => 'Step 2',
                'title' => 'Impacto por monto',
                'description' => 'Mensajes dinámicos del bloque "Con $X ARS...".',
                'fields' => $impact_fields,
            ],
            'metodos' => [
                'menu' => 'Métodos de pago',
                'step' => 'Step 2',
                'title' => 'Métodos de pago',
                'description' => 'Nombre, descripción y tags de cada método. Tags separados por coma.',
                'fields' => [
                    'method_mp_name' => ['Mercado Pago - nombre'],
                    'method_mp_desc' => ['Mercado Pago - descripcion'],
                    'method_mp_tags' => ['Mercado Pago - tags'],
                    'method_local_name' => ['Tarjeta local - nombre'],
                    'method_local_desc' => ['Tarjeta local - descripcion'],
                    'method_local_tags' => ['Tarjeta local - tags'],
                    'method_intl_name' => ['Tarjeta internacional - nombre'],
                    'method_intl_desc' => ['Tarjeta internacional - descripcion'],
                    'method_intl_tags' => ['Tarjeta internacional - tags'],
                    'method_bank_name' => ['Transferencia - nombre'],
                    'method_bank_desc' => ['Transferencia - descripcion'],
                    'method_bank_tags' => ['Transferencia - tags'],
                ],
            ],
            'confirmacion' => [
                'menu' => 'Confirmación',
                'step' => 'Step 3',
                'title' => 'Confirmación y transferencia',
                'description' => 'Pantalla final, errores y datos bancarios.',
                'fields' => [
                    'step3_back_label' => ['Botón cambiar metodo'],
                    'step3_loading_title' => ['Título cargando'],
                    'step3_loading_text_prefix' => ['Texto cargando antes del método'],
                    'step3_loading_text_suffix' => ['Texto cargando después del método'],
                    'step3_error_title' => ['Título error'],
                    'step3_error_text' => ['Error Mercado Pago'],
                    'step3_connection_error_text' => ['Error conexión'],
                    'step3_retry_label' => ['Botón reintentar'],
                    'bank_title' => ['Título transferencia'],
                    'bank_lede_prefix' => ['Texto transferencia antes del monto'],
                    'bank_lede_middle' => ['Texto entre monto y nombre'],
                    'bank_block_title' => ['Título bloque bancario'],
                    'bank_holder' => ['Titular'],
                    'bank_cuit' => ['CUIT'],
                    'bank_name' => ['Banco'],
                    'bank_cbu' => ['CBU'],
                    'bank_alias' => ['Alias'],
                    'bank_note' => ['Texto comprobante'],
                    'bank_email' => ['Email comprobantes', 'email'],
                    'restart_button' => ['Botón otra donación'],
                ],
            ],
            'modal' => [
                'menu' => 'Modal',
                'step' => 'Post datos',
                'title' => 'Modal post datos',
                'description' => 'Modal que aparece después de guardar los datos personales.',
                'fields' => [
                    'modal_title_prefix' => ['Titulo antes del nombre'],
                    'modal_title_suffix' => ['Titulo despues del nombre'],
                    'modal_lede_prefix' => ['Bajada antes del email'],
                    'modal_lede_suffix' => ['Bajada despues del email'],
                    'modal_card_title' => ['Titulo tarjeta'],
                    'modal_card_text' => ['Texto tarjeta'],
                    'modal_donate_now' => ['Boton donar ahora'],
                    'modal_donate_later' => ['Boton donar mas tarde'],
                    'modal_footer' => ['Texto seguridad'],
                ],
            ],
            'footer' => [
                'menu' => 'Confianza y footer',
                'step' => 'Cierre',
                'title' => 'Confianza y footer',
                'description' => 'Sellos de confianza, footer y links.',
                'fields' => [
                    'trust_1_title' => ['Confianza 1 - titulo'],
                    'trust_1_text' => ['Confianza 1 - texto'],
                    'trust_2_title' => ['Confianza 2 - titulo'],
                    'trust_2_text' => ['Confianza 2 - texto'],
                    'trust_3_title' => ['Confianza 3 - titulo'],
                    'trust_3_text' => ['Confianza 3 - texto'],
                    'footer_text' => ['Texto footer'],
                    'footer_seal_1' => ['Sello 1'],
                    'footer_seal_2' => ['Sello 2'],
                    'footer_seal_3' => ['Sello 3'],
                    'footer_link_1_label' => ['Link 1 - texto'],
                    'footer_link_1_url' => ['Link 1 - URL', 'text', 'Puede ser relativa o #.'],
                    'footer_link_2_label' => ['Link 2 - texto'],
                    'footer_link_2_url' => ['Link 2 - URL', 'text', 'Puede ser relativa o #.'],
                    'footer_link_3_label' => ['Link 3 - texto'],
                    'footer_link_3_url' => ['Link 3 - URL', 'text', 'Puede ser relativa o #.'],
                ],
            ],
        ];
    }

    private static function get_impact_fields($labels = null) {
        $defaults = MS_Donaciones_Shortcodes::default_labels();
        $labels = is_array($labels) ? array_merge($defaults, $labels) : $defaults;
        $amounts = self::parse_amount_presets($labels['amount_presets'] ?? $defaults['amount_presets']);
        $fields = [];

        foreach ($amounts as $index => $amount) {
            $key = 'impact_tier_' . ($index + 1);
            $formatted_amount = number_format($amount, 0, ',', '.');

            $fields[$key] = [
                'Impacto para $' . $formatted_amount,
                'text',
                'Se muestra cuando la donación seleccionada es de $' . $formatted_amount . ' ARS o más.',
            ];
        }

        return $fields ?: [
            'impact_tier_1' => ['Impacto por defecto'],
        ];
    }

    private static function parse_amount_presets($value) {
        $amounts = array_filter(array_map('absint', preg_split('/[\s,;]+/', (string) $value)));
        $amounts = array_values(array_unique($amounts));
        sort($amounts, SORT_NUMERIC);

        return $amounts;
    }

    private static function current_section_slug($sections) {
        $page = sanitize_key($_GET['page'] ?? 'ms-donaciones');

        if ($page === 'ms-donaciones') {
            return array_key_first($sections);
        }

        $slug = preg_replace('/^ms-donaciones-/', '', $page);
        return isset($sections[$slug]) ? $slug : array_key_first($sections);
    }

    private static function section_url($slug) {
        return admin_url('admin.php?page=ms-donaciones-' . $slug);
    }

    private static function get_prev_next($sections, $current_slug) {
        $slugs = array_keys($sections);
        $index = array_search($current_slug, $slugs, true);

        return [
            'prev' => $index > 0 ? $slugs[$index - 1] : null,
            'next' => $index < count($slugs) - 1 ? $slugs[$index + 1] : null,
        ];
    }

    private static function render_tabs($sections, $current_slug) {
        ?>
        <nav class="nav-tab-wrapper ms-tabs" aria-label="Secciones de configuracion">
            <?php foreach ($sections as $slug => $section) : ?>
                <a
                    class="nav-tab <?php echo $slug === $current_slug ? 'nav-tab-active' : ''; ?>"
                    href="<?php echo esc_url(self::section_url($slug)); ?>"
                >
                    <?php echo esc_html($section['menu']); ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <?php
    }

    private static function render_help_box($help) {
        ?>
        <aside class="ms-help-box">
            <h3><?php echo esc_html($help['title'] ?? 'Ayuda'); ?></h3>

            <?php if (!empty($help['items']) && is_array($help['items'])) : ?>
                <ol>
                    <?php foreach ($help['items'] as $item) : ?>
                        <li><?php echo esc_html($item); ?></li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>

            <?php if (!empty($help['link'])) : ?>
                <a href="<?php echo esc_url($help['link']); ?>" target="_blank" rel="noopener noreferrer">
                    <?php echo esc_html($help['link_label'] ?? $help['link']); ?>
                </a>
            <?php endif; ?>
        </aside>
        <?php
    }

    private static function render_styles() {
        ?>
        <style>
            .ms-donaciones-admin .ms-tabs {
                margin-top: 18px;
                display: flex;
                flex-wrap: wrap;
                gap: 0;
            }
            .ms-donaciones-admin .ms-card {
                background: #fff;
                border: 1px solid #dcdcde;
                border-radius: 8px;
                margin: 18px 0;
                padding: 22px;
                max-width: 1080px;
            }
            .ms-donaciones-admin .ms-section-head {
                align-items: flex-start;
                border-bottom: 1px solid #dcdcde;
                display: flex;
                gap: 18px;
                justify-content: space-between;
                margin: 0 0 10px;
                padding: 0 0 16px;
            }
            .ms-donaciones-admin .ms-section-head h2 {
                margin: 3px 0 4px;
            }
            .ms-donaciones-admin .ms-section-head p {
                color: #646970;
                margin: 0;
            }
            .ms-donaciones-admin .ms-step-label {
                color: #2271b1;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: .04em;
                text-transform: uppercase;
            }
            .ms-donaciones-admin input.regular-text {
                max-width: 720px;
                width: 100%;
            }
            .ms-donaciones-admin .ms-help-box {
                background: #f6f7f7;
                border-left: 4px solid #2271b1;
                margin-top: 18px;
                max-width: 760px;
                padding: 14px 16px;
            }
            .ms-donaciones-admin .ms-help-box h3 {
                margin: 0 0 8px;
            }
            .ms-donaciones-admin .ms-help-box ol {
                margin: 0 0 10px 20px;
            }
            .ms-donaciones-admin .ms-help-box li {
                margin-bottom: 4px;
            }
            .ms-donaciones-admin .ms-save-bar {
                align-items: center;
                background: #fff;
                border: 1px solid #dcdcde;
                border-radius: 8px;
                bottom: 14px;
                box-shadow: 0 6px 20px rgba(0,0,0,.08);
                display: flex;
                justify-content: space-between;
                margin: 18px 0;
                max-width: 1080px;
                padding: 14px 16px;
                position: sticky;
                z-index: 5;
            }
            .ms-donaciones-admin .ms-step-nav {
                display: flex;
                gap: 8px;
            }
        </style>
        <?php
    }

    private static function render_input($key, $type, $value, $description = '') {
        if ($type === 'checkbox') {
            ?>
            <input
                type="hidden"
                name="ms_donaciones_labels[<?php echo esc_attr($key); ?>]"
                value="0"
            >
            <label>
                <input
                    id="ms-donaciones-<?php echo esc_attr($key); ?>"
                    type="checkbox"
                    name="ms_donaciones_labels[<?php echo esc_attr($key); ?>]"
                    value="1"
                    <?php checked($value, '1'); ?>
                >
                Activado
            </label>
            <?php if ($description) : ?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
            <?php
            return;
        }

        $input_type = in_array($type, ['url', 'email', 'number', 'password'], true) ? $type : 'text';
        ?>
        <input
            id="ms-donaciones-<?php echo esc_attr($key); ?>"
            type="<?php echo esc_attr($input_type); ?>"
            name="ms_donaciones_labels[<?php echo esc_attr($key); ?>]"
            value="<?php echo esc_attr($value); ?>"
            class="regular-text"
        >
        <?php if ($description) : ?>
            <p class="description"><?php echo esc_html($description); ?></p>
        <?php endif; ?>
        <?php
    }
}
