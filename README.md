# ingsoft3

Rediseño del flujo de donación única para [modulosanitario.org](https://modulosanitario.org), una ONG argentina que construye baños dignos para familias sin acceso a saneamiento básico.

---

## Stack de la ONG

| Capa | Tecnología |
|------|-----------|
| Pagina web | WordPress |
| Formularios | Formidable Forms Business |
| Donaciones unicas | WooCommerce + Mercado Pago |
| Integración forms↔productos | YayCommerce / FormWoo |
| Donaciones mensuales | Salesforce + Debi (Prisma) |

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
                    # → wp-content/themes/[tema-activo]/donacion/donacion.js

functions.php       # Fragmento para agregar al functions.php del tema WordPress
                    # → wp-content/themes/[tema-activo]/functions.php (al final del archivo)

inicio.html         # Mock de página de inicio
                    # → crear una página en WordPress, pegar el contenido como bloque HTML

README.md

En mi caso el [tema-activo] es twentytwentyfive
```

---

## Instalación en WordPress

**1. Subir el archivo React al tema:**
```
wp-content/themes/[tema-activo]/donacion/donacion.js
```

**2. Agregar al `functions.php` del tema:**


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

## Vista previa

### Paso 1 — Pagina inicio
![inicio](inicio.png)

### Pagina donar
![donar](donar.png)

### Admin — Formulario en Formidable
![formulario](formulario.png)


## Pendientes

- [ ] Conectar Mercado Pago Checkout Bricks con monto dinámico real
- [ ] Paso 4 post-pago: motivación + localidad
- [ ] Ajustar selectores CSS al tema real de la ONG (no es `twentytwentyfive`)
- [ ] Definir flujo de donaciones mensuales desde el nuevo formulario
- [ ] Evaluar si WooCommerce se mantiene para historial de pedidos

---

## Desarrollo local

Probado con [LocalWP](https://localwp.com) — WordPress 7.0, PHP 8.2, MySQL 8.4.
