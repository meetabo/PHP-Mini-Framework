<?php

namespace Core\Router;

use Route\Route;
use Core\Methods\Methods;
use Core\Exception\Router\UrlException;
use Core\Exception\Methods\IncorrectMethodException;

class Router {
    public function __construct() {
        $route = new Route();
    }

    public static $params = [];

    private const UUIDRegex = '/[\'\/~`\!#\$%\^&\*\(\)_\+=\{\}\[\]\|;"\<\>,\.\?\\\]/';

    /**
     * @param string $url
     * @param string $controller_namespace
     * @param string $controller_function_name
     *
     * @throws UrlException
     */
    public static function route(string $url, string $controller_namespace, string $controller_function_name) {
        $checked = self::checkUrl($url);

        if (!$checked) {
            throw new UrlException();
        }

        self::checkController($controller_namespace, $controller_function_name);
    }

    /**
     * @param array $params
     *
     * @throws UrlException
     *
     * @return array
     */
    public static function requestParams(array $params = []) {
        foreach ($params as $param) {
            if (empty($_REQUEST[$param])) {
                throw new UrlException();
            }
            self::$params[$param] = $_REQUEST[$param];
        }

        return self::$params;
    }

    /**
     * @param string $url
     *
     * @throws UrlException
     *
     * @return bool
     */
    private static function checkUrl(string $url) {
        $curr_url = $_SERVER['REQUEST_URI'];

        // split curr url and url from route slash by slash
        $curr_url_params = array_filter(explode('/', parse_url($curr_url, PHP_URL_PATH)));
        $route_url_params = array_filter(explode('/', parse_url($url, PHP_URL_PATH)));

        if (count($curr_url_params) != count($route_url_params)) {
            throw new UrlException();
        }

        foreach ($curr_url_params as $curr_url_param) {
            foreach ($route_url_params as $key => $route_url_param) {
                //find query param name from route url
                $check_query_param = preg_match('/[{}]/', $route_url_param);

                // check if matches request url part with route url part and it is query param or not
                if ($curr_url_param != $route_url_param && $check_query_param == 0) {
                    throw new UrlException();
                } elseif ($curr_url_param == $route_url_param) {
                    unset($route_url_params[$key]);
                    continue 2;
                } elseif ($check_query_param == 1) {
                    // check UUID and ID is true
                    if (preg_match(self::UUIDRegex, urldecode($curr_url_param)) == 0) {
                        $query_param_dash = strpos($route_url_param, '}-{');
                        // check query params style
                        if ($query_param_dash) {
                            // check query params style
                            $route_query_params = explode('-', $route_url_param);
                            $curr_query_params = explode('-:', $curr_url_param);

                            if (count($curr_query_params) == count($route_query_params)) {
                                $collected_route_query_params = [];

                                foreach ($route_query_params as $route_query_param) {
                                    //collect query params
                                    $route_query_param = str_replace('{', '', $route_query_param);
                                    $route_query_param = str_replace('}', '', $route_query_param);
                                    $collected_route_query_params[] = $route_query_param;
                                }

                                self::$params = array_merge(self::$params, array_combine($collected_route_query_params, $curr_query_params));
                            } else {
                                throw new UrlException();
                            }
                        } else {
                            //collect query params
                            $route_url_param = str_replace('{', '', $route_url_param);
                            $route_url_param = str_replace('}', '', $route_url_param);

                            self::$params[$route_url_param] = urldecode($curr_url_param);
                        }

                        unset($route_url_params[$key]);
                        continue 2;
                    } else {
                        throw new UrlException();
                    }
                } else {
                    //check is parameters is true
                    $url_params = explode('?', urldecode($curr_url_param), 2);
                    $checked = preg_match(self::UUIDRegex, $url_params[0]);

                    if ($checked == 1) {
                        throw new UrlException();
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param string $controller_namespace
     * @param string $controller_function_name
     */
    private static function checkController(string $controller_namespace, string $controller_function_name) {
        $class_name = substr($controller_namespace, strrpos($controller_namespace, '\\') + 1);
        $class = $controller_namespace . '\\' . $class_name . 'Controller';

        $new_class = new $class();
        $new_class->$controller_function_name();
    }

    /**
     * @param string $url
     * @param string $controller_namespace
     * @param string $controller_function_name
     *
     * @throws IncorrectMethodException
     * @throws UrlException
     * @return bool
     */
    public static function get(string $url, string $controller_namespace, string $controller_function_name) {
        if (Methods::GET == $_SERVER['REQUEST_METHOD']) {
            self::route($url, $controller_namespace, $controller_function_name);

            return true;
        }

        throw new IncorrectMethodException();
    }

    /**
     * @param string $url
     * @param string $controller_namespace
     * @param string $controller_function_name
     *
     * @throws IncorrectMethodException
     * @throws UrlException
     * @return bool
     */
    public static function put(string $url, string $controller_namespace, string $controller_function_name) {
        if (Methods::PUT == $_SERVER['REQUEST_METHOD']) {
            self::route($url, $controller_namespace, $controller_function_name);

            return true;
        }

        throw new IncorrectMethodException();
    }

    /**
     * @param string $url
     * @param string $controller_namespace
     * @param string $controller_function_name
     *
     * @throws IncorrectMethodException
     * @throws UrlException
     * @return bool
     */
    public static function post(string $url, string $controller_namespace, string $controller_function_name) {
        if (Methods::POST == $_SERVER['REQUEST_METHOD']) {
            self::route($url, $controller_namespace, $controller_function_name);

            return true;
        }

        throw new IncorrectMethodException();
    }

    /**
     * @param string $url
     * @param string $controller_namespace
     * @param string $controller_function_name
     *
     * @throws IncorrectMethodException
     * @throws UrlException
     * @return bool
     */
    public static function delete(string $url, string $controller_namespace, string $controller_function_name) {
        if (Methods::DELETE == $_SERVER['REQUEST_METHOD']) {
            self::route($url, $controller_namespace, $controller_function_name);

            return true;
        }

        throw new IncorrectMethodException();
    }
}