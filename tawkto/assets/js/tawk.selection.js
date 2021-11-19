// variables
const currentHost = window.location.protocol + '//' + window.location.host;
const iframeUrl = tawk_selection_data.url.iframe + '&parentDomain=' + currentHost;
const baseUrl = tawk_selection_data.url.base;

jQuery( '#tawk-iframe' ).attr( 'src', iframeUrl );

window.addEventListener( 'message', function ( e ) {
	if ( baseUrl === e.origin ) {

		if ( 'setWidget' === e.data.action ) {
			setWidget( e );
		}

		if ( 'removeWidget' === e.data.action ) {
			removeWidget( e );
		}

		if ( 'reloadHeight' === e.data.action ) {
			reloadIframeHeight( e.data.height );
		}
	}
});

function setWidget ( e ) {
	const data = {
		pageId : e.data.pageId,
		widgetId : e.data.widgetId,
		nonce : tawk_selection_data.nonce.setWidget
	};

	jQuery.ajax( {
		type : 'POST',
		url : ajaxurl + '?action=tawkto_setwidget',
		contentType : 'application/json',
		dataType : 'json',
		data : JSON.stringify( data ),
		success : function ( r ) {
			if ( !r.success ) {
				return e.source.postMessage( { action: 'setFail' }, baseUrl );
			}
			e.source.postMessage( { action: 'setDone' }, baseUrl );
		},
		error : function () {
			e.source.postMessage( { action: 'setFail' }, baseUrl );
		}
	} );
}

function removeWidget ( e ) {
	const data = {
		nonce : tawk_selection_data.nonce.removeWidget
	};

	jQuery.ajax( {
		type : 'POST',
		url : ajaxurl + '?action=tawkto_removewidget',
		contentType : 'application/json',
		dataType : 'json',
		data : JSON.stringify( data ),
		success : function ( r ) {
			if ( !r.success ) {
				return e.source.postMessage( { action: 'removeFail' }, baseUrl );
			}
			e.source.postMessage( { action: 'removeDone' }, baseUrl );
		},
		error : function () {
			e.source.postMessage( { action: 'removeFail' }, baseUrl );
		}
	});
}

function reloadIframeHeight( height ) {
	if ( !height ) {
		return;
	}

	const iframe = jQuery( '#tawk-iframe' );
	if ( height === iframe.height() ) {
		return;
	}

	iframe.height( height );
}