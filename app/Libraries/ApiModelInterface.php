<?php

namespace App\Libraries;

use Illuminate\Contracts\Support\Arrayable;

interface ApiModelInterface extends Arrayable, Filterable
{
    public function __construct(array $row = [], bool $exists = false);

    public function setAttribute($key, $value);

    public function getAttribute($key);

    public function setApi(string $api);

    public static function getApi();
    public function __get($key);

    public function __set($key, $value);

    public function getTable();

    public static function all();

    public static function with(string $table, string $foreignKey, string $reference = 'id');

    public static function find($id);

    public static function findWith($id, string $table, string $foreignKey, string $reference = 'id');

    public static function create($row = []);

    public function save();

    public function decrease($key, $decrease_by);

    public static function builder($rows);
}