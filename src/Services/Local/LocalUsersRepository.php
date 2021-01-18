<?php

namespace Osana\Challenge\Services\Local;


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

class LocalUsersRepository implements UsersRepository
{
    
    private $filePathUsers=__DIR__ . '/../../../data/users.csv';
    private $filePathProfiles=__DIR__ . '/../../../data/profiles.csv';

    private function getData(string $filePath,string $filter = null): Collection
    {
        $json = csvToJson($filePath,$filter); 
        $jsonObject = json_decode($json , false);
        
        $data = new Collection($jsonObject);

        return  $data;
    }

    private function validateLogin($item,string $query = ''): bool
    {
        $valid = true;

        if(!empty($query)){
            $search = stripos($item->login,$query,0);
            $valid = $search !== false && $search === 0 ? true : false;
        }
            
        return $valid;
    }

    private function createUser($item): User
    {
                $dataItem = $this->getData($this->filePathProfiles,$item->id);
                $dataItem = $dataItem[0];
                
                $itemName = $dataItem->name ?? '';
                $name = new Name ($itemName);

                $itemCompany = $dataItem->company ?? '';
                $company = new Company($itemCompany);

                $itemLocation = $dataItem->location ?? '';
                $location = new Location($itemLocation);

                $profile = new Profile($name,$company,$location);

                $id = new Id($item->id); 
                $login = new Login($item->login); 
                $type = Type::Local();

                $user = new User($id,$login,$type,$profile);  
                
                 return $user; 
    }

    private function generateRelease($items,string $query,int $limit): Collection
    {
        
        $filtered = $items->filter(function ($item, $key) use($query){
                    $valid = $this->validateLogin($item,$query);

                    if($valid){
                      return $item;
                    }
        });

        $filtered = $filtered->slice(0, $limit);

        $release = $filtered->map(function ($item) use($query){
                $user = $this->createUser($item);
                return $user;
        });

        return $release;
    }

    private function findByQuery(string $query,int $limit): Collection
    {
        
        $data = $this->getData($this->filePathUsers);

        return $this->generateRelease($data,$query,$limit);

        //return $data;
    }

    public function findByLogin(Login $login, int $limit = 0): Collection
    {
        // TODO: implement me
        
        $query = $login->getValue();
        $users = $this->findByQuery($query,$limit);
       
        return $users;
    }

    public function getByLogin(Login $login, int $limit = 0): User
    {
        // TODO: implement me
        $query = $login->getValue();
        $data = $this->getData($this->filePathUsers);

        $filtered = $data->filter(function ($item, $key) use($query){
        
                    if($item->login === $query){
                      return $item;
                    }
        });

        $user = $filtered->map(function ($item){
                $user = $this->createUser($item);
                return $user;
        })->first();

        return $user;
    }

    public function countUsers(): int
    {
        $data = $this->getData($this->filePathUsers);
        return $data->count();
    }

    public function add(User $user): void
    {
        // TODO: implement me
        $userData = $user->getId()->getValue().",".$user->getLogin()->getValue().",".$user->getType()->getValue()."\n";
        $fp = fopen ($this->filePathUsers,'a');
        fwrite ($fp, $userData);
        fclose ($fp);
        
        $profile = $user->getProfile();
        $profileData = $user->getId()->getValue().",".$profile->getCompany()->getValue().",".$profile->getLocation()->getValue().",".$profile->getName()->getValue()."\n";

        $fp = fopen ($this->filePathProfiles,'a');
        fwrite ($fp, $profileData);
        fclose ($fp);

    }
}








