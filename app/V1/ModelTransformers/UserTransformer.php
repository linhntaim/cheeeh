<?php

namespace App\V1\ModelTransformers;

class UserTransformer extends ModelTransformer
{
    use TransformTrait;

    public function toArray()
    {
        $user = $this->getModel();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'display_name' => $user->display_name,
            'url_avatar' => $user->url_avatar,
            'email' => $this->safeObject($user->email, function ($userEmail) {
                return $this->transform(UserEmailTransformer::class, $userEmail);
            }),
            'roles' => $this->safeObject($user->roles, function ($roles) {
                return $this->transform(RoleTransformer::class, $roles);
            }),
        ];
    }
}
