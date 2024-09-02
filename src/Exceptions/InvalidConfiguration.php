<?php

namespace Jdsantos\Laradocker\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    public function __construct()
    {
        parent::__construct('Invalid configuration provided');
    }
}
