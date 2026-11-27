<?php
/**
 * Menús clásicos (Apariencia › Menús) para la cabecera y el pie.
 *
 * @package el-llano
 */

defined( 'ABSPATH' ) || exit;

/**
 * Ubicaciones de menú.
 */
function ell_menu_locations() {
	return array(
		'principal'       => 'Cabecera — menú principal',
		'pie-navegacion'  => 'Pie de página — Navegación',
		'pie-informacion' => 'Pie de página — Información',
	);
}

add_action( 'after_setup_theme', function () {
	register_nav_menus( ell_menu_locations() );
} );

/**
 * Enlaces de respaldo si todavía no hay un menú asignado.
 *
 * @param string $location Ubicación.
 */
function ell_menu_fallback_links( $location ) {
	$map = array(
		'principal'       => array( 'inicio' => 'Inicio', 'nosotros' => 'Nosotros', 'especialidades' => 'Especialidades', 'examenes' => 'Exámenes', 'contacto' => 'Contacto y agenda' ),
		'pie-navegacion'  => array( 'nosotros' => 'Nosotros', 'especialidades' => 'Especialidades', 'examenes' => 'Exámenes y procedimientos', 'nosotros#equipo' => 'Cuerpo médico' ),
		'pie-informacion' => array( 'contacto' => 'Contacto y agenda', 'examenes#preguntas' => 'Preguntas frecuentes', 'examenes#indicaciones' => 'Indicaciones previas' ),
	);
	$out = '';
	foreach ( $map[ $location ] ?? array() as $target => $label ) {
		list( $slug, $hash ) = array_pad( explode( '#', $target, 2 ), 2, '' );
		$url     = 'inicio' === $slug ? home_url( '/' ) : ell_page_url( $slug, $hash );
		$page    = 'inicio' === $slug ? (int) get_option( 'page_on_front' ) : ( get_page_by_path( $slug ) ? get_page_by_path( $slug )->ID : 0 );
		$current = ! $hash && $page && is_page( $page );
		$out    .= '<li class="menu-item' . ( $current ? ' current-menu-item' : '' ) . '"><a href="' . esc_url( $url ) . '"' . ( $current ? ' aria-current="page"' : '' ) . '>' . esc_html( $label ) . '</a></li>';
	}
	return $out;
}

/**
 * HTML de un menú clásico (solo los <li>).
 *
 * @param string $location Ubicación.
 */
function ell_menu_items( $location ) {
	if ( has_nav_menu( $location ) ) {
		return wp_nav_menu( array(
			'theme_location' => $location,
			'container'      => false,
			'items_wrap'     => '%3$s',
			'echo'           => false,
			'depth'          => 'principal' === $location ? 2 : 1,
			'fallback_cb'    => false,
		) );
	}
	return ell_menu_fallback_links( $location );
}

/**
 * Bloque "Menú": cabecera (con versión para celular) o lista del pie.
 *
 * @param array $a Atributos.
 */
function ell_render_menu( $a ) {
	$loc = $a['ubicacion'] ?? 'principal';
	if ( ! array_key_exists( $loc, ell_menu_locations() ) ) {
		$loc = 'principal';
	}
	$items = ell_menu_items( $loc );
	if ( 'principal' !== $loc ) {
		return '<ul class="ell-footer-menu">' . $items . '</ul>';
	}
	static $n = 0;
	$id = 'ell-menu-' . ( ++$n );
	$out  = '<nav class="ell-menu" aria-label="Menú principal">';
	$out .= '<button class="ell-menu__toggle" type="button" aria-expanded="false" aria-controls="' . esc_attr( $id ) . '"><span class="ell-menu__bars" aria-hidden="true"></span><span class="screen-reader-text">Abrir menú</span></button>';
	$out .= '<ul class="ell-menu__list" id="' . esc_attr( $id ) . '">' . $items . '</ul>';
	return $out . '</nav>';
}

/**
 * Acceso a Apariencia › Menús también desde "Datos del centro".
 */
add_action( 'admin_menu', function () {
	add_submenu_page( 'ell-datos', 'Menús del sitio', 'Menús del sitio', 'edit_theme_options', 'nav-menus.php' );
}, 20 );

/**
 * Crea los menús de ejemplo y los asigna a sus ubicaciones (solo si no hay uno asignado).
 *
 * @param array $ids IDs de páginas por slug.
 */
function ell_demo_menus( $ids ) {
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$defs      = array(
		'principal'       => array( 'Menú principal', array( array( 'inicio', 'Inicio', '' ), array( 'nosotros', 'Nosotros', '' ), array( 'especialidades', 'Especialidades', '' ), array( 'examenes', 'Exámenes', '' ), array( 'contacto', 'Contacto y agenda', '' ) ) ),
		'pie-navegacion'  => array( 'Pie — Navegación', array( array( 'nosotros', 'Nosotros', '' ), array( 'especialidades', 'Especialidades', '' ), array( 'examenes', 'Exámenes y procedimientos', '' ), array( 'nosotros', 'Cuerpo médico', 'equipo' ) ) ),
		'pie-informacion' => array( 'Pie — Información', array( array( 'contacto', 'Contacto y agenda', '' ), array( 'examenes', 'Preguntas frecuentes', 'preguntas' ), array( 'examenes', 'Indicaciones previas', 'indicaciones' ) ) ),
	);
	foreach ( $defs as $loc => $def ) {
		if ( ! empty( $locations[ $loc ] ) && wp_get_nav_menu_object( $locations[ $loc ] ) ) {
			continue;
		}
		$menu = wp_get_nav_menu_object( $def[0] );
		$menu_id = $menu ? $menu->term_id : wp_create_nav_menu( $def[0] );
		if ( is_wp_error( $menu_id ) ) {
			continue;
		}
		if ( ! $menu || ! wp_get_nav_menu_items( $menu_id ) ) {
			foreach ( $def[1] as $pos => $item ) {
				$page_id = (int) ( $ids[ $item[0] ] ?? 0 );
				if ( ! $page_id ) {
					continue;
				}
				if ( $item[2] ) {
					wp_update_nav_menu_item( $menu_id, 0, array(
						'menu-item-title'    => $item[1],
						'menu-item-url'      => get_permalink( $page_id ) . '#' . $item[2],
						'menu-item-type'     => 'custom',
						'menu-item-status'   => 'publish',
						'menu-item-position' => $pos + 1,
					) );
				} else {
					wp_update_nav_menu_item( $menu_id, 0, array(
						'menu-item-title'     => $item[1],
						'menu-item-object'    => 'page',
						'menu-item-object-id' => $page_id,
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
						'menu-item-position'  => $pos + 1,
					) );
				}
			}
		}
		$locations[ $loc ] = $menu_id;
	}
	set_theme_mod( 'nav_menu_locations', $locations );
}
