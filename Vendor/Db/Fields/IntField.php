<?php

namespace Vendor\Db\Fields;

class IntField {
    private const TYPE = 'INT';

    public static function field($length = 0, $is_null = true) {
        return [
            'type' => self::TYPE,
            'length' => $length,
            'isNull' => $is_null,
        ];
    }
}