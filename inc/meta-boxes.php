<?php
/**
 * Pantallas de edición de las fichas (campos personalizados sin plugins).
 *
 * @package el-llano
 */

defined( 'ABSPATH' ) || exit;

add_action( 'add_meta_boxes', function () {
	$titles = array(
		'ell_profesional'  => 'Datos del profesional',
		'ell_examen'       => 'Datos del examen',
		'ell_especialidad' => 'Contenido de la especialidad',
	);
	foreach ( $titles as $type => $title ) {
		add_meta_box( 'ell_campos', $title, 'ell_render_meta_box', $type, 'normal', 'high' );
	}
	add_meta_box( 'ell_ayuda', 'Cómo se muestra', 'ell_render_help_box', array_keys( $titles ), 'side', 'low' );
} );

/**
 * Caja de ayuda lateral.
 */
function ell_render_help_box( $post ) {
	$help = array(
		'ell_profesional'  => 'Aparece en <strong>Nosotros</strong>, en las especialidades donde se marque y, si está activado, en el formulario de horas.<br><br>La foto se elige en el recuadro <strong>Foto</strong>. Si no hay foto, se muestran las iniciales.',
		'ell_examen'       => 'Aparece en el acordeón de <strong>Exámenes</strong>, en las especialidades donde se marque y, si está destacado, en <strong>Inicio</strong>.',
		'ell_especialidad' => 'Aparece en <strong>Especialidades</strong> y como tarjeta en <strong>Inicio</strong>.',
	);
	echo '<p>' . wp_kses_post( $help[ $post->post_type ] ?? '' ) . '</p>';
	echo '<p>Para ocultarlo sin borrarlo, cambia su estado a <strong>Borrador</strong>.</p>';
	echo '<p>Escribe un texto entre corchetes, por ejemplo <code>[Por confirmar]</code>, para que se vea resaltado en amarillo como pendiente.</p>';
	echo '<p>El orden se cambia arrastrando las filas en el listado.</p>';
}

/**
 * Formulario de campos.
 */
function ell_render_meta_box( $post ) {
	$fields = ell_fields()[ $post->post_type ] ?? array();
	wp_nonce_field( 'ell_guardar_' . $post->ID, 'ell_nonce' );
	echo '<div class="ell-fields">';
	foreach ( $fields as $key => $f ) {
		$name  = 'ell[' . $key . ']';
		$id    = 'ell-' . $key;
		$value = get_post_meta( $post->ID, '_ell_' . $key, true );
		if ( '' === $value && isset( $f['default'] ) && 'auto-draft' === $post->post_status ) {
			$value = $f['default'];
		}
		echo '<div class="ell-field ell-field--' . esc_attr( $f['type'] ) . '">';
		if ( 'checkbox' !== $f['type'] ) {
			echo '<label class="ell-label" for="' . esc_attr( $id ) . '">' . esc_html( $f['label'] ) . '</label>';
		}
		switch ( $f['type'] ) {
			case 'text':
				echo '<input type="text" class="widefat" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '">';
				break;
			case 'select':
				echo '<input type="text" class="widefat" list="' . esc_attr( $id ) . '-list" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" placeholder="Elige o escribe">';
				echo '<datalist id="' . esc_attr( $id ) . '-list">';
				foreach ( $f['options'] as $opt ) {
					echo '<option value="' . esc_attr( $opt ) . '">';
				}
				echo '</datalist>';
				break;
			case 'textarea':
			case 'lines':
				$rows = $f['rows'] ?? ( 'lines' === $f['type'] ? 5 : 3 );
				echo '<textarea class="widefat" rows="' . (int) $rows . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">' . esc_textarea( $value ) . '</textarea>';
				break;
			case 'checkbox':
				echo '<label><input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="1" ' . checked( (bool) $value, true, false ) . '> ' . esc_html( $f['label'] ) . '</label>';
				break;
			case 'icon':
				echo '<div class="ell-icon-picker">';
				foreach ( ell_icons() as $slug => $icon ) {
					if ( in_array( $slug, array( 'flecha', 'chevron', 'check', 'info', 'calendario', 'telefono', 'ubicacion', 'reloj', 'alerta' ), true ) ) {
						continue;
					}
					echo '<label title="' . esc_attr( $icon[0] ) . '"><input type="radio" name="' . esc_attr( $name ) . '" value="' . esc_attr( $slug ) . '" ' . checked( $value ?: 'ojo', $slug, false ) . '>' . ell_icon( $slug ) . '</label>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}
				echo '</div>';
				break;
			case 'relation':
				$selected = array_map( 'intval', (array) $value );
				$all      = get_posts( array( 'post_type' => $f['post_type'], 'post_status' => array( 'publish', 'draft' ), 'posts_per_page' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC' ) );
				$by_id    = array();
				foreach ( $all as $p ) {
					$by_id[ $p->ID ] = $p;
				}
				$ordered = array();
				foreach ( $selected as $sid ) {
					if ( isset( $by_id[ $sid ] ) ) {
						$ordered[] = $by_id[ $sid ];
						unset( $by_id[ $sid ] );
					}
				}
				$ordered = array_merge( $ordered, array_values( $by_id ) );
				echo '<input type="hidden" name="' . esc_attr( $name ) . '[]" value="">';
				echo '<ul class="ell-relation" id="' . esc_attr( $id ) . '">';
				foreach ( $ordered as $p ) {
					$on = in_array( $p->ID, $selected, true );
					echo '<li class="' . ( $on ? 'is-on' : '' ) . '"><span class="dashicons dashicons-menu"></span><label><input type="checkbox" name="' . esc_attr( $name ) . '[]" value="' . (int) $p->ID . '" ' . checked( $on, true, false ) . '> ' . esc_html( $p->post_title ) . ( 'publish' !== $p->post_status ? ' <em>(oculto)</em>' : '' ) . '</label></li>';
				}
				echo '</ul>';
				break;
		}
		if ( ! empty( $f['help'] ) ) {
			echo '<p class="description">' . esc_html( $f['help'] ) . '</p>';
		}
		echo '</div>';
	}
	echo '</div>';
}

/**
 * Guardado y saneamiento.
 */
add_action( 'save_post', function ( $post_id, $post ) {
	$fields = ell_fields()[ $post->post_type ] ?? null;
	if ( ! $fields || ! isset( $_POST['ell_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ell_nonce'] ) ), 'ell_guardar_' . $post_id ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$input = isset( $_POST['ell'] ) && is_array( $_POST['ell'] ) ? wp_unslash( $_POST['ell'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	foreach ( $fields as $key => $f ) {
		$raw = $input[ $key ] ?? null;
		switch ( $f['type'] ) {
			case 'checkbox':
				$val = empty( $raw ) ? 0 : 1;
				break;
			case 'textarea':
			case 'lines':
				$val = sanitize_textarea_field( (string) $raw );
				break;
			case 'icon':
				$val = array_key_exists( (string) $raw, ell_icons() ) ? (string) $raw : 'ojo';
				break;
			case 'relation':
				$val = array_values( array_filter( array_map( 'absint', (array) $raw ) ) );
				break;
			default:
				$val = sanitize_text_field( (string) $raw );
		}
		update_post_meta( $post_id, '_ell_' . $key, $val );
	}
}, 10, 2 );

/**
 * Estilos y scripts del panel.
 */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->post_type, array( 'ell_profesional', 'ell_examen', 'ell_especialidad' ), true ) && 'toplevel_page_ell-datos' !== $hook ) {
		return;
	}
	wp_enqueue_style( 'el-llano-admin', ELL_URI . '/assets/css/admin.css', array(), ELL_VERSION );
	wp_enqueue_script( 'el-llano-admin', ELL_URI . '/assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable' ), ELL_VERSION, true );
	wp_localize_script( 'el-llano-admin', 'ellAdmin', array(
		'ajax'  => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'ell_orden' ),
	) );
	if ( 'toplevel_page_ell-datos' === $hook ) {
		wp_enqueue_media();
	}
} );
