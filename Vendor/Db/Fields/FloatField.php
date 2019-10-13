<?php

namespace Vendor\Db\Fields;

class FloatField {
    private const TYPE = 'FLOAT';

    public static function field($is_null = true) {
        return [
            'type' => self::TYPE,
            'isNull' => $is_null,
        ];
    }
}