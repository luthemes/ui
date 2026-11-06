/**
 * Builds `dist/chassis.js`, the script the service providers load. It wraps the
 * app navigation so it runs on its own, with no build step in the theme.
 */

import { mkdirSync, readFileSync, writeFileSync } from 'node:fs';

const source = readFileSync( new URL( '../js/app-nav.js', import.meta.url ), 'utf8' )
	.replace( /^export default .*$/m, '' )
	.replace( /^export function/m, 'function' )
	.trim();

mkdirSync( new URL( '../dist/', import.meta.url ), { recursive: true } );

writeFileSync(
	new URL( '../dist/chassis.js', import.meta.url ),
	`( function () {\n${ source }\n\nappNav();\n} )();\n`
);
