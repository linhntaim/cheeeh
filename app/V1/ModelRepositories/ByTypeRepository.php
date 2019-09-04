<?php

namespace App\V1\ModelRepositories;

abstract class ByTypeRepository extends ModelRepository
{
    protected $type;

    public function __construct($type, $id = null)
    {
        $this->type = $type;

        parent::__construct($id);
    }
}
