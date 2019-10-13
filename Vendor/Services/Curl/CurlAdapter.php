<?php

namespace Vendor\Services\Curl;

class CurlAdapter {
    /**
     *
     * @param string $url
     * @param array $params
     * @param array $headers
     *
     * @return mixed
     */
    function fetchGet($url, $params = [], $headers = []) {
        $link = curl_init();
        curl_setopt($link, CURLOPT_CUSTOMREQUEST, "GET");

        if (!empty($headers)) {
            curl_setopt($link, CURLOPT_HTTPHEADER, $headers);
        }

        return $this->execRequest($url . '?' . $this->curlBuildParams($params), $link);
    }

    /**
     *
     * @param string $url
     * @param array $params
     * @param array $headers
     *
     * @return mixed
     */
    function fetchPost($url, $params = [], $headers = []) {
        $link = curl_init();
        curl_setopt($link, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($link, CURLOPT_POSTFIELDS, $this->curlBuildParams($params));

        if (!empty($headers)) {
            curl_setopt($link, CURLOPT_HTTPHEADER, $headers);
        }

        return $this->execRequest($url, $link);
    }

    /**
     * @param string $url
     * @param string $link
     *
     * @return mixed
     */
    function execRequest($url, $link) {
        curl_setopt($link, CURLOPT_URL, $url);
        curl_setopt($link, CURLOPT_MAXREDIRS, 10);
        curl_setopt($link, CURLOPT_TIMEOUT, 30);
        curl_setopt($link, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($link, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($link, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($link);
        curl_close($link);
        return $res;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    function curlBuildParams($params) {
        return urldecode(http_build_query($params));
    }
}