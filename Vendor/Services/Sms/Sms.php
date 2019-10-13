<?php

namespace Core\Services\Sms;

use Vendor\Services\Curl\CurlAdapter;
use Vendor\Services\Redis\RedisAdapter;

class Sms {
    private $twl_base_url = '';
    private $cache_smskey_tpl = '';
    private $twl_auth_token = '';

    /**
     * @param $trigger
     *
     * @return bool
     */
    private function checkTrigger($trigger): bool {
        $redis = RedisAdapter::shared();

        // check trigger
        if ($trigger > 0) {
            // user, act once per 30 sec
            $user_id = $trigger;
            $key = str_replace('{id}', $user_id, $this->cache_smskey_tpl);
            $now = time();

            if ($redis->exists($key)) {
                return false;
            } else {
                $redis->setEx($key, 30, $now);
                return true;
            }
        } elseif ($trigger == 0) {
            // superuser, act every time
            return true;
        }

        return false;
    }

    /**
     * @param $trigger
     * @param $phone_code
     * @param $phone_number
     *
     * @return bool
     */
    public function sendCode($trigger, $phone_code, $phone_number): bool {
        if ($this->checkTrigger($trigger)) {
            if ($phone_code && $phone_number) {
                $curl = new CurlAdapter();
                $response = $curl->fetchPost($this->twl_base_url . '/start', [
                    'via' => 'sms',
                    'country_code' => $phone_code,
                    'phone_number' => $phone_number,
                    'code_length' => 4,
                ], [
                    'X-Authy-API-Key: ' . $this->twl_auth_token,
                ]);

                return (bool)json_decode($response, true)['success'];
            }
        }

        return false;
    }

    /**
     * @param $phone_code
     * @param $phone_number
     * @param $code
     *
     * @return bool
     */
    public function checkCode($phone_code, $phone_number, $code): bool {
        if ($phone_code && $phone_number && $code) {
            $curl = new CurlAdapter();
            $response = $curl->fetchGet($this->twl_base_url . '/check', [
                'country_code' => $phone_code,
                'phone_number' => $phone_number,
                'verification_code' => $code,
            ], [
                'X-Authy-API-Key: ' . $this->twl_auth_token,
            ]);

            return (bool)json_decode($response, true)['success'];
        }

        return false;
    }
}
