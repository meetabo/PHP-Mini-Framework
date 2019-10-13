<?php

namespace Controllers\User;

use Vendor\Db\Where;
use Vendor\Router\Router;
use Models\User;
use Vendor\Db\Db;
use Vendor\Db\Fields\VarcharField;

class UserController {
    public function test() {
        Db::createTable('user', User::fields());

        Db::dropColumn('user', 'first_name');

        Db::addColumn('user', ['first_name' => VarcharField::field(32)]);

        Db::insert('user', ['first_name', 'last_name', 'price', 'amount'], ['John', 'Smith', 24, 19.5]);

        Db::update('user', ['last_name' => 'Snow'], Where::equal(['id' => 1, 'first_name' => 'John']));

        Db::update('user', ['last_name' => 'Smith'],
            Where::common([
                Where::equal(['id' => 1, 'first_name' => 'John']),
                Where::lower(['price' => 25, 'amount' => 23.24], Where::LogicalOR)
            ]));

        $res = Db::select('user', ['id', 'first_name', 'last_name'], Where::common([
            Where::equal(['id' => 1, 'first_name' => 'John']),
            Where::lower(['price' => 25, 'amount' => 23.24], Where::LogicalOR),
            Where::query()->groupBy(['first_name']),
            Where::query()->limit(1),
            Where::query()->offset(0),
            Where::query()->ASC(),
        ]));


        Db::delete('user', Where::equal(['last_name' => 'Smith']));

        var_dump(Router::requestParams());

    }
}