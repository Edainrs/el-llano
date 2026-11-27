<?php
/**
 * Tipos de contenido: Equipo, Exámenes y Especialidades.
 *
 * @package el-llano
 */

defined( 'ABSPATH' ) || exit;

/**
 * Definición de campos de cada tipo. Se usa para registrar, editar y guardar.
 * type: text | textarea | lines | select | checkbox | icon | relation
 */
function ell_fields() {
	return array(
		'ell_profesional'  => array(
			'cargo'         => array( 'label' => 'Cargo', 'type' => 'select', 'options' => array( 'Oftalmólogo', 'Oftalmóloga', 'Tecnóloga médica', 'Tecnólogo médico' ), 'other' => true ),
			'especialidad'  => array( 'label' => 'Especialidad que destacar', 'type' => 'text', 'help' => 'Ej: Especialista en glaucoma, Retinóloga. Déjalo vacío si no corresponde.' ),
			'edad'          => array( 'label' => 'Edad de atención', 'type' => 'text', 'help' => 'Ej: Atiende desde los 6 años.' ),
			'en_formulario' => array( 'label' => 'Mostrar en el campo "Profesional" del formulario de horas', 'type' => 'checkbox', 'default' => 1 ),
		),
		'ell_examen'       => array(
			'descripcion'   => array( 'label' => 'Descripción', 'type' => 'textarea' ),
			'duracion'      => array( 'label' => 'Duración', 'type' => 'text', 'help' => 'Ej: 15 minutos.' ),
			'horario'       => array( 'label' => 'Horario (si el examen tiene uno fijo)', 'type' => 'text', 'help' => 'Ej: 08:30 a 13:30 horas. Si lo completas, se muestra en lugar de la duración.' ),
			'procedimiento' => array( 'label' => 'Procedimiento / sesiones', 'type' => 'text' ),
			'codigo'        => array( 'label' => 'Código', 'type' => 'text', 'help' => 'Ej: 1201014 × 2. Déjalo vacío si no tiene.' ),
			'indicaciones'  => array( 'label' => 'Indicaciones para el paciente', 'type' => 'lines', 'help' => 'Una indicación por línea.' ),
			'destacado'     => array( 'label' => 'Destacar en la página de Inicio', 'type' => 'checkbox' ),
		),
		'ell_especialidad' => array(
			'icono'         => array( 'label' => 'Ícono', 'type' => 'icon' ),
			'resumen'       => array( 'label' => 'Resumen (tarjeta de Inicio)', 'type' => 'textarea', 'rows' => 2 ),
			'descripcion'   => array( 'label' => 'Descripción', 'type' => 'textarea', 'rows' => 5 ),
			'puntos'        => array( 'label' => 'Qué incluye', 'type' => 'lines', 'help' => 'Un punto por línea.' ),
			'profesionales' => array( 'label' => 'Especialistas', 'type' => 'relation', 'post_type' => 'ell_profesional', 'help' => 'Marca los profesionales y arrástralos para ordenarlos.' ),
			'examenes'      => array( 'label' => 'Exámenes relacionados', 'type' => 'relation', 'post_type' => 'ell_examen', 'help' => 'Marca los exámenes y arrástralos para ordenarlos.' ),
		),
	);
}

add_action( 'init', function () {
	$common = array(
		'public'              => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => false,
		'has_archive'         => false,
		'rewrite'             => false,
		'hierarchical'        => false,
		'capability_type'     => 'page',
		'map_meta_cap'        => true,
	);

	register_post_type( 'ell_profesional', array_merge( $common, array(
		'labels'        => ell_labels( 'Equipo', 'Profesional', 'Profesionales', true ),
		'menu_icon'     => 'dashicons-groups',
		'menu_position' => 21,
		'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
	) ) );

	register_post_type( 'ell_examen', array_merge( $common, array(
		'labels'        => ell_labels( 'Exámenes', 'Examen', 'Exámenes', true ),
		'menu_icon'     => 'dashicons-visibility',
		'menu_position' => 22,
		'supports'      => array( 'title', 'page-attributes' ),
	) ) );

	register_post_type( 'ell_especialidad', array_merge( $common, array(
		'labels'        => ell_labels( 'Especialidades', 'Especialidad', 'Especialidades', false ),
		'menu_icon'     => 'dashicons-heart',
		'menu_position' => 23,
		'supports'      => array( 'title', 'page-attributes' ),
	) ) );

	foreach ( ell_fields() as $type => $fields ) {
		foreach ( $fields as $key => $f ) {
			register_post_meta( $type, '_ell_' . $key, array(
				'single'        => true,
				'type'          => in_array( $f['type'], array( 'relation' ), true ) ? 'array' : ( 'checkbox' === $f['type'] ? 'boolean' : 'string' ),
				'auth_callback' => function () {
					return current_user_can( 'edit_pages' );
				},
			) );
		}
	}
} );

/**
 * Etiquetas en español.
 */
function ell_labels( $menu, $singular, $plural, $masculine ) {
	$nuevo = $masculine ? 'Nuevo' : 'Nueva';
	return array(
		'name'               => $plural,
		'singular_name'      => $singular,
		'menu_name'          => $menu,
		'all_items'          => $plural,
		'add_new'            => 'Añadir ' . mb_strtolower( $singular ),
		'add_new_item'       => 'Añadir ' . mb_strtolower( $singular ),
		'edit_item'          => 'Editar ' . mb_strtolower( $singular ),
		'new_item'           => $nuevo . ' ' . mb_strtolower( $singular ),
		'search_items'       => 'Buscar',
		'not_found'          => 'No hay ' . mb_strtolower( $plural ) . ' todavía.',
		'not_found_in_trash' => 'No hay ' . mb_strtolower( $plural ) . ' en la papelera.',
		'featured_image'     => 'Foto',
		'set_featured_image' => 'Elegir foto',
		'remove_featured_image' => 'Quitar foto',
		'use_featured_image' => 'Usar como foto',
	);
}

/**
 * Lee un campo de una ficha.
 *
 * @param int    $id  ID.
 * @param string $key Campo.
 */
function ell_meta( $id, $key ) {
	return get_post_meta( $id, '_ell_' . $key, true );
}

/**
 * Título del campo "Nombre" según el tipo.
 */
add_filter( 'enter_title_here', function ( $text, $post ) {
	switch ( $post->post_type ) {
		case 'ell_profesional':
			return 'Nombre (ej: Dra. Ruth Soto)';
		case 'ell_examen':
			return 'Nombre del examen';
		case 'ell_especialidad':
			return 'Nombre de la especialidad';
	}
	return $text;
}, 10, 2 );

/**
 * Columnas útiles en los listados.
 */
foreach ( array( 'ell_profesional', 'ell_examen', 'ell_especialidad' ) as $ell_type ) {
	add_filter( "manage_{$ell_type}_posts_columns", function ( $cols ) use ( $ell_type ) {
		$new = array( 'cb' => $cols['cb'], 'ell_orden' => '<span class="screen-reader-text">Orden</span>' );
		$new['title'] = 'Nombre';
		if ( 'ell_profesional' === $ell_type ) {
			$new['ell_foto']  = 'Foto';
			$new['ell_cargo'] = 'Cargo';
			$new['ell_extra'] = 'Especialidad';
		} elseif ( 'ell_examen' === $ell_type ) {
			$new['ell_cargo'] = 'Duración';
			$new['ell_extra'] = 'En Inicio';
		} else {
			$new['ell_cargo'] = 'Especialistas';
			$new['ell_extra'] = 'Exámenes';
		}
		$new['ell_estado'] = 'Estado';
		return $new;
	} );
	add_action( "manage_{$ell_type}_posts_custom_column", function ( $col, $id ) use ( $ell_type ) {
		switch ( $col ) {
			case 'ell_orden':
				echo '<span class="ell-handle dashicons dashicons-menu" title="Arrastra para ordenar"></span>';
				break;
			case 'ell_foto':
				echo has_post_thumbnail( $id ) ? get_the_post_thumbnail( $id, array( 40, 40 ), array( 'style' => 'border-radius:50%;width:40px;height:40px;object-fit:cover' ) ) : '<span class="ell-ini">' . esc_html( ell_initials( get_the_title( $id ) ) ) . '</span>';
				break;
			case 'ell_cargo':
				if ( 'ell_profesional' === $ell_type ) {
					echo esc_html( ell_meta( $id, 'cargo' ) );
				} elseif ( 'ell_examen' === $ell_type ) {
					echo esc_html( ell_meta( $id, 'horario' ) ?: ell_meta( $id, 'duracion' ) );
				} else {
					echo esc_html( count( (array) ell_meta( $id, 'profesionales' ) ) );
				}
				break;
			case 'ell_extra':
				if ( 'ell_profesional' === $ell_type ) {
					echo wp_kses_post( ell_text( ell_meta( $id, 'especialidad' ) ) );
				} elseif ( 'ell_examen' === $ell_type ) {
					echo ell_meta( $id, 'destacado' ) ? '✓' : '—';
				} else {
					echo esc_html( count( (array) ell_meta( $id, 'examenes' ) ) );
				}
				break;
			case 'ell_estado':
				echo 'publish' === get_post_status( $id ) ? '<span style="color:#008a20">Visible</span>' : '<span style="color:#996800">Oculto</span>';
				break;
		}
	}, 10, 2 );
}
