<?php

namespace App\V1\Http\Controllers;

use App\V1\Configuration;
use App\V1\Exceptions\Exception;
use App\V1\Exceptions\UnhandledException;
use App\V1\Exceptions\UserException;
use App\V1\Http\Requests\Request;
use App\V1\Utils\ClientAppHelper;
use App\V1\Utils\LocalizationHelper;
use App\V1\Utils\LogHelper;
use App\V1\Utils\PaginationHelper;
use Closure;
use Exception as BaseException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use League\OAuth2\Server\Exception\OAuthServerException;

trait ApiResponseTrait
{
    protected static $extraResponse = null;

    public static function addBlockResponseMessage($message, $fresh = false)
    {
        if (!empty(static::$extraResponse)) {
            static::$extraResponse = [];
        }
        if ($fresh || !isset(static::$extraResponse['_block']) || static::$extraResponse['_block'] == null) {
            static::$extraResponse['_block'] = [];
        }
        static::$extraResponse['_block'][] = $message;
    }

    public static function addErrorResponseMessage($level, $data)
    {
        if (!empty(static::$extraResponse)) {
            static::$extraResponse = [];
        }
        static::$extraResponse['_error'] = [
            'level' => $level,
            'data' => $data,
        ];
    }

    /**
     * @param array|null $data
     * @param BaseException|array|string|null $message
     * @return array
     */
    public static function payload($data = null, $message = null)
    {
        $debug = null;
        $debugMode = config('app.debug');

        if ($message instanceof BaseException) {
            $exception = $message;
            $debug = [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
            if ($debugMode) {
                $debug['trace'] = $exception->getTrace();
            }
            if ($exception instanceof OAuthServerException) {
                $exception = new UserException(
                    trans('passport.' . $exception->getErrorType() . ($exception->getCode() == 8 ? '_refresh_token' : '')),
                    0,
                    $exception
                );
            }
            if (!($exception instanceof Exception)) {
                $exception = new UnhandledException($exception->getMessage(), 0, $exception);
            }

            $message = $exception->getMessages();
            static::addErrorResponseMessage($exception->getLevel(), $exception->getAttachedData());
        }

        return [
            '_messages' => empty($message) ? null : (array)$message,
            '_data' => $data,
            '_extra' => static::$extraResponse,
            '_exception' => empty($debug) ? null : ($debugMode ? $debug : base64_encode(json_encode($debug))),
        ];
    }

    protected function withThrottlingMiddleware()
    {
        $this->middleware(function (Request $request, Closure $next) {
            $this->throttleMiddleware($request);
            return $next($request);
        });
    }

    protected function throttleMiddleware(Request $request = null)
    {
        LocalizationHelper::getInstance()->autoFetch();
        ClientAppHelper::getInstance();
    }

    /**
     * @param boolean $failed
     * @param array $payload
     * @return JsonResponse
     */
    protected function response($failed, $payload)
    {
        return response()->json(
            $payload,
            $failed ? Configuration::HTTP_RESPONSE_STATUS_ERROR : Configuration::HTTP_RESPONSE_STATUS_OK
        );
    }

    /**
     * @param array|null $data
     * @param array|string|null $message
     * @return JsonResponse
     */
    protected function responseSuccess($data = null, $message = null)
    {
        $this->transactionComplete();
        return $this->response(false, static::payload($data, $message));
    }

    /**
     * @param Exception|array|string|null $message
     * @param array|null $data
     * @return JsonResponse
     */
    protected function responseFail($message = null, $data = null)
    {
        $this->transactionStop();
        if ($message instanceof BaseException) {
            LogHelper::error($message);
        }
        return $this->response(true, static::payload($data, $message));
    }

    protected function getRespondedModel($model)
    {
        if ($model instanceof LengthAwarePaginator) {
            return [
                'models' => $this->modelTransform($this->modelTransformerClass, $model),
                'pagination' => PaginationHelper::parse($model),
            ];
        }
        if ($model instanceof Collection) {
            return [
                'models' => $this->modelTransform($this->modelTransformerClass, $model),
            ];
        }
        if ($model instanceof Model) {
            return [
                'model' => $this->modelTransform($this->modelTransformerClass, $model),
            ];
        }
        return Arr::isAssoc($model) ? [
            'model' => $model,
        ] : [
            'models' => $model,
        ];
    }

    protected function responseModel($model)
    {
        return $this->responseSuccess($this->getRespondedModel($model));
    }
}
