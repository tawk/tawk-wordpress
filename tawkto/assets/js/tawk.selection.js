'use strict';

var currentHost = window.location.protocol + '//' + window.location.host;
var iframeUrl   = tawkSelectionData.url.iframe + '&parentDomain=' + currentHost;
var baseUrl     = tawkSelectionData.url.base;

jQuery( '#tawk-iframe' ).attr( 'src', iframeUrl );

window.addEventListener(
	'message',
	function( e ) {
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
	}
);

function setWidget( e ) {
	var data = {
		pageId: e.data.pageId,
		widgetId: e.data.widgetId,
		nonce: tawkSelectionData.nonce.setWidget
	};

	jQuery.ajax(
		{
			type: 'POST',
			url: ajaxurl + '?action=tawkto_setwidget',
			contentType: 'application/json',
			dataType: 'json',
			data: JSON.stringify( data ),
			success: function( r ) {
				if ( ! r.success ) {
					return e.source.postMessage({ action: 'setFail' }, baseUrl );
				}
				e.source.postMessage({ action: 'setDone' }, baseUrl );
			},
			error: function() {
				e.source.postMessage({ action: 'setFail' }, baseUrl );
			}
		}
	);
}

function removeWidget( e ) {
	var data = {
		nonce: tawkSelectionData.nonce.removeWidget
	};

	jQuery.ajax(
		{
			type: 'POST',
			url: ajaxurl + '?action=tawkto_removewidget',
			contentType: 'application/json',
			dataType: 'json',
			data: JSON.stringify( data ),
			success: function( r ) {
				if ( ! r.success ) {
					return e.source.postMessage({ action: 'removeFail' }, baseUrl );
				}
				e.source.postMessage({ action: 'removeDone' }, baseUrl );
			},
			error: function() {
				e.source.postMessage({ action: 'removeFail' }, baseUrl );
			}
		}
	);
}

function reloadIframeHeight( height ) {
	var iframe = jQuery( '#tawk-iframe' );

	if ( ! height ) {
		return;
	}
	if ( height === iframe.height() ) {
		return;
	}

	iframe.height( height );
}
