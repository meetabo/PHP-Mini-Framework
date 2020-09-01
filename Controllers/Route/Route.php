<?php

namespace Route;

use Core\Router\Router;

class Route {
    public function __construct() {
        Router::get('/test/{id}-{uuid}/pro/{name}-{lastName}', 'Controllers\User', 'test');
    }
}