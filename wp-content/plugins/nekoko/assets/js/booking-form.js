( function () {
	'use strict';

	function showStep( wrap, stepNumber ) {
		wrap.querySelectorAll( '.nekoko-booking-step' ).forEach( function ( step ) {
			step.style.display = step.getAttribute( 'data-step' ) === String( stepNumber ) ? 'block' : 'none';
		} );
	}

	function initForm( wrap ) {
		var step1Next    = wrap.querySelector( '.nekoko-booking-step1-next' );
		var step2Back    = wrap.querySelector( '.nekoko-booking-step2-back' );
		var reviewButton = wrap.querySelector( '.nekoko-booking-review' );
		var backButton   = wrap.querySelector( '.nekoko-booking-back' );
		var confirmButton = wrap.querySelector( '.nekoko-booking-confirm' );
		var submitInput  = wrap.querySelector( '.nekoko-booking-submit' );

		// Step 1 → Step 2: validate name + email
		step1Next.addEventListener( 'click', function () {
			var name  = wrap.querySelector( '[data-field="name"]' ).value.trim();
			var email = wrap.querySelector( '[data-field="email"]' ).value.trim();
			if ( ! name || ! email ) {
				alert( 'Popunite ime i email adresu.' );
				return;
			}
			showStep( wrap, 2 );
		} );

		// Step 2 → Step 1
		step2Back.addEventListener( 'click', function () {
			showStep( wrap, 1 );
		} );

		// Step 2 → Step 3: validate message, populate review table
		reviewButton.addEventListener( 'click', function () {
			var name    = wrap.querySelector( '[data-field="name"]' ).value.trim();
			var email   = wrap.querySelector( '[data-field="email"]' ).value.trim();
			var message = wrap.querySelector( '[data-field="message"]' ).value.trim();
			var date    = wrap.querySelector( '[data-field="date"]' ).value;

			if ( ! message ) {
				alert( 'Unesite poruku / poseban zahtev.' );
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

			showStep( wrap, 3 );
		} );

		// Step 3 → Step 2
		backButton.addEventListener( 'click', function () {
			showStep( wrap, 2 );
		} );

		// Step 3: confirm → submit
		confirmButton.addEventListener( 'click', function () {
			submitInput.click();
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.nekoko-booking-form' ).forEach( initForm );
	} );
} )();
