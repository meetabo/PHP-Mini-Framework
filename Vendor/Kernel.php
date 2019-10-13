<?php

namespace Kernel;

use Vendor\Db\Db;
use Vendor\Router\Router;
use Vendor\Loader\Loader;

class Kernel {
    public static function loader() {
        $LOADER = [];

        require_once BASE_PATH . '/Vendor/loader_config.php';
        require_once BASE_PATH . '/Vendor/Loader.php';

        $loader_prefixes = $LOADER;

        if (!empty($loader_prefixes['autoloader'])) {
            // Init Loader
            $loader = new Loader($loader_prefixes['autoloader']);
            $loader->load();

            if (!empty($loader_prefixes['db'])) {
                $conf = $loader_prefixes['db'];
                $host = $conf['host'];
                $user_name = $conf['user_name'];
                $user_password = $conf['user_password'];
                $name = $conf['name'];

                // Connect Db;
                $db = new Db();
                $db->connection($host, $user_name, $user_password, $name);
            }

            // Turn Router
            $router = new Router;
        }
    }
}

