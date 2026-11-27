<?php
/**
 * Funciones auxiliares.
 *
 * @package el-llano
 */

defined( 'ABSPATH' ) || exit;

/**
 * Valores por defecto de "Datos del centro".
 */
function ell_datos_defaults() {
	return array(
		'logo'                => 0,
		'logo_pie'            => 0,
		'linea_superior'      => 'Centro Oftalmológico',
		'nombre'              => 'El Llano',
		'descripcion'         => 'Atención oftalmológica integral, de alta calidad y con aranceles razonables.',
		'direccion'           => 'Llano Subercaseaux 3811',
		'comuna'              => 'San Miguel',
		'telefono_principal'  => '22 555 0727',
		'telefonos'           => "22 555 0727\n22 556 3581\n22 555 0638",
		'whatsapp'            => '+56 9 6333 7646',
		'horario'             => "Lunes a viernes|08:15 a 18:45 horas\nSábados|09:00 a 14:45 horas",
		'horario_corto'       => 'Lun a Vie 08:15 – 18:45 · Sáb 09:00 – 14:45',
		'urgencias_activo'    => 1,
		'urgencias_titulo'    => 'Atendemos urgencias oftalmológicas',
		'urgencias_texto'     => 'Si tienes una molestia o lesión en tus ojos, llámanos y te orientamos.',
		'mapa_url'            => '',
		'imagen_titulo'       => 0,
		'servicios'           => "Bono externo|Si ya compraste tu bono de atención fuera del centro (en Fonasa, tu Isapre o en línea).\nConsulta General Consalud|Consulta oftalmológica general con cobertura de Isapre Consalud.\nConsulta General Fonasa|Consulta oftalmológica general con cobertura Fonasa.\nConsulta General Particular|Consulta oftalmológica general sin previsión, con arancel particular.\nEvaluación|Evaluación con el especialista indicada por tu médico, por ejemplo, para definir un tratamiento o cirugía.\nGarantía Doctores|Control con el mismo doctor dentro del plazo de garantía de tu consulta anterior.",
	);
}

/**
 * Lee un dato del centro (o todos).
 *
 * @param string|null $key Clave.
 */
function ell_dato( $key = null ) {
	static $cache = null;
	if ( null === $cache ) {
		$saved = get_option( 'ell_datos', array() );
		$cache = wp_parse_args( is_array( $saved ) ? $saved : array(), ell_datos_defaults() );
	}
	if ( null === $key ) {
		return $cache;
	}
	return $cache[ $key ] ?? '';
}

/**
 * Convierte un texto con una entrada por línea en un arreglo limpio.
 *
 * @param string $text Texto.
 */
function ell_lines( $text ) {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
		$line = trim( $line );
		if ( '' !== $line ) {
			$out[] = $line;
		}
	}
	return $out;
}

/**
 * Líneas con formato "Etiqueta|Valor".
 *
 * @param string $text Texto.
 */
function ell_pairs( $text ) {
	$out = array();
	foreach ( ell_lines( $text ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		$out[] = array( $parts[0], $parts[1] ?? '' );
	}
	return $out;
}

/**
 * Dirección completa.
 */
function ell_direccion() {
	$d = ell_dato( 'direccion' );
	$c = ell_dato( 'comuna' );
	return $c ? $d . ', ' . $c : $d;
}

/**
 * Enlace tel: a partir de un número.
 *
 * @param string $phone Número.
 */
function ell_tel( $phone ) {
	$digits = preg_replace( '/[^\d+]/', '', $phone );
	if ( $digits && '+' !== $digits[0] ) {
		$digits = '+56' . $digits;
	}
	return 'tel:' . $digits;
}

/**
 * Enlace de WhatsApp.
 */
function ell_whatsapp_url() {
	$n = preg_replace( '/\D/', '', ell_dato( 'whatsapp' ) );
	return $n ? 'https://wa.me/' . $n : '';
}

/**
 * URL de una página del sitio a partir de su slug.
 *
 * @param string $slug Slug.
 * @param string $hash Ancla opcional.
 */
function ell_page_url( $slug, $hash = '' ) {
	$page = get_page_by_path( $slug );
	$url  = $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
	return $hash ? $url . '#' . $hash : $url;
}

/**
 * Obtiene elementos publicados de un tipo de contenido, en su orden.
 *
 * @param string $type Tipo.
 * @param array  $args Argumentos extra.
 */
function ell_items( $type, $args = array() ) {
	return get_posts(
		array_merge(
			array(
				'post_type'        => $type,
				'post_status'      => 'publish',
				'posts_per_page'   => -1,
				'orderby'          => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
				'suppress_filters' => false,
			),
			$args
		)
	);
}

/**
 * Obtiene elementos por ID respetando el orden recibido.
 *
 * @param array $ids IDs.
 */
function ell_items_by_ids( $ids ) {
	$out = array();
	foreach ( array_filter( array_map( 'absint', (array) $ids ) ) as $id ) {
		$p = get_post( $id );
		if ( $p && 'publish' === $p->post_status ) {
			$out[] = $p;
		}
	}
	return $out;
}

/**
 * Iniciales para el avatar de un profesional.
 *
 * @param string $name Nombre.
 */
function ell_initials( $name ) {
	$name  = preg_replace( '/^(Dra?\.|Tm\.|TM\.)\s*/u', '', $name );
	$words = array_values( array_filter( preg_split( '/\s+/u', $name ), function ( $w ) {
		return ! in_array( mb_strtolower( $w ), array( 'de', 'del', 'la', 'van', 'von' ), true );
	} ) );
	$ini = '';
	foreach ( array_slice( $words, 0, 2 ) as $w ) {
		$ini .= mb_strtoupper( mb_substr( $w, 0, 1 ) );
	}
	return $ini;
}

/**
 * Texto marcado como "por completar" (fondo amarillo) cuando empieza con [ y termina con ].
 *
 * @param string $text Texto.
 */
function ell_text( $text ) {
	$text = trim( (string) $text );
	if ( preg_match( '/^\[(.+)\]$/u', $text, $m ) ) {
		return '<span class="ell-pendiente">' . esc_html( $m[1] ) . '</span>';
	}
	return esc_html( $text );
}
