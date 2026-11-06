( function () {
/**
 * App navigation
 *
 * Opens and closes the mobile menu for every `.app-nav` on the page. Pairs
 * with `scss/06.layouts/03.navigation`.
 *
 *   import { appNav } from '../../vendor/techkodeninja/chassis/js/app-nav.js';
 *   appNav();
 *
 * - The Menu button is `.app-nav__toggle`, or the `.menu-toggle` that Novaris
 *   prints. It toggles `.app-nav--open` and keeps `aria-expanded` in sync.
 * - Escape closes the menu and returns focus to the button.
 * - Clicking outside the menu closes it.
 * - Widening the screen past the mobile breakpoint resets it to closed.
 *
 * @package Techkodeninja\Chassis
 * @license GPL-2.0-or-later
 */

function appNav( root = document ) {
	root.querySelectorAll( '.app-nav' ).forEach( ( nav ) => {
		const toggle = nav.querySelector( '.app-nav__toggle, .menu-toggle' );

		if ( ! toggle || toggle.dataset.appNav ) {
			return;
		}

		// Guard against wiring the same menu twice.
		toggle.dataset.appNav = 'ready';

		const setOpen = ( open ) => {
			nav.classList.toggle( 'app-nav--open', open );
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		};

		const isOpen = () => nav.classList.contains( 'app-nav--open' );

		toggle.addEventListener( 'click', () => setOpen( ! isOpen() ) );

		nav.addEventListener( 'keydown', ( event ) => {
			if ( 'Escape' === event.key && isOpen() ) {
				setOpen( false );
				toggle.focus();
			}
		} );

		document.addEventListener( 'click', ( event ) => {
			if ( isOpen() && ! nav.contains( event.target ) ) {
				setOpen( false );
			}
		} );

		// The button is hidden on wider screens, so close the menu there.
		window.addEventListener( 'resize', () => {
			if ( isOpen() && 'none' === getComputedStyle( toggle ).display ) {
				setOpen( false );
			}
		} );
	} );
}

appNav();
} )();
