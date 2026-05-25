<?php

if (!defined('ABSPATH')) {
    exit;
}

class MS_Donaciones_REST {

    public static function init() {
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }

    public static function register_routes() {
        register_rest_route('donacion/v1', '/guardar', [
            'methods'             => 'POST',
            'callback'            => [__CLASS__, 'guardar_cliente'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function guardar_cliente($request) {
        $params = $request->get_json_params();

        $data = [
            'nombre'   => sanitize_text_field($params['nombre'] ?? ''),
            'apellido' => sanitize_text_field($params['apellido'] ?? ''),
            'email'    => sanitize_email($params['email'] ?? ''),
            'dni'      => sanitize_text_field($params['dni'] ?? ''),
            'telefono' => sanitize_text_field($params['telefono'] ?? ''),
            'monto'    => sanitize_text_field($params['monto'] ?? ''),
            'metodo'   => sanitize_text_field($params['metodo'] ?? ''),
        ];
        $crm_event = sanitize_text_field($params['crm_event'] ?? '');

        error_log('MS Donaciones - Cliente recibido: ' . wp_json_encode($data));

        $crm_result = $crm_event === 'step_1_completed'
            ? self::send_to_airtable($data)
            : [
                'enabled' => false,
                'success' => null,
                'message' => 'CRM no disparado para este evento.',
            ];

        return new WP_REST_Response([
            'success'    => true,
            'message'    => 'Datos recibidos correctamente',
            'data'       => $data,
            'crm_result' => $crm_result,
        ], 200);
    }

    private static function send_to_airtable($data) {
        $settings = array_merge(
            MS_Donaciones_Shortcodes::default_labels(),
            get_option('ms_donaciones_labels', [])
        );

        if (($settings['crm_enabled'] ?? '0') !== '1') {
            return [
                'enabled' => false,
                'success' => null,
                'message' => 'CRM desactivado.',
            ];
        }

        $base_id = sanitize_text_field($settings['airtable_base_id'] ?? '');
        $table_name = sanitize_text_field($settings['airtable_table_name'] ?? '');
        $token = sanitize_text_field($settings['airtable_token'] ?? '');

        if (!$base_id || !$table_name || !$token) {
            error_log('MS Donaciones - CRM activo sin credenciales completas de Airtable.');

            return [
                'enabled' => true,
                'success' => false,
                'message' => 'Falta configurar Base ID, tabla o token de Airtable.',
            ];
        }

        $endpoint = sprintf(
            'https://api.airtable.com/v0/%s/%s',
            rawurlencode($base_id),
            rawurlencode($table_name)
        );

        $fields = self::build_airtable_fields($settings, $data);

        if (!$fields) {
            return [
                'enabled' => true,
                'success' => false,
                'message' => 'No hay columnas de Airtable configuradas para enviar.',
            ];
        }

        $payload = [
            'records' => [
                [
                    'fields' => $fields,
                ],
            ],
            'typecast' => true,
        ];

        $response = wp_remote_post($endpoint, [
            'timeout' => 12,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ],
            'body' => wp_json_encode($payload),
        ]);

        if (is_wp_error($response)) {
            error_log('MS Donaciones - Error enviando a Airtable: ' . $response->get_error_message());

            return [
                'enabled' => true,
                'success' => false,
                'message' => $response->get_error_message(),
            ];
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $airtable_error = self::get_airtable_error($body);
        $success = $status_code >= 200 && $status_code < 300;

        if (!$success) {
            error_log('MS Donaciones - Airtable respondio con HTTP ' . $status_code);
            error_log('MS Donaciones - Airtable body: ' . substr($body, 0, 1000));
        }

        return [
            'enabled'        => true,
            'success'        => $success,
            'status'         => $status_code,
            'message'        => $success ? 'Datos enviados a Airtable.' : 'Airtable respondio con error.',
            'airtable_error' => $success ? null : $airtable_error,
            'sent_fields'    => array_keys($fields),
        ];
    }

    private static function build_airtable_fields($settings, $data) {
        $field_map = [
            'airtable_field_nombre'   => $data['nombre'],
            'airtable_field_apellido' => $data['apellido'],
            'airtable_field_email'    => $data['email'],
            'airtable_field_dni'      => $data['dni'],
            'airtable_field_telefono' => $data['telefono'],
        ];
        $fields = [];

        foreach ($field_map as $setting_key => $value) {
            $field_name = sanitize_text_field($settings[$setting_key] ?? '');

            if ($field_name && $value !== '') {
                $fields[$field_name] = $value;
            }
        }

        $origin_field = sanitize_text_field($settings['airtable_field_origen'] ?? '');
        if ($origin_field) {
            $fields[$origin_field] = 'Formulario web';
        }

        $date_field = sanitize_text_field($settings['airtable_field_fecha'] ?? '');
        if ($date_field) {
            $fields[$date_field] = current_time('mysql');
        }

        return $fields;
    }

    private static function get_airtable_error($body) {
        $decoded = json_decode($body, true);

        if (!is_array($decoded)) {
            return $body ? substr($body, 0, 500) : null;
        }

        if (!empty($decoded['error']['message'])) {
            return $decoded['error']['message'];
        }

        if (!empty($decoded['error']['type'])) {
            return $decoded['error']['type'];
        }

        return substr($body, 0, 500);
    }
}
