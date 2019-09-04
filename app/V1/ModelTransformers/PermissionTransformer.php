<?php

namespace App\V1\ModelTransformers;

class PermissionTransformer extends ModelTransformer
{
    public function toArray()
    {
        $role = $this->getModel();

        return [
            'id' => $role->id,
            'name' => $role->name,
            'display_name' => $role->display_name,
            'description' => $role->description,
        ];
    }
}
