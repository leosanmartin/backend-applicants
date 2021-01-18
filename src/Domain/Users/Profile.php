<?php

namespace Osana\Challenge\Domain\Users;

class Profile
{
    /** @var Name */
    private $name;

    /** @var Company */
    private $company;

    /** @var Location */
    private $location;

    public function __construct(Name $name, Company $company, Location $location)
    {
        $this->name = $name;
        $this->company = $company;
        $this->location = $location;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }
}
