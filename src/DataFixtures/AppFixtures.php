<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Categorie;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private ObjectManager $manager;
    private UserPasswordHasherInterface $hasher;
    private Generator $generator;
    private $users;
    private $sorties;
    private $campuss;
    private $etat;
    private $lieux;

    public function __construct(UserPasswordHasherInterface $hasher, EntityManagerInterface $manager){
        $this->manager = $manager;
        $this->users = $this->manager->getRepository(User::class)->findAll();
        $this->sorties = $this->manager->getRepository(Sortie::class)->findAll();
        $this->campuss = $this->manager->getRepository(Campus::class)->findAll();
        $this->etats = $this->manager->getRepository(Etat::class)->find(6);
        $this->lieux = $this->manager->getRepository(Lieu::class)->findAll();
        $this->categories = $this->manager->getRepository(Categorie::class)->findAll();
        $this->hasher = $hasher;
        $this->generator = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->addSortie();
    }

    public function addSortie(){
        for ($i = 0; $i <5 ; $i++) {
            $sortie = new Sortie();
            $sortie->setNom($this->generator->sentence(3));
            $sortie->setCampus($this->generator->randomElement($this->campuss));
            $sortie->setEtat($this->etats);
            $sortie->setCategorie($this->generator->randomElement($this->categories));
            $sortie->setDateHeureDebut(date_create());
            $sortie->setDateLimiteInscription($this->generator->dateTimeBetween('now', '+3 Days'));
            $sortie->setInfosSortie($this->generator->word);
            $sortie->setOrganisateur($this->generator->randomElement($this->users));
            $sortie->setDuree(60);
            $sortie->setNbInscriptionMax($this->generator->randomDigitNotNull);
            $sortie->setLieu($this->generator->randomElement($this->lieux));

            $this->manager->persist($sortie);
        }
        $this->manager->flush();
    }


}
