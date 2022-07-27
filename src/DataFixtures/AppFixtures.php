<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Categorie;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
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
    private $etat;

    public function __construct(UserPasswordHasherInterface $hasher, EntityManagerInterface $manager){
        $this->manager = $manager;
        $this->hasher = $hasher;
        $this->generator = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        //Commenter les fixtures non voulue pour ajouter des Entites sur une bdd remplie
        $this->manager = $manager;
        $this->addEtat();
        $this->addVille();
        $this->addLieu();
        $this->addCategorie();
        $this->addCampus();
        $this->addUsers();
        $this->addSortie();
    }
    public function addEtat(){
        $etat1 = new Etat(); $etat1->setLibelle("Brouillon");
        $etat2 = new Etat(); $etat2->setLibelle("Fini");
        $etat3 = new Etat(); $etat3->setLibelle("En cours");
        $etat4 = new Etat(); $etat4->setLibelle("Archive");
        $etat5 = new Etat(); $etat5->setLibelle("Annule");
        $etat6 = new Etat(); $etat6->setLibelle("Ouverte");
        $etat7 = new Etat(); $etat7->setLibelle("Cloture");
        $this->manager->persist($etat1);
        $this->manager->persist($etat2);
        $this->manager->persist($etat3);
        $this->manager->persist($etat4);
        $this->manager->persist($etat5);
        $this->manager->persist($etat6);
        $this->manager->persist($etat7);
        $this->manager->flush();
    }

    public function addVille(){
        $ville1= new Ville();
        $ville1->setNom('Paris');
        $ville1->setCodePostal('75000');
        $this->manager->persist($ville1);

        $ville2= new Ville();
        $ville2->setNom('Lille');
        $ville2->setCodePostal('59000');
        $this->manager->persist($ville2);

        $ville3= new Ville();
        $ville3->setNom('Strasbourg');
        $ville3->setCodePostal('67000');
        $this->manager->persist($ville3);

        $ville4= new Ville();
        $ville4->setNom('Angers');
        $ville4->setCodePostal('49000');
        $this->manager->persist($ville4);

        $ville5= new Ville();
        $ville5->setNom('Nantes');
        $ville5->setCodePostal('44000');
        $this->manager->persist($ville5);

        $ville6= new Ville();
        $ville6->setNom('Marseille');
        $ville6->setCodePostal('13000');
        $this->manager->persist($ville6);
        $this->manager->flush();
    }

    public function addLieu(){
       $villes = $this->manager->getRepository(Ville::class)->findAll();
        for ($i=0; $i<6; $i++){
            $lieu = new Lieu();
            $lieu->setNom($this->generator->word);
            $lieu->setLatitude($this->generator->numberBetween(-1000, 1000));
            $lieu->setLongitude($this->generator->numberBetween(-1000, 1000));
            $lieu->setRue($this->generator->streetName);
            $lieu->setVille($this->generator->randomElement($villes));
            $this->manager->persist($lieu);
        }
        $this->manager->flush();
    }

    public function addCategorie(){
        $categorie1 = new Categorie();
        $categorie1->setLibelleCat("Culturel");
        $categorie1->setPicto("learning.svg");
        $categorie1->setBackdrop("bg_museum.jpg");
        $this->manager->persist($categorie1);

        $categorie2 = new Categorie();
        $categorie2->setLibelleCat("Sport");
        $categorie2->setPicto("sport.svg");
        $categorie2->setBackdrop("bg_sport.jpg");
        $this->manager->persist($categorie2);

        $categorie3 = new Categorie();
        $categorie3->setLibelleCat("Soiree");
        $categorie3->setPicto("party.svg");
        $categorie3->setBackdrop("bg_party.jpg");
        $this->manager->persist($categorie3);
        $this->manager->flush();

    }

    public function addCampus(){
     $campus1 = new Campus();
     $campus1->setNom("Nantes");
     $this->manager->persist($campus1);

     $campus2 = new Campus();
     $campus2->setNom("Rennes");
     $this->manager->persist($campus2);

     $campus3 = new Campus();
     $campus3->setNom("Quimper");
     $this->manager->persist($campus3);

     $campus4 = new Campus();
     $campus4->setNom("Campus en ligne");
     $this->manager->persist($campus4);
     $this->manager->flush();

    }


    public function addUsers(){
        //Récuperation des campus existants avant le set randomElement
        $campuss = $this->manager->getRepository(Campus::class)->findAll();
        for ($i = 0; $i < 10 ; $i++) {
            $user = new User();
            $user->setNom($this->generator->lastName);
            $user->setPrenom($this->generator->firstName);
            $user->setPseudo($this->generator->userName);
            $user->setEmail($this->generator->safeEmail);
            $user->setCampus($this->generator->randomElement($campuss));
            $user->setPassword(
                $this->hasher->hashPassword(
                    $user,
                    '123456'
                )
            );
            $user->setTelephone($this->generator->phoneNumber);
            $user->setActif(1);
            $user->setRoles(['ROLE_USER']);
            $this->manager->persist($user);
        }
        $this->manager->flush();
    }

    public function addSortie(){
        //Récuperation des Users/Campus/Etats/Lieux existants en bdd avant de set randomElements
        $users = $this->manager->getRepository(User::class)->findAll();
        $campuss = $this->manager->getRepository(Campus::class)->findAll();
        $etat = $this->manager->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']); // id 6 = sortie ouverte
        $categories = $this->manager->getRepository(Categorie::class)->findAll();
        $lieux = $this->manager->getRepository(Lieu::class)->findAll();

        for ($i = 0; $i < 10 ; $i++) {
            $sortie = new Sortie();
            $sortie->setNom($this->generator->sentence(3));
            $sortie->setCampus($this->generator->randomElement($campuss));
            $sortie->setEtat($etat);
            $sortie->setCategorie($this->generator->randomElement($categories));
            $sortie->setDateHeureDebut(date_create('+3 Days'));
            $sortie->setDateLimiteInscription($this->generator->dateTimeBetween('now', '+3 Days'));
            $sortie->setInfosSortie($this->generator->realText(200, 2));
            $sortie->setOrganisateur($this->generator->randomElement($users));
            $sortie->setDuree(60);
            $sortie->setNbInscriptionMax($this->generator->randomDigitNotNull);
            $sortie->setLieu($this->generator->randomElement($lieux));

            $this->manager->persist($sortie);
        }
        $this->manager->flush();
    }
}
