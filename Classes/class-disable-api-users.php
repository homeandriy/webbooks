<?php

class Class_disable_api_users {
	/**
	 * Constructor function.
	 */
	public function __construct() {
		add_filter( 'rest_endpoints', array( $this, 'disable_rest_endpoints' ) );
	}

	/**
	 * Disable REST API user endpoints.
	 *
	 * @param array $endpoints The original endpoints.
	 * @return array The updated endpoints.
	 * @since 1.0.0
	 */
	public function disable_rest_endpoints( $endpoints ) {
		if ( isset( $endpoints['/wp/v2/users'] ) ) {
			unset( $endpoints['/wp/v2/users'] );
		}

		if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
			unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
		}

		return $endpoints;
	}
}
