<?php


namespace App\V1\ModelTransformers;


class UserEmailTransformer extends ModelTransformer
{
    protected function toArray()
    {
        $userEmail = $this->getModel();
        return [
            'id' => $userEmail->id,
            'email' => $userEmail->email,
            'is_alias' => $userEmail->isAlias,
            'verified' => $userEmail->verified,
        ];
    }
}
