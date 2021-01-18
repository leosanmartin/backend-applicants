<?php

namespace Osana\Challenge\Domain\Users;

class User
{
    /** @var Id */
    private $id;

    /** @var Login */
    private $login;

    /** @var Type */
    private $type;

    /** @var Profile */
    private $profile;

    public function __construct(Id $id, Login $login, Type $type, Profile $profile)
    {
        $this->id = $id;
        $this->login = $login;
        $this->type = $type;
        $this->profile = $profile;
    }

    public function getId(): Id
    {
        return $this->id;
    }


    public function getLogin(): Login
    {
        return $this->login;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getProfile(): Profile
    {
        return $this->profile;
    }
}
