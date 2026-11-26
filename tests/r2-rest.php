<?php
/**
 * Native WordPress Application Password and REST permission/response tests.
 * Included by r2-upload.php inside the isolated database guard.
 */

use Webbooks\Media\ImageUpload\RestController;

$server = rest_get_server();
$transport = new R2TestTransport();
$controller = new RestController( $validator, static fn() => $transport->storage() );
register_rest_route( 'webbooks-ai/v1', '/images', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( $controller, 'upload' ), 'permission_callback' => array( $controller, 'permissions' ) ), true );
$request = static function ( string $content_type = 'multipart/form-data; boundary=qa' ) use ( $png_path ): WP_REST_Request {
	$r = new WP_REST_Request( 'POST', RestController::ROUTE );
	$r->set_header( 'Content-Type', $content_type );
	$r->set_header( 'X-Webbooks-Edge-Key', 'local-tests-do-not-implement-cloudflare' );
	$r->set_file_params( r2_test_file( $png_path ) );
	return $r;
};
$dispatch = static function ( WP_REST_Request $request ) use ( $server ): WP_REST_Response {
	$response = $server->dispatch( $request );
	return apply_filters( 'rest_post_dispatch', $response, $server, $request );
};
$r2_accounts = array();
foreach ( array( 'administrator', 'subscriber' ) as $role ) {
	$login = 'r2_qa_' . $role . '_' . bin2hex( random_bytes( 4 ) );
	$id = wp_insert_user( array( 'user_login' => $login, 'user_pass' => wp_generate_password( 30 ), 'role' => $role ) );
	if ( is_wp_error( $id ) ) { throw new RuntimeException( 'Cannot create isolated test user' ); }
	$user_ids[] = $id;
	$credential = WP_Application_Passwords::create_new_application_password( $id, array( 'name' => 'Isolated R2 upload tests' ) );
	if ( is_wp_error( $credential ) ) { throw new RuntimeException( 'Cannot create isolated application password' ); }
	$r2_accounts[ $role ] = array( 'id' => $id, 'login' => $login, 'password' => $credential[0] );
}
$authenticate = static function ( array $account ): void {
	wp_set_current_user( 0 );
	rest_application_password_collect_status( null );
	$user = wp_authenticate_application_password( null, $account['login'], $account['password'] );
	r2_test_assert( $user instanceof WP_User && $user->ID === $account['id'], 'WordPress core validates the test Application Password' );
	wp_set_current_user( $user->ID );
};

wp_set_current_user( 0 ); rest_application_password_collect_status( null );
$response = $dispatch( $request() );
r2_test_assert( $response->get_status() === 401 && count( $transport->requests ) === 0, 'An edge header alone never authorizes an upload' );
r2_test_assert( ( $response->get_headers()['Cache-Control'] ?? '' ) === 'no-store', 'Unauthorized REST responses are not cacheable' );

wp_set_current_user( $r2_accounts['administrator']['id'] );
$response = $dispatch( $request() );
r2_test_assert( $response->get_status() === 403 && count( $transport->requests ) === 0, 'A cookie-style current user without an Application Password cannot upload' );

$authenticate( $r2_accounts['subscriber'] );
$response = $dispatch( $request() );
r2_test_assert( $response->get_status() === 403 && count( $transport->requests ) === 0, 'An authenticated user without upload_files cannot create objects' );

$authenticate( $r2_accounts['administrator'] );
$_SERVER['HTTPS'] = 'off';
$response = $dispatch( $request() );
r2_test_assert( $response->get_status() === 403 && count( $transport->requests ) === 0, 'The route refuses insecure transport even for an authenticated uploader' );
$_SERVER['HTTPS'] = 'on';

$posts_before = (int) $GLOBALS['wpdb']->get_var( "SELECT COUNT(*) FROM {$GLOBALS['wpdb']->posts}" );
$response = $dispatch( $request() );
$data = $response->get_data();
r2_test_assert( $response->get_status() === 201 && $data['created'] === true && $data['width'] === 1200 && $data['height'] === 675, 'The REST response returns 201 and exact dimensions for a new image' );
r2_test_assert( array_keys( $data ) === array( 'url', 'sha256', 'mime_type', 'bytes', 'width', 'height', 'created' ), 'The public response contains only contract fields, with no attachment ID or temporary path' );
r2_test_assert( str_starts_with( $data['url'], 'https://data.webbooks.com.ua/articles/images/sha256/' ), 'The response uses the fixed public R2 domain' );
$duplicate = $request(); $params = $duplicate->get_file_params(); $params['file']['name'] = 'different-language-and-name.svg'; $duplicate->set_file_params( $params );
$second = $dispatch( $duplicate );
$second_data = $second->get_data();
r2_test_assert( $second->get_status() === 200 && $second_data['created'] === false, 'A renamed duplicate returns 200 and created false' );
unset( $data['created'], $second_data['created'] );
r2_test_assert( $data === $second_data && count( $transport->objects ) === 1, 'The URL and all identity fields remain identical across filenames' );

$json = $request( 'application/json' ); $json->set_body( '{"url":"https://example.test/a.png"}' ); $json->set_file_params( array() );
r2_test_assert( $dispatch( $json )->get_status() === 415, 'JSON and external-URL upload requests are unsupported' );
$extra = $request(); $extra->set_body_params( array( 'path' => '../../not-a-destination' ) );
r2_test_assert( $dispatch( $extra )->get_status() === 400, 'Client-supplied destination fields are rejected' );
$extra = $request(); $extra->set_query_params( array( 'bucket' => 'not-a-destination' ) );
r2_test_assert( $dispatch( $extra )->get_status() === 400, 'Client-supplied bucket query parameters are rejected' );
$empty = $request(); $empty->set_file_params( array() );
r2_test_assert( $dispatch( $empty )->get_status() === 400, 'A multipart request without a file is rejected' );

$unconfigured = new RestController( $validator, static fn() => \Webbooks\Media\ImageUpload\UploadError::STORAGE_UNAVAILABLE->response() );
register_rest_route( 'webbooks-ai/v1', '/images', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( $unconfigured, 'upload' ), 'permission_callback' => array( $unconfigured, 'permissions' ) ), true );
$response = $dispatch( $request() );
r2_test_assert( $response->get_status() === 503 && ! isset( $response->get_data()['url'] ), 'Missing storage configuration never produces a fictitious URL' );
r2_test_assert( ( $response->get_headers()['Cache-Control'] ?? '' ) === 'no-store', 'Storage error responses are not cacheable' );
$posts_after = (int) $GLOBALS['wpdb']->get_var( "SELECT COUNT(*) FROM {$GLOBALS['wpdb']->posts}" );
r2_test_assert( $posts_before === $posts_after, 'Image uploads create no posts, attachments or translation records' );
r2_test_assert( isset( $server->get_routes()['/wp/v2/media'] ), 'The existing WordPress media endpoint remains registered' );
wp_set_current_user( 0 ); rest_application_password_collect_status( null );
