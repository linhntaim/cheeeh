<?php

namespace App\V1\Http\Controllers\Api;

use App\V1\Http\Controllers\ApiResponseTrait;
use Exception;
use Illuminate\Http\Response;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\TokenRepository;
use Lcobucci\JWT\Parser as JwtParser;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;
use Zend\Diactoros\Response as Psr7Response;

class LoginController extends AccessTokenController
{
    use ApiResponseTrait;

    public function __construct(AuthorizationServer $server, TokenRepository $tokens, JwtParser $jwt)
    {
        parent::__construct($server, $tokens, $jwt);

        $this->throttleMiddleware();
    }

    protected function withErrorHandling($callback)
    {
        try {
            return $callback();
        } catch (OAuthServerException $e) {
            $this->exceptionHandler()->report($e);

            $e->setPayload(static::payload(null, $e));

            return $this->convertResponse(
                $e->generateHttpResponse(new Psr7Response())
            );
        } catch (Exception $e) {
            $this->exceptionHandler()->report($e);

            return new Response($this->configuration()->get('app.debug') ? $e->getMessage() : 'Error.', 500);
        } catch (Throwable $e) {
            $this->exceptionHandler()->report(new FatalThrowableError($e));

            return new Response($this->configuration()->get('app.debug') ? $e->getMessage() : 'Error.', 500);
        }
    }
}
