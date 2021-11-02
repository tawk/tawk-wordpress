// variables
var currentHost = window.location.protocol + "//" + window.location.host;
var iframeUrl = tawk_selection_data.url.iframe + '&parentDomain=' + currentHost; // "<?php echo $iframe_url ?>&parentDomain=" + currentHost;
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
	jQuery.post(ajaxurl, {
		action : 'tawkto_setwidget',
		pageId : e.data.pageId,
		widgetId : e.data.widgetId,
		nonce : tawk_selection_data.nonce.setWidget
	}, function (r) {
		if (!r.success) {
			return e.source.postMessage({action: 'setFail'}, baseUrl);
		}
		e.source.postMessage({action: 'setDone'}, baseUrl);
	});
}

function removeWidget (e) {
	jQuery.post(ajaxurl, {
		action : 'tawkto_removewidget',
		nonce : tawk_selection_data.nonce.removeWidget
	}, function (r) {
		if (!r.success) {
			return  e.source.postMessage({action: 'removeFail'}, baseUrl);
		}
		e.source.postMessage({action: 'removeDone'}, baseUrl);
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