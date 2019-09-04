<?php

namespace App\V1\Http\Controllers\Api;

use App\V1\Http\Controllers\ApiController;
use App\V1\Http\Requests\Request;
use App\V1\ModelRepositories\UserEmailRepository;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordController extends ApiController
{
    public function store(Request $request)
    {
        if ($request->has('_forgot')) {
            return $this->forgot($request);
        }
        if ($request->has('_reset')) {
            return $this->reset($request);
        }

        return $this->responseFail();
    }

    private function forgot(Request $request)
    {
        $this->validated($request, [
            'email' => 'required|email',
        ]);

        $userEmail = (new UserEmailRepository())->getByEmail($request->input('email'), false);
        if (empty($userEmail)) {
            return $this->responseFail(trans(Password::INVALID_USER));
        }

        $response = Password::broker()->sendResetLink([
            'id' => $userEmail->user_id
        ]);

        return $response == Password::RESET_LINK_SENT
            ? $this->responseSuccess()
            : $this->responseFail(trans($response));
    }

    private function reset(Request $request)
    {
        $this->validated($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $userEmail = (new UserEmailRepository())->getByEmail($request->input('email'), false);
        if (empty($userEmail)) {
            return $this->responseFail(trans(Password::INVALID_USER));
        }

        $response = Password::broker()->reset(
            [
                'id' => $userEmail->user_id,
                'password' => $request->input('password'),
                'password_confirmation' => $request->input('password_confirmation'),
                'token' => $request->input('token'),
            ],
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $response == Password::PASSWORD_RESET
            ? $this->responseSuccess()
            : $this->responseFail(trans($response));
    }

    public function show(Request $request)
    {
        if ($request->has('_reset')) {
            return $this->showReset($request);
        }

        return $this->responseFail();
    }

    private function showReset(Request $request)
    {
        $this->validated($request, [
            'token' => 'required',
            'email' => 'required|email',
        ]);

        $userEmail = (new UserEmailRepository())->getByEmail($request->input('email'), false);
        if (empty($userEmail)) {
            return $this->responseFail(trans(Password::INVALID_USER));
        }

        $passwordBroker = Password::broker();
        $user = $passwordBroker->getUser([
            'id' => $userEmail->user_id,
        ]);
        if (is_null($user)) {
            return $this->responseFail(trans(Password::INVALID_USER));
        }
        if (!$passwordBroker->tokenExists($user, $request->input('token'))) {
            return $this->abort404();
        }

        return $this->responseSuccess();
    }
}
