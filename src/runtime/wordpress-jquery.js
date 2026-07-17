/**
 * Makes WordPress' registered jQuery available to Vite dependencies.
 *
 * AssetManager declares the `jquery` script handle before the Vite module.
 */
const jQuery = window.jQuery;

if ( ! jQuery ) {
	throw new Error( 'Webbooks requires the WordPress jQuery handle before the Vite bundle.' );
}

export default jQuery;
export { jQuery };
