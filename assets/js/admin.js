/* El Llano — panel de administración */
jQuery( function ( $ ) {
	// Orden arrastrable en los listados.
	var $list = $( 'body.post-type-ell_profesional, body.post-type-ell_examen, body.post-type-ell_especialidad' ).find( 'table.wp-list-table #the-list' );
	if ( $list.length && ! /orderby=/.test( location.search ) ) {
		$list.sortable( {
			handle: '.ell-handle',
			axis: 'y',
			items: '> tr',
			placeholder: 'ell-sort-placeholder',
			update: function () {
				var ids = $list.children( 'tr' ).map( function () {
					return this.id.replace( 'post-', '' );
				} ).get();
				$list.addClass( 'ell-saving' );
				$.post( ellAdmin.ajax, { action: 'ell_orden', nonce: ellAdmin.nonce, ids: ids } ).always( function () {
					$list.removeClass( 'ell-saving' );
				} );
			}
		} );
	}

	// Relaciones ordenables (especialistas / exámenes de una especialidad).
	$( '.ell-relation' ).sortable( { axis: 'y', handle: '.dashicons-menu' } );
	$( document ).on( 'change', '.ell-relation input', function () {
		$( this ).closest( 'li' ).toggleClass( 'is-on', this.checked );
	} );

	// Selector de imagen en "Datos del centro".
	$( document ).on( 'click', '.ell-image-pick', function ( e ) {
		e.preventDefault();
		var $f = $( this ).closest( '.ell-image-field' );
		var frame = wp.media( { title: 'Elegir imagen', library: { type: 'image' }, button: { text: 'Usar esta imagen' }, multiple: false } );
		frame.on( 'select', function () {
			var a = frame.state().get( 'selection' ).first().toJSON();
			$f.find( 'input' ).val( a.id );
			$f.find( 'img' ).attr( 'src', ( a.sizes && a.sizes.medium ? a.sizes.medium.url : a.url ) ).show();
		} );
		frame.open();
	} );
	$( document ).on( 'click', '.ell-image-remove', function ( e ) {
		e.preventDefault();
		var $f = $( this ).closest( '.ell-image-field' );
		$f.find( 'input' ).val( '' );
		$f.find( 'img' ).hide();
	} );
} );
