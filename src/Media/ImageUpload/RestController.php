<?php
/**
 * Application-password image upload endpoint for the cloud publishing process.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Media\ImageUpload;

final class RestController {
	public const ROUTE = '/webbooks-ai/v1/images';

	private readonly ImageValidator $validator;
	private readonly \Closure $storage_factory;

	public function __construct( ?ImageValidator $validator = null, ?\Closure $storage_factory = null ) {
		$this->validator = $validator ?? new ImageValidator();
		$this->storage_factory = $storage_factory ?? \Closure::fromCallable( array( R2Storage::class, 'fromEnvironment' ) );
	}

	public static function register(): void {
		add_action( 'rest_api_init', static function (): void { ( new self() )->registerRoutes(); } );
		add_filter( 'rest_post_dispatch', array( self::class, 'preventCaching' ), 10, 3 );
	}

	public function registerRoutes(): void {
		register_rest_route(
			'webbooks-ai/v1',
			'/images',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'upload' ),
				'permission_callback' => array( $this, 'permissions' ),
			)
		);
	}

	public function permissions(): true|\WP_Error {
		// Authentication is entirely WordPress core; no Basic header or password is parsed here.
		if ( ! is_ssl() || ! is_user_logged_in() || ! rest_get_authenticated_app_password() || ! current_user_can( 'upload_files' ) ) {
			return new \WP_Error(
				'rest_cannot_create',
				'An HTTPS WordPress Application Password with upload_files permission is required.',
				array( 'status' => rest_authorization_required_code() )
			);
		}
		return true;
	}

	public function upload( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
		if ( strtolower( trim( explode( ';', (string) $request->get_header( 'Content-Type' ), 2 )[0] ) ) !== 'multipart/form-data' ) {
			return UploadError::UNSUPPORTED_TYPE->response();
		}
		$query = array_diff_key( $request->get_query_params(), array_flip( array( 'rest_route', '_locale' ) ) );
		if ( array() !== $request->get_body_params() || array() !== $query ) {
			return UploadError::INVALID_REQUEST->response();
		}
		$image = $this->validator->validate(
			$request->get_file_params(),
			(string) $request->get_header( 'Content-Type' ),
			max( 0, (int) $request->get_header( 'Content-Length' ) )
		);
		if ( is_wp_error( $image ) ) {
			return $image;
		}
		try {
			$storage = ( $this->storage_factory )();
			if ( is_wp_error( $storage ) ) {
				return $storage;
			}
			$created = $storage->store( $image );
			if ( is_wp_error( $created ) ) {
				return $created;
			}
			return new \WP_REST_Response( $image->payload( $created ), $created ? 201 : 200, array( 'Cache-Control' => 'no-store' ) );
		} catch ( \Throwable ) {
			return UploadError::STORAGE_UNAVAILABLE->response();
		} finally {
			// PHP also cleans upload temporaries at request shutdown. No media-library file is created.
			if ( is_uploaded_file( $image->path ) ) {
				@unlink( $image->path );
			}
		}
	}

	public static function preventCaching( \WP_HTTP_Response $response, \WP_REST_Server $server, \WP_REST_Request $request ): \WP_HTTP_Response {
		if ( strcasecmp( self::ROUTE, rtrim( $request->get_route(), '/' ) ) === 0 ) {
			$response->header( 'Cache-Control', 'no-store' );
		}
		return $response;
	}
}
