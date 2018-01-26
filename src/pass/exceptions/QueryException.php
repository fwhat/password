<?php
namespace Dowte\Password\pass\exceptions;

class QueryException extends \Exception
{
    public function __construct($message = "", $code = 10001, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}