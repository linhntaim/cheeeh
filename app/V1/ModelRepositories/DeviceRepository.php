<?php

namespace App\V1\ModelRepositories;

use App\V1\Exceptions\AppException;
use App\V1\Exceptions\DatabaseException;
use App\V1\Models\Device;
use App\V1\Utils\ClientHelper;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PDOException;
use Ramsey\Uuid\Uuid;

class DeviceRepository extends ModelRepository
{
    protected function modelClass()
    {
        return Device::class;
    }

    /**
     * @param $provider
     * @param $secret
     * @return Device
     */
    public function getByProviderAndSecret($provider, $secret)
    {
        return $this->query()->where('provider', $provider)->where('secret', $secret)->first();
    }

    /**
     * @param $provider
     * @param $secret
     * @return boolean
     */
    public function hasProviderAndSecret($provider, $secret)
    {
        return $this->query()->where('provider', $provider)->where('secret', $secret)->count() > 0;
    }

    public function trySecretWithProvider($provider)
    {
        $secret = Uuid::uuid1()->toString();
        $maxTry = 5;
        $try = 0;
        while ($this->hasProviderAndSecret($provider, $secret)) {
            $secret = Uuid::uuid1()->toString();
            ++$try;
            if ($try == $maxTry) {
                abort(403);
            }
        }
        return $secret;
    }

    /**
     * @param array $attributes
     * @return Device
     * @throws Exception
     */
    public function create($attributes = [])
    {
        try {
            $this->model = $this->query()->create(array_merge([
                'secret' => Hash::make(Str::random(32)),
            ], $attributes));
            return $this->model;
        } catch (PDOException $exception) {
            throw DatabaseException::from($exception);
        }
    }

    /**
     * @param array $attributes
     * @return Device
     * @throws Exception
     */
    public function update($attributes)
    {
        try {
            $this->model->update($attributes);
            return $this->model;
        } catch (PDOException $exception) {
            throw new DatabaseException(null, 0, $exception);
        }
    }

    /**
     * @param string $provider
     * @param string $secret
     * @param string $clientIp
     * @param int $userId
     * @return Device
     * @throws Exception
     */
    public function save($provider = Device::PROVIDER_BROWSER, $secret = null, $clientIp = null, $userId = null)
    {
        try {
            if (empty($provider)) {
                $provider = Device::PROVIDER_BROWSER;
            }
            $device = empty($secret) ? null : $this->getByProviderAndSecret($provider, $secret);
            if (empty($device)) {
                if (empty($secret)) {
                    $secret = $this->trySecretWithProvider($provider);
                }
                $attributes = [
                    'user_id' => $userId,
                    'provider' => $provider,
                    'secret' => $secret,
                    'client_ip' => $clientIp,
                    'meta_array_value' => [
                        'client_info' => ClientHelper::information(),
                    ],
                ];
                $this->create($attributes);
            } else {
                $this->model($device);
                $attributes = [
                    'user_id' => $userId,
                    'client_id' => $clientIp,
                    'meta_array_value' => [
                        'client_info' => ClientHelper::information(),
                    ],
                ];
                $this->update($attributes);
            }
            return $this->model;
        } catch (PDOException $exception) {
            throw DatabaseException::from($exception);
        } catch (BaseException $exception) {
            throw AppException::from($exception);
        }
    }
}
