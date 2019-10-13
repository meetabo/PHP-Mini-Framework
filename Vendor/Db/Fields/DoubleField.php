<?php

namespace Vendor\Db\Fields;

class DoubleField {
    private const TYPE = 'DOUBLE';

    public static function field($is_null = true) {
        return [
            'type' => self::TYPE,
            'isNull' => $is_null,
        ];
    }
}