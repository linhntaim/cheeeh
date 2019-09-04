<?php

namespace App\V1\Rules;

use Illuminate\Contracts\Validation\Rule as IRule;
use Illuminate\Support\Str;

abstract class Rule implements IRule
{
    protected $attribute;
    protected $transPath;
    protected $name;

    public function __construct()
    {
        $this->transPath = 'error.rules';
    }

    protected function getAttributeName()
    {
        return strtolower(implode(' ', explode('_', Str::snake($this->attribute))));
    }

    public abstract function passes($attribute, $value);

    public function setTransPath($transPath)
    {
        $this->transPath = $transPath;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans($this->transPath . '.' . $this->name, [
            'attribute' => $this->getAttributeName(),
        ]);
    }

    public function __toString()
    {
        return $this->name;
    }
}
