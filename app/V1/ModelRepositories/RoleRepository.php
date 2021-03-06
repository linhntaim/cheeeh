<?php

namespace App\V1\ModelRepositories;

use App\V1\Exceptions\AppException;
use App\V1\Exceptions\Exception;
use App\V1\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository extends ModelRepository
{
    protected function modelClass()
    {
        return Role::class;
    }

    protected function searchOn($query, array $search)
    {
        if (!empty($search['except_protected'])) {
            $query->whereNotIn('id', Role::PROTECTED);
        }
        if (!empty($search['name'])) {
            $query->where('name', 'like', '%' . $search['name'] . '%');
        }
        if (!empty($search['display_name'])) {
            $query->where('display_name', 'like', '%' . $search['display_name'] . '%');
        }
        if (!empty($search['permissions'])) {
            $query->whereHas('permissions', function ($query) use ($search) {
                $query->whereIn('id', $search['permissions']);
            });
        }

        return $query;
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
     * @param array $attributes
     * @param array $permissions
     * @return Role
     * @throws Exception
     */
    public function create(array $attributes, array $permissions = [])
    {
        $this->createWithAttributes($attributes);
        return $this->catch(function () use ($permissions) {
            if (count($permissions) > 0) {
                $this->model->permissions()->attach($permissions);
            }
            return $this->model;
        });
    }

    /**
     * @param array $attributes
     * @param array $permissions
     * @return Role
     * @throws AppException
     * @throws Exception
     */
    public function update(array $attributes, array $permissions = [])
    {
        if (in_array($this->model->id, Role::PROTECTED)) {
            throw new AppException('Cannot edit this protected role');
        }

        $this->updateWithAttributes($attributes);
        return $this->catch(function () use ($permissions) {
            if (count($permissions) > 0) {
                $this->model->permissions()->sync($permissions);
            } else {
                $this->model->permissions()->detach();
            }
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
            $this->queryByIds($ids)->noneProtected()->delete();
            return true;
        });
    }
}
