<?php

namespace App\V1\ModelTransformers;

class UserTransformer extends ModelTransformer
{
    use ModelTransformTrait;

    public function toArray()
    {
        $user = $this->getModel();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'display_name' => $user->display_name,
            'url_avatar' => $user->url_avatar,
            'email' => $this->safeObject($user->email, function ($userEmail) {
                return $this->modelTransform(UserEmailTransformer::class, $userEmail);
            }),
            'roles' => $this->safeObject($user->roles, function ($roles) {
                return $this->modelTransform(RoleTransformer::class, $roles);
            }),
        ];
    }
}
