<?php
/**
 * Twenty Twenty-Five functions and definitions.
 */

if ( ! function_exists( 'twentytwentyfive_post_format_setup' ) ) :
    function twentytwentyfive_post_format_setup() {
        add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
    }
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_post_format_setup' );

if ( ! function_exists( 'twentytwentyfive_editor_style' ) ) :
    function twentytwentyfive_editor_style() {
        add_editor_style( 'assets/css/editor-style.css' );
    }
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_editor_style' );

if ( ! function_exists( 'twentytwentyfive_enqueue_styles' ) ) :
    function twentytwentyfive_enqueue_styles() {
        $suffix = SCRIPT_DEBUG ? '' : '.min';
        $src    = 'style' . $suffix . '.css';
        wp_enqueue_style( 'twentytwentyfive-style', get_parent_theme_file_uri( $src ), array(), wp_get_theme()->get( 'Version' ) );
        wp_style_add_data( 'twentytwentyfive-style', 'path', get_parent_theme_file_path( $src ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_enqueue_styles' );

if ( ! function_exists( 'twentytwentyfive_block_styles' ) ) :
    function twentytwentyfive_block_styles() {
        register_block_style( 'core/list', array(
            'name'         => 'checkmark-list',
            'label'        => __( 'Checkmark', 'twentytwentyfive' ),
            'inline_style' => 'ul.is-style-checkmark-list { list-style-type: "✓"; } ul.is-style-checkmark-list li { padding-inline-start: 1ch; }',
        ) );
    }
endif;
add_action( 'init', 'twentytwentyfive_block_styles' );

if ( ! function_exists( 'twentytwentyfive_pattern_categories' ) ) :
    function twentytwentyfive_pattern_categories() {
        register_block_pattern_category( 'twentytwentyfive_page', array( 'label' => __( 'Pages', 'twentytwentyfive' ), 'description' => __( 'A collection of full page layouts.', 'twentytwentyfive' ) ) );
        register_block_pattern_category( 'twentytwentyfive_post-format', array( 'label' => __( 'Post formats', 'twentytwentyfive' ), 'description' => __( 'A collection of post format patterns.', 'twentytwentyfive' ) ) );
    }
endif;
add_action( 'init', 'twentytwentyfive_pattern_categories' );

if ( ! function_exists( 'twentytwentyfive_register_block_bindings' ) ) :
    function twentytwentyfive_register_block_bindings() {
        register_block_bindings_source( 'twentytwentyfive/format', array(
            'label'              => _x( 'Post format name', 'Label for the block binding placeholder in the editor', 'twentytwentyfive' ),
            'get_value_callback' => 'twentytwentyfive_format_binding',
        ) );
    }
endif;
add_action( 'init', 'twentytwentyfive_register_block_bindings' );

if ( ! function_exists( 'twentytwentyfive_format_binding' ) ) :
    function twentytwentyfive_format_binding() {
        $post_format_slug = get_post_format();
        if ( $post_format_slug && 'standard' !== $post_format_slug ) {
            return get_post_format_string( $post_format_slug );
        }
    }
endif;

// ══════════════════════════════════════════════════════
// DONACIÓN — Scripts React
// ══════════════════════════════════════════════════════
function donacion_scripts() {
    if ( is_page('donar') ) {
        wp_enqueue_script('react',     'https://unpkg.com/react@18.3.1/umd/react.development.js',     [], null, true);
        wp_enqueue_script('react-dom', 'https://unpkg.com/react-dom@18.3.1/umd/react-dom.development.js', ['react'], null, true);
        wp_enqueue_script('babel',     'https://unpkg.com/@babel/standalone@7.29.0/babel.min.js',     [], null, true);
        wp_enqueue_script('donacion',  get_template_directory_uri() . '/donacion/donacion.js', ['react','react-dom','babel'], null, true);
    }
}
add_action('wp_enqueue_scripts', 'donacion_scripts');

// Ocultar header y título en páginas donar e inicio
function donacion_hide_theme_elements() {
    if ( is_page('donar') || is_page('inicio') ) {
        echo '<style>
            .wp-block-template-part,
            .site-header,
            .wp-site-blocks > header,
            .entry-header,
            h1.entry-title,
            h1.wp-block-post-title,
            .wp-block-post-title { display: none !important; }
            .wp-block-post-content { padding: 0 !important; max-width: 100% !important; }
            main.wp-block-group { padding: 0 !important; max-width: 100% !important; }
            .wp-site-blocks { padding: 0 !important; }
            .is-layout-constrained > * { max-width: 100% !important; }
            #root { width: 100%; }
            .entry-content { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
            .wp-block-shortcode { display: block; width: 100%; }
            .wp-block-post-content > * { margin-top: 0 !important; padding-top: 0 !important; }
            .wp-site-blocks > main { padding-top: 0 !important; margin-top: 0 !important; }
            .wp-block-group { margin-top: 0 !important; padding-top: 0 !important; }
        </style>';
    }
}
add_action('wp_head', 'donacion_hide_theme_elements');

// Shortcode [formulario_donacion]
function donacion_shortcode() { return '<div id="root"></div>'; }
add_shortcode('formulario_donacion', 'donacion_shortcode');

// ══════════════════════════════════════════════════════
// DONACIÓN — Endpoint Formidable Forms
// ══════════════════════════════════════════════════════
add_action('rest_api_init', function() {
    register_rest_route('donacion/v1', '/guardar', array(
        'methods'             => 'POST',
        'callback'            => 'donacion_guardar_entrada',
        'permission_callback' => '__return_true',
    ));
});

function donacion_guardar_entrada($request) {
    $params = $request->get_json_params();
    $entry_data = array(
        'form_id'   => 2,
        'item_meta' => array(
            7  => sanitize_text_field($params['nombre']        ?? ''),
            8  => sanitize_text_field($params['apellido']      ?? ''),
            9  => sanitize_email($params['email']              ?? ''),
            10 => sanitize_text_field($params['dni']           ?? ''),
            11 => sanitize_text_field($params['telefono']      ?? ''),
            13 => sanitize_text_field($params['monto']         ?? ''),
            14 => sanitize_text_field($params['metodo']        ?? ''),
            15 => 'pendiente',
            17 => sanitize_text_field($params['preference_id'] ?? ''),
        )
    );
    $entry_id = FrmEntry::create($entry_data);
    if ($entry_id) {
        return new WP_REST_Response(array('success' => true, 'entry_id' => $entry_id), 200);
    }
    return new WP_REST_Response(array('success' => false), 500);
}

// ══════════════════════════════════════════════════════
// DONACIÓN — Mercado Pago
// ══════════════════════════════════════════════════════

// ⚠️ REEMPLAZÁ con tu Access Token de prueba (TEST-...) o producción (APP_USR-...)
// NUNCA subas este archivo a GitHub con el token real
define('MP_ACCESS_TOKEN', 'PONER_TOKEN_ACA');

add_action('rest_api_init', function() {
    register_rest_route('donacion/v1', '/crear-preferencia', array(
        'methods'             => 'POST',
        'callback'            => 'donacion_crear_preferencia',
        'permission_callback' => '__return_true',
    ));
});

function donacion_crear_preferencia($request) {
    $params   = $request->get_json_params();
    $monto    = floatval($params['monto']    ?? 0);
    $nombre   = sanitize_text_field($params['nombre']   ?? '');
    $apellido = sanitize_text_field($params['apellido'] ?? '');
    $email    = sanitize_email($params['email']         ?? '');
    $dni      = sanitize_text_field($params['dni']      ?? '');

    if ($monto < 100) {
        return new WP_REST_Response(array('error' => 'Monto inválido'), 400);
    }

    $body = array(
        'items' => array(array(
            'title'      => 'Donación Módulo Sanitario',
            'quantity'   => 1,
            'unit_price' => $monto,
            'currency_id'=> 'ARS',
        )),
        'payer' => array(
            'name'    => $nombre,
            'surname' => $apellido,
            'email'   => $email,
            'identification' => array('type' => 'DNI', 'number' => $dni),
        ),
        'back_urls' => array(
            'success' => 'https://modulosanitario.org/gracias',
            'failure' => 'https://modulosanitario.org/donar',
            'pending' => 'https://modulosanitario.org/gracias',
        ),
        'statement_descriptor' => 'MODULO SANITARIO',
        'external_reference'   => 'donacion-' . time(),
    );

    $response = wp_remote_post(
        'https://api.mercadopago.com/checkout/preferences',
        array(
            'headers' => array(
                'Authorization' => 'Bearer ' . MP_ACCESS_TOKEN,
                'Content-Type'  => 'application/json',
            ),
            'body'    => json_encode($body),
            'timeout' => 15,
        )
    );

    if (is_wp_error($response)) {
        return new WP_REST_Response(array('error' => 'Error conectando con MP', 'detalle' => $response->get_error_message()), 500);
    }

    $data      = json_decode(wp_remote_retrieve_body($response), true);
    $http_code = wp_remote_retrieve_response_code($response);

    // Sandbox usa sandbox_init_point, producción usa init_point
    $init_point = $data['sandbox_init_point'] ?? $data['init_point'] ?? null;

    if ($init_point) {
        return new WP_REST_Response(array('success' => true, 'init_point' => $init_point, 'id' => $data['id']), 200);
    }

    return new WP_REST_Response(array(
        'error'     => 'Error creando preferencia',
        'detalle'   => $data,
        'http_code' => $http_code,
    ), 500);
}

// Webhook de Mercado Pago
add_action('rest_api_init', function() {
    register_rest_route('donacion/v1', '/webhook', array(
        'methods'             => 'POST',
        'callback'            => 'donacion_webhook_mp',
        'permission_callback' => '__return_true',
    ));
});

function donacion_webhook_mp($request) {
    $params = $request->get_json_params();
    $topic  = $params['type']       ?? '';
    $id     = $params['data']['id'] ?? '';

    if ($topic === 'payment' && $id) {
        $response = wp_remote_get(
            'https://api.mercadopago.com/v1/payments/' . $id,
            array('headers' => array('Authorization' => 'Bearer ' . MP_ACCESS_TOKEN))
        );
        $payment      = json_decode(wp_remote_retrieve_body($response), true);
        $status       = $payment['status']             ?? 'unknown';
        $external_ref = $payment['external_reference'] ?? '';
        $preference_id = $payment['preference_id']     ?? '';

        // Buscar la entrada en Formidable por preference_id
        $entries = FrmEntry::getAll(array('it.form_id' => 2), '', '', true);
        foreach ($entries as $entry) {
            $meta = FrmEntryMeta::getAll(array('item_id' => $entry->id));
            foreach ($meta as $m) {
                if ($m->field_id == 17 && $m->meta_value == $preference_id) {
                    // Actualizar estado y payment ID
                    FrmEntryMeta::update_entry_metas(array(
                        15 => $status,
                        16 => strval($id),
                    ), $entry->id);
                    break 2;
                }
            }
        }

        error_log('MP Webhook: payment ' . $id . ' status: ' . $status);
    }

    return new WP_REST_Response(array('success' => true), 200);
}
