<?php

namespace Osana\Challenge\Services\GitHub;

use Osana\Challenge\Domain\Users\Company;
use Osana\Challenge\Domain\Users\Id;
use Osana\Challenge\Domain\Users\Location;
use Osana\Challenge\Domain\Users\Login;
use Osana\Challenge\Domain\Users\Name;
use Osana\Challenge\Domain\Users\Profile;
use Osana\Challenge\Domain\Users\Type;
use Osana\Challenge\Domain\Users\User;
use Osana\Challenge\Domain\Users\UsersRepository;
use Tightenco\Collect\Support\Collection;

class GitHubUsersRepository implements UsersRepository
{
    public function findByLogin(Login $name, int $limit = 0): Collection
    {
        // TODO: implement me
    }

    public function getByLogin(Login $name, int $limit = 0): User
    {
        // TODO: implement me
    }

    public function add(User $user): void
    {
        throw new OperationNotAllowedException();
    }
}
