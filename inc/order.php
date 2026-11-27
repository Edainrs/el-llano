<?php
/**
 * Orden arrastrable en los listados del panel.
 *
 * @package el-llano
 */

defined( 'ABSPATH' ) || exit;

/**
 * Los listados del panel se ordenan por el orden definido.
 */
add_action( 'pre_get_posts', function ( $q ) {
	if ( ! is_admin() || ! $q->is_main_query() ) {
		return;
	}
	$type = $q->get( 'post_type' );
	if ( in_array( $type, array( 'ell_profesional', 'ell_examen', 'ell_especialidad' ), true ) && ! isset( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$q->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
		$q->set( 'posts_per_page', 200 );
	}
} );

/**
 * Guarda el nuevo orden (AJAX).
 */
add_action( 'wp_ajax_ell_orden', function () {
	check_ajax_referer( 'ell_orden', 'nonce' );
	$ids = isset( $_POST['ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['ids'] ) ) : array();
	foreach ( $ids as $i => $id ) {
		if ( ! $id || ! current_user_can( 'edit_post', $id ) ) {
			continue;
		}
		$type = get_post_type( $id );
		if ( ! in_array( $type, array( 'ell_profesional', 'ell_examen', 'ell_especialidad' ), true ) ) {
			continue;
		}
		wp_update_post( array( 'ID' => $id, 'menu_order' => $i + 1 ) );
	}
	wp_send_json_success();
} );

/**
 * Las fichas nuevas quedan al final.
 */
add_filter( 'wp_insert_post_data', function ( $data, $postarr ) {
	if ( in_array( $data['post_type'], array( 'ell_profesional', 'ell_examen', 'ell_especialidad' ), true ) && empty( $postarr['ID'] ) && empty( $data['menu_order'] ) ) {
		global $wpdb;
		$max                = (int) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(menu_order) FROM {$wpdb->posts} WHERE post_type = %s", $data['post_type'] ) );
		$data['menu_order'] = $max + 1;
	}
	return $data;
}, 10, 2 );
