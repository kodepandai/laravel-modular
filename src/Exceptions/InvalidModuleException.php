<?php

namespace KodePandai\Modular\Exceptions;

use Exception;

class InvalidModuleException extends Exception
{
    public static function nameIsRequired(): self
    {
        $message = '
            This module does not have a name.
            You can set one with `$module->name("yourName")`';

        return new self($message);
    }
}
