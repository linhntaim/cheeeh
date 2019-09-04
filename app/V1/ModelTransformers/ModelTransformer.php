<?php

namespace App\V1\ModelTransformers;

use App\V1\Utils\Helper;
use Illuminate\Database\Eloquent\Model;

abstract class ModelTransformer
{
    protected $isCollection;

    /**
     * @var iterable
     */
    protected $models;

    /**
     * @var Model
     */
    protected $realModel;

    protected $context;

    public function setModel($model)
    {
        if (is_iterable($model)) {
            $this->isCollection = true;
            $this->models = $model;
        } else {
            $this->isCollection = false;
            $this->realModel = $model;
        }
        return $this;
    }

    protected function getModel()
    {
        return $this->realModel;
    }

    public function setContext($context = [])
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @param string|null $name
     * @return mixed|null
     */
    protected function getContext($name = null)
    {
        return empty($name) ?
            $this->context : (isset($this->context[$name]) ? $this->context[$name] : null);
    }

    /**
     * @param \Closure|null $mapping
     * @return array
     */
    public function toTransformed($mapping = null)
    {
        if ($this->isCollection) {
            $items = [];
            foreach ($this->models as $model) {
                $this->realModel = $model;
                $items[] = $this->map($mapping);
            }
            return $items;
        }

        if (empty($this->realModel)) return null;
        return $this->map($mapping);
    }

    /**
     * @param \Closure|null $mapping
     * @return array
     */
    protected function map($mapping = null)
    {
        return empty($mapping) ? $this->toArray() : $mapping($this->toArray(), $this);
    }

    protected abstract function toArray();

    /**
     * @param mixed $data
     * @param callable|null $callback
     * @return array|null
     */
    protected function safeObject($data, $callback = null)
    {
        return Helper::default($data, null, $callback);
    }
}
