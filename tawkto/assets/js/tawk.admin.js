'use strict';

jQuery(
	function() {
		document.getElementById( 'account-settings-tab' ).click();

		jQuery( '.tawk-selected-display input[type="checkbox"]' ).change(
			function() {
				if ( this.checked ) {
					jQuery( '#always-display' ).prop( 'checked', false );
				}
			}
		);

		jQuery( '#always-display' ).change(
			function() {
				if ( this.checked ) {
					jQuery( '#show-onfrontpage' ).prop( 'checked', false );
					jQuery( '#show-oncategory' ).prop( 'checked', false );
					jQuery( '#show-ontagpage' ).prop( 'checked', false );
					jQuery( '#show-onarticlepages' ).prop( 'checked', false );
					jQuery( '#include-url' ).prop( 'checked', false );
					jQuery( '#included-urls-container' ).fadeOut();
				}
			}
		);

		jQuery( '#exclude-url' ).change(
			function() {
				if ( this.checked ) {
					jQuery( '#exlucded-urls-container' ).fadeIn();
				} else {
					jQuery( '#exlucded-urls-container' ).fadeOut();
				}
			}
		);

		if ( jQuery( '#include-url' ).prop( 'checked' ) ) {
			jQuery( '#included-urls-container' ).show();
		}

		jQuery( '#include-url' ).change(
			function() {
				if ( this.checked ) {
					jQuery( '#included-urls-container' ).fadeIn();
				} else {
					jQuery( '#included-urls-container' ).fadeOut();
				}
			}
		);

		if ( jQuery( '#exclude-url' ).prop( 'checked' ) ) {
			jQuery( '#exlucded-urls-container' ).fadeIn();
		}
	}
);

function opentab( evt, tabName ) {
	var i, tabcontent, tablinks;

	tabcontent = document.getElementsByClassName( 'tawk-tab-content' );
	for ( i = 0; i < tabcontent.length; i++ ) {
		tabcontent[i].style.display = 'none';
	}

	tablinks = document.getElementsByClassName( 'tawk-tab-links' );
	for ( i = 0; i < tablinks.length; i++ ) {
		tablinks[i].className = tablinks[i].className.replace( ' active', '' );
	}

	document.getElementById( tabName ).style.display = 'block';

	evt.currentTarget.className += ' active';
}
