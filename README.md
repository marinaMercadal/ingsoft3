# MS Donaciones

Plugin WordPress para embeber y administrar el formulario de donaciones de Modulo Sanitario.

Version actual: `0.1.16`

La implementacion actual separa la logica del theme y concentra el formulario, el shortcode, la configuracion del admin, la pagina de equipo y el endpoint REST dentro del plugin.

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

## Instalacion

### Opcion 1: instalacion manual

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

### Opcion 2: instalacion por ZIP

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
Plugins > Anadir nuevo > Subir plugin
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

## Panel de administracion

El plugin agrega una seccion al admin de WordPress:

```txt
Donaciones MS
```

Desde ahi se pueden configurar textos y valores del formulario por secciones:

- Textos visibles, con selector interno por seccion
- Media y links
- Datos personales a CRM
- Montos
- Impacto
- Mercado Pago
- Transferencia
- Equipo

La configuracion se guarda en la tabla:

```txt
wp_options
```

con la opcion:

```txt
ms_donaciones_labels
```

## Configuraciones disponibles

Actualmente se pueden editar desde el admin, entre otros:

- Labels de Nombre, Apellido, Email, DNI y Telefono.
- URL de imagen principal.
- Texto sobre la imagen principal.
- Metricas del hero.
- Cita del hero.
- Titulos y bajadas del paso 1.
- Configuracion de envio a Airtable.
- Montos predefinidos.
- Monto inicial.
- Monto minimo.
- Textos de frecuencia.
- Mensajes de impacto por monto.
- Nombre, descripcion y tags de metodos de pago.
- Textos de confirmacion.
- Datos de transferencia bancaria.
- Textos del modal.
- Sellos de confianza.
- Links del footer.

## CRM con Airtable

Cuando el usuario completa el primer paso del formulario, el frontend llama al endpoint REST del plugin:

```txt
POST /wp-json/donacion/v1/guardar
```

Si el envio a CRM esta activado desde el admin, WordPress reenvia esos datos a Airtable usando la API oficial:

```txt
https://api.airtable.com/v0/{baseId}/{table}
```

La configuracion se realiza desde:

```txt
Donaciones MS > Datos personales a CRM
```

Campos requeridos:

- Activar envio a Airtable.
- Base ID, por ejemplo `appXXXXXXXXXXXXXX`.
- Nombre o ID de tabla, por ejemplo `Donaciones`.
- Personal Access Token de Airtable.
- Mapeo de columnas de Airtable para Nombre, Apellido, Email, DNI y Telefono.

La seccion incluye un boton para probar la conexion guardada con Airtable. Para que esa prueba funcione, el token debe tener permisos `data.records:write` y `data.records:read`.

El token se usa server-side desde WordPress y no se expone en `window.MS_DONACIONES`.

Los nombres de columnas deben coincidir exactamente con Airtable, incluyendo tildes, espacios y mayusculas. La tabla de Airtable deberia tener al menos las columnas que se configuren en el mapeo, por ejemplo:

```txt
Nombre
Apellido
Email
DNI
Telefono
```


Para generar el Personal Access Token, usar la guia oficial de Airtable:

```txt
https://support.airtable.com/docs/creating-personal-access-tokens
```

Scopes recomendados para este MVP:

```txt
data.records:write
data.records:read
```

Ademas, el token debe tener acceso al recurso/base donde se encuentra la tabla.

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
  "monto": "",
  "metodo": "",
  "crm_event": "step_1_completed"
}
```

Respuesta esperada:

```json
{
  "success": true,
  "message": "Datos recibidos correctamente",
  "crm_result": {
    "enabled": true,
    "success": true,
    "status": 200,
    "message": "Datos enviados a Airtable."
  }
}
```

Si el CRM esta desactivado, el endpoint igualmente responde correctamente y devuelve `crm_result.enabled` en `false`.

## Mercado Pago

El plugin crea preferencias de Mercado Pago Checkout Pro desde el endpoint:

```txt
POST /wp-json/donacion/v1/crear-preferencia
```

La configuracion se realiza desde:

```txt
Donaciones MS > Mercado Pago
```

Campos requeridos:

- Access Token de Mercado Pago (`TEST-...` para pruebas o `APP_USR-...` para produccion).
- Titulo del item.
- Descriptor.
- URLs de exito, fallo y pendiente.

El Access Token se usa server-side desde WordPress y no se expone en `window.MS_DONACIONES`.

El endpoint devuelve `init_point` y el frontend redirige al donante a Mercado Pago.

La seccion incluye un boton para probar la conexion guardada con Mercado Pago. Si la conexion no esta validada, la opcion Mercado Pago aparece deshabilitada en el formulario con el texto "No disponible por el momento".

## Archivos principales

### `ms-donaciones.php`

Archivo principal del plugin. Define constantes, carga clases e inicializa:

- Shortcodes
- REST API
- Admin panel
- Pagina de equipo

### `includes/class-shortcodes.php`

Registra:

```txt
[formulario_donacion]
```

Tambien carga React, ReactDOM, Babel y `assets/donacion.js`.

Ademas pasa la configuracion publica del admin al frontend mediante:

```php
wp_localize_script()
```

como variable global:

```js
window.MS_DONACIONES
```

Las credenciales de Airtable no se pasan al frontend.

### `includes/class-admin.php`

Define el panel de administracion `Donaciones MS`.

Permite editar los textos, montos, mensajes de impacto, datos bancarios y configuracion CRM.

### `includes/class-rest.php`

Define el endpoint REST:

```txt
/wp-json/donacion/v1/guardar
```

Sanitiza los datos recibidos y, si esta activado, los envia a Airtable.

### `includes/class-about.php`

Define la subpagina `Equipo` dentro del admin del plugin.

Muestra integrantes, informacion institucional y la version actual del plugin.

### `assets/donacion.js`

Contiene el formulario React embebido.

Lee la configuracion publica desde:

```js
window.MS_DONACIONES.labels
```

## Notas de desarrollo

El formulario actual usa React 18 y Babel desde CDN para facilitar la integracion rapida dentro de WordPress.

A futuro se recomienda compilar el frontend con un build step y reemplazar Babel en navegador por un bundle estatico.

## Pendientes

- Separar CSS a `assets/donacion.css`.
- Agregar validaciones REST mas estrictas.
- Agregar tests o validaciones automatizadas.
