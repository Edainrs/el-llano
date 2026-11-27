<?php
/**
 * Bloques dinámicos del tema (se muestran con datos de las fichas y de "Datos del centro").
 *
 * @package el-llano
 */

defined( 'ABSPATH' ) || exit;

/**
 * Definición de los bloques: nombre => [título, ícono dashicons, atributos, controles].
 */
function ell_block_defs() {
	return array(
		'logo'               => array( 'Logo del centro', 'visibility', array( 'variante' => 'cabecera' ), array( 'variante' => array( 'Ubicación', array( 'cabecera' => 'Cabecera (fondo claro)', 'pie' => 'Pie (fondo oscuro)' ) ) ) ),
		'menu'               => array( 'Menú (Apariencia › Menús)', 'menu', array( 'ubicacion' => 'principal' ), array( 'ubicacion' => array( 'Menú', ell_menu_locations() ) ) ),
		'barra-superior'     => array( 'Barra superior (dirección, horario, teléfonos)', 'info-outline', array(), array() ),
		'datos-contacto'     => array( 'Datos de contacto', 'phone', array( 'variante' => 'pie' ), array( 'variante' => array( 'Formato', array( 'pie' => 'Lista (pie de página)', 'tarjetas' => 'Tarjetas (página de contacto)' ) ) ) ),
		'botones-contacto'   => array( 'Botones llamar y WhatsApp', 'button', array( 'estilo' => 'claro' ), array( 'estilo' => array( 'Estilo', array( 'claro' => 'Sobre fondo claro', 'oscuro' => 'Sobre fondo oscuro' ) ) ) ),
		'urgencias'          => array( 'Aviso de urgencias', 'warning', array( 'variante' => 'franja' ), array( 'variante' => array( 'Formato', array( 'franja' => 'Franja con botón', 'compacto' => 'Compacto' ) ) ) ),
		'titulo-pagina'      => array( 'Título de página con imagen', 'cover-image', array(), array() ),
		'icono'              => array( 'Ícono', 'star-empty', array( 'icono' => 'ojo' ), array( 'icono' => array( 'Ícono', 'icons' ) ) ),
		'especialidades'     => array( 'Especialidades', 'heart', array( 'modo' => 'tarjetas' ), array( 'modo' => array( 'Formato', array( 'tarjetas' => 'Tarjetas (Inicio)', 'detalle' => 'Detalle (página Especialidades)' ) ) ) ),
		'examenes'           => array( 'Exámenes', 'visibility', array( 'modo' => 'acordeon' ), array( 'modo' => array( 'Formato', array( 'acordeon' => 'Acordeón (página Exámenes)', 'destacados' => 'Tarjetas destacadas (Inicio)' ) ) ) ),
		'equipo'             => array( 'Equipo médico', 'groups', array(), array() ),
		'cifras'             => array( 'Cifras del centro', 'chart-bar', array(), array() ),
		'mapa'               => array( 'Mapa', 'location-alt', array(), array() ),
		'whatsapp-flotante'  => array( 'Botón flotante de WhatsApp', 'format-chat', array(), array() ),
	);
}

add_filter( 'block_categories_all', function ( $cats ) {
	array_unshift( $cats, array( 'slug' => 'el-llano', 'title' => 'El Llano' ) );
	return $cats;
} );

add_action( 'init', function () {
	wp_register_script(
		'ell-blocks-editor',
		ELL_URI . '/assets/js/blocks-editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render' ),
		ELL_VERSION,
		true
	);
	$defs  = ell_block_defs();
	$icons = array();
	foreach ( ell_icons() as $slug => $i ) {
		$icons[ $slug ] = $i[0];
	}
	wp_localize_script( 'ell-blocks-editor', 'ellBlocks', array( 'defs' => $defs, 'icons' => $icons ) );

	foreach ( $defs as $slug => $def ) {
		$attrs = array();
		foreach ( $def[2] as $k => $default ) {
			$attrs[ $k ] = array( 'type' => 'string', 'default' => $default );
		}
		$fn = 'ell_render_' . str_replace( '-', '_', $slug );
		register_block_type( 'ell/' . $slug, array(
			'api_version'           => 3,
			'title'                 => $def[0],
			'category'              => 'el-llano',
			'attributes'            => $attrs,
			'uses_context'          => array( 'postId', 'postType' ),
			'supports'              => array( 'html' => false, 'align' => array( 'wide', 'full' ) ),
			'editor_script_handles' => array( 'ell-blocks-editor' ),
			'render_callback'       => function ( $attributes, $content, $block ) use ( $fn ) {
				return function_exists( $fn ) ? call_user_func( $fn, $attributes, $block ) : '';
			},
		) );
	}
} );

/* ---------------------------------------------------------------------------
 * Render de cada bloque.
 * ------------------------------------------------------------------------ */

function ell_render_logo( $a = array() ) {
	$out = '<a class="ell-logo" href="' . esc_url( home_url( '/' ) ) . '" rel="home">';
	$id  = (int) ell_dato( 'pie' === ( $a['variante'] ?? '' ) ? 'logo_pie' : 'logo' );
	if ( $id && wp_attachment_is_image( $id ) ) {
		$out .= wp_get_attachment_image( $id, 'full', false, array( 'class' => 'ell-logo__img', 'alt' => ell_dato( 'linea_superior' ) . ' ' . ell_dato( 'nombre' ) ) );
	} else {
		$out .= '<svg class="ell-logo__mark" viewBox="0 0 48 48" aria-hidden="true"><defs><linearGradient id="ellLg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#1559B5"/><stop offset="1" stop-color="#4FB8EA"/></linearGradient></defs><rect width="48" height="48" rx="14" fill="url(#ellLg)"/><path d="M8 24c4-7 9.5-10.5 16-10.5S36 17 40 24c-4 7-9.5 10.5-16 10.5S12 31 8 24z" fill="none" stroke="#fff" stroke-width="2.6"/><circle cx="24" cy="24" r="6" fill="#fff"/><circle cx="24" cy="24" r="2.6" fill="#0A2E5C"/></svg>';
		$out .= '<span class="ell-logo__txt"><small>' . esc_html( ell_dato( 'linea_superior' ) ) . '</small><strong>' . esc_html( ell_dato( 'nombre' ) ) . '</strong></span>';
	}
	return $out . '</a>';
}

function ell_render_barra_superior() {
	$tel = ell_dato( 'telefono_principal' );
	$wa  = ell_dato( 'whatsapp' );
	$out = '<div class="ell-topbar"><div class="ell-topbar__in">';
	$out .= '<div><span>' . ell_icon( 'ubicacion' ) . esc_html( ell_direccion() ) . '</span>';
	$out .= '<span>' . ell_icon( 'reloj' ) . esc_html( ell_dato( 'horario_corto' ) ) . '</span></div>';
	$out .= '<div class="ell-topbar__tel"><span>' . ell_icon( 'telefono' );
	$out .= '<a href="' . esc_attr( ell_tel( $tel ) ) . '">' . esc_html( $tel ) . '</a>';
	if ( $wa ) {
		$out .= ' · <a href="' . esc_url( ell_whatsapp_url() ) . '">' . esc_html( $wa ) . '</a>';
	}
	$out .= '</span></div></div></div>';
	return $out;
}

function ell_render_datos_contacto( $a ) {
	$tels = ell_lines( ell_dato( 'telefonos' ) );
	$wa   = ell_dato( 'whatsapp' );
	$hor  = ell_pairs( ell_dato( 'horario' ) );
	if ( 'tarjetas' === ( $a['variante'] ?? '' ) ) {
		$out  = '<div class="ell-cstack">';
		$out .= '<div class="ell-cline"><span class="ell-ico">' . ell_icon( 'ubicacion' ) . '</span><div><h3>Visítanos</h3><p>' . esc_html( ell_direccion() ) . '</p></div></div>';
		$out .= '<div class="ell-cline"><span class="ell-ico">' . ell_icon( 'telefono' ) . '</span><div><h3>Llámanos</h3><p>';
		$rows = array();
		foreach ( $tels as $t ) {
			$rows[] = '<a href="' . esc_attr( ell_tel( $t ) ) . '">' . esc_html( $t ) . '</a>';
		}
		if ( $wa ) {
			$rows[] = '<a href="' . esc_url( ell_whatsapp_url() ) . '">' . esc_html( $wa ) . '</a> (WhatsApp)';
		}
		$out .= implode( '<br>', $rows ) . '</p></div></div>';
		$out .= '<div class="ell-cline"><span class="ell-ico">' . ell_icon( 'reloj' ) . '</span><div><h3>Horario de atención</h3><p>';
		$rows = array();
		foreach ( $hor as $h ) {
			$rows[] = esc_html( $h[0] . ( $h[1] ? ', ' . $h[1] : '' ) );
		}
		$out .= implode( '<br>', $rows ) . '</p></div></div></div>';
		return $out;
	}
	$out = '<ul class="ell-footer-list">';
	$out .= '<li>' . esc_html( ell_direccion() ) . '</li>';
	foreach ( $tels as $t ) {
		$out .= '<li><a href="' . esc_attr( ell_tel( $t ) ) . '">' . esc_html( $t ) . '</a></li>';
	}
	if ( $wa ) {
		$out .= '<li><a href="' . esc_url( ell_whatsapp_url() ) . '">' . esc_html( $wa ) . '</a> (WhatsApp)</li>';
	}
	foreach ( $hor as $h ) {
		$out .= '<li>' . esc_html( $h[0] . ( $h[1] ? ': ' . $h[1] : '' ) ) . '</li>';
	}
	if ( ell_dato( 'urgencias_activo' ) ) {
		$out .= '<li><span class="ell-chip-urg">' . ell_icon( 'alerta' ) . 'Atendemos urgencias</span></li>';
	}
	return $out . '</ul>';
}

function ell_render_botones_contacto( $a ) {
	$tel  = ell_dato( 'telefono_principal' );
	$dark = 'oscuro' === ( $a['estilo'] ?? '' );
	$out  = '<div class="ell-btns">';
	$out .= '<a class="ell-btn ' . ( $dark ? 'ell-btn--blanco' : 'ell-btn--azul' ) . '" href="' . esc_attr( ell_tel( $tel ) ) . '">' . ell_icon( 'telefono' ) . esc_html( $tel ) . '</a>';
	if ( ell_whatsapp_url() ) {
		$out .= '<a class="ell-btn ell-btn--wa" href="' . esc_url( ell_whatsapp_url() ) . '" target="_blank" rel="noopener">' . ell_icon( 'whatsapp' ) . 'WhatsApp</a>';
	}
	return $out . '</div>';
}

function ell_render_urgencias( $a ) {
	if ( ! ell_dato( 'urgencias_activo' ) ) {
		return '';
	}
	$tel = ell_dato( 'telefono_principal' );
	$out = '<div class="ell-urg ell-urg--' . esc_attr( $a['variante'] ?? 'franja' ) . '">' . ell_icon( 'alerta', 'ell-urg__ico' );
	$out .= '<div><strong>' . esc_html( ell_dato( 'urgencias_titulo' ) ) . '</strong><span>' . esc_html( ell_dato( 'urgencias_texto' ) ) . '</span></div>';
	if ( 'compacto' !== ( $a['variante'] ?? '' ) ) {
		$out .= '<a class="ell-btn ell-btn--azul" href="' . esc_attr( ell_tel( $tel ) ) . '">' . ell_icon( 'telefono' ) . esc_html( $tel ) . '</a>';
	}
	return $out . '</div>';
}

function ell_render_titulo_pagina( $a, $block ) {
	$id = ! empty( $block->context['postId'] ) ? (int) $block->context['postId'] : ( is_singular() ? get_queried_object_id() : 0 );
	if ( ! $id && defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		$id = (int) get_the_ID();
	}
	$img = $id ? get_the_post_thumbnail_url( $id, 'full' ) : '';
	if ( ! $img && ell_dato( 'imagen_titulo' ) ) {
		$img = wp_get_attachment_image_url( (int) ell_dato( 'imagen_titulo' ), 'full' );
	}
	if ( ! $img ) {
		$img = ELL_URI . '/assets/img/placeholder-titulo.jpg';
	}
	$title = $id ? get_the_title( $id ) : ( is_404() ? 'Página no encontrada' : wp_get_document_title() );
	$sub   = $id && has_excerpt( $id ) ? get_the_excerpt( $id ) : '';
	$out   = '<section class="ell-pagetitle alignfull" style="--ell-bg:url(\'' . esc_url( $img ) . '\')"><div class="ell-pagetitle__in">';
	$out  .= '<nav class="ell-crumbs" aria-label="Ruta"><a href="' . esc_url( home_url( '/' ) ) . '">Inicio</a> <span aria-hidden="true">›</span> <span>' . esc_html( $title ) . '</span></nav>';
	$out  .= '<h1>' . esc_html( $title ) . '</h1>';
	if ( $sub ) {
		$out .= '<p>' . esc_html( $sub ) . '</p>';
	}
	return $out . '</div></section>';
}

function ell_render_icono( $a ) {
	return '<span class="ell-ico">' . ell_icon( $a['icono'] ?? 'ojo' ) . '</span>';
}

function ell_render_especialidades( $a ) {
	$items = ell_items( 'ell_especialidad' );
	if ( ! $items ) {
		return '<p class="ell-vacio">Aún no hay especialidades publicadas.</p>';
	}
	if ( 'detalle' === ( $a['modo'] ?? '' ) ) {
		$out = '<div class="ell-espdetalle">';
		foreach ( $items as $i => $p ) {
			$puntos = ell_lines( ell_meta( $p->ID, 'puntos' ) );
			$pros   = ell_items_by_ids( ell_meta( $p->ID, 'profesionales' ) );
			$exs    = ell_items_by_ids( ell_meta( $p->ID, 'examenes' ) );
			$out   .= '<article class="ell-espblock" id="esp-' . esc_attr( $p->post_name ) . '"><div class="ell-espblock__num">' . sprintf( '%02d', $i + 1 ) . '</div>';
			$out   .= '<div><h2>' . esc_html( $p->post_title ) . '</h2>';
			$out   .= wpautop( esc_html( ell_meta( $p->ID, 'descripcion' ) ) );
			if ( $puntos ) {
				$out .= '<ul class="ell-check">';
				foreach ( $puntos as $pt ) {
					$out .= '<li>' . ell_icon( 'check' ) . '<span>' . ell_text( $pt ) . '</span></li>';
				}
				$out .= '</ul>';
			}
			$out .= '</div>';
			if ( $pros || $exs ) {
				$out .= '<aside class="ell-espblock__side">';
				if ( $pros ) {
					$out .= '<h4>Especialistas</h4><div class="ell-tags">';
					foreach ( $pros as $pp ) {
						$out .= '<a href="' . esc_url( ell_page_url( 'nosotros', 'pro-' . $pp->post_name ) ) . '">' . esc_html( $pp->post_title ) . '</a>';
					}
					$out .= '</div>';
				}
				if ( $exs ) {
					$out .= '<h4>Exámenes relacionados</h4><div class="ell-tags">';
					foreach ( $exs as $ep ) {
						$out .= '<a href="' . esc_url( ell_page_url( 'examenes', 'ex-' . $ep->post_name ) ) . '">' . esc_html( $ep->post_title ) . '</a>';
					}
					$out .= '</div>';
				}
				$out .= '</aside>';
			}
			$out .= '</article>';
		}
		return $out . '</div>';
	}
	$out = '<div class="ell-cards ell-cards--' . min( 4, count( $items ) ) . '">';
	foreach ( $items as $p ) {
		$out .= '<a class="ell-card" href="' . esc_url( ell_page_url( 'especialidades', 'esp-' . $p->post_name ) ) . '"><span class="ell-ico">' . ell_icon( ell_meta( $p->ID, 'icono' ) ?: 'ojo' ) . '</span>';
		$out .= '<h3>' . esc_html( $p->post_title ) . '</h3><p>' . esc_html( ell_meta( $p->ID, 'resumen' ) ) . '</p><span class="ell-card__more">Ver más →</span></a>';
	}
	return $out . '</div>';
}

function ell_render_examenes( $a ) {
	if ( 'destacados' === ( $a['modo'] ?? '' ) ) {
		$items = ell_items( 'ell_examen', array( 'meta_key' => '_ell_destacado', 'meta_value' => '1', 'posts_per_page' => 4 ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
		if ( ! $items ) {
			$items = ell_items( 'ell_examen', array( 'posts_per_page' => 4 ) );
		}
		$out = '<div class="ell-cards ell-cards--4">';
		foreach ( $items as $p ) {
			$dur  = ell_meta( $p->ID, 'horario' ) ?: ell_meta( $p->ID, 'duracion' );
			$out .= '<a class="ell-card ell-card--exam" href="' . esc_url( ell_page_url( 'examenes', 'ex-' . $p->post_name ) ) . '"><span class="ell-ico ell-ico--sm">' . ell_icon( 'ojo' ) . '</span>';
			$out .= '<h3>' . esc_html( $p->post_title ) . '</h3><p>' . esc_html( ell_meta( $p->ID, 'descripcion' ) ) . '</p>';
			$out .= '<span class="ell-card__meta"><span>' . ell_icon( 'reloj' ) . esc_html( $dur ) . '</span><span>Ver más →</span></span></a>';
		}
		return $out . '</div>';
	}
	$items = ell_items( 'ell_examen' );
	if ( ! $items ) {
		return '<p class="ell-vacio">Aún no hay exámenes publicados.</p>';
	}
	$out = '<div class="ell-acc ell-acc--examenes">';
	foreach ( $items as $p ) {
		$hor  = ell_meta( $p->ID, 'horario' );
		$dur  = ell_meta( $p->ID, 'duracion' );
		$proc = ell_meta( $p->ID, 'procedimiento' );
		$cod  = ell_meta( $p->ID, 'codigo' );
		$ind  = ell_lines( ell_meta( $p->ID, 'indicaciones' ) );
		$out .= '<details class="ell-acc__item" name="ell-examenes" id="ex-' . esc_attr( $p->post_name ) . '"><summary>';
		$out .= '<span class="ell-ico">' . ell_icon( 'ojo' ) . '</span><span class="ell-acc__head"><span class="ell-acc__title">' . esc_html( $p->post_title ) . '</span><span class="ell-acc__desc">' . esc_html( ell_meta( $p->ID, 'descripcion' ) ) . '</span></span>';
		if ( $hor || $dur ) {
			$out .= '<span class="ell-pill">' . esc_html( $hor ?: $dur ) . '</span>';
		}
		$out .= ell_icon( 'chevron', 'ell-acc__chev' ) . '</summary><div class="ell-acc__body"><dl>';
		if ( $dur ) {
			$out .= '<dt>Duración</dt><dd>' . ell_text( $dur ) . '</dd>';
		}
		if ( $hor ) {
			$out .= '<dt>Horario</dt><dd>' . ell_text( $hor ) . '</dd>';
		}
		if ( $proc ) {
			$out .= '<dt>Procedimiento</dt><dd>' . ell_text( $proc ) . '</dd>';
		}
		if ( $cod ) {
			$out .= '<dt>Código</dt><dd>' . ell_text( $cod ) . '</dd>';
		}
		$out .= '</dl><div>';
		if ( $ind ) {
			$out .= '<h4>Indicaciones para el paciente</h4><ul>';
			foreach ( $ind as $line ) {
				$out .= '<li>' . ell_text( $line ) . '</li>';
			}
			$out .= '</ul>';
		}
		$out .= '</div></div></details>';
	}
	return $out . '</div>';
}

function ell_render_equipo() {
	$items = ell_items( 'ell_profesional' );
	if ( ! $items ) {
		return '<p class="ell-vacio">Aún no hay profesionales publicados.</p>';
	}
	$out = '<div class="ell-team">';
	foreach ( $items as $p ) {
		$cargo = ell_meta( $p->ID, 'cargo' );
		$esp   = ell_meta( $p->ID, 'especialidad' );
		$edad  = ell_meta( $p->ID, 'edad' );
		$tm    = false !== mb_stripos( $cargo, 'tecnólog' );
		$out  .= '<div class="ell-doc' . ( $tm ? ' ell-doc--tm' : '' ) . '" id="pro-' . esc_attr( $p->post_name ) . '">';
		if ( has_post_thumbnail( $p ) ) {
			$out .= get_the_post_thumbnail( $p, 'medium', array( 'class' => 'ell-doc__foto', 'alt' => esc_attr( $p->post_title ) ) );
		} else {
			$out .= '<span class="ell-doc__ini" aria-hidden="true">' . esc_html( ell_initials( $p->post_title ) ) . '</span>';
		}
		$out .= '<h3>' . esc_html( $p->post_title ) . '</h3>';
		if ( $cargo ) {
			$out .= '<span class="ell-doc__cargo">' . esc_html( $cargo ) . '</span>';
		}
		if ( $esp ) {
			$out .= '<span class="ell-doc__esp">' . ell_text( $esp ) . '</span>';
		}
		if ( $edad ) {
			$out .= '<span class="ell-doc__edad">' . ell_text( $edad ) . '</span>';
		}
		$out .= '</div>';
	}
	return $out . '</div>';
}

function ell_render_cifras() {
	$pros = 0;
	foreach ( ell_items( 'ell_profesional' ) as $p ) {
		if ( false !== mb_stripos( ell_meta( $p->ID, 'cargo' ), 'oftalm' ) ) {
			++$pros;
		}
	}
	$rows = array(
		array( $pros, 'Oftalmólogos' ),
		array( count( ell_items( 'ell_especialidad' ) ), 'Especialidades' ),
		array( count( ell_items( 'ell_examen' ) ), 'Exámenes' ),
	);
	$out = '<div class="ell-cifras">';
	foreach ( $rows as $r ) {
		$out .= '<div><strong>' . (int) $r[0] . '</strong><span>' . esc_html( $r[1] ) . '</span></div>';
	}
	return $out . '</div>';
}

function ell_render_mapa() {
	$url = ell_dato( 'mapa_url' );
	if ( $url ) {
		return '<div class="ell-mapa"><iframe src="' . esc_url( $url ) . '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Mapa: ' . esc_attr( ell_direccion() ) . '" allowfullscreen></iframe></div>';
	}
	$q = rawurlencode( ell_direccion() . ', Santiago, Chile' );
	return '<div class="ell-mapa ell-mapa--vacio"><div><span class="ell-pendiente">Falta el mapa</span><p>Pega el enlace para incrustar en <strong>Datos del centro › Contacto</strong>.</p><a class="ell-btn ell-btn--azul" href="https://www.google.com/maps/search/?api=1&query=' . esc_attr( $q ) . '" target="_blank" rel="noopener">' . ell_icon( 'ubicacion' ) . 'Ver en Google Maps</a></div></div>';
}

function ell_render_whatsapp_flotante() {
	$url = ell_whatsapp_url();
	if ( ! $url ) {
		return '';
	}
	return '<a class="ell-wa-float" href="' . esc_url( $url ) . '" target="_blank" rel="noopener" aria-label="Escríbenos por WhatsApp">' . ell_icon( 'whatsapp' ) . '</a>';
}
