<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordEncoderInterface $passwordHasher)
    {
        $this -> passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user -> setUsername('admin');
        $user -> setEmail('daphne.bordel@gmail.com');
        $user -> setPassword($this->passwordHasher->encodePassword(
            $user,
            'test'
        ));
        $manager -> persist($user);
        $manager->flush();
    }
}
