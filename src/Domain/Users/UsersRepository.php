<?php

namespace Osana\Challenge\Domain\Users;

use Tightenco\Collect\Support\Collection;

interface UsersRepository
{
    public function findByLogin(Login $login, int $limit = 0): Collection;

    public function getByLogin(Login $login, int $limit = 0): User;

    public function add(User $user): void;
}
