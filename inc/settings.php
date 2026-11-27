<?php
/**
 * Pantalla "Datos del centro".
 *
 * @package el-llano
 */

defined( 'ABSPATH' ) || exit;

/**
 * Campos de la pantalla, agrupados.
 */
function ell_settings_fields() {
	return array(
		'Identidad'          => array(
			'logo'           => array( 'Logo', 'image', 'Opcional. Si lo subes, reemplaza al ícono y al nombre en la cabecera. Ideal: PNG o SVG con fondo transparente, unos 320 × 96 px.' ),
			'logo_pie'       => array( 'Logo para el pie de página', 'image', 'Opcional. Versión clara del logo para el fondo azul del pie. Si no lo subes, se usa el ícono y el nombre.' ),
			'linea_superior' => array( 'Línea superior del nombre', 'text', 'Se muestra pequeña sobre el nombre cuando no hay logo.' ),
			'nombre'         => array( 'Nombre', 'text', '' ),
			'descripcion'    => array( 'Descripción breve (pie de página)', 'textarea', '' ),
		),
		'Contacto'           => array(
			'direccion'          => array( 'Dirección', 'text', '' ),
			'comuna'             => array( 'Comuna', 'text', '' ),
			'telefono_principal' => array( 'Teléfono principal', 'text', 'Se usa en los botones de llamar y en el aviso de urgencias.' ),
			'telefonos'          => array( 'Todos los teléfonos', 'textarea', 'Uno por línea.' ),
			'whatsapp'           => array( 'WhatsApp', 'text', 'Con código de país, ej: +56 9 6333 7646.' ),
			'mapa_url'           => array( 'Enlace para incrustar el mapa', 'text', 'En Google Maps: Compartir › Incorporar un mapa › copia solo la dirección que aparece dentro de src="…".' ),
		),
		'Horario'            => array(
			'horario'       => array( 'Horario de atención', 'textarea', 'Una línea por día o rango, con el formato Días|Horas. Ej: Sábados|09:00 a 14:45 horas' ),
			'horario_corto' => array( 'Horario abreviado (barra superior)', 'text', '' ),
		),
		'Urgencias'          => array(
			'urgencias_activo' => array( 'Mostrar el aviso de urgencias', 'checkbox', '' ),
			'urgencias_titulo' => array( 'Título del aviso', 'text', '' ),
			'urgencias_texto'  => array( 'Texto del aviso', 'textarea', '' ),
		),
		'Formulario de horas' => array(
			'servicios' => array( 'Servicios', 'textarea', 'Uno por línea, con el formato Nombre|Explicación breve. La explicación aparece bajo el campo al elegirlo.' ),
		),
		'Imágenes'           => array(
			'imagen_titulo' => array( 'Imagen de fondo por defecto de los títulos de página', 'image', 'Se usa cuando una página no tiene imagen destacada. Ideal: 1920 × 500 px.' ),
		),
	);
}

add_action( 'admin_menu', function () {
	add_menu_page( 'Datos del centro', 'Datos del centro', 'edit_pages', 'ell-datos', 'ell_render_settings', 'dashicons-building', 20 );
	// Acceso directo al editor del menú (en los temas de bloques no existe Apariencia › Menús).
	add_submenu_page( 'themes.php', 'Menú del sitio', 'Menú del sitio', 'edit_theme_options', 'site-editor.php?p=%2Fnavigation' );
	add_submenu_page( 'ell-datos', 'Datos del centro', 'Datos del centro', 'edit_pages', 'ell-datos', 'ell_render_settings' );
	add_submenu_page( 'ell-datos', 'Menú del sitio', 'Menú del sitio', 'edit_theme_options', 'site-editor.php?p=%2Fnavigation' );
} );

/**
 * Guardado.
 */
add_action( 'admin_post_ell_guardar_datos', function () {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( 'Sin permisos.' );
	}
	check_admin_referer( 'ell_guardar_datos' );
	$in  = isset( $_POST['ell_datos'] ) && is_array( $_POST['ell_datos'] ) ? wp_unslash( $_POST['ell_datos'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$out = array();
	foreach ( ell_settings_fields() as $group ) {
		foreach ( $group as $key => $f ) {
			$raw = $in[ $key ] ?? '';
			switch ( $f[1] ) {
				case 'checkbox':
					$out[ $key ] = empty( $raw ) ? 0 : 1;
					break;
				case 'textarea':
					$out[ $key ] = sanitize_textarea_field( (string) $raw );
					break;
				case 'image':
					$out[ $key ] = absint( $raw );
					break;
				default:
					$out[ $key ] = 'mapa_url' === $key ? esc_url_raw( (string) $raw ) : sanitize_text_field( (string) $raw );
			}
		}
	}
	if ( $out['mapa_url'] && ! preg_match( '#^https://(www\.)?google\.[a-z.]+/maps/embed#', $out['mapa_url'] ) ) {
		$out['mapa_url'] = '';
	}
	update_option( 'ell_datos', $out );
	wp_safe_redirect( admin_url( 'admin.php?page=ell-datos&guardado=1' ) );
	exit;
} );

/**
 * Pantalla.
 */
function ell_render_settings() {
	$d = ell_dato();
	echo '<div class="wrap ell-settings"><h1>Datos del centro</h1>';
	echo '<p>Estos datos se usan en todo el sitio: barra superior, pie de página, contacto, botones y formulario. Si cambias algo aquí, se actualiza en todas partes.</p>';
	if ( isset( $_GET['guardado'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		echo '<div class="notice notice-success is-dismissible"><p>Datos guardados.</p></div>';
	}
	if ( isset( $_GET['demo'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		echo '<div class="notice notice-success is-dismissible"><p>Contenido de ejemplo cargado. Revisa el sitio.</p></div>';
	}
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'ell_guardar_datos' );
	echo '<input type="hidden" name="action" value="ell_guardar_datos">';
	foreach ( ell_settings_fields() as $group => $fields ) {
		echo '<h2>' . esc_html( $group ) . '</h2><table class="form-table" role="presentation">';
		foreach ( $fields as $key => $f ) {
			$name = 'ell_datos[' . $key . ']';
			$val  = $d[ $key ] ?? '';
			echo '<tr><th scope="row"><label for="ell-' . esc_attr( $key ) . '">' . esc_html( $f[0] ) . '</label></th><td>';
			switch ( $f[1] ) {
				case 'textarea':
					$rows = in_array( $key, array( 'servicios' ), true ) ? 7 : 3;
					echo '<textarea class="large-text" rows="' . (int) $rows . '" id="ell-' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '">' . esc_textarea( $val ) . '</textarea>';
					break;
				case 'checkbox':
					echo '<label><input type="checkbox" id="ell-' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '" value="1" ' . checked( (bool) $val, true, false ) . '> Sí</label>';
					break;
				case 'image':
					$src = $val ? wp_get_attachment_image_url( (int) $val, 'medium' ) : '';
					echo '<div class="ell-image-field"><img src="' . esc_url( $src ) . '" alt="" style="' . ( $src ? '' : 'display:none;' ) . 'max-width:320px;height:auto;border-radius:6px;display:block;margin-bottom:8px">';
					echo '<input type="hidden" id="ell-' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $val ) . '">';
					echo '<button type="button" class="button ell-image-pick">Elegir imagen</button> <button type="button" class="button-link ell-image-remove">Quitar</button></div>';
					break;
				default:
					echo '<input type="text" class="regular-text" id="ell-' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $val ) . '">';
			}
			if ( $f[2] ) {
				echo '<p class="description">' . esc_html( $f[2] ) . '</p>';
			}
			echo '</td></tr>';
		}
		echo '</table>';
	}
	submit_button( 'Guardar datos' );
	echo '</form>';

	if ( current_user_can( 'manage_options' ) ) {
		echo '<hr><h2>Contenido de ejemplo</h2>';
		echo '<p>Crea las páginas (Inicio, Nosotros, Especialidades, Exámenes, Contacto y agenda), el menú, las fichas de Equipo, Exámenes y Especialidades, y el formulario de Contact Form 7 si está activo. No borra nada: si una página o ficha ya existe con el mismo nombre, la omite.</p>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'ell_demo' );
		echo '<input type="hidden" name="action" value="ell_demo">';
		submit_button( 'Cargar contenido de ejemplo', 'secondary' );
		echo '</form>';
	}
	echo '</div>';
}
