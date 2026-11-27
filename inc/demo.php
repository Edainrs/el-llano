<?php
/**
 * Contenido de ejemplo: páginas, menú, fichas y formulario.
 *
 * @package el-llano
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_post_ell_demo', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Sin permisos.' );
	}
	check_admin_referer( 'ell_demo' );
	ell_demo_import();
	wp_safe_redirect( admin_url( 'admin.php?page=ell-datos&demo=1' ) );
	exit;
} );

/**
 * Contenido HTML de un patrón registrado.
 *
 * @param string $slug Patrón sin prefijo.
 */
function ell_pattern( $slug ) {
	$p = WP_Block_Patterns_Registry::get_instance()->get_registered( 'el-llano/' . $slug );
	return $p ? $p['content'] : '';
}

/**
 * Crea una ficha si no existe otra con el mismo título.
 */
function ell_demo_post( $type, $title, $meta = array(), $order = 0 ) {
	$existing = get_posts( array( 'post_type' => $type, 'title' => $title, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) );
	if ( $existing ) {
		return (int) $existing[0];
	}
	$id = wp_insert_post( array( 'post_type' => $type, 'post_title' => $title, 'post_status' => 'publish', 'menu_order' => $order ) );
	foreach ( $meta as $k => $v ) {
		update_post_meta( $id, '_ell_' . $k, $v );
	}
	return (int) $id;
}

/**
 * Importación.
 */
function ell_demo_import() {
	$ind_ns = 'No debe suspender ningún tratamiento ocular o general, salvo que su médico se lo haya indicado.';
	$ind_nd = 'No se puede realizar junto con exámenes que requieran dilatación pupilar.';
	$ind_lc = 'Si utiliza lentes de contacto, debe retirarlos antes del examen y traer su estuche.';

	/* ---------- Equipo ---------- */
	$equipo = array(
		array( 'Dra. Lissette Caballero', 'Oftalmóloga', '' ),
		array( 'Dr. Wilfredo García Álvarez', 'Oftalmólogo', '' ),
		array( 'Dra. Linsdy Hernández', 'Oftalmóloga', '' ),
		array( 'Dr. Carlos Van Grieken', 'Oftalmólogo', 'Especialista en glaucoma' ),
		array( 'Dr. Jorge Tamayo García', 'Oftalmólogo', '' ),
		array( 'Dra. Eylym Sánchez Sálcedo', 'Oftalmóloga', 'Retinóloga' ),
		array( 'Dra. Greylin Palencia', 'Oftalmóloga', '' ),
		array( 'Dra. Ruth Soto', 'Oftalmóloga', 'Retinóloga' ),
		array( 'Dra. Dayana de Freitas', 'Oftalmóloga', '' ),
		array( 'Dr. Gino Rojas Margas', 'Oftalmólogo', '[Especialidad por confirmar]' ),
		array( 'Dra. Mónica Suárez', 'Oftalmóloga', '[Especialidad por confirmar]' ),
		array( 'Mariolli Pimentel', 'Tecnóloga médica', '' ),
	);
	$pro = array();
	foreach ( $equipo as $i => $e ) {
		$is_tm           = false !== mb_stripos( $e[1], 'tecnólog' );
		$pro[ $e[0] ] = ell_demo_post( 'ell_profesional', $e[0], array(
			'cargo'         => $e[1],
			'especialidad'  => $e[2],
			'edad'          => $is_tm ? '' : '[Atiende desde los X años]',
			'en_formulario' => $is_tm ? 0 : 1,
		), $i + 1 );
	}

	/* ---------- Exámenes ---------- */
	$examenes = array(
		'tension'     => array( 'Tensión ocular aplanática', array( 'descripcion' => 'Es la medida de la presión intraocular.', 'duracion' => '15 minutos', 'codigo' => '1201014 × 2', 'indicaciones' => implode( "\n", array( $ind_ns, $ind_lc, $ind_nd ) ), 'destacado' => 1 ) ),
		'campimetria' => array( 'Campimetría o campo visual computarizado (CVC)', array( 'descripcion' => 'Examen que permite medir la sensibilidad umbral y el área visual de la retina.', 'duracion' => '30 minutos', 'codigo' => '1201042 × 2', 'indicaciones' => implode( "\n", array( $ind_ns, $ind_lc, $ind_nd ) ), 'destacado' => 1 ) ),
		'paquimetria' => array( 'Paquimetría ultrasónica', array( 'descripcion' => 'Examen indoloro que permite medir el grosor de la córnea.', 'duracion' => '15 minutos', 'indicaciones' => 'Si utiliza lentes de contacto y el examen se requiere para la evaluación de una cirugía refractiva, debe suspender su uso durante dos semanas y presentarse sin ellos al examen.', 'destacado' => 1 ) ),
		'estrabismo'  => array( 'Estudio de estrabismo', array( 'descripcion' => 'Examen que se realiza para evaluar el estado motor y sensorial de una desviación ocular.', 'duracion' => '30 minutos', 'codigo' => '1201009', 'indicaciones' => "Debe traer sus últimos lentes recetados, ópticos o de contacto.\nEn el caso de niños, deben utilizar sus lentes durante las dos semanas previas al examen para adaptarse a posibles cambios en la desviación ocular y permitir su registro durante la evaluación.", 'destacado' => 1 ) ),
		'diplopia'    => array( 'Test de diplopía o diploscopia', array( 'descripcion' => 'Examen que se realiza para estudiar a pacientes que presentan visión doble.', 'duracion' => '30 minutos', 'codigo' => '1201005', 'indicaciones' => 'Debe traer sus lentes para visión de lejos o acudir con sus lentes de contacto.' ) ),
		'schirmer'    => array( 'Test de Schirmer', array( 'descripcion' => 'Examen que permite cuantificar la lágrima durante un determinado lapso de tiempo.', 'duracion' => '15 minutos', 'codigo' => '1201003 × 2', 'indicaciones' => 'Si utiliza lentes de contacto, debe traer su estuche y líquido para su conservación.' ) ),
		'cta'         => array( 'Curva de tensión aplanática (CTA)', array( 'descripcion' => 'Permite determinar la variación de la presión intraocular a diferentes horas del día. Puede realizarse durante uno o dos días, según lo indique el médico.', 'horario' => '08:30 a 13:30 horas', 'procedimiento' => 'Tres mediciones de presión intraocular.', 'codigo' => '1 día: 1202004 × 2 · 2 días: 1201004 × 4', 'indicaciones' => implode( "\n", array( $ind_ns, 'Debe traer sus anteojos o la última receta de lentes indicada por su médico.', 'Si utiliza lentes de contacto, debe retirarlos antes del examen.', $ind_nd ) ) ) ),
		'ortoptico'   => array( 'Tratamiento ortóptico', array( 'descripcion' => 'Serie de ejercicios oculares en sesiones semanales que se realizan para mejorar la motilidad ocular en algunos casos especiales de estrabismo.', 'duracion' => '30 minutos por sesión', 'procedimiento' => 'Cantidad de sesiones habitualmente indicada por el oftalmólogo tratante.', 'codigo' => '1201015 × número de sesiones', 'indicaciones' => 'Debe traer sus últimos lentes recetados, ópticos o de contacto.' ) ),
	);
	$ex = array();
	$i  = 0;
	foreach ( $examenes as $key => $e ) {
		$ex[ $key ] = ell_demo_post( 'ell_examen', $e[0], $e[1], ++$i );
	}

	/* ---------- Especialidades ---------- */
	$especialidades = array(
		array( 'Oftalmología general', array(
			'icono'       => 'ojo',
			'resumen'     => 'Consulta oftalmológica integral para la evaluación y el control de la salud visual de adultos.',
			'descripcion' => 'Evaluación completa de la salud de tus ojos con un equipo de médicos oftalmólogos, en un espacio moderno y con aranceles razonables. Las consultas generales pueden agendarse directamente.',
			'puntos'      => "Control oftalmológico periódico\nEvaluación de la agudeza visual\nReceta de lentes ópticos\nDerivación a exámenes específicos",
			'examenes'    => array( $ex['tension'] ),
		) ),
		array( 'Oftalmología infantil', array(
			'icono'       => 'nino',
			'resumen'     => 'Atención oftalmológica para niños desde los 0 años, con indicaciones especiales para cada edad.',
			'descripcion' => 'Atendemos a niños desde los 0 años de edad con un trato cercano. Cada profesional atiende desde una edad específica, por lo que te orientamos al momento de agendar. Para pacientes menores de 10 años, se debe solicitar hora en la mañana antes de las 11:00 o en la tarde antes de las 17:00 horas.',
			'puntos'      => "Consulta oftalmológica infantil\nEvaluación de desviaciones oculares\nControl del uso de lentes",
			'examenes'    => array( $ex['estrabismo'], $ex['ortoptico'] ),
		) ),
		array( 'Glaucoma y presión ocular', array(
			'icono'         => 'presion',
			'resumen'       => 'Medición y seguimiento de la presión intraocular y del campo visual.',
			'descripcion'   => 'Contamos con un especialista en glaucoma y con exámenes para medir la presión intraocular, su variación durante el día y el estado del campo visual, apoyando el diagnóstico y el seguimiento de cada paciente.',
			'puntos'        => "Medición de presión intraocular\nCurva de presión durante el día\nEstudio del campo visual\nMedición del grosor corneal",
			'profesionales' => array( $pro['Dr. Carlos Van Grieken'] ),
			'examenes'      => array( $ex['tension'], $ex['cta'], $ex['campimetria'], $ex['paquimetria'] ),
		) ),
		array( 'Retina', array(
			'icono'         => 'retina',
			'resumen'       => 'Diagnóstico y seguimiento de enfermedades de la retina y la mácula.',
			'descripcion'   => 'Nuestras retinólogas evalúan, diagnostican y controlan las enfermedades de la retina, la capa del ojo encargada de captar la luz y enviar las imágenes al cerebro. Un control oportuno es clave para cuidar la visión central, especialmente en pacientes con diabetes o mayores de 50 años.',
			'puntos'        => "Maculopatías\nRetinopatía diabética\nDMRE (degeneración macular relacionada con la edad)",
			'profesionales' => array( $pro['Dra. Eylym Sánchez Sálcedo'], $pro['Dra. Ruth Soto'] ),
		) ),
	);
	foreach ( $especialidades as $i => $e ) {
		ell_demo_post( 'ell_especialidad', $e[0], $e[1], $i + 1 );
	}

	/* ---------- Formulario CF7 ---------- */
	if ( class_exists( 'WPCF7_ContactForm' ) && ! get_post( (int) get_option( 'ell_cf7_form_id' ) ) ) {
		$form = WPCF7_ContactForm::get_template( array( 'title' => 'Solicitud de hora' ) );
		$form->set_properties( array(
			'form' => ell_cf7_form_template(),
			'mail' => array(
				'active'             => true,
				'subject'            => 'Solicitud de hora: [servicio] — [nombre]',
				'sender'             => '[_site_title] <wordpress@' . wp_parse_url( home_url(), PHP_URL_HOST ) . '>',
				'recipient'          => '[_site_admin_email]',
				'body'               => "Nombre: [nombre]\nTeléfono: [telefono]\nCorreo: [correo]\nServicio: [servicio]\nProfesional: [profesional]\n\nMensaje:\n[mensaje]\n\n--\nEnviado desde [_site_url]",
				'additional_headers' => 'Reply-To: [correo]',
				'attachments'        => '',
				'use_html'           => false,
				'exclude_blank'      => true,
			),
		) );
		$form->save();
		update_option( 'ell_cf7_form_id', $form->id() );
	}

	/* ---------- Páginas ---------- */
	$pages = array(
		'inicio'         => array( 'Inicio', '', array( 'hero-inicio', 'destacados', 'urgencias', 'especialidades-inicio', 'examenes-inicio', 'nosotros-inicio', 'llamado-agendar' ) ),
		'nosotros'       => array( 'Nosotros', 'Un equipo humano y profesional dedicado a la salud visual de la comunidad.', array( 'quienes-somos', 'mision-vision-valores', 'equipo', 'instalaciones', 'llamado-agendar' ) ),
		'especialidades' => array( 'Especialidades', 'Áreas de atención oftalmológica para adultos y niños.', array( 'especialidades-detalle', 'preguntas-especialidades', 'llamado-agendar' ) ),
		'examenes'       => array( 'Exámenes', 'Descripción, duración, código e indicaciones de preparación de cada examen.', array( 'examenes-lista', 'indicaciones-preguntas', 'llamado-agendar' ) ),
		'contacto'       => array( 'Contacto y agenda', 'Agenda tu hora por teléfono o WhatsApp, o déjanos tus datos y te contactamos.', array( 'contacto', 'reserva-horas', 'mapa' ) ),
	);
	$ids = array();
	foreach ( $pages as $slug => $pg ) {
		$found = get_page_by_path( $slug );
		if ( $found ) {
			$ids[ $slug ] = $found->ID;
			continue;
		}
		$content      = implode( "\n\n", array_map( 'ell_pattern', $pg[2] ) );
		$ids[ $slug ] = wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $pg[0],
			'post_name'    => $slug,
			'post_excerpt' => $pg[1],
			'post_content' => wp_slash( $content ),
		) );
	}
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids['inicio'] );

	/* ---------- Menús (Apariencia › Menús) ---------- */
	ell_demo_menus( $ids );

	/* ---------- Enlaces permanentes ---------- */
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	flush_rewrite_rules();
}
