<?php

namespace Osana\Challenge\Http\Controllers;

use Osana\Challenge\Domain\Users\Login;
use Osana\Challenge\Domain\Users\User;
use Osana\Challenge\Services\GitHub\GitHubUsersRepository;
use Osana\Challenge\Services\Local\LocalUsersRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Tightenco\Collect\Support\Collection;

class FindUsersController
{
    /** @var LocalUsersRepository */
    private $localUsersRepository;

    /** @var GitHubUsersRepository */
    private $gitHubUsersRepository;

    const DIVIDER = 2;


    private function getUsersRepository(string $query, int $limit): Collection
    {
        $limitGet = ceil($limit/self::DIVIDER);
        $login = new Login($query);

        $githubUsers = $this->gitHubUsersRepository->findByLogin($login, $limit);
        $usersGithubCount = $githubUsers->count();
    
        $localUsers = $this->localUsersRepository->findByLogin($login, $limit);
        $usersLocalCount = $localUsers->count();

        //Se calcula $limitGetLocal dando prioridad al repositorio Local en caso que 
        //el repositorio de Github no cubra el 50% del limite solicitado
        $limitGetLocal =  $usersGithubCount < $limitGet ? $limit - $usersGithubCount : $limitGet;
        $localUsers = $localUsers->slice(0, $limitGetLocal);

        
        $users = $localUsers->merge($githubUsers->slice(0, $limitGet));
        $usersCount = $users->count();

        if( $usersCount < $limit){
            if($limitGetLocal === $limitGet){
                $limitGetGithub = $limit - $localUsers->count();
                $users = $localUsers->merge($githubUsers->slice(0, $limitGetGithub));
            }
        }       
        

        $users = $users->map(function (User $user) {
            return [
                'id' => $user->getId()->getValue(),
                'login' => $user->getLogin()->getValue(),
                'type' => $user->getType()->getValue(),
                'profile' => [
                    'name' => $user->getProfile()->getName()->getValue(),
                    'company' => $user->getProfile()->getCompany()->getValue(),
                    'location' => $user->getProfile()->getLocation()->getValue(),
                ]
            ];
        });

        return $users;
    }

    public function __construct(LocalUsersRepository $localUsersRepository, GitHubUsersRepository $gitHubUsersRepository)
    {
        $this->localUsersRepository = $localUsersRepository;
        $this->gitHubUsersRepository = $gitHubUsersRepository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $query = $request->getQueryParams()['q'] ?? '';
        $limit = $request->getQueryParams()['limit'] ?? 0;

        // FIXME: Se debe tener cuidado en la implementación
        // para que siga las notas del documento de requisitos
        $users =  $this->getUsersRepository($query,$limit);

        $response->getBody()->write($users->toJson());

        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(200, 'OK');
    }
}
