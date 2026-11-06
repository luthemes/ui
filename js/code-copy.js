/**
 * Code copy
 *
 * Adds a Copy button to code blocks in post content, and announces the result
 * to screen readers. Pairs with `scss/07.components/09.code-copy`.
 *
 * @package Techkodeninja\Chassis
 */

export function codeCopy( root = document ) {
	if ( ! navigator.clipboard ) {
		return;
	}

	root.querySelectorAll( '.entry__content pre, .entry-content pre' ).forEach( ( block ) => {
		if ( block.dataset.codeCopy ) {
			return;
		}

		block.dataset.codeCopy = 'ready';

		// Read the text now, so it doesn't include the button's label.
		const text = ( block.querySelector( 'code' ) ?? block ).textContent;

		const button = document.createElement( 'button' );

		button.type = 'button';
		button.className = 'code-copy';
		button.textContent = 'Copy';
		button.setAttribute( 'aria-live', 'polite' );

		button.addEventListener( 'click', async () => {
			try {
				await navigator.clipboard.writeText( text );
				button.textContent = 'Copied';
			} catch {
				button.textContent = 'Copy failed';
			}

			setTimeout( () => {
				button.textContent = 'Copy';
			}, 2000 );
		} );

		block.append( button );
	} );
}

export default codeCopy;
