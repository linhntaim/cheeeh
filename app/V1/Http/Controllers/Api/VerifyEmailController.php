<?php

namespace App\V1\Http\Controllers\Api;

use App\V1\Http\Controllers\ApiController;
use App\V1\Http\Requests\Request;
use App\V1\ModelRepositories\UserEmailRepository;
use App\V1\ModelTransformers\UserEmailTransformer;

class VerifyEmailController extends ApiController
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
            'email' => 'required|string|email',
            'verified_code' => 'required|string',
        ];
    }

    protected function storeExecute(Request $request)
    {
        return $this->modelRepository->updateVerifiedAtByEmailAndCode(
            $request->input('email'),
            $request->input('verified_code')
        );
    }
}
