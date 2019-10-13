<?php

namespace Vendor\Db;

class Where {
    private static $CurrentOperator = '';
    private static $SeparateQueryField = '';

    const LogicalAND = 'AND';
    const LogicalOR = 'OR';

    const OperatorEq = '=';
    const OperatorLower = '<';
    const OperatorLowerEq = '<=';
    const OperatorGreater = '>';
    const OperatorGreaterEq = '>=';
    const OperatorIN = 'IN';
    const OperatorIs = 'IS';
    const OperatorNot = 'NOT';
    const OperatorIsNot = 'IS NOT';

    /**
     * @return QueryBuilder
     */
    public static function query() {
        return new QueryBuilder();
    }

    /**
     * @param array $fields
     * @param string $logical
     *
     * @return string
     */
    public static function common(array $fields, string $logical = self::LogicalAND) {
        if (empty($fields)) {
            return "";
        }

        foreach ($fields as $field) {
            $updated_fields[] = str_replace("WHERE", '', $field);
        }

        self::$CurrentOperator = $logical;

        $query_field = implode(" {$logical} ", array_map(function ($value) {
            if (strpos($value, self::LogicalOR)) {
                $value = '(' . $value . ')';
            }

            if (strstr($value, 'LIMIT') || strstr($value, 'OFFSET')
                || strstr($value, 'GROUP') || strstr($value, 'ORDER')) {
                self::$SeparateQueryField .= " {$value} ";
            }

            return $value;
        }, $updated_fields));

        if (strstr($query_field, 'GROUP')) {
            $query_field = substr($query_field, 0, strpos($query_field, "AND GROUP"));

        } elseif (strstr($query_field, 'ORDER')) {
            $query_field = substr($query_field, 0, strpos($query_field, "AND ORDER"));

        } elseif (strstr($query_field, 'LIMIT')) {
            $query_field = substr($query_field, 0, strpos($query_field, "AND LIMIT"));
        }

        return "WHERE " . $query_field . self::$SeparateQueryField;
    }

    /**
     * @param array $fields
     * @param string $logical
     * @param string $operator
     *
     * @return string
     */
    private static function base(array $fields, string $logical, string $operator): string {
        if (empty($fields)) {
            return "";
        }
        self::$CurrentOperator = $operator;

        $query_field = implode(" {$logical} ", array_map(function ($column, $value) {
            return "{$column} " . self::$CurrentOperator . " '{$value}'";
        }, array_keys($fields), $fields));

        return "WHERE " . $query_field;
    }

    /**
     * @param array $fields
     * @param string $logical
     *
     * @return string
     */
    public static function equal(array $fields, string $logical = self::LogicalAND): string {
        return self::base($fields, $logical, self::OperatorEq);
    }

    /**
     * @param array $fields
     * @param string $logical
     *
     * @return string
     */
    public static function lower(array $fields, string $logical = self::LogicalAND): string {
        return self::base($fields, $logical, self::OperatorLower);
    }

    /**
     * @param array $fields
     * @param string $logical
     *
     * @return string
     */
    public static function lowerEq(array $fields, string $logical = self::LogicalAND): string {
        return self::base($fields, $logical, self::OperatorLowerEq);
    }

    /**
     * @param array $fields
     * @param string $logical
     *
     * @return string
     */
    public static function greater(array $fields, string $logical = self::LogicalAND): string {
        return self::base($fields, $logical, self::OperatorGreater);
    }

    /**
     * @param array $fields
     * @param string $logical
     *
     * @return string
     */
    public static function greaterEq(array $fields, string $logical = self::LogicalAND): string {
        return self::base($fields, $logical, self::OperatorGreaterEq);
    }
}
