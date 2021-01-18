<?php

namespace Osana\Challenge\Http\Controllers;

use Osana\Challenge\Domain\Users\Company;
use Osana\Challenge\Domain\Users\Id;
use Osana\Challenge\Domain\Users\Location;
use Osana\Challenge\Domain\Users\Login;
use Osana\Challenge\Domain\Users\Name;
use Osana\Challenge\Domain\Users\Profile;
use Osana\Challenge\Domain\Users\Type;
use Osana\Challenge\Domain\Users\User;
use Osana\Challenge\Services\Local\LocalUsersRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Tightenco\Collect\Support\Collection;

class StoreUserController
{
    /** @var LocalUsersRepository */
    private $localUsersRepository;

    public function __construct(LocalUsersRepository $localUsersRepository)
    {
        $this->localUsersRepository = $localUsersRepository;
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

    public function __invoke(Request $request, Response $response): Response
    {
        // TODO: implement me
        $requestBody = $request->getBody();
        
        $loginParam = $request->getQueryParams()['login'] ?? '';
        $nameParam = $request->getQueryParams()['name'] ?? '';
        $companyParam = $request->getQueryParams()['company'] ?? '';
        $locationParam = $request->getQueryParams()['location'] ?? '';

        if(!empty($loginParam)&&!empty($nameParam)&&!empty($companyParam)&&!empty($locationParam)){

            $countLocalUsers = $this->localUsersRepository->countUsers();

            $name = new Name ($nameParam);
            $company = new Company($companyParam);
            $location = new Location($locationParam);
            $profile = new Profile($name,$company,$location);

            $idValue = $countLocalUsers+1;
            $id = new Id('CSV'.$idValue); 
            $login = new Login($loginParam); 
            $type = Type::Local();

            $user = new User($id,$login,$type,$profile);  
                
            $this->localUsersRepository->add($user);

            $response->getBody()->write($this->generateRelease($user)->toJson());

            return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(201, 'Created');
        }
        else{
            
            $response->getBody()->write('{"Error":"No se creo el usuario, los parametros enviados son incorrectos"}');
            return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(424, 'Failded');
        }


        
    }
}
