<?php

namespace App\V1\ModelRepositories;

use App\V1\Configuration;
use App\V1\Events\UserEmailUpdated;
use App\V1\Exceptions\DatabaseException;
use App\V1\Exceptions\UserException;
use App\V1\Models\UserEmail;
use App\V1\Utils\DateTimeHelper;
use PDOException;

class UserEmailRepository extends ModelRepository
{
    protected function modelClass()
    {
        return UserEmail::class;
    }

    public function getByEmail($email, $strict = true)
    {
        $query = $this->query()->where('email', $email);
        return $strict ? $query->firstOrFail() : $query->first();
    }

    public function updateEmail($email, $appVerifyPath)
    {
        if ($this->model->email != $email) {
            try {
                $this->model->update([
                    'email' => $email,
                    'verified_at' => null,
                ]);
            } catch (PDOException $exception) {
                throw DatabaseException::from($exception);
            }
        }

        event(new UserEmailUpdated($this->model, $appVerifyPath));

        return $this->model;
    }

    public function updateVerifiedAtByEmailAndCode($email, $verifiedCode)
    {
        $this->model = $this->query()
            ->where('email', $email)
            ->where('verified_code', $verifiedCode)
            ->first();

        if (empty($this->model)) {
            throw new UserException(static::__transErrorWithModule('email_and_verified_code_not_matched'));
        }

        try {
            $this->model->update([
                'verified_at' => DateTimeHelper::syncNow(),
            ]);
        } catch (PDOException $exception) {
            throw DatabaseException::from($exception);
        }

        return $this->model;
    }

    public function search($search = [], $paging = Configuration::FETCH_PAGING_YES, $itemsPerPage = Configuration::DEFAULT_ITEMS_PER_PAGE, $sortBy = null, $sortOrder = null)
    {
        $query = $this->query();

        if (!empty($search['user_id'])) {
            $query->where('user_id', $search['user_id']);
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
     * @param int $userId
     * @param string $email
     * @param bool $isAlias
     * @param bool $verified
     * @param string $appVerifyEmailPath
     * @return UserEmail
     */
    public function create($userId, $email, $isAlias, $verified, $appVerifyEmailPath)
    {
        try {
            if (!$isAlias) {
                $this->query()->where('user_id', $userId)
                    ->update([
                        'is_alias' => UserEmail::IS_ALIAS_YES,
                    ]);
            }

            $this->model = $this->query()->create([
                'user_id' => $userId,
                'email' => $email,
                'verified_at' => $verified ? DateTimeHelper::syncNow() : null,
                'is_alias' => $isAlias ? UserEmail::IS_ALIAS_YES : UserEmail::IS_ALIAS_NO,
            ]);

            if (!$verified) {
                event(new UserEmailUpdated($this->model, $appVerifyEmailPath));
            }

            return $this->model;
        } catch (PDOException $exception) {
            throw DatabaseException::from($exception);
        }
    }

    /**
     * @param int $userId
     * @param string $email
     * @param bool $isAlias
     * @param bool $verified
     * @param string $appVerifyEmailPath
     * @return UserEmail
     */
    public function update($userId, $email, $isAlias, $verified, $appVerifyEmailPath)
    {
        try {
            if (!$isAlias) {
                $this->query()->where('user_id', $userId)
                    ->where('id', '<>', $this->model->id)
                    ->update([
                        'is_alias' => UserEmail::IS_ALIAS_YES,
                    ]);
            }

            $this->model->update([
                'user_id' => $userId,
                'email' => $email,
                'verified_at' => $verified ? DateTimeHelper::syncNow() : null,
                'is_alias' => $isAlias ? UserEmail::IS_ALIAS_YES : UserEmail::IS_ALIAS_NO,
            ]);

            if (!$verified) {
                event(new UserEmailUpdated($this->model, $appVerifyEmailPath));
            }

            return $this->model;
        } catch (PDOException $exception) {
            throw DatabaseException::from($exception);
        }
    }

    public function delete($ids)
    {
        try {
            $this->query()->whereIn('id', $ids)
                ->notMain()
                ->delete();
        } catch (PDOException $exception) {
            throw DatabaseException::from($exception);
        }
    }
}
