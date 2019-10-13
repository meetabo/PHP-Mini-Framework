<?php

namespace Vendor\Db\Fields;

class VarcharField {
    private const TYPE = 'VARCHAR';

    public static function field($length = 0, $is_null = true) {
        return [
            'type' => self::TYPE,
            'length' => $length,
            'isNull' => $is_null,
        ];
    }
}