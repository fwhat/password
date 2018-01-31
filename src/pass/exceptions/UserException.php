<?php

namespace Dowte\Password\pass\exceptions;


class UserException extends BaseException
{
    public function __construct($message = "", $code = parent::USER_CODE, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}