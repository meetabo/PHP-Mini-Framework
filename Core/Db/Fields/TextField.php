<?php

namespace Core\Db\Fields;

class TextField {
    private const TYPE = 'TEXT';

    public static function field($is_null = true) {
        return [
            'type' => self::TYPE,
            'isNull' => $is_null,
        ];
    }
}