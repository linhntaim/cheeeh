<?php

namespace App\V1\Http\Controllers\Api;

use App\V1\Http\Controllers\ApiController;
use App\V1\Http\Requests\Request;
use App\V1\ModelRepositories\UserEmailRepository;
use App\V1\ModelTransformers\UserEmailTransformer;
use Illuminate\Validation\Rule;

class UserEmailController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new UserEmailRepository();
        $this->modelTransformerClass = UserEmailTransformer::class;
    }

    protected function storeValidatedRules(Request $request)
    {
        return [
            'user_id' => 'required|exists:users,id',
            'email' => 'required|email|max:255|unique:user_emails,email',
        ];
    }

    protected function storeExecute(Request $request)
    {
        return $this->modelRepository->create(
            $request->input('user_id'),
            $request->input('email'),
            $request->input('is_alias', false) == true,
            $request->input('verified', false) == true,
            $request->input('app_verify_email_path')
        );
    }

    protected function updateValidatedRules(Request $request)
    {
        return [
            'user_id' => 'required|exists:users,id',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('user_emails', 'email')->ignore($this->modelRepository->getId()),
            ],
        ];
    }

    protected function updateExecute(Request $request)
    {
        return $this->modelRepository->update(
            $request->input('user_id'),
            $request->input('email'),
            $request->input('is_alias', false) == true,
            $request->input('verified', false) == true,
            $request->input('app_verify_email_path')
        );
    }
}
