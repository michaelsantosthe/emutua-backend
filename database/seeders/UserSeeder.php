<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Doctrine\ORM\EntityManager;
use App\Entities\User;

class UserSeeder extends Seeder
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

   public function run()
    {
        $user = new User();
        $user->setName('Michael Santos');
        $user->setEmail('michaelsantos.the@hotmail.com');
        $user->setPassword('12345678');
        $user->setEmailVerifiedAt(null);
        $user->setRememberToken(null);
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        echo "Usuário criado com sucesso!\n";
    }
}
