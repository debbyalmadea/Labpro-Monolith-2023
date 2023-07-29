<?php

namespace App\Libraries;

use App\Exceptions\HttpCustomException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

abstract class ApiModel implements Arrayable, Filterable
{
    protected string $table;

    protected string $primaryKey = 'id';

    protected array $row = [];

    protected bool $exists = false;

    protected string $apiName = 'single_service';

    public function __construct(array $row = [], bool $exists = false)
    {
        $this->fill($row, $exists);
    }

    protected function fill($row = [], $exists = false)
    {
        $this->exists = $exists;
        foreach ($row as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    public function setAttribute($key, $value)
    {
        $this->row[$key] = $value;
    }

    public function getAttribute($key)
    {
        return $this->row[$key];
    }

    public function setApi(string $apiName)
    {
        $this->$apiName = $apiName;
    }

    public static function getApi()
    {
        return Api::connection((new static())->apiName);
    }

    protected function newInstance($row, $exists)
    {
        $model = new static($row, $exists);

        return $model;
    }

    protected function mapCollectionToModels(Collection $collection)
    {
        $models = $collection->keyBy($this->primaryKey);

        $models = $models->map(function ($item) {
            return $this->newInstance($item, true);
        });

        return $models;
    }


    public function __get($key)
    {
        return $this->getAttribute($key);
    }
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    public function getTable()
    {
        return $this->table ?? strtolower(class_basename($this));
    }

    public static function all()
    {
        $model = new static();
        $rows = collect(self::getApi()->get($model->getTable()));
        $rows = $model->mapCollectionToModels($rows);

        return new FilterBuilder($model, $rows);
    }

    public static function with(string $table, string $foreignKey, string $reference = 'id')
    {
        $model = new static();
        $rows = collect(self::getApi()->get($model->getTable()));
        $rows = $model->mapCollectionToModels($rows);

        $with_rows = collect(self::getApi()->get($table));
        $with_rows = $model->mapCollectionToModels($with_rows);

        $rows = $rows->map(function ($row) use ($table, $with_rows, $foreignKey, $reference, $model) {
            $with_row = $with_rows[$row->{$foreignKey}] ?? $model;
            foreach ($with_row->toArray() as $key => $value) {
                if ($key != $reference) {
                    $row->{$key . '_' . $table} = $value;
                }
            }

            return $row;
        });

        return new FilterBuilder($model, $rows);
    }

    protected static function findOrFail($id)
    {
        $model = new static();
        $row = self::getApi()->get($model->getTable() . '/' . $id);
        $model->fill($row, true);

        return $model;
    }

    public static function find($id)
    {
        try {
            return static::findOrFail($id);
        } catch (HttpCustomException $e) {
            return null;
        }
    }

    public static function findWith($id, string $table, string $foreignKey, string $reference = 'id')
    {
        try {
            $model = static::findOrFail($id);

            $with_row = self::getApi()->get($table . '/' . $model->getAttribute($foreignKey));
            $with_row = $model->newInstance($with_row, true);

            foreach ($with_row->toArray() as $key => $value) {
                if ($key != $reference) {
                    $model->{$key . '_' . $table} = $value;
                }
            }

            return $model;
        } catch (HttpCustomException $e) {
            return null;
        }
    }

    public static function create($row = [])
    {
        $model = new static($row);
        $model->save();

        return $model;
    }

    public function save()
    {
        if ($this->exists) {
            $row = self::getApi()->put($this->getTable(), $this->row);
            $this->fill($row, true);
        } else {
            $row = self::getApi()->post($this->getTable(), $this->row);
            $this->fill($row, true);
        }
    }

    public function copy()
    {
        return static::newInstance($this->row, $this->exists);
    }

    public function decrease($key, $decrease_by)
    {
        $id = $this->{$this->primaryKey};
        if (is_numeric($this->{$key}) && $id) {
            $row = self::getApi()->patch($this->getTable() . '/' . $id . '/' . $key . '/decrease', ['decrease_by' => $decrease_by]);
            $this->fill($row, true);
        }

        return $this;
    }

    public function filter(FilterBuilder $builder)
    {
        return $builder;
    }

    public function toArray()
    {
        return $this->row;
    }

    public static function builder($rows)
    {
        return (new FilterBuilder(new static(), $rows));
    }
}