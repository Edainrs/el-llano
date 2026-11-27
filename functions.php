<?php
/**
 * Tema El Llano — funciones principales.
 *
 * @package el-llano
 */

defined( 'ABSPATH' ) || exit;

define( 'ELL_VERSION', wp_get_theme( get_template() )->get( 'Version' ) ?: '1.0.0' );
define( 'ELL_DIR', get_template_directory() );
define( 'ELL_URI', get_template_directory_uri() );

require ELL_DIR . '/inc/icons.php';
require ELL_DIR . '/inc/helpers.php';
require ELL_DIR . '/inc/post-types.php';
require ELL_DIR . '/inc/meta-boxes.php';
require ELL_DIR . '/inc/order.php';
require ELL_DIR . '/inc/settings.php';
require ELL_DIR . '/inc/menus.php';
require ELL_DIR . '/inc/blocks.php';
require ELL_DIR . '/inc/cf7.php';
require ELL_DIR . '/inc/demo.php';

/**
 * Soportes del tema.
 */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array( 'height' => 96, 'width' => 320, 'flex-width' => true, 'flex-height' => true ) );
	add_theme_support( 'responsive-embeds' );
	remove_theme_support( 'core-block-patterns' );
	add_editor_style( 'assets/css/theme.css' );
	add_post_type_support( 'page', 'excerpt' );
} );

/**
 * Estilos y scripts del sitio.
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'el-llano', ELL_URI . '/assets/css/theme.css', array(), ELL_VERSION );
	wp_enqueue_script( 'el-llano', ELL_URI . '/assets/js/front.js', array(), ELL_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );
} );

/**
 * Seguridad básica: sin edición de archivos desde el panel y sin XML-RPC.
 */
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}
add_filter( 'xmlrpc_enabled', '__return_false' );
remove_action( 'wp_head', 'wp_generator' );

/**
 * Las secciones prediseñadas se agrupan en una categoría propia.
 */
add_action( 'init', function () {
	register_block_pattern_category( 'el-llano', array( 'label' => __( 'El Llano — Secciones', 'el-llano' ) ) );
	register_block_style( 'core/cover', array( 'name' => 'ola', 'label' => __( 'Con curva inferior', 'el-llano' ) ) );
	register_block_style( 'core/group', array( 'name' => 'tarjeta', 'label' => __( 'Tarjeta', 'el-llano' ) ) );
	register_block_style( 'core/group', array( 'name' => 'tarjeta-oscura', 'label' => __( 'Tarjeta oscura', 'el-llano' ) ) );
	register_block_style( 'core/list', array( 'name' => 'check', 'label' => __( 'Con ticket', 'el-llano' ) ) );
	register_block_style( 'core/button', array( 'name' => 'blanco', 'label' => __( 'Blanco', 'el-llano' ) ) );
	register_block_style( 'core/button', array( 'name' => 'whatsapp', 'label' => __( 'WhatsApp', 'el-llano' ) ) );
	register_block_style( 'core/button', array( 'name' => 'contorno-claro', 'label' => __( 'Contorno claro', 'el-llano' ) ) );
} );

/**
 * Enlaces y textos que se actualizan solos en el contenido:
 *  #whatsapp → enlace de WhatsApp · #llamar → teléfono principal · {año} → año actual · {telefono} → teléfono principal
 */
add_filter( 'render_block', function ( $html, $block ) {
	if ( ! in_array( $block['blockName'], array( 'core/button', 'core/paragraph', 'core/list-item', 'core/heading' ), true ) ) {
		return $html;
	}
	if ( false === strpos( $html, '#whatsapp' ) && false === strpos( $html, '#llamar' ) && false === strpos( $html, '{' ) ) {
		return $html;
	}
	return strtr( $html, array(
		'href="#whatsapp"' => 'href="' . esc_url( ell_whatsapp_url() ) . '" target="_blank" rel="noopener"',
		'href="#llamar"'   => 'href="' . esc_attr( ell_tel( ell_dato( 'telefono_principal' ) ) ) . '"',
		'{año}'            => esc_html( wp_date( 'Y' ) ),
		'{telefono}'       => esc_html( ell_dato( 'telefono_principal' ) ),
	) );
}, 10, 2 );
