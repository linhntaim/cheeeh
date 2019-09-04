<?php

namespace App\V1\Http\Controllers\Api;

use App\V1\Http\Controllers\ApiController;
use App\V1\Http\Requests\Request;
use App\V1\ModelRepositories\UserEmailRepository;
use App\V1\ModelTransformers\UserEmailTransformer;

class VerifyEmailController extends ApiController
{
    protected $userEmailRepository;

    public function __construct()
    {
        parent::__construct();

        $this->userEmailRepository = new UserEmailRepository();
    }

    public function store(Request $request)
    {
        $this->validated($request, [
            'email' => 'required|string|email',
            'verified_code' => 'required|string',
        ]);

        return $this->responseSuccess([
            'user_email' => $this->transform(
                UserEmailTransformer::class,
                $this->userEmailRepository->updateVerifiedAtByEmailAndCode(
                    $request->input('email'),
                    $request->input('verified_code')
                )
            ),
        ]);
    }
}
