<?php

namespace App\V1\ModelTransformers;

class RoleTransformer extends ModelTransformer
{
    use ModelTransformTrait;

    public function toArray()
    {
        $role = $this->getModel();

        return [
            'id' => $role->id,
            'name' => $role->name,
            'display_name' => $role->display_name,
            'description' => $role->description,
            'permissions' => $this->safeObject($role->permissions, function ($permissions) {
                return $this->modelTransform(PermissionTransformer::class, $permissions);
            }),
        ];
    }
}
