/**
 * Builds `dist/chassis.js`, the script the service providers load. It joins the
 * scripts in `js/` so they run on their own, with no build step in the theme.
 */

import { mkdirSync, readFileSync, writeFileSync } from 'node:fs';

const read = ( file ) => readFileSync( new URL( `../js/${ file }`, import.meta.url ), 'utf8' )
	.replace( /^export default .*$/m, '' )
	.replace( /^export function/m, 'function' )
	.trim();

const source = [ 'app-nav.js', 'code-copy.js' ].map( read ).join( '\n\n' );

mkdirSync( new URL( '../dist/', import.meta.url ), { recursive: true } );

writeFileSync(
	new URL( '../dist/chassis.js', import.meta.url ),
	`( function () {\n${ source }\n\nappNav();\ncodeCopy();\n} )();\n`
);
