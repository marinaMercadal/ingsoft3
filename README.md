# ingsoft3

Rediseño del flujo de donación única para [modulosanitario.org](https://modulosanitario.org), una ONG argentina que construye baños dignos para familias sin acceso a saneamiento básico.

---

## Stack de la ONG

| Capa | Tecnología |
|------|-----------|
| CMS | WordPress |
| Formularios | Formidable Forms Business |
| E-commerce | WooCommerce + Mercado Pago |
| Integración forms↔productos | YayCommerce / FormWoo |
| CRM / Donaciones mensuales | Salesforce + Debi (Prisma) |
| Page builder | Elementor |

---

## Problema

El flujo anterior era estático y manual:

```
Botón → Formulario Formidable → Producto fijo WooCommerce ($35.000) → Checkout MP
```

- El monto era fijo (atado a un producto de WooCommerce)
- No había multi-paso — todos los datos se pedían juntos
- Sin posibilidad de elegir monto libre
- Las donaciones mensuales por MP no funcionaban bien

---

## Solución

Formulario React multi-paso embebido en WordPress, con monto dinámico y envío de datos a Formidable via REST API.

```
Paso 1: Datos personales (Nombre, Apellido, Email, DNI, Teléfono)
   ↓ (modal de guardado)
Paso 2: Frecuencia + Monto libre + Método de pago
   ↓
Paso 3: Confirmación → redirect a Mercado Pago / tarjeta / transferencia
   ↓ (silencioso)
Formidable Forms REST API → registro guardado
```

Las donaciones mensuales via Debi/Salesforce se mantienen sin cambios.

---

## Archivos

```
donacion.js         # Formulario React completo (CSS + lógica + 3 pasos)
functions.php       # Fragmento para agregar al functions.php del tema WordPress
inicio.html         # Mock de página de inicio
paso1.html          # Mock estático paso 1 (presentación)
paso2.html          # Mock estático paso 2 (presentación)
paso3.html          # Mock estático paso 3 (presentación)
README.md
```

---

## Instalación en WordPress

**1. Subir el archivo React al tema:**
```
wp-content/themes/[tema-activo]/donacion/donacion.js
```

**2. Agregar al `functions.php` del tema:**
```php
function donacion_scripts() {
    if ( is_page('donar') ) {
        wp_enqueue_script('react',     'https://unpkg.com/react@18.3.1/umd/react.development.js',     [], null, true);
        wp_enqueue_script('react-dom', 'https://unpkg.com/react-dom@18.3.1/umd/react-dom.development.js', ['react'], null, true);
        wp_enqueue_script('babel',     'https://unpkg.com/@babel/standalone@7.29.0/babel.min.js',     [], null, true);
        wp_enqueue_script('donacion',  get_template_directory_uri() . '/donacion/donacion.js', ['react','react-dom','babel'], null, true);
    }
}
add_action('wp_enqueue_scripts', 'donacion_scripts');

function donacion_shortcode() { return '<div id="root"></div>'; }
add_shortcode('formulario_donacion', 'donacion_shortcode');

// Endpoint REST para guardar en Formidable Forms
add_action('rest_api_init', function() {
    register_rest_route('donacion/v1', '/guardar', array(
        'methods'  => 'POST',
        'callback' => 'donacion_guardar_entrada',
        'permission_callback' => '__return_true',
    ));
});

function donacion_guardar_entrada($request) {
    $params = $request->get_json_params();
    $entry_id = FrmEntry::create(array(
        'form_id' => 2, // ID del formulario en Formidable
        'item_meta' => array(
            7  => sanitize_text_field($params['nombre'] ?? ''),
            8  => sanitize_text_field($params['apellido'] ?? ''),
            9  => sanitize_email($params['email'] ?? ''),
            10 => sanitize_text_field($params['dni'] ?? ''),
            11 => sanitize_text_field($params['telefono'] ?? ''),
        )
    ));
    return new WP_REST_Response(array('success' => (bool)$entry_id), $entry_id ? 200 : 500);
}
```

**3. Flush de permalinks:**
```
WP Admin → Settings → Permalinks → Save Changes
```

**4. Crear página con slug `donar` y shortcode:**
```
[formulario_donacion]
```

---

## Mapeo de campos Formidable (formulario ID 2)

| Campo | ID Formidable |
|-------|--------------|
| Nombre | 7 |
| Apellido | 8 |
| Email | 9 |
| DNI | 10 |
| Teléfono | 11 |

> ⚠️ Si los IDs difieren en el WordPress de la ONG, actualizar el mapeo en `functions.php`.

---

## Pendientes

- [ ] Conectar Mercado Pago Checkout Bricks con monto dinámico real
- [ ] Paso 4 post-pago: motivación + localidad
- [ ] Ajustar selectores CSS al tema real de la ONG (no es `twentytwentyfive`)
- [ ] Definir flujo de donaciones mensuales desde el nuevo formulario
- [ ] Evaluar si WooCommerce se mantiene para historial de pedidos

---

## Desarrollo local

Probado con [LocalWP](https://localwp.com) — WordPress 7.0, PHP 8.2, MySQL 8.4.
