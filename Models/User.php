<?php

namespace Models;

use Vendor\Db\Fields\FloatField;
use Vendor\Db\Fields\IntField;
use Vendor\Db\Fields\VarcharField;
use Vendor\Db\Fields\TextField;
use Vendor\Db\Fields\DoubleField;

class User {
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $first_name;

    /**
     * @var string
     */
    public $last_name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var double
     */
    public $price;


    public static function fields() {
        return [
            'id' => IntField::field(11),
            'first_name' => VarcharField::field(32),
            'last_name' => VarcharField::field(64),
            'description' => TextField::field(),
            'price' => DoubleField::field(),
            'amount' => FloatField::field(),
        ];
    }
}
