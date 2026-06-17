( function () {
	'use strict';

	function showStep( wrap, stepNumber ) {
		wrap.querySelectorAll( '.nekoko-booking-step' ).forEach( function ( step ) {
			step.style.display = step.getAttribute( 'data-step' ) === String( stepNumber ) ? 'block' : 'none';
		} );
	}

	function initForm( wrap ) {
		var reviewButton  = wrap.querySelector( '.nekoko-booking-review' );
		var backButton    = wrap.querySelector( '.nekoko-booking-back' );
		var confirmButton = wrap.querySelector( '.nekoko-booking-confirm' );
		var submitInput   = wrap.querySelector( '.nekoko-booking-submit' );

		reviewButton.addEventListener( 'click', function () {
			var name    = wrap.querySelector( '[data-field="name"]' ).value.trim();
			var email   = wrap.querySelector( '[data-field="email"]' ).value.trim();
			var message = wrap.querySelector( '[data-field="message"]' ).value.trim();
			var date    = wrap.querySelector( '[data-field="date"]' ).value;

			if ( ! name || ! email || ! message ) {
				alert( 'Popunite sva obavezna polja (ime, email, poruka).' );
				return;
			}

			wrap.querySelector( '[data-review="name"]' ).textContent    = name;
			wrap.querySelector( '[data-review="email"]' ).textContent   = email;
			wrap.querySelector( '[data-review="message"]' ).textContent = message;

			var dateRow = wrap.querySelector( '[data-review-row="date"]' );
			if ( date ) {
				wrap.querySelector( '[data-review="date"]' ).textContent = date;
				dateRow.style.display = '';
			} else {
				dateRow.style.display = 'none';
			}

			showStep( wrap, 2 );
		} );

		backButton.addEventListener( 'click', function () {
			showStep( wrap, 1 );
		} );

		confirmButton.addEventListener( 'click', function () {
			submitInput.click();
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.nekoko-booking-form' ).forEach( initForm );
	} );
} )();
