<?php

namespace App\V1\ModelTransformers;

class DeviceTransformer extends ModelTransformer
{
    public function toArray()
    {
        $device = $this->getModel();
        return [
            'provider' => $device->provider,
            'secret' => $device->secret,
        ];
    }
}
