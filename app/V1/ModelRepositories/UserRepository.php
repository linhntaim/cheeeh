<?php

namespace App\V1\ModelRepositories;

use App\V1\Events\UserCreated;
use App\V1\Events\UserEmailUpdated;
use App\V1\Events\UserRegistered;
use App\V1\Exceptions\AppException;
use App\V1\Exceptions\Exception;
use App\V1\Models\User;
use App\V1\Models\UserEmail;
use App\V1\Utils\DateTimeHelper;
use App\V1\Utils\Files\Filer\ImageFiler;
use App\V1\Utils\StringHelper;
use Illuminate\Http\UploadedFile;

class UserRepository extends ModelRepository
{
    protected function modelClass()
    {
        return User::class;
    }

    protected function searchOn($query, array $search)
    {
        if (!empty($search['except_protected'])) {
            $query->whereNotIn('id', User::PROTECTED);
        }
        if (!empty($search['name'])) {
            $query->where('name', 'like', '%' . $search['name'] . '%');
        }
        if (!empty($search['display_name'])) {
            $query->where('display_name', 'like', '%' . $search['display_name'] . '%');
        }
        if (!empty($search['roles'])) {
            $query->whereHas('roles', function ($query) use ($search) {
                $query->whereIn('id', $search['roles']);
            });
        }
        if (!empty($search['permissions'])) {
            $query->whereHas('roles.permissions', function ($query) use ($search) {
                $query->whereIn('id', $search['permissions']);
            });
        }
        return $query;
    }

    /**
     * @param $name
     * @return bool
     * @throws Exception
     */
    public function existed($name)
    {
        return $this->catch(function () use ($name) {
            return $this->query()->where('name', $name)->count() > 0;
        });
    }

    /**
     * @param string $displayName
     * @param string $email
     * @param string $password
     * @param string $urlAvatar
     * @param string $provider
     * @param string $providerId
     * @param string $appVerifyEmailPath
     * @return User
     * @throws Exception
     */
    public function createWhenRegistering($displayName, $email, $password, $urlAvatar, $provider, $providerId, $appVerifyEmailPath)
    {
        $this->model = $this->createWithAttributes([
            'display_name' => $displayName,
            'name' => (function ($email) {
                $originalName = $name = strtok($email, '@');
                $extraNumber = 0;
                while ($this->existed($name)) {
                    $name = $originalName . '.' . (++$extraNumber);
                }
                return $name;
            })($email),
            'password' => StringHelper::hash($password),
            'url_avatar' => $urlAvatar,
        ]);
        $userEmail = $this->model->emails()->create([
            'email' => $email,
            'is_alias' => UserEmail::IS_ALIAS_NO,
        ]);
        $this->model->localizations()->create();
        if (!empty($provider) && !empty($providerId)) {
            $this->model->socials()->create([
                'provider' => $provider,
                'provider_id' => $providerId,
            ]);
        }

        event(new UserRegistered($this->model, $password));
        event(new UserEmailUpdated($userEmail, $appVerifyEmailPath));

        return $this->model;
    }

    /**
     * @param array $attributes
     * @param bool $notified
     * @param string $email
     * @param bool $emailVerified
     * @param string $appVerifyEmailPath
     * @param array $roles
     * @return User
     * @throws Exception
     */
    public function create(array $attributes, $notified, $email, $emailVerified, $appVerifyEmailPath, array $roles = [])
    {
        $password = $attributes['password'];
        $attributes['password'] = StringHelper::hash($password);
        $this->createWithAttributes($attributes);
        $userEmail = $this->model->emails()->create([
            'email' => $email,
            'verified_at' => $emailVerified ? DateTimeHelper::syncNow() : null,
            'is_alias' => UserEmail::IS_ALIAS_NO,
        ]);
        $this->model->localizations()->create();
        if (count($roles) > 0) {
            $this->model->roles()->attach($roles);
        }

        if ($notified) {
            event(new UserCreated($userEmail, $password));
        }
        if (!$emailVerified) {
            event(new UserEmailUpdated($userEmail, $appVerifyEmailPath));
        }

        return $this->model;
    }

    /**
     * @param array $attributes
     * @param string $email
     * @param bool $emailVerified
     * @param string $appVerifyEmailPath
     * @param array|bool $roles
     * @return User
     * @throws Exception
     * @throws AppException
     */
    public function update(array $attributes, $email = '', $emailVerified = true, $appVerifyEmailPath = '', $roles = [])
    {
        if (in_array($this->model->id, User::PROTECTED)) {
            throw new AppException('Cannot edit this user');
        }

        if (!empty($attributes['password'])) {
            $attributes['password'] = StringHelper::hash($attributes['password']);
        } else {
            unset($attributes['password']);
        }

        $this->updateWithAttributes($attributes);

        if (!empty($email)) {
            $emailAttributes = [
                'email' => $email,
            ];
            if (!$emailVerified) {
                $emailAttributes['verified_at'] = null;
            }
            $this->model->email->update($emailAttributes);
        }

        if ($roles !== false) {
            if (count($roles) > 0) {
                $this->model->roles()->sync($roles);
            } else {
                $this->model->roles()->detach();
            }
        }

        if (!$emailVerified) {
            event(new UserEmailUpdated($this->model->email, $appVerifyEmailPath));
        }

        return $this->model;
    }

    /**
     * @param array $attributes
     * @return User
     * @throws AppException
     * @throws Exception
     */
    public function updateLocalization(array $attributes)
    {
        if (in_array($this->model->id, User::PROTECTED)) {
            throw new AppException('Cannot edit this protected user');
        }

        return $this->catch(function () use ($attributes) {
            $this->model->localization->update($attributes);
            return $this->model;
        });
    }

    /**
     * @param UploadedFile $imageFile
     * @return User
     * @throws Exception
     */
    public function updateAvatar($imageFile)
    {
        return $this->catch(function () use ($imageFile) {
            return $this->updateWithAttributes([
                'url_avatar' => (new ImageFiler($imageFile))
                    ->store()
                    ->getUrl(),
            ]);
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
            return $this->queryByIds($ids)
                ->whereNotIn('id', User::PROTECTED)
                ->delete();
        });
    }
}
