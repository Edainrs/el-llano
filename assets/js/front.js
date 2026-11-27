/* El Llano — comportamiento del sitio */
( function () {
	// Abre el examen enlazado (#ex-...) y lleva la vista hasta él.
	function abrirDesdeHash() {
		if ( ! location.hash ) return;
		var el;
		try { el = document.querySelector( location.hash ); } catch ( e ) { return; }
		if ( el && el.tagName === 'DETAILS' ) {
			el.open = true;
			setTimeout( function () { el.scrollIntoView( { behavior: 'smooth', block: 'start' } ); }, 60 );
		}
	}
	window.addEventListener( 'hashchange', abrirDesdeHash );
	document.addEventListener( 'DOMContentLoaded', abrirDesdeHash );

	// Respaldo para navegadores sin acordeón exclusivo (<details name>): cierra los demás.
	document.addEventListener( 'toggle', function ( e ) {
		var d = e.target;
		if ( d.tagName !== 'DETAILS' || ! d.open || ! d.getAttribute( 'name' ) ) return;
		document.querySelectorAll( 'details[name="' + d.getAttribute( 'name' ) + '"][open]' ).forEach( function ( o ) {
			if ( o !== d ) o.open = false;
		} );
	}, true );

	// Explicación del servicio elegido en el formulario.
	document.addEventListener( 'change', function ( e ) {
		var s = e.target;
		if ( ! s.matches || ! s.matches( 'select' ) ) return;
		var wrap = s.closest( '.wpcf7-form-control-wrap' );
		var help = wrap && wrap.nextElementSibling;
		if ( ! help || ! help.classList.contains( 'ell-ayuda' ) ) return;
		var opt = s.options[ s.selectedIndex ];
		help.textContent = ( opt && opt.dataset.ayuda ) || '';
	} );
} )();
