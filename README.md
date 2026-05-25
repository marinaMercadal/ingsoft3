# MS Donaciones

Plugin WordPress para embeber y administrar el formulario de donaciones de Módulo Sanitario.

La implementación actual separa la lógica del theme y concentra el formulario, el shortcode, la configuración del admin y el endpoint REST dentro del plugin.

## Estructura

```txt
ms-donaciones/
  ms-donaciones.php
  README.md
  assets/
    donacion.js
  includes/
    class-admin.php
    class-shortcodes.php
    class-rest.php
    class-about.php
```

## Instalación

### Opción 1: instalación manual

Copiar la carpeta completa:

```txt
ms-donaciones/
```

dentro de:

```txt
wp-content/plugins/
```

La ruta final debe quedar:

```txt
wp-content/plugins/ms-donaciones/ms-donaciones.php
```

Luego ir al panel de WordPress:

```txt
Plugins > Plugins instalados
```

y activar:

```txt
MS Donaciones
```

### Opción 2: instalación por ZIP

Comprimir la carpeta completa `ms-donaciones`, no solo sus archivos internos.

El ZIP debe tener esta forma:

```txt
ms-donaciones.zip
  ms-donaciones/
    ms-donaciones.php
    assets/
    includes/
    README.md
```

Luego subirlo desde:

```txt
Plugins > Añadir nuevo > Subir plugin
```

## Uso del shortcode

El plugin registra el shortcode:

```txt
[formulario_donacion]
```

Ese shortcode renderiza el contenedor React:

```html
<div id="ms-donacion-root"></div>
```

y carga los assets del formulario solo cuando el shortcode se usa.

## Uso en Elementor

En Elementor se puede insertar de dos formas:

1. Usando el widget **Shortcode**.
2. Usando un widget **HTML** con el shortcode.

Contenido:

```txt
[formulario_donacion]
```

## Panel de administración

El plugin agrega una sección al admin de WordPress:

```txt
Donaciones MS
```

Desde ahí se pueden configurar textos y valores del formulario por secciones:

- Navegación
- Hero lateral
- Datos personales
- Montos
- Métodos de pago
- Impacto
- Confirmación
- Modal
- Confianza y footer

La configuración se guarda en la tabla:

```txt
wp_options
```

con la opción:

```txt
ms_donaciones_labels
```

## Configuraciones disponibles

Actualmente se pueden editar desde el admin, entre otros:

- Labels de Nombre, Apellido, Email, DNI y Teléfono.
- URL de imagen principal.
- Texto sobre la imagen principal.
- Métricas del hero.
- Cita del hero.
- Títulos y bajadas del paso 1.
- Montos predefinidos.
- Monto inicial.
- Monto mínimo.
- Textos de frecuencia.
- Nombre, descripción y tags de métodos de pago.
- Mensajes de impacto por monto.
- Textos de confirmación.
- Datos de transferencia bancaria.
- Textos del modal.
- Sellos de confianza.
- Links del footer.

## REST API

El plugin registra el endpoint:

```txt
POST /wp-json/donacion/v1/guardar
```

Payload esperado:

```json
{
  "nombre": "Facundo",
  "apellido": "Alonso",
  "email": "facundoalonso@uca.edu.ar",
  "dni": "12345678",
  "telefono": "1122334455",
  "monto": 5000,
  "metodo": "mp"
}
```

Por ahora el endpoint sanitiza la información recibida, la registra en el log de WordPress y devuelve una respuesta mock:

```json
{
  "success": true,
  "message": "Datos recibidos correctamente"
}
```

## Mercado Pago

Mercado Pago no está implementado en esta etapa.

El formulario conserva pantallas y textos relacionados con métodos de pago para mantener el flujo visual, pero la integración real de pago queda pendiente.

## Google Sheets

Google Sheets no está implementado todavía.

El lugar preparado para agregar esa integración más adelante es:

```txt
includes/class-rest.php
```

en el método:

```php
MS_Donaciones_REST::guardar_cliente()
```

## Archivos principales

### `ms-donaciones.php`

Archivo principal del plugin. Define constantes, carga clases e inicializa:

- Shortcodes
- REST API
- Admin panel
- Página de equipo

### `includes/class-shortcodes.php`

Registra:

```txt
[formulario_donacion]
```

También carga React, ReactDOM, Babel y `assets/donacion.js`.

Además pasa la configuración del admin al frontend mediante:

```php
wp_localize_script()
```

como variable global:

```js
window.MS_DONACIONES
```

### `includes/class-admin.php`

Define el panel de administración `Donaciones MS`.

Permite editar los textos y valores visibles del formulario.

### `includes/class-rest.php`

Define el endpoint REST:

```txt
/wp-json/donacion/v1/guardar
```

### `assets/donacion.js`

Contiene el formulario React embebido.

Lee la configuración desde:

```js
window.MS_DONACIONES.labels
```

## Notas de desarrollo

El formulario actual usa React 18 y Babel desde CDN para facilitar la integración rápida dentro de WordPress.

A futuro se recomienda compilar el frontend con un build step y reemplazar Babel en navegador por un bundle estático.

## Pendientes

- Separar CSS a `assets/donacion.css`.
- Implementar integración real con Mercado Pago.
- Implementar envío de datos a Google Sheets.
- Agregar validaciones REST más estrictas.
- Agregar tests o validaciones automatizadas.

