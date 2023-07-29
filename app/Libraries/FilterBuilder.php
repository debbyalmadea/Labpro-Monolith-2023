<?php

namespace App\Libraries;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class FilterBuilder implements FilterBuilderInterface, Arrayable
{
    protected Filterable $model;

    protected Collection $queriedRows;

    public function __construct(Filterable $model, ?Collection $rows)
    {
        $this->model = $model;
        $this->queriedRows = $rows ?? collect([]);
    }

    public function get()
    {
        return $this->queriedRows;
    }

    public function paginate($perPage)
    {
        $page = request('page') ?? 1;

        if ($page < 1) {
            return $this->get();
        }

        $total = $this->queriedRows->count();
        $offset = ($page - 1) * $perPage;

        $this->queriedRows = $this->queriedRows->slice($offset, $perPage);

        $paginator = new LengthAwarePaginator(
            $this->queriedRows,
            $total,
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
            ]
        );

        return $paginator;
    }

    public function setModel(Filterable $model)
    {
        $this->model = $model;

        return $this;
    }

    public function set(Collection $rows)
    {
        $this->queriedRows = $rows;

        return $this;
    }

    public function getModel()
    {
        return $this->model;
    }

    private function whereEquals($field, $value)
    {
        $this->queriedRows = $this->queriedRows->filter(function ($row) use ($field, $value) {
            return $row->{$field} === $value;
        });
    }

    private function whereContains($field, $value)
    {
        $this->queriedRows = $this->queriedRows->filter(function ($row) use ($field, $value) {
            return str_contains(strtolower($row->{$field}), strtolower($value));
        });

    }

    public function where($field, $operator, $value)
    {
        if ($operator === '=') {
            $this->whereEquals($field, $value);
        } else if ($operator === 'contains') {
            $this->whereContains($field, $value);
        }

        return $this;
    }

    public function orWhere($field, $operator, $value)
    {
        $builder = (new static($this->model, $this->queriedRows))
            ->where($field, $operator, $value);

        $this->queriedRows = $this->queriedRows->union($builder->get())->unique();

        return $this;
    }

    public function first()
    {
        return $this->get()->first();
    }

    public function filter(array $parameters = [])
    {
        return $this->model->scopeFilter($this, $parameters);
    }

    public function toArray()
    {
        return $this->get()->map(function ($row) {
            return $row->toArray();
        });
    }
}