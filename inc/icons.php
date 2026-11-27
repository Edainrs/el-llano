<?php
/**
 * Íconos SVG del tema (trazo, 24x24).
 *
 * @package el-llano
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lista de íconos disponibles: slug => [etiqueta, contenido SVG].
 */
function ell_icons() {
	return array(
		'ojo'        => array( 'Ojo', '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>' ),
		'nino'       => array( 'Niño', '<circle cx="12" cy="6" r="3"/><path d="M12 9v6M8 21l4-6 4 6M7 12h10"/>' ),
		'presion'    => array( 'Presión / medidor', '<path d="M12 14l4-4"/><path d="M3.3 19a10 10 0 1 1 17.4 0"/>' ),
		'retina'     => array( 'Retina / diana', '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>' ),
		'tecnologia' => array( 'Tecnología', '<rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><path d="M9 1v3M15 1v3M9 20v3M15 20v3M20 9h3M20 14h3M1 9h3M1 14h3"/>' ),
		'corazon'    => array( 'Corazón', '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 0 0 0-7.8z"/>' ),
		'personas'   => array( 'Personas', '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>' ),
		'escudo'     => array( 'Escudo', '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>' ),
		'gota'       => array( 'Gota', '<path d="M12 2.7l5.7 5.6a8 8 0 1 1-11.3 0z"/>' ),
		'movimiento' => array( 'Movimiento ocular', '<circle cx="7" cy="12" r="3"/><circle cx="17" cy="12" r="3"/><path d="M10 12h4"/>' ),
		'telefono'   => array( 'Teléfono', '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>' ),
		'ubicacion'  => array( 'Ubicación', '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>' ),
		'reloj'      => array( 'Reloj', '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>' ),
		'calendario' => array( 'Calendario', '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>' ),
		'check'      => array( 'Ticket', '<circle cx="12" cy="12" r="10"/><path d="M8 12l3 3 5-6"/>' ),
		'alerta'     => array( 'Alerta', '<path d="M10.3 3.9L1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><path d="M12 9v4M12 17h.01"/>' ),
		'flecha'     => array( 'Flecha', '<path d="M5 12h14M12 5l7 7-7 7"/>' ),
		'chevron'    => array( 'Chevron', '<path d="M9 6l6 6-6 6"/>' ),
		'info'       => array( 'Información', '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>' ),
	);
}

/**
 * Devuelve el SVG de un ícono.
 *
 * @param string $slug  Ícono.
 * @param string $class Clase CSS.
 */
function ell_icon( $slug, $class = 'ell-icon' ) {
	if ( 'whatsapp' === $slug ) {
		return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M17.5 14.4c-.3-.1-1.8-.9-2-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.4-.5c.2-.2.2-.3.3-.5.1-.2 0-.4 0-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5 2.5 1 3 .8 3.6.7.5-.1 1.8-.7 2-1.4.2-.7.2-1.3.2-1.4-.1-.2-.3-.3-.6-.4zM12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2c-1.5 0-3-.4-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2z"/></svg>';
	}
	$icons = ell_icons();
	if ( ! isset( $icons[ $slug ] ) ) {
		$slug = 'ojo';
	}
	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $icons[ $slug ][1] . '</svg>';
}
