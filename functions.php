<?php
/**
 * Twenty Twenty-Five functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

if ( ! function_exists( 'twentytwentyfive_post_format_setup' ) ) :
	/**
	 * Adds theme support for post formats.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_post_format_setup() {
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_post_format_setup' );

if ( ! function_exists( 'twentytwentyfive_editor_style' ) ) :
	/**
	 * Enqueues editor-style.css in the editors.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_editor_style() {
		add_editor_style( 'assets/css/editor-style.css' );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_editor_style' );

if ( ! function_exists( 'twentytwentyfive_enqueue_styles' ) ) :
	/**
	 * Enqueues the theme stylesheet on the front.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_enqueue_styles() {
		$suffix = SCRIPT_DEBUG ? '' : '.min';
		$src    = 'style' . $suffix . '.css';

		wp_enqueue_style(
			'twentytwentyfive-style',
			get_parent_theme_file_uri( $src ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
		wp_style_add_data(
			'twentytwentyfive-style',
			'path',
			get_parent_theme_file_path( $src )
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_enqueue_styles' );

if ( ! function_exists( 'twentytwentyfive_block_styles' ) ) :
	/**
	 * Registers custom block styles.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_block_styles() {
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfive' ),
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_block_styles' );

if ( ! function_exists( 'twentytwentyfive_pattern_categories' ) ) :
	/**
	 * Registers pattern categories.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfive_page',
			array(
				'label'       => __( 'Pages', 'twentytwentyfive' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfive' ),
			)
		);

		register_block_pattern_category(
			'twentytwentyfive_post-format',
			array(
				'label'       => __( 'Post formats', 'twentytwentyfive' ),
				'description' => __( 'A collection of post format patterns.', 'twentytwentyfive' ),
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_pattern_categories' );

if ( ! function_exists( 'twentytwentyfive_register_block_bindings' ) ) :
	/**
	 * Registers the post format block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_register_block_bindings() {
		register_block_bindings_source(
			'twentytwentyfive/format',
			array(
				'label'              => _x( 'Post format name', 'Label for the block binding placeholder in the editor', 'twentytwentyfive' ),
				'get_value_callback' => 'twentytwentyfive_format_binding',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_register_block_bindings' );

if ( ! function_exists( 'twentytwentyfive_format_binding' ) ) :
	/**
	 * Callback function for the post format name block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return string|void Post format name, or nothing if the format is 'standard'.
	 */
	function twentytwentyfive_format_binding() {
		$post_format_slug = get_post_format();

		if ( $post_format_slug && 'standard' !== $post_format_slug ) {
			return get_post_format_string( $post_format_slug );
		}
	}
endif;

// ── Formulario donación React ──────────────────────────
function donacion_scripts() {
    if ( is_page('donar') ) {
        wp_enqueue_script('react',     'https://unpkg.com/react@18.3.1/umd/react.development.js',     [], null, true);
        wp_enqueue_script('react-dom', 'https://unpkg.com/react-dom@18.3.1/umd/react-dom.development.js', ['react'], null, true);
        wp_enqueue_script('babel',     'https://unpkg.com/@babel/standalone@7.29.0/babel.min.js',     [], null, true);
        wp_enqueue_script('donacion',  get_template_directory_uri() . '/donacion/donacion.js', ['react','react-dom','babel'], null, true);
    }
}
add_action('wp_enqueue_scripts', 'donacion_scripts');

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

function donacion_shortcode() { return '<div id="root"></div>'; }
add_shortcode('formulario_donacion', 'donacion_shortcode');

// Endpoint para guardar datos en Formidable desde React
add_action('rest_api_init', function() {
    register_rest_route('donacion/v1', '/guardar', array(
        'methods'  => 'POST',
        'callback' => 'donacion_guardar_entrada',
        'permission_callback' => '__return_true',
    ));
});

function donacion_guardar_entrada($request) {
    $params = $request->get_json_params();
    
    $entry_data = array(
        'form_id' => 2,
        'item_meta' => array(
            7  => sanitize_text_field($params['nombre'] ?? ''),
            8  => sanitize_text_field($params['apellido'] ?? ''),
            9  => sanitize_email($params['email'] ?? ''),
            10 => sanitize_text_field($params['dni'] ?? ''),
            11 => sanitize_text_field($params['telefono'] ?? ''),
        )
    );
    
    $entry_id = FrmEntry::create($entry_data);
    
    if ($entry_id) {
        return new WP_REST_Response(array('success' => true, 'entry_id' => $entry_id), 200);
    } else {
        return new WP_REST_Response(array('success' => false), 500);
    }
}
