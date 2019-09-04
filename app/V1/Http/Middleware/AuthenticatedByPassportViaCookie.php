<?php

namespace App\V1\Http\Middleware;

use Closure;
use App\V1\Http\Requests\Request;
use App\V1\Utils\ConfigHelper;
use App\V1\Utils\CryptoJs\AES;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Guards\TokenGuard;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\ResourceServer;

class AuthenticatedByPassportViaCookie
{
    public function handle(Request $request, Closure $next)
    {
        $defaultCookieName = ConfigHelper::get('app.cookie.names.default');
        if (!auth()->check() && $request->hasCookie($defaultCookieName)) {
            $token = json_decode(AES::decrypt($request->cookie($defaultCookieName), ConfigHelper::get('app.cookie.secret')));
            if ($token !== false && isset($token->access_token) && isset($token->token_type) && isset($token->refresh_token) && isset($token->token_end_time)) {
                $request->headers->set(
                    'Authorization',
                    $token->token_type . ' ' . $token->access_token
                );

                $app = app();
                $user = (new TokenGuard(
                    $app->make(ResourceServer::class),
                    Auth::createUserProvider('users'),
                    $app->make(TokenRepository::class),
                    $app->make(ClientRepository::class),
                    $app->make('encrypter')
                ))->user($request);

                if (!empty($user)) {
                    auth()->login($user);
                }
            }
        }

        return $next($request);
    }
}
