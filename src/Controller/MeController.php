<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Security;

class MeController
{

    public function __construct(private readonly Security $security){}

    public function __invoke()
    {
        $user = $this->security->getUser();

        return $user;
    }
}