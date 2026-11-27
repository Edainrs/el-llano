# Tema El Llano — Centro Oftalmológico El Llano

Tema de bloques (Gutenberg) autoadministrable. No necesita Avada ni otros constructores.

- **Requiere:** WordPress 6.9 o superior (probado en 7.1.5) y PHP 8.0 o superior.
- **Plugin recomendado:** Contact Form 7, para el formulario de horas.

---

## 1. Instalación

1. Instala y activa **Contact Form 7** en **Plugins › Añadir nuevo**.
2. En **Apariencia › Temas › Añadir nuevo › Subir tema**, sube `el-llano.zip` y actívalo.
3. Ve a **Ajustes › Generales** y elige **Español de Chile** como idioma del sitio, para que el panel y los mensajes del formulario queden en español.
4. Ve a **Datos del centro** y presiona **Cargar contenido de ejemplo**. Esto crea:
   - las páginas Inicio, Nosotros, Especialidades, Exámenes y Contacto y agenda, con la portada en Inicio;
   - los menús de la cabecera y del pie (Apariencia › Menús);
   - las fichas de Equipo (12), Exámenes (8) y Especialidades (4);
   - el formulario "Solicitud de hora" de Contact Form 7.

   No borra nada. Si una página o ficha ya existe con el mismo nombre, la omite.

5. Revisa **Datos del centro** (teléfonos, horario, WhatsApp, mapa) y guarda.

> Las direcciones de las páginas (`/nosotros/`, `/especialidades/`, `/examenes/`, `/contacto/`) se usan en enlaces internos (tarjetas, etiquetas de especialidades, botones). Si cambias el **slug** de una página, esos enlaces dejan de funcionar.

## Actualizaciones del tema (GitHub + WP Pusher)

- **Repositorio:** el tema vive en `github.com/Edainrs/el-llano` y se instala y actualiza con **WP Pusher**.
- **No edites los archivos del tema en el servidor:** se sobrescriben en cada actualización.
- **Plantillas:** no modifiques la cabecera, el pie ni las plantillas desde **Apariencia › Editor**. Esa copia queda guardada en la base de datos y tapa las versiones nuevas del tema. Si ya ocurrió, en el Editor usa **Restablecer** en esa plantilla.
- **Versión:** cada versión nueva sube el número en `style.css`. Así los navegadores descargan los estilos nuevos.

---

## 2. Qué se administra y dónde

| Quiero cambiar… | Dónde |
|---|---|
| Teléfonos, dirección, horario, WhatsApp, aviso de urgencias, mapa | **Datos del centro** |
| Opciones del campo "Servicio" del formulario y sus explicaciones | **Datos del centro › Formulario de horas** |
| Imagen de fondo por defecto de los títulos de página | **Datos del centro › Imágenes** |
| Agregar, editar u ocultar un profesional | **Equipo** |
| Agregar, editar u ocultar un examen | **Exámenes** |
| Agregar, editar u ocultar una especialidad | **Especialidades** |
| Orden en que aparecen | Arrastra las filas (icono ☰) en el listado de Equipo, Exámenes o Especialidades |
| Textos de cada página, preguntas frecuentes | **Páginas** › editar la página |
| Imagen de la portada de Inicio | **Páginas › Inicio** › clic en la portada › *Reemplazar* |
| Imagen del título de una página interna | Editar la página › panel lateral › **Imagen destacada** |
| Subtítulo bajo el título de una página interna | Editar la página › panel lateral › **Extracto** |
| Logo (cabecera y pie) | **Datos del centro › Identidad** |
| Menú de la cabecera y enlaces del pie | **Apariencia › Menús** (o **Datos del centro › Menús del sitio**) |
| Pie de página | **Apariencia › Editor › Patrones › Pie de página** |

### Fichas
- **Ocultar sin borrar:** cambia el estado a **Borrador**.
- **Texto pendiente:** si escribes un texto entre corchetes, por ejemplo `[Atiende desde los X años]`, se muestra resaltado en amarillo en el sitio. Sirve para marcar lo que falta confirmar.
- **Relaciones:** en cada especialidad se marcan sus especialistas y exámenes, y se ordenan arrastrándolos. Los enlaces entre páginas se arman solos.
- **Formulario:** un profesional aparece en el campo "Profesional" solo si tiene activada la casilla *Mostrar en el formulario*.
- **Inicio:** solo aparecen los exámenes con la casilla *Destacar en la página de Inicio* (hasta 4).

### Menús
El tema usa los menús clásicos de **Apariencia › Menús**, con tres ubicaciones:

- **Cabecera — menú principal.** Admite un nivel de submenú: al arrastrar un elemento un poco a la derecha, queda dentro del anterior. En computador se despliega al pasar el mouse; en celular aparece indentado.
- **Pie de página — Navegación**
- **Pie de página — Información**

Para cambiar qué menú va en cada lugar, usa la pestaña **Gestionar ubicaciones**. Si una ubicación no tiene menú asignado, se muestran enlaces por defecto a las páginas del sitio. Los títulos "Navegación" e "Información" del pie se editan en **Apariencia › Editor › Patrones › Pie de página**.

### Acordeones
- **Exámenes:** se arman solos a partir de las fichas. Al abrir uno se cierran los demás, y ninguno parte abierto.
- **Preguntas frecuentes:** usan el bloque **Acordeón** de WordPress y se editan directamente en la página.
- **Enlace directo a un examen:** `/examenes/#ex-nombre-del-examen` abre ese examen.

### Enlaces que se actualizan solos
Dentro de botones y párrafos se pueden usar estos atajos:
- `#whatsapp` como enlace: abre WhatsApp con el número de *Datos del centro*.
- `#llamar` como enlace: llama al teléfono principal.
- `{telefono}`: muestra el teléfono principal.
- `{año}`: muestra el año actual.

---

## 3. Formulario de horas (Contact Form 7)

El tema agrega dos campos propios a Contact Form 7:

- `[servicio* servicio]`: lista de servicios definida en *Datos del centro*. Al elegir uno, aparece su explicación.
- `[profesional profesional]`: lista de profesionales con *Mostrar en el formulario* activado.

En la página de contacto, el formulario se muestra con el atajo `[ell-formulario]`.

**Recomendaciones:**
- **No instales Flamingo** ni plugins que guarden los envíos en la base de datos. Así, las solicitudes solo llegan por correo.
- **Correo saliente:** configura un plugin SMTP (por ejemplo WP Mail SMTP) con la cuenta de correo del centro, para que los mensajes no caigan en spam.
- **Destinatario:** por defecto es el correo del administrador. Se cambia en **Contacto › Solicitud de hora › Correo**.
- **Spam:** agrega Cloudflare Turnstile o reCAPTCHA (integraciones de Contact Form 7).

---

## 4. Seguridad incluida

- Edición de archivos desde el panel desactivada (`DISALLOW_FILE_EDIT`).
- XML-RPC desactivado.
- Todo lo que se guarda se limpia, y todo lo que se muestra se escapa.
- Formularios del panel con verificación (nonce) y permisos.
- El campo del mapa solo acepta enlaces para incrustar de Google Maps.
- El formulario solo acepta servicios y profesionales de la lista.

**Recomendado en el sitio:**
- El cliente con rol **Editor**: puede administrar todo lo anterior, pero no instalar plugins.
- Verificación en dos pasos para entrar al panel.
- Copias de seguridad automáticas.
- Actualizaciones al día.

---

## 5. Estructura del tema

```
el-llano/
├── theme.json            Colores, tipografía (Manrope), tamaños y botones globales
├── functions.php
├── inc/
│   ├── post-types.php    Equipo, Exámenes, Especialidades y sus campos
│   ├── meta-boxes.php    Pantallas de edición
│   ├── order.php         Orden arrastrable
│   ├── settings.php      Datos del centro
│   ├── menus.php         Menús clásicos (cabecera y pie)
│   ├── blocks.php        Bloques dinámicos (equipo, exámenes, especialidades, etc.)
│   ├── cf7.php           Campos [servicio] y [profesional]
│   ├── demo.php          Contenido de ejemplo
│   ├── helpers.php
│   └── icons.php
├── parts/                Cabecera y pie
├── templates/            Plantillas de página
├── patterns/             18 secciones prediseñadas (categoría "El Llano — Secciones")
└── assets/               CSS, JS, fuentes e imágenes de relleno
```

La tipografía Manrope va incluida en el tema (licencia SIL Open Font License), así que no se carga desde Google Fonts.
