<?php

namespace App\V1\ModelRepositories;

use App\V1\Configuration;
use App\V1\Exceptions\AppException;
use App\V1\Exceptions\DatabaseException;
use App\V1\Models\Role;
use PDOException;

class RoleRepository extends ModelRepository
{
    protected function modelClass()
    {
        return Role::class;
    }

    public function getNoneProtected()
    {
        return $this->query()->noneProtected()->get();
    }

    public function search($search = [], $paging = Configuration::FETCH_PAGING_YES, $itemsPerPage = Configuration::DEFAULT_ITEMS_PER_PAGE, $sortBy = null, $sortOrder = null)
    {
        $query = $this->query();

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

        if (!empty($sortBy)) {
            $query->orderBy($sortBy, $sortOrder);
        }
        if ($paging == Configuration::FETCH_PAGING_NO) {
            return $query->get();
        } elseif ($paging == Configuration::FETCH_PAGING_YES) {
            return $query->paginate($itemsPerPage);
        }

        return $query;
    }

    public function create(array $attributes, array $permissions = [])
    {
        try {
            $this->model = $this->query()->create($attributes);
            if (count($permissions) > 0) {
                $this->model->permissions()->attach($permissions);
            }
            return $this->model;
        } catch (PDOException $exception) {
            throw DatabaseException::from($exception);
        }
    }

    public function update(array $attributes, array $permissions = [])
    {
        if (in_array($this->model->id, Role::PROTECTED)) {
            throw new AppException('Cannot edit this role');
        }

        try {
            $this->model->update($attributes);
            if (count($permissions) > 0) {
                $this->model->permissions()->sync($permissions);
            } else {
                $this->model->permissions()->detach();
            }
            return $this->model;
        } catch (PDOException $exception) {
            throw DatabaseException::from($exception);
        }
    }

    public function delete($ids)
    {
        try {
            $this->query()->whereIn('id', $ids)
                ->noneProtected()
                ->delete();
        } catch (PDOException $exception) {
            throw DatabaseException::from($exception);
        }
    }
}
