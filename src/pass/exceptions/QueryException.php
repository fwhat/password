<?php
namespace Dowte\Password\pass\exceptions;

class QueryException extends BaseException
{
    public function __construct($message = "", $code = parent::QUERY_CODE, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}