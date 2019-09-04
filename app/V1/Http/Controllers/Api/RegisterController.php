<?php

namespace App\V1\Http\Controllers\Api;

use App\V1\Http\Controllers\ApiController;
use App\V1\Http\Requests\Request;
use App\V1\ModelRepositories\UserRepository;
use App\V1\ModelTransformers\AccountTransformer;
use App\V1\Utils\ConfigHelper;

class RegisterController extends ApiController
{
    protected $userRepository;

    public function __construct()
    {
        parent::__construct();

        $this->userRepository = new UserRepository();
    }

    public function store(Request $request)
    {
        $this->validated($request, [
            'display_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:user_emails'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);;

        $this->transactionStart();
        return $this->responseSuccess([
            'user' => $this->transform(
                AccountTransformer::class,
                $this->userRepository->createWhenRegistering(
                    $request->input('display_name'),
                    $request->input('email'),
                    $request->input('password'),
                    $request->input('url_avatar', ConfigHelper::defaultAvatarUrl()),
                    $request->input('provider'),
                    $request->input('provider_id'),
                    $request->input('app_verify_email_path')
                )
            ),
        ]);
    }
}
