<?php
/**
 * Integración con Contact Form 7: campos [servicio] y [profesional] que se llenan solos.
 *
 * Uso en el formulario:
 *   [servicio* servicio]      → lista de "Datos del centro › Formulario de horas"
 *   [profesional profesional] → profesionales con "Mostrar en el formulario" activado
 *
 * @package el-llano
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wpcf7_init', function () {
	wpcf7_add_form_tag( array( 'servicio', 'servicio*' ), 'ell_cf7_servicio', array( 'name-attr' => true ) );
	wpcf7_add_form_tag( array( 'profesional', 'profesional*' ), 'ell_cf7_profesional', array( 'name-attr' => true ) );
} );

/**
 * Opciones de profesionales.
 */
function ell_cf7_profesionales() {
	$out = array();
	foreach ( ell_items( 'ell_profesional' ) as $p ) {
		if ( ell_meta( $p->ID, 'en_formulario' ) ) {
			$out[] = $p->post_title;
		}
	}
	return $out;
}

/**
 * Select genérico con el marcado y la validación de CF7.
 *
 * @param WPCF7_FormTag $tag     Etiqueta.
 * @param array         $options [valor, ayuda].
 * @param string        $first   Primera opción.
 */
function ell_cf7_select( $tag, $options, $first ) {
	if ( empty( $tag->name ) ) {
		return '';
	}
	$error = wpcf7_get_validation_error( $tag->name );
	$class = wpcf7_form_controls_class( $tag->type ) . ' wpcf7-select' . ( $error ? ' wpcf7-not-valid' : '' );
	$atts  = array(
		'name'          => $tag->name,
		'class'         => $class,
		'id'            => $tag->get_id_option(),
		'aria-required' => $tag->is_required() ? 'true' : 'false',
		'aria-invalid'  => $error ? 'true' : 'false',
	);
	$html  = '<option value="">' . esc_html( $first ) . '</option>';
	$help  = false;
	foreach ( $options as $o ) {
		$html .= '<option value="' . esc_attr( $o[0] ) . '"' . ( $o[1] ? ' data-ayuda="' . esc_attr( $o[1] ) . '"' : '' ) . '>' . esc_html( $o[0] ) . '</option>';
		$help  = $help || '' !== $o[1];
	}
	$out = sprintf( '<span class="wpcf7-form-control-wrap" data-name="%1$s"><select %2$s>%3$s</select>%4$s</span>', esc_attr( $tag->name ), wpcf7_format_atts( $atts ), $html, $error );
	if ( $help ) {
		$out .= '<small class="ell-ayuda" aria-live="polite"></small>';
	}
	return $out;
}

function ell_cf7_servicio( $tag ) {
	return ell_cf7_select( $tag, ell_pairs( ell_dato( 'servicios' ) ), 'Elige un servicio' );
}

function ell_cf7_profesional( $tag ) {
	$opts = array_map( function ( $n ) {
		return array( $n, '' );
	}, ell_cf7_profesionales() );
	return ell_cf7_select( $tag, $opts, 'Sin preferencia' );
}

/**
 * Validación: solo se aceptan valores de la lista.
 */
foreach ( array( 'servicio', 'profesional' ) as $ell_t ) {
	foreach ( array( $ell_t, $ell_t . '*' ) as $ell_tag_type ) {
		add_filter( 'wpcf7_validate_' . $ell_tag_type, function ( $result, $tag ) {
			$value   = isset( $_POST[ $tag->name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $tag->name ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			$allowed = 'servicio' === $tag->basetype ? wp_list_pluck( array_map( function ( $p ) {
				return array( 'n' => $p[0] );
			}, ell_pairs( ell_dato( 'servicios' ) ) ), 'n' ) : ell_cf7_profesionales();
			if ( $tag->is_required() && '' === $value ) {
				$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
			} elseif ( '' !== $value && ! in_array( $value, $allowed, true ) ) {
				$result->invalidate( $tag, 'Elige una opción de la lista.' );
			}
			return $result;
		}, 10, 2 );
	}
}

/**
 * Texto del formulario que crea el contenido de ejemplo.
 */
function ell_cf7_form_template() {
	return '<div class="ell-form-grid">
<p><label>Nombre completo<br>[text* nombre autocomplete:name placeholder "Ej: María González"]</label></p>
<p><label>Teléfono<br>[tel* telefono autocomplete:tel placeholder "+56 9 ..."]</label></p>
<p><label>Correo<br>[email correo autocomplete:email placeholder "correo@ejemplo.cl"]</label></p>
<p><label>Servicio<br>[servicio* servicio]</label></p>
</div>
<p><label>Profesional<br>[profesional profesional]</label></p>
<p><label>Mensaje<br>[textarea mensaje x4 placeholder "Cuéntanos qué necesitas o tu disponibilidad"]</label></p>
<p class="ell-form-legal">[acceptance privacidad] Acepto que el centro use estos datos solo para contactarme por mi solicitud de hora. [/acceptance]</p>
<p>[submit "Enviar solicitud"]</p>';
}

/**
 * [ell-formulario]: muestra el formulario de horas configurado.
 */
add_shortcode( 'ell-formulario', function () {
	$id = (int) get_option( 'ell_cf7_form_id' );
	if ( ! function_exists( 'wpcf7_contact_form' ) ) {
		return current_user_can( 'edit_pages' ) ? '<p class="ell-vacio">Activa el plugin <strong>Contact Form 7</strong> y vuelve a cargar el contenido de ejemplo para mostrar el formulario.</p>' : '';
	}
	if ( ! $id || ! wpcf7_contact_form( $id ) ) {
		return current_user_can( 'edit_pages' ) ? '<p class="ell-vacio">No se encontró el formulario. Créalo con <strong>Datos del centro › Cargar contenido de ejemplo</strong>.</p>' : '';
	}
	return do_shortcode( '[contact-form-7 id="' . $id . '"]' );
} );
