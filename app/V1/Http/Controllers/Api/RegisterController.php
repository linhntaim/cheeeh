<?php

namespace App\V1\Http\Controllers\Api;

use App\V1\Http\Controllers\ApiController;
use App\V1\Http\Requests\Request;
use App\V1\ModelRepositories\UserRepository;
use App\V1\ModelTransformers\AccountTransformer;
use App\V1\Utils\ConfigHelper;

class RegisterController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new UserRepository();
        $this->modelTransformerClass = AccountTransformer::class;
    }

    protected function storeValidatedRules(Request $request)
    {
        return [
            'display_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:user_emails'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    protected function storeExecute(Request $request)
    {
        return $this->modelRepository->createWhenRegistering(
            $request->input('display_name'),
            $request->input('email'),
            $request->input('password'),
            $request->input('url_avatar', ConfigHelper::defaultAvatarUrl()),
            $request->input('provider'),
            $request->input('provider_id'),
            $request->input('app_verify_email_path')
        );
    }
}
