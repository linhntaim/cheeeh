<?php

namespace App\V1\Exceptions;

class DatabaseException extends Exception
{
    const LEVEL = 2;
    const CODE = 503;
}
