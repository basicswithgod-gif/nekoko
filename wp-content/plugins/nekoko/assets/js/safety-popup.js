( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var overlay = document.getElementById( 'nekoko-safety-overlay' );
		if ( ! overlay || typeof nekokoSafetyData === 'undefined' ) {
			return;
		}

		var checkbox = document.getElementById( 'nekoko-safety-agree' );
		var button   = document.getElementById( 'nekoko-safety-confirm' );

		checkbox.addEventListener( 'change', function () {
			button.disabled       = ! checkbox.checked;
			button.style.opacity  = checkbox.checked ? '1' : '.5';
			button.style.cursor   = checkbox.checked ? 'pointer' : 'not-allowed';
		} );

		button.addEventListener( 'click', function () {
			if ( ! checkbox.checked ) {
				return;
			}
			fetch( nekokoSafetyData.ajaxUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: 'action=nekoko_safety_ack&nonce=' + encodeURIComponent( nekokoSafetyData.nonce ),
			} ).then( function () {
				overlay.remove();
			} );
		} );
	} );
} )();
