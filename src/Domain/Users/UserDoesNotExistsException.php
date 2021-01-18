<?php

namespace Osana\Challenge\Domain\Users;

use Exception;

class UserNotFoundException extends Exception
{
    public static function fromLogin(Login $login): UserNotFoundException
    {
        return new self(sprintf('Login: %s', $login->getValue()), 404);
    }
}
