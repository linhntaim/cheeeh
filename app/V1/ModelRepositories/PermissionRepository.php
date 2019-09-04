<?php

namespace App\V1\ModelRepositories;

use App\V1\Exceptions\DatabaseException;
use App\V1\Models\Permission;
use PDOException;

class PermissionRepository extends ModelRepository
{
    protected function modelClass()
    {
        return Permission::class;
    }

    public function getNoneProtected()
    {
        return $this->query()->noneProtected()->get();
    }

    public function getCompacts()
    {
        try {
            return $this->query()->select(['display_name', 'id'])->pluck('display_name', 'id');
        } catch (PDOException $exception) {
            throw DatabaseException::from($exception);
        }
    }
}
