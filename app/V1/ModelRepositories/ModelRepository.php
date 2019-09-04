<?php

namespace App\V1\ModelRepositories;

use App\V1\Utils\AbortTrait;
use App\V1\Utils\ClassTrait;
use App\V1\Utils\DateTimeHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class ModelRepository
{
    use ClassTrait, AbortTrait;

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array|null
     */
    protected $batch;

    public function __construct($id = null)
    {
        $this->modelClass = $this->modelClass();
        $this->model($id);
    }

    protected abstract function modelClass();

    public function query()
    {
        return call_user_func($this->modelClass . '::query');
    }

    public function newModel()
    {
        $modelClass = $this->modelClass;
        $this->model = new $modelClass();
        return $this->model;
    }

    public function model($id = null)
    {
        if (!empty($id)) {
            $this->model = $id instanceof Model ? $id : $this->getById($id);
        }
        return $this->model;
    }

    public function forgetModel()
    {
        $this->model = null;
    }

    public function getId()
    {
        return empty($this->model) ? null : $this->model->id;
    }

    public function getById($id, $strict = true)
    {
        return $strict ? $this->query()->findOrFail($id) : $this->query()->find($id);
    }

    public function queryByIds($ids)
    {
        return $this->query()->whereIn('id', $ids);
    }

    /**
     * @param array $ids
     * @param callable|null $callback
     * @return mixed
     */
    public function getByIds($ids, $callback = null)
    {
        return empty($callback) ? $this->queryByIds($ids)->get() : $callback($this->queryByIds($ids));
    }

    public function getAll()
    {
        return $this->query()->get();
    }

    public function batchInsertStart($batch = 1000)
    {
        $this->newModel();
        $this->batch = [
            'type' => 'insert',
            'values' => [],
            'batch' => $batch,
            'run' => 0,
        ];
        return $this;
    }

    protected function batchInsertReset()
    {
        $this->batch['run'] = 0;
        $this->batch['values'] = [];
    }

    public function batchInsert($attributes)
    {
        $this->batchInsertAdd($attributes);
        $this->batchInsertTryToSave();
        return $this;
    }

    protected function batchInsertAdd($attributes)
    {
        if ($this->model->timestamps) {
            $attributes['created_at'] = DateTimeHelper::syncNow();
            $attributes['updated_at'] = DateTimeHelper::syncNow();
        }
        $this->batch['values'][] = $attributes;
    }

    protected function batchInsertTryToSave()
    {
        if (++$this->batch['run'] == $this->batch['batch']) {
            $this->batchInsertSave();
            $this->batchInsertReset();
        }
    }

    protected function batchInsertSave()
    {
        if (count($this->batch['values']) > 0) {
            $this->query()->insert($this->batch['values']);
        }
    }

    public function batchInsertEnd()
    {
        $this->batchInsertSave();
        $this->model = null;
        $this->batch = null;
    }

    public function batchReadStart($query, $batch = 1000)
    {
        $this->batch = [
            'type' => 'read',
            'query' => $query,
            'batch' => $batch,
            'run' => 0,
        ];
        return $this;
    }

    /**
     * @param int $length
     * @param bool $shouldEnd
     * @return Collection
     */
    public function batchRead(&$length, &$shouldEnd)
    {
        $collections = $this->batch['query']->skip((++$this->batch['run'] - 1) * $this->batch['batch'])->take($this->batch['batch'])->get();
        $length = $collections->count();
        $shouldEnd = $length < $this->batch['batch'];
        return $collections;
    }

    public function batchReadEnd()
    {
        $this->batch = null;
    }
}
