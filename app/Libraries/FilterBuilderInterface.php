<?php

namespace App\Libraries;

use Illuminate\Support\Collection;

interface FilterBuilderInterface
{
    public function get();
    public function where($field, $operator, $value);
    public function orWhere($field, $operator, $value);
    public function first();
    public function filter(array $parameters = []);
    public function paginate($perPage);
    public function setModel(Filterable $model);
    public function getModel();
    public function set(Collection $rows);
}