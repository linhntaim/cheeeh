<?php

namespace App\V1\ModelRepositories;

use App\V1\Exceptions\Exception;
use App\V1\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class PermissionRepository extends ModelRepository
{
    protected function modelClass()
    {
        return Permission::class;
    }

    /**
     * @return Collection
     * @throws Exception
     */
    public function getNoneProtected()
    {
        return $this->catch(function () {
            return $this->query()->noneProtected()->get();
        });
    }

    /**
     * @return Collection
     * @throws Exception
     */
    public function getCompacts()
    {
        return $this->catch(function () {
            return $this->query()
                ->select(['display_name', 'id'])
                ->pluck('display_name', 'id');
        });
    }
}
