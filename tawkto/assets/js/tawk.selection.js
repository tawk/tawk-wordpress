// variables
var currentHost = window.location.protocol + '//' + window.location.host;
var iframeUrl = tawk_selection_data.url.iframe + '&parentDomain=' + currentHost;
var baseUrl = tawk_selection_data.url.base;

jQuery('#tawkIframe').attr('src', iframeUrl);

window.addEventListener('message', function (e) {
	if(e.origin === baseUrl) {

		if(e.data.action === 'setWidget') {
			setWidget(e);
		}

		if(e.data.action === 'removeWidget') {
			removeWidget(e);
		}

		if(e.data.action === 'reloadHeight') {
			reloadIframeHeight(e.data.height);
		}
	}
});

function setWidget (e) {
	const data = {
		pageId : e.data.pageId,
		widgetId : e.data.widgetId,
		nonce : tawk_selection_data.nonce.setWidget
	};

	jQuery.ajax({
		type : 'POST',
		url : ajaxurl + '?action=tawkto_setwidget',
		contentType : 'application/json',
		dataType : 'json',
		data : JSON.stringify(data),
		success : function (r) {
			if (!r.success) {
				return e.source.postMessage({action: 'setFail'}, baseUrl);
			}
			e.source.postMessage({action: 'setDone'}, baseUrl);
		},
		error : function () {
			e.source.postMessage({action: 'setFail'}, baseUrl);
		}
	});
}

function removeWidget (e) {
	const data = {
		nonce : tawk_selection_data.nonce.removeWidget
	}

	jQuery.ajax({
		type : 'POST',
		url : ajaxurl + '?action=tawkto_removewidget',
		contentType : 'application/json',
		dataType : 'json',
		data : JSON.stringify(data),
		success : function (r) {
			if (!r.success) {
				return e.source.postMessage({action: 'removeFail'}, baseUrl);
			}
			e.source.postMessage({action: 'removeDone'}, baseUrl);
		},
		error : function () {
			e.source.postMessage({action: 'removeFail'}, baseUrl);
		}
	});
}

function reloadIframeHeight(height) {
	if (!height) {
		return;
	}

	var iframe = jQuery('#tawkIframe');
	if (height === iframe.height()) {
		return;
	}

	iframe.height(height);
}