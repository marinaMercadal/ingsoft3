# Guía de conexión con Salesforce — ms-donaciones

Esta guía explica cómo conectar el plugin con Salesforce desde cero.
Al terminar, cada vez que alguien complete el Paso 1 del formulario de donación, sus datos van a aparecer automáticamente como un **Contacto** en Salesforce.

---

## Paso 1 — Crear una cuenta de Salesforce Developer Edition (gratis)

Si ya tenés una cuenta de Salesforce, saltá al Paso 2.

1. Entrá a: https://developer.salesforce.com/signup
2. Completá el formulario → revisá tu email → verificá la cuenta
3. Elegí una contraseña → accedés a tu org de desarrollo

---

## Paso 2 — Encontrar las URLs de tu org

Cuando entrás a Salesforce vas a ver dos URLs distintas según dónde estés:

- **URL de la interfaz** (lo que ves en el navegador): `https://tudominio.develop.lightning.force.com`
- **URL de login/API** (la que usa WordPress): `https://tudominio.develop.my.salesforce.com`

Son el mismo org pero con dominios distintos. Para el plugin necesitás la segunda (`my.salesforce.com`).

**Cómo encontrar tu URL de API:**
Estando en Salesforce, reemplazá `lightning.force.com` por `my.salesforce.com` en la barra del navegador.
Ejemplo:
```
Interfaz:  https://orgfarm-5780dccadb-dev-ed.develop.lightning.force.com/...
API:       https://orgfarm-5780dccadb-dev-ed.develop.my.salesforce.com
```
Copiá esa URL (sin nada después del `.com`). La vas a usar en el Paso 5.

---

## Paso 3 — Crear la aplicación cliente externa

1. En Salesforce, hacé clic en el engranaje ⚙️ → **Configuración**
2. En el buscador escribí `aplicacion cliente` → clic en **Gestor de aplicaciones cliente externas**
3. Clic en **Nueva aplicación cliente externa**
4. Completá:
   - **Nombre**: `ms-donaciones`
   - **Nombre de API**: se completa solo
   - **Email de contacto**: tu email
5. En **Flujos de OAuth y mejoras de aplicaciones cliente externas**:
   - Tildá **Activar flujo de credenciales de cliente**
   - **Ejecutar como (nombre de usuario)** → seleccioná tu usuario admin
6. En **Configuración de OAuth**:
   - Tildá **Habilitar configuración de OAuth**
   - **URL de devolución de llamada**: `https://localhost`
   - **Alcances de OAuth seleccionados**: agregá:
     - `Administrar datos de usuario mediante API (api)`
     - `Realizar solicitudes en cualquier momento (refresh_token, offline_access)`
7. Clic en **Guardar**

> ⏳ Salesforce puede tardar hasta 10 minutos en activar la app.

---

## Paso 4 — Copiar las credenciales

1. **Gestor de aplicaciones cliente externas** → clic en `ms-donaciones`
2. En **Detalles de consumidor**:
   - Copiá la **Clave de consumidor**
   - Copiá la **Pregunta secreta de consumidor**

---

## Paso 5 — Configurar WordPress

1. WordPress → **Donaciones MS** → tab **Datos personales a CRM**
2. Completá:

| Campo | Qué poner |
|-------|-----------|
| Activar envío a Salesforce | ✅ tildado |
| Usar sandbox de Salesforce | Sin tildar |
| URL/Dominio de login | Tu URL de API del Paso 2. Ej: `https://orgfarm-5780dccadb-dev-ed.develop.my.salesforce.com` |
| Consumer Key | **Clave de consumidor** del Paso 4 |
| Consumer Secret | **Pregunta secreta de consumidor** del Paso 4 |
| API Name: Nombre | `FirstName` (no cambiar) |
| API Name: Apellido | `LastName` (no cambiar) |
| API Name: Email | `Email` (no cambiar) |
| API Name: Telefono | `MobilePhone` (no cambiar) |
| API Name: DNI | Dejalo vacío por ahora |
| Stage de Oportunidad | `Closed Won` (no cambiar) |

3. Clic en **Guardar esta sección**
4. Clic en **Probar conexión con Salesforce** → tiene que decir **"Conexión válida"**

---

## Paso 6 — Testear

1. Abrí el formulario de donación en el frontend de WordPress
2. Completá el Paso 1 (nombre, apellido, email, DNI, teléfono) → clic en Continuar
3. Aparece el modal de confirmación ✅

**Verificar en Salesforce usando el Developer Console:**

1. En Salesforce → engranaje ⚙️ → **Developer Console**
2. Clic en **Query Editor** (abajo)
3. Pegá esta consulta reemplazando el email:
```sql
SELECT Id, FirstName, LastName, Email, CreatedDate
FROM Contact
WHERE Email = 'el-email-que-usaste@ejemplo.com'
ORDER BY CreatedDate DESC
```
4. Clic en **Execute**
5. Si aparece una fila con los datos → funcionó ✅
6. Podés hacer clic en el Id para ver el registro completo

---

## Errores comunes

| Error | Solución |
|-------|----------|
| `request not supported on this domain` | La URL/Dominio de login está vacía o usa `lightning.force.com` en lugar de `my.salesforce.com` |
| `authentication failure` | La Clave de consumidor o Pregunta secreta están mal copiadas |
| Faltan credenciales | Consumer Key y/o Consumer Secret están vacíos en WordPress |
