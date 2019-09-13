<?php

namespace App\V1\ModelRepositories;

use App\V1\Events\UserEmailUpdated;
use App\V1\Exceptions\Exception;
use App\V1\Exceptions\UserException;
use App\V1\Models\UserEmail;
use App\V1\Utils\DateTimeHelper;

class UserEmailRepository extends ModelRepository
{
    protected function modelClass()
    {
        return UserEmail::class;
    }

    protected function searchOn($query, array $search)
    {
        if (!empty($search['user_id'])) {
            $query->where('user_id', $search['user_id']);
        }
        return $query;
    }

    public function getByEmail($email, $strict = true)
    {
        $query = $this->query()->where('email', $email);
        return $this->catch(function () use ($strict, $query) {
            return $strict ? $query->firstOrFail() : $query->first();
        });
    }

    /**
     * @param string $email
     * @param string $appVerifyPath
     * @return UserEmail
     * @throws Exception
     */
    public function updateEmail($email, $appVerifyPath)
    {
        if ($this->model->email != $email) {
            $this->catch(function () use ($email) {
                return $this->updateWithAttributes([
                    'email' => $email,
                    'verified_at' => null,
                ]);
            });
        }

        event(new UserEmailUpdated($this->model, $appVerifyPath));

        return $this->model;
    }

    /**
     * @param string $email
     * @param string $verifiedCode
     * @return UserEmail
     * @throws Exception
     * @throws UserException
     */
    public function updateVerifiedAtByEmailAndCode($email, $verifiedCode)
    {
        $this->model = $this->catch(function () use ($email, $verifiedCode) {
            return $this->query()
                ->where('email', $email)
                ->where('verified_code', $verifiedCode)
                ->first();
        });

        if (empty($this->model)) {
            throw new UserException(static::__transErrorWithModule('email_and_verified_code_not_matched'));
        }

        return $this->catch(function () {
            return $this->updateWithAttributes([
                'verified_at' => DateTimeHelper::syncNow(),
            ]);
        });
    }

    /**
     * @param int $userId
     * @param string $email
     * @param bool $isAlias
     * @param bool $verified
     * @param string $appVerifyEmailPath
     * @return UserEmail
     * @throws Exception
     */
    public function create($userId, $email, $isAlias, $verified, $appVerifyEmailPath)
    {
        if (!$isAlias) {
            $this->catch(function () use ($userId) {
                return $this->query()
                    ->where('user_id', $userId)
                    ->update([
                        'is_alias' => UserEmail::IS_ALIAS_YES,
                    ]);
            });
        }

        $this->createWithAttributes([
            'user_id' => $userId,
            'email' => $email,
            'verified_at' => $verified ? DateTimeHelper::syncNow() : null,
            'is_alias' => $isAlias ? UserEmail::IS_ALIAS_YES : UserEmail::IS_ALIAS_NO,
        ]);

        if (!$verified) {
            event(new UserEmailUpdated($this->model, $appVerifyEmailPath));
        }

        return $this->model;
    }

    /**
     * @param int $userId
     * @param string $email
     * @param bool $isAlias
     * @param bool $verified
     * @param string $appVerifyEmailPath
     * @return UserEmail
     * @throws Exception
     */
    public function update($userId, $email, $isAlias, $verified, $appVerifyEmailPath)
    {
        if (!$isAlias) {
            $this->catch(function () use ($userId) {
                $this->query()
                    ->where('user_id', $userId)
                    ->where('id', '<>', $this->model->id)
                    ->update([
                        'is_alias' => UserEmail::IS_ALIAS_YES,
                    ]);
            });
        }

        $this->updateWithAttributes([
            'user_id' => $userId,
            'email' => $email,
            'verified_at' => $verified ? DateTimeHelper::syncNow() : null,
            'is_alias' => $isAlias ? UserEmail::IS_ALIAS_YES : UserEmail::IS_ALIAS_NO,
        ]);

        if (!$verified) {
            event(new UserEmailUpdated($this->model, $appVerifyEmailPath));
        }

        return $this->model;
    }

    /**
     * @param array $ids
     * @return bool
     * @throws Exception
     */
    public function deleteWithIds(array $ids)
    {
        return $this->catch(function () use ($ids) {
            return $this->queryByIds($ids)->notMain()->delete();
        });
    }
}
