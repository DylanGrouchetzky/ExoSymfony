<?php

namespace App\DataFixtures;

use App\Entity\Document;
use App\Entity\Place;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hash;
    public function __construct(UserPasswordHasherInterface $hash)
    {
        $this->hash = $hash;
    }
    public function load(ObjectManager $manager): void
    {
        $slugify = new Slugify();
        $date = new DateTime();
        
        $user1 = new User();
        $user1
        ->setEmail('test@test.fr')
        ->setMainPicture('https://picsum.photos/seed/cat66/1600')
        ->setNameUser('Takumi')
        ->setFirtsName('Takumi')
        ->setLastName('Futfiwara')
        ->setRoles(['ROLE_USER']);
        $password = $this->hash->hashPassword($user1,'test');
        $user1->setPassword($password);
        $manager->persist($user1);

        for ($i=0; $i < 5; $i++) { 
            $place = new Place();
            $place
            ->setName('Place'.$i)
            ->setSlug($slugify->slugify($place->getName()))
            ->setDescription('Bonjour ceci est une description')
            ->setCoordinateLatitude(500)
            ->setCoordinateLongitude(500)
            ->setDatePublish($date)
            ->setNumberLike(0)
            ->setUser($user1);
            $manager->persist($place);
            for ($e=0; $e < 5; $e++) { 
                $document = new Document();
                $document
                ->setUrl('https://picsum.photos/seed/cat'.$e.'/1600')
                ->setPlace($place);
                $manager->persist($document);
            }
        }

        // $product = new Product();

        $manager->flush();
    }
}
