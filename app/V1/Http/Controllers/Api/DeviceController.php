<?php

namespace App\V1\Http\Controllers\Api;

use App\V1\Http\Controllers\ApiController;
use App\V1\Http\Requests\Request;
use App\V1\ModelRepositories\DeviceRepository;
use App\V1\Models\Device;
use App\V1\ModelTransformers\DeviceTransformer;

class DeviceController extends ApiController
{
    private $deviceRepository;

    public function __construct()
    {
        parent::__construct();

        $this->deviceRepository = new DeviceRepository();
    }

    public function currentStore(Request $request)
    {
        $this->validated($request, [
            'provider' => 'nullable|sometimes|string',
            'secret' => 'nullable|sometimes|string|max:255',
        ]);

        $currentUser = $request->user();
        return $this->responseSuccess([
            'device' => $this->transform(
                DeviceTransformer::class,
                $this->deviceRepository->save(
                    $request->input('provider', Device::PROVIDER_BROWSER),
                    $request->input('secret', ''),
                    $request->getClientIp(),
                    empty($currentUser) ? null : $currentUser->id
                )
            ),
        ]);
    }
}
