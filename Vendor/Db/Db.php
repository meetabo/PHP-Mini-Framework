<?php

namespace Vendor\Db;

use Vendor\Exception\Db\InvalidInsertQueryException;

class Db {
    private static $conn;

    /**
     * @param $value
     *
     * @return mixed
     */
    static public function escape($value) {
        return self::$conn->escape_string($value);
    }

    /**
     * @param string $host
     * @param string $user_name
     * @param string $user_password
     * @param string $name
     */
    public function connection(string $host, string $user_name, string $user_password, string $name) {
        //Connect with db
        $conn = mysqli_connect($host, $user_name, $user_password, $name, 3306);
        $conn->set_charset('utf8');

        if ($conn->connect_error) {
            die();
        }

        self::$conn = $conn;
    }

    /**
     * @param string $table_name
     * @param array $fields
     *
     * @return bool
     */
    public static function createTable(string $table_name, array $fields): bool {
        $sql = "CREATE TABLE {$table_name} (";
        $sql = self::parseFieldsForTable($sql, $fields) . ")";
        $res = mysqli_query(self::$conn, $sql);

        if (!$res) {
            echo "Error: " . self::$conn->error; die();
        }

        self::$conn->close();

        return true;
    }

    /**
     * @param string $table_name
     * @param array $fields
     *
     * @return bool
     */
    public static function addColumn(string $table_name, array $fields): bool {
        $sql = "ALTER TABLE {$table_name} ADD ";
        $sql = self::parseFieldsForTable($sql, $fields);
        $res = mysqli_query(self::$conn, $sql);

        if (!$res) {
            echo "Error: " . self::$conn->error; die();
        }

        self::$conn->close();

        return true;
    }

    /**
     * @param string $table_name
     * @param string $column_name
     *
     * @return bool
     */
    public static function dropColumn(string $table_name, string $column_name): bool {
        $sql = "ALTER TABLE {$table_name} DROP COLUMN {$column_name}";
        $res = mysqli_query(self::$conn, $sql);

        if (!$res) {
            echo "Error: " . self::$conn->error; die();
        }

        self::$conn->close();

        return true;
    }

    /**
     * @param string $sql
     * @param array $fields
     *
     * @return string
     */
    public static function parseFieldsForTable(string $sql, array $fields): string {
        $not_null = '';
        foreach ($fields as $field_name => $field) {
            if (!empty($field['length'])) {
                if (!$field['isNull']) {
                    $not_null = "NOT NULL ";
                }
                if (strtolower($field_name) == 'id') {
                    $sql .= "id INT({$field['length']}) UNSIGNED AUTO_INCREMENT PRIMARY KEY, ";
                } else {
                    $sql .= "{$field_name} {$field['type']} ({$field['length']}) {$not_null}, ";
                }
            } else {
                $sql .=  "{$field_name} {$field['type']} {$not_null}, ";
            }
        }

        return substr($sql, 0, -2);
    }

    /**
     * @param string $table_name
     * @param array $fields
     * @param array $values
     *
     * @throws InvalidInsertQueryException
     *
     * @return bool
     */
    public static function insert(string $table_name, array $fields, array $values): bool {
        if (count($fields) != count($values)) {
            throw new InvalidInsertQueryException();
        }

        $query_fields = implode(', ', $fields);
        $query_values = implode(',', array_map(function ($value) {
            if (is_string($value)) {
                return "'" . self::escape($value) . "'";
            } elseif (is_int($value) || is_float($value) || is_double($value)) {
                return $value;
            } else {
                return 'NULL';
            }
        }, $values));


        $sql = "INSERT INTO `{$table_name}` ({$query_fields}) VALUES ({$query_values})";
        $res = mysqli_query(self::$conn, $sql);

        if (!$res) {
            echo "Error: " . self::$conn->error; die();
        }

        self::$conn->close();

        return true;
    }

    /**
     * @param string $table_name
     * @param array $update_field
     * @param string $where
     *
     * @return bool
     */
    public static function update(string $table_name, array $update_field, string $where): bool {
        $sql = "UPDATE {$table_name} SET ";

        $query_field = implode(',', array_map(function ($column, $value) {
            return "{$column} = '{$value}'";
        }, array_keys($update_field), $update_field));

        $sql .= $query_field . " " . $where;
        $res = mysqli_query(self::$conn, $sql);

        if (!$res) {
            echo "Error: " . self::$conn->error; die();
        }

        self::$conn->close();

        return true;
    }

    /**
     * @param string $table_name
     * @param string $where
     *
     * @return bool
     */
    public static function delete(string $table_name, string $where): bool {
        $sql = "DELETE FROM {$table_name} {$where}";
        $res = mysqli_query(self::$conn, $sql);

        if (!$res) {
            echo "Error: " . self::$conn->error; die();
        }

        self::$conn->close();

        return true;
    }

    /**
     * @param string $table_name
     * @param string $where
     * @param array $fields
     *
     * @return array
     */
    public static function select(string $table_name, array $fields, string $where): array {
        $sql = "SELECT ";

        if (empty($fields)) {
            $select_field = '*';
        } else {
            $select_field = implode(', ', array_map(function ($field) {
                return "$field";
            }, $fields));
        }

        $sql .=  $select_field . " FROM {$table_name} ". $where;
        $res = self::$conn->query($sql);

        if (!$res) {
            echo "Error: " . self::$conn->error; die();
        }

        $rows = $res->num_rows;
        if ($rows == 0) {
            return [];
        }

        return $res->fetch_assoc();
    }
}

