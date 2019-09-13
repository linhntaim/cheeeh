<?php

namespace App\V1\ModelRepositories;

use App\V1\Configuration;
use App\V1\Exceptions\DatabaseException;
use App\V1\Exceptions\Exception;
use App\V1\Utils\AbortTrait;
use App\V1\Utils\ClassTrait;
use App\V1\Utils\DateTimeHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use PDOException;

abstract class ModelRepository
{
    use ClassTrait, AbortTrait;

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @var Model|mixed
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

    /**
     * @return Builder
     */
    public function rawQuery()
    {
        return call_user_func($this->modelClass . '::query');
    }

    /**
     * @return Builder
     */
    public function query()
    {
        return $this->rawQuery();
    }

    /**
     * @return Model|mixed
     */
    public function newModel()
    {
        $modelClass = $this->modelClass;
        $this->model = new $modelClass();
        return $this->model;
    }

    /**
     * @param Model|mixed|null $id
     * @return Model|mixed|null
     */
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

    protected function catch(callable $callback)
    {
        try {
            return $callback();
        } catch (PDOException $exception) {
            throw DatabaseException::from($exception);
        }
    }

    public function getById($id, $strict = true)
    {
        return $this->catch(function () use ($id, $strict) {
            return $strict ? $this->query()->findOrFail($id) : $this->query()->find($id);
        });
    }

    public function queryByIds(array $ids)
    {
        return $this->query()->whereIn('id', $ids);
    }

    /**
     * @param array $ids
     * @param callable|null $callback
     * @return Collection
     * @throws Exception
     */
    public function getByIds(array $ids, $callback = null)
    {
        return $this->catch(function () use ($ids, $callback) {
            return empty($callback) ? $this->queryByIds($ids)->get() : $callback($this->queryByIds($ids));
        });

    }

    public function getAll()
    {
        return $this->catch(function () {
            return $this->query()->get();
        });
    }

    /**
     * @param Builder $query
     * @param array $search
     * @return Builder
     */
    protected function searchOn($query, array $search)
    {
        return $query;
    }

    /**
     * @param array $search
     * @param int $paging
     * @param int $itemsPerPage
     * @param string|null $sortBy
     * @param string|null $sortOrder
     * @return Collection|LengthAwarePaginator|Builder
     * @throws Exception
     */
    public function search($search = [], $paging = Configuration::FETCH_PAGING_YES, $itemsPerPage = Configuration::DEFAULT_ITEMS_PER_PAGE, $sortBy = null, $sortOrder = null)
    {
        $query = $this->query();

        if (!empty($search)) {
            $query = $this->searchOn($query, $search);
        }

        if (!empty($sortBy)) {
            $query->orderBy($sortBy, $sortOrder);
        }
        if ($paging == Configuration::FETCH_PAGING_NO) {
            return $this->catch(function () use ($query) {
                return $query->get();
            });
        } elseif ($paging == Configuration::FETCH_PAGING_YES) {
            return $this->catch(function () use ($query, $itemsPerPage) {
                return $query->paginate($itemsPerPage);
            });
        }

        return $query;
    }

    /**
     * @param array $attributes
     * @return Model|mixed
     * @throws Exception
     */
    public function createWithAttributes(array $attributes)
    {
        return $this->catch(function () use ($attributes) {
            $this->model = $this->rawQuery()->create($attributes);
            return $this->model;
        });
    }

    /**
     * @param array $attributes
     * @return Model|mixed
     * @throws Exception
     */
    public function updateWithAttributes(array $attributes)
    {
        return $this->catch(function () use ($attributes) {
            $this->model->update($attributes);
            return $this->model;
        });
    }

    /**
     * @param array $ids
     * @return bool
     * @throws Exception
     */
    public function deleteWithIds(array $ids)
    {
        return $this->catch(function () use ($ids) {
            $this->queryByIds($ids)->delete();
            return true;
        });
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
