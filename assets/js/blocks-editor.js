/* El Llano — registro de bloques dinámicos en el editor (sin compilación) */
( function ( wp, data ) {
	var el = wp.element.createElement;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var ServerSideRender = wp.serverSideRender;

	Object.keys( data.defs ).forEach( function ( slug ) {
		var def = data.defs[ slug ];
		var name = 'ell/' + slug;
		var attributes = {};
		Object.keys( def[ 2 ] || {} ).forEach( function ( k ) {
			attributes[ k ] = { type: 'string', 'default': def[ 2 ][ k ] };
		} );

		wp.blocks.registerBlockType( name, {
			apiVersion: 3,
			title: def[ 0 ],
			icon: def[ 1 ],
			category: 'el-llano',
			attributes: attributes,
			supports: { html: false, align: [ 'wide', 'full' ] },
			usesContext: [ 'postId', 'postType' ],
			edit: function ( props ) {
				var controls = [];
				Object.keys( def[ 3 ] || {} ).forEach( function ( k ) {
					var c = def[ 3 ][ k ];
					var source = c[ 1 ] === 'icons' ? data.icons : c[ 1 ];
					controls.push( el( SelectControl, {
						key: k,
						label: c[ 0 ],
						value: props.attributes[ k ],
						options: Object.keys( source ).map( function ( v ) { return { value: v, label: source[ v ] }; } ),
						onChange: function ( v ) { var o = {}; o[ k ] = v; props.setAttributes( o ); },
						__nextHasNoMarginBottom: true
					} ) );
				} );
				return el( 'div', useBlockProps( { className: 'ell-editor-preview' } ),
					controls.length ? el( InspectorControls, null, el( PanelBody, { title: 'Opciones' }, controls ) ) : null,
					el( 'div', { style: { pointerEvents: 'none' } },
						el( ServerSideRender, { block: name, attributes: props.attributes, urlQueryArgs: props.context && props.context.postId ? { post_id: props.context.postId } : {} } )
					)
				);
			},
			save: function () { return null; }
		} );
	} );
} )( window.wp, window.ellBlocks );
