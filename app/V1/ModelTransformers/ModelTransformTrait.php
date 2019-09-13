<?php

namespace App\V1\ModelTransformers;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait ModelTransformTrait
{
    /**
     * @param string $modelTransformerClass
     * @param Model|Collection $model
     * @param array $context
     * @param Closure|null $mapping
     * @return array
     */
    protected function modelTransform($modelTransformerClass, $model, $context = [], $mapping = null)
    {
        return $this->modelTransformer($modelTransformerClass)
            ->setModel($model)
            ->setContext($context)
            ->toTransformed($mapping);
    }

    /**
     * @param string $modelTransformerClass
     * @return ModelTransformer
     */
    protected function modelTransformer($modelTransformerClass)
    {
        return new $modelTransformerClass;
    }
}
