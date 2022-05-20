'use strict';

jQuery(
	function() {
		document.getElementById( 'account-settings-tab' ).click();

		if ( jQuery( '#always-display' ).prop( 'checked' ) ) {
			jQuery( '.tawk-selected-display' ).hide();
			jQuery( '#show-onfrontpage' ).prop( 'disabled', true );
			jQuery( '#show-oncategory' ).prop( 'disabled', true );
			jQuery( '#show-ontagpage' ).prop( 'disabled', true );
			jQuery( '#show-onarticlepages' ).prop( 'disabled', true );
			jQuery( '#include-url' ).prop( 'disabled', true );
		} else {
			jQuery( '.tawk-selected-display' ).show();
		}

		jQuery( '#always-display' ).change(
			function() {
				if ( this.checked ) {
					jQuery( '.tawk-selected-display' ).fadeOut();
					jQuery( '#show-onfrontpage' ).prop( 'disabled', true );
					jQuery( '#show-oncategory' ).prop( 'disabled', true );
					jQuery( '#show-ontagpage' ).prop( 'disabled', true );
					jQuery( '#show-onarticlepages' ).prop( 'disabled', true );
					jQuery( '#include-url' ).prop( 'disabled', true );
				} else {
					jQuery( '.tawk-selected-display' ).fadeIn();
					jQuery( '#show-onfrontpage' ).prop( 'disabled', false );
					jQuery( '#show-oncategory' ).prop( 'disabled', false );
					jQuery( '#show-ontagpage' ).prop( 'disabled', false );
					jQuery( '#show-onarticlepages' ).prop( 'disabled', false );
					jQuery( '#include-url' ).prop( 'disabled', false );
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

		jQuery( '.tooltip' ).on( 'mouseenter', function() {
			var tooltipTextHeight = jQuery( this ).find( '.tooltiptext' ).height();
			if ( jQuery( '#url-exclusion' ).height() > tooltipTextHeight ) {
				jQuery( this ).removeClass( 'reverse' );
				return;
			}

			jQuery( this ).addClass( 'reverse' );
		});
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
