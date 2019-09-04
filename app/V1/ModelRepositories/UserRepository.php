<?php

namespace App\V1\ModelRepositories;

use App\V1\Configuration;
use App\V1\Events\UserCreated;
use App\V1\Events\UserEmailUpdated;
use App\V1\Events\UserRegistered;
use App\V1\Exceptions\AppException;
use App\V1\Exceptions\DatabaseException;
use App\V1\Models\User;
use App\V1\Models\UserEmail;
use App\V1\Utils\DateTimeHelper;
use App\V1\Utils\Files\Filer\ImageFiler;
use Illuminate\Support\Facades\Hash;
use PDOException;

class UserRepository extends ModelRepository
{
    protected function modelClass()
    {
        return User::class;
    }

    public function existed($name)
    {
        return $this->query()->where('name', $name)->count() > 0;
    }

    public function createWhenRegistering($displayName, $email, $password, $urlAvatar, $provider, $providerId, $appVerifyEmailPath)
    {
        try {
            $originalName = $name = strtok($email, '@');
            $extraNumber = 0;
            while ($this->existed($name)) {
                $name = $originalName . '.' . (++$extraNumber);
            }

            $this->model = $this->query()->create([
                'display_name' => $displayName,
                'name' => $name,
                'password' => Hash::make($password),
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
        } catch (PDOException $exception) {
            throw DatabaseException::from($exception);
        }
    }

    public function search($search = [], $paging = Configuration::FETCH_PAGING_YES, $itemsPerPage = Configuration::DEFAULT_ITEMS_PER_PAGE, $sortBy = null, $sortOrder = null)
    {
        $query = $this->query();

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

    /**
     * @param array $attributes
     * @param bool $notified
     * @param string $email
     * @param bool $emailVerified
     * @param string $appVerifyEmailPath
     * @param array $roles
     * @return mixed
     */
    public function create(array $attributes, $notified, $email, $emailVerified, $appVerifyEmailPath, array $roles = [])
    {
        try {
            $password = $attributes['password'];
            $attributes['password'] = Hash::make($password);

            $this->model = $this->query()->create($attributes);
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
        } catch (PDOException $exception) {
            throw DatabaseException::from($exception);
        }
    }

    /**
     * @param array $attributes
     * @param string $email
     * @param bool $emailVerified
     * @param string $appVerifyEmailPath
     * @param array|bool $roles
     * @return User
     * @throws AppException
     */
    public function update(array $attributes, $email = '', $emailVerified = true, $appVerifyEmailPath = '', $roles = [])
    {
        if (in_array($this->model->id, User::PROTECTED)) {
            throw new AppException('Cannot edit this user');
        }

        try {
            if (!empty($attributes['password'])) {
                $attributes['password'] = Hash::make($attributes['password']);
            } else {
                unset($attributes['password']);
            }

            $this->model->update($attributes);

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
        } catch (PDOException $exception) {
            throw DatabaseException::from($exception);
        }
    }

    public function updateLocalization(array $attributes)
    {
        if (in_array($this->model->id, User::PROTECTED)) {
            throw new AppException('Cannot edit this user');
        }

        try {
            $this->model->localization->update($attributes);
            return $this->model;
        } catch (PDOException $exception) {
            throw DatabaseException::from($exception);
        }
    }

    public function updateAvatar($imageFile)
    {
        try {
            $this->model->update([
                'url_avatar' => (new ImageFiler($imageFile))
                    ->store()
                    ->getUrl(),
            ]);
            return $this->model;
        } catch (AppException $exception) {
            throw $exception;
        } catch (PDOException $exception) {
            throw DatabaseException::from($exception);
        } catch (Exception $exception) {
            throw AppException::from($exception);
        }
    }

    public function delete($ids)
    {
        try {
            $this->query()->whereIn('id', $ids)
                ->whereNotIn('id', User::PROTECTED)
                ->delete();
        } catch (PDOException $exception) {
            throw DatabaseException::from($exception);
        }
    }
}
