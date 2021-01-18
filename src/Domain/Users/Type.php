<?php

namespace Osana\Challenge\Domain\Users;

use MyCLabs\Enum\Enum;

class Type extends Enum
{
    const GITHUB = 'github';
    const LOCAL = 'local';

    public static function GitHub(): Type
    {
        return new self(static::GITHUB);
    }

    public static function Local(): Type
    {
        return new self(static::LOCAL);
    }
}
