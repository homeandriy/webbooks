<?php

namespace Webbooks\Security;

class DisableApiUsers {

	public function __construct() {
		add_filter( 'rest_pre_dispatch', array( $this, 'restrictAnonymousAccess' ), 10, 3 );
	}

	public function restrictAnonymousAccess( $result, \WP_REST_Server $server, \WP_REST_Request $request ) {
		if ( $result !== null || is_user_logged_in() ) {
			return $result;
		}
		if ( preg_match( '~^/wp/v2/(?:users|comments)(?:/|$)~', $request->get_route() ) ) {
			return new \WP_Error( 'rest_forbidden', __( 'Authentication is required for this endpoint.', 'webbooks' ), array( 'status' => 401 ) );
		}
		return $result;
	}
}
