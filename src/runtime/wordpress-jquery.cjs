/**
 * CommonJS bridge for Slick's require( 'jquery' ).
 *
 * AssetManager declares the WordPress jquery handle before the Vite module.
 */
if ( ! window.jQuery ) {
	throw new Error( 'Webbooks requires the WordPress jQuery handle before the Vite bundle.' );
}

module.exports = window.jQuery;
