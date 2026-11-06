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
 * - Links with a submenu get a button that opens it, which helps on touch
 *   screens. It toggles `.app-nav__item--open` and keeps `aria-expanded` in sync.
 * - Escape closes the menu and returns focus to the button.
 * - Clicking outside the menu closes it.
 * - Widening the screen past the mobile breakpoint resets it to closed.
 *
 * @package Techkodeninja\Chassis
 * @license GPL-2.0-or-later
 */

export function appNav( root = document ) {
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

		const closeSubmenus = ( except ) => {
			nav.querySelectorAll( '.app-nav__item--open' ).forEach( ( item ) => {
				if ( ! except || ! item.contains( except ) ) {
					item.classList.remove( 'app-nav__item--open' );
					item.querySelector( ':scope > .app-nav__submenu-toggle' )?.setAttribute( 'aria-expanded', 'false' );
				}
			} );
		};

		nav.querySelectorAll( '.app-nav__menu li' ).forEach( ( item ) => {
			const submenu = item.querySelector( ':scope > ul' );
			const link = item.querySelector( ':scope > a' );

			if ( ! submenu || ! link ) {
				return;
			}

			const button = document.createElement( 'button' );

			button.type = 'button';
			button.className = 'app-nav__submenu-toggle';
			button.textContent = '▾';
			button.setAttribute( 'aria-expanded', 'false' );
			button.setAttribute( 'aria-label', `Submenu for ${ link.textContent.trim() }` );

			button.addEventListener( 'click', () => {
				const open = ! item.classList.contains( 'app-nav__item--open' );

				closeSubmenus( item );
				item.classList.toggle( 'app-nav__item--open', open );
				button.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			} );

			link.after( button );
		} );

		nav.classList.add( 'app-nav--ready' );

		toggle.addEventListener( 'click', () => setOpen( ! isOpen() ) );

		nav.addEventListener( 'keydown', ( event ) => {
			if ( 'Escape' !== event.key ) {
				return;
			}

			const openItem = event.target.closest( '.app-nav__item--open' );

			if ( openItem ) {
				closeSubmenus();
				openItem.querySelector( ':scope > .app-nav__submenu-toggle' )?.focus();
			} else if ( isOpen() ) {
				setOpen( false );
				toggle.focus();
			}
		} );

		document.addEventListener( 'click', ( event ) => {
			if ( ! nav.contains( event.target ) ) {
				closeSubmenus();

				if ( isOpen() ) {
					setOpen( false );
				}
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

export default appNav;
