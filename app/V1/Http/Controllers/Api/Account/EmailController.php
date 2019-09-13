<?php

namespace App\V1\Http\Controllers\Api\Account;

use App\V1\Http\Controllers\Api\UserEmailController;
use App\V1\Http\Requests\Request;
use Illuminate\Validation\Rule;

class EmailController extends UserEmailController
{
    protected function search(Request $request)
    {
        return [
            'user_id' => $request->user()->id,
        ];
    }

    protected function storeValidatedRules(Request $request)
    {
        return [
            'email' => 'required|email|max:255|unique:user_emails,email',
        ];
    }

    protected function storeExecute(Request $request)
    {
        return $this->modelRepository->create(
            $request->user()->id,
            $request->input('email'),
            $request->input('is_alias', false) == true,
            $request->input('verified', false) == true,
            $request->input('app_verify_email_path')
        );
    }

    protected function updateValidatedRules(Request $request)
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('user_emails', 'email')->ignore($this->modelRepository->getId()),
            ],
        ];
    }

    protected function updateValidated(Request $request)
    {
        parent::updateValidated($request);

        if ($this->modelRepository->model()->user_id != $request->user()->id) {
            $this->abort403();
        }
    }

    protected function updateExecute(Request $request)
    {
        return $this->modelRepository->update(
            $request->user()->id,
            $request->input('email'),
            $request->input('is_alias', false) == true,
            $request->input('verified', false) == true,
            $request->input('app_verify_email_path')
        );
    }

    protected function bulkDestroyValidated(Request $request)
    {
        parent::bulkDestroyValidated($request);

        $ids = $request->input('ids');
        if ($request->user()->emails()->whereIn('id', $ids)->count() != count($ids)) {
            $this->abort403();
        }
    }
}
