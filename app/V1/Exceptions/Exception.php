<?php

namespace App\V1\Exceptions;

use App\V1\Utils\ClassTrait;
use Exception as BaseException;
use PDOException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

abstract class Exception extends BaseException implements HttpExceptionInterface
{
    use ClassTrait;

    const LEVEL = 4;
    const CODE = 503;

    /**
     * @param BaseException $exception
     * @return Exception
     */
    public static function from($exception)
    {
        $class = static::__class();
        return new $class(null, 0, $exception);
    }

    protected static function getExceptionMessage(BaseException $exception)
    {
        if ($exception instanceof PDOException) {
            return $exception->errorInfo[2];
        }
        return $exception->getMessage();
    }

    protected $attachedData;
    protected $messages;

    public function __construct($message = null, $code = 0, BaseException $previous = null)
    {
        if (is_array($message)) {
            $this->messages = $message;
            $message = array_values($this->messages)[0][0];
        } elseif (!empty($message)) {
            $message = $this->formatMessage($message);
            $this->messages = [$message];
        }

        parent::__construct($message, $code, $previous);

        if ($previous) {
            $this->line = $previous->getLine();
            $this->file = $previous->getFile();
            if (empty($message)) {
                $this->message = $this->formatMessage(static::getExceptionMessage($previous));
                $this->messages = [$this->message];
            }
        }
    }

    public function getStatusCode()
    {
        return $this::CODE;
    }

    public function getHeaders()
    {
        return [];
    }

    public function setAttachedData($attachedData)
    {
        $this->attachedData = $attachedData;
        return $this;
    }

    public function getAttachedData()
    {
        return $this->attachedData;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function getLevel()
    {
        return $this::LEVEL;
    }

    public function formatMessage($message = '')
    {
        return empty($message) ?
            $this->__transErrorWithModule('level_failed')
            : $this->__transErrorWithModule('level', ['message' => $message]);
    }
}
