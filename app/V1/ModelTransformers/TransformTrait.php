<?php

namespace App\V1\ModelTransformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait TransformTrait
{
    /**
     * @param string $transformerClass
     * @param Model|Collection $model
     * @param array $context
     * @param \Closure|null $mapping
     * @return array
     */
    protected function transform($transformerClass, $model, $context = [], $mapping = null)
    {
        return (new $transformerClass)->setModel($model)->setContext($context)->toTransformed($mapping);
    }
}
