( function () {
	'use strict';

	document.addEventListener( 'click', function ( event ) {
		var link = event.target.closest( 'a[href^="#"]' );
		if ( ! link ) {
			return;
		}
		var target = document.querySelector( link.getAttribute( 'href' ) );
		if ( ! target ) {
			return;
		}
		event.preventDefault();
		window.scrollTo( {
			top: target.getBoundingClientRect().top + window.scrollY - 80,
			behavior: 'smooth',
		} );
	} );

	document.addEventListener( 'DOMContentLoaded', function () {
		var path = window.location.pathname;
		document.querySelectorAll( '.nekoko-site-header__links a, .dashboard-sidebar a' ).forEach( function ( link ) {
			var href = link.getAttribute( 'href' );
			if ( href === path || ( href !== '/' && path.indexOf( href ) > -1 ) ) {
				link.classList.add( 'active' );
			}
		} );

		var hamburger = document.querySelector( '.nekoko-site-header__hamburger' );
		var nav       = document.getElementById( 'nekoko-primary-nav' );
		if ( hamburger && nav ) {
			hamburger.addEventListener( 'click', function () {
				var isOpen = nav.classList.toggle( 'is-open' );
				hamburger.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
			} );
			document.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'Escape' && nav.classList.contains( 'is-open' ) ) {
					nav.classList.remove( 'is-open' );
					hamburger.setAttribute( 'aria-expanded', 'false' );
					hamburger.focus();
				}
			} );
		}
	} );
} )();
