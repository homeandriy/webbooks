<?php

namespace Webbooks\Security;

class DisableApiUsers {

	public function __construct() {
		add_filter( 'rest_endpoints', array( $this, 'disableRestEndpoints' ) );
	}

	public function disableRestEndpoints( $endpoints ) {
		unset(
			$endpoints['/wp/v2/users'],
			$endpoints['/wp/v2/users/(?P<id>[\\d]+)'],
			$endpoints['/wp/v2/comments'],
			$endpoints['/wp/v2/comments/(?P<id>[\\d]+)']
		);

		return $endpoints;
	}
}
