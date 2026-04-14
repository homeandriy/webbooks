<?php

namespace Webbooks\Security;

class DisableApiUsers
{
    public function __construct()
    {
        add_filter('rest_endpoints', [$this, 'disableRestEndpoints']);
    }

    public function disableRestEndpoints($endpoints)
    {
        unset($endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)']);

        return $endpoints;
    }
}
