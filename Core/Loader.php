<?php

namespace Core\Loader;

class Loader {
    private $prefixes = [];

    /**
     * @param array $prefixes
     *
     */
    public function __construct(array $prefixes) {
        $this->prefixes = $prefixes;
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    private function loadFilesControllers(string $class) {
        $file_path = BASE_PATH;
        $class_name = substr($class, strrpos($class, '\\') + 1);
        $class_path = str_replace('\\', '/', $class) . '.php';

        foreach ($this->prefixes as $prefix_class_name => $prefix_class_path) {
            if ($class_name == $prefix_class_name) {
                $file_path .= '/' . $prefix_class_path . $class_path;
            } else {
                $file_path .= '/' . $class_path;
            }
        }

        if (file_exists($file_path)) {
            require_once $file_path;
        }

        return false;
    }

    public function load() {
        spl_autoload_register([$this, 'loadFilesControllers']);
    }
}