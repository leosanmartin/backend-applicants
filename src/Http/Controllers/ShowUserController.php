<?php

namespace Osana\Challenge\Http\Controllers;

use Osana\Challenge\Domain\Users\Login;
use Osana\Challenge\Domain\Users\Type;
use Osana\Challenge\Services\GitHub\GitHubUsersRepository;
use Osana\Challenge\Services\Local\LocalUsersRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Tightenco\Collect\Support\Collection;

class ShowUserController
{
    /** @var LocalUsersRepository */
    private $localUsersRepository;

    /** @var GitHubUsersRepository */
    private $gitHubUsersRepository;

    public function __construct(LocalUsersRepository $localUsersRepository, GitHubUsersRepository $gitHubUsersRepository)
    {
        $this->localUsersRepository = $localUsersRepository;
        $this->gitHubUsersRepository = $gitHubUsersRepository;
    }

    private function generateRelease($user): Collection
    {
        $array = array(
          'id' => $user->getId()->getValue(),
          'login' => $user->getLogin()->getValue(),
          'type' => $user->getType()->getValue(),
          'profile' => array(
                            'name' => $user->getProfile()->getName()->getValue(),
                            'company' => $user->getProfile()->getCompany()->getValue(),
                            'location' => $user->getProfile()->getLocation()->getValue(),
                        )
        );

        return new Collection([0 => $array]); 
    }

    public function __invoke(Request $request, Response $response, array $params): Response
    {
        $type = new Type($params['type']);
        $login = new Login($params['login']);

        // TODO: implement me

        $user = $type->getValue() === 'local'   ? $this->localUsersRepository->getByLogin($login) 
                                                : $this->gitHubUsersRepository->getByLogin($login);
        

        $response->getBody()->write($this->generateRelease($user)->toJson());

        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(200, 'OK');
        
    }
}
