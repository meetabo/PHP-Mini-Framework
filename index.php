<?php
define('BASE_PATH', realpath(dirname(__FILE__)));

require_once './Vendor/Kernel.php';
//new \Kernel\Kernel();
\Kernel\Kernel::loader();