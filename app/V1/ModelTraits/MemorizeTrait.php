<?php


namespace App\V1\ModelTraits;


trait MemorizeTrait
{
    protected $memmories = [];

    protected function memorizable($key)
    {
        return isset($this->memmories[$key]);
    }

    protected function remind($key)
    {
        return isset($this->memmories[$key]) ? $this->memmories[$key] : null;
    }

    public function memorize($key, $value)
    {
        $this->memmories[$key] = $value;
        return $value;
    }
}
