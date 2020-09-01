<?php

namespace Core\Db;

class QueryBuilder {

    /**
     * @var string
     */
    public const ASC = 'ASC';

    /**
     * @var string
     */
    public const DESC = 'DESC';

    /**
     * @param int $offset
     *
     * @return string
     */
    public function offset(int $offset): string {
        return "OFFSET {$offset}";
    }

    /**
     * @param int $limit
     *
     * @return string
     */
    public function limit(int $limit): string {
        return "LIMIT {$limit}";
    }

    /**
     * @param array $field
     *
     * @return string
     */
    public function groupBy(array $field): string {
        $sql = "GROUP BY ";
        $query_field = implode(' ,', array_map(function ($value) {
            return $value;
        }, $field));

        return $sql . $query_field;
    }

    /**
     * @param array $field
     *
     * @return string
     */
    public function orderBy(array $field): string {
        $sql = "ORDER BY ";
        $query_field = implode(' ,', array_map(function ($value) {
            return $value;
        }, $field));

        return $sql . $query_field;
    }

    /**
     * @return string
     */
    public function DESC(): string {
        return self::DESC;
    }

    /**
     * @return string
     */
    public function ASC(): string {
        return self::ASC;
    }
}