<?php
define('BASE_PATH', realpath(dirname(__FILE__)));

require_once './Core/Kernel.php';
//new \Kernel\Kernel();
\Kernel\Kernel::loader();