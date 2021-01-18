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
   
    private function getData(string $url): Collection
    {
        $data = getDataApi($url);
        $data = new Collection($data);
        return  $data;
    }

    private function createUser($data): User
    {
            $dataItem = $this->getData($data->url);
                
            $itemName = $dataItem['name'] ?? '';
            $name = new Name ($itemName);

            $itemCompany = $dataItem['company'] ?? '';
            $company = new Company($itemCompany);

            $itemLocation = $dataItem['location'] ?? '';
            $location = new Location($itemLocation);

            $profile = new Profile($name,$company,$location);

             $id = new Id($data->id); 
             $login = new Login($data->login); 
             $type = Type::GitHub();

             $user = new User($id,$login,$type,$profile);  

             return $user;
    }

    private function generateRelease($items): Collection
    {
        $release = $items->map(function ($item){

             $user = $this->createUser($item);
            
             return $user;
        });

        return $release;
    }

    private function getDataByQuery(string $query,int $limit): Collection
    {
        
        $items = null;

        if(!empty($query)){
             $url =  env('API_GITHUB_URL')."/search/users?q=$query+in:login&per_page=$limit";
             $response = $this->getData($url);
             $items = collect($response["items"]);
        }
        else{
            $url = env('API_GITHUB_URL')."/users?per_page=$limit"; 
            $items = $this->getData($url);
        }

        return $this->generateRelease($items);
        
    }

    public function findByLogin(Login $name, int $limit = 0): Collection
    {
        // TODO: implement me
        $query = $name->getValue();
        $users = $this->getDataByQuery($query,$limit);
       
        return $users;
    }

    public function getByLogin(Login $name, int $limit = 0): User
    {
        // TODO: implement me
        $url = env('API_GITHUB_URL')."/users/".$name->getValue();
        $data = getDataApi($url);
        $user = $this->createUser($data);

        return $user;
    }

    public function add(User $user): void
    {
        throw new OperationNotAllowedException();
    }
}
