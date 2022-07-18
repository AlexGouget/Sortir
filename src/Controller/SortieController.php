<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\CreeSortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/sortie", name="sortie_")
 */

class SortieController extends AbstractController
{
    /**
     * @Route("/cree", name="cree")
     */
    public function cree(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $user = new User();
        $user->setNom('lol');
        $user->setActif('true');
        $user->setEmail('lol@l');
        $user->setIsVerified('true');
        $user->setPassword('abc');
        $user->setTelephone('03');
        $user->setRoles((array)'[ROLES_USER]');




        $formulaireSortie=$this->createForm(CreeSortieType::class, $sortie);




        //TODO Seter avec le nom de l'utilisateur courant

        $formulaireSortie->handleRequest($request);

        if($formulaireSortie->isSubmitted()&&$formulaireSortie->isValid()){
            $entityManager->persist($sortie);
            $entityManager->flush();
        }





        return $this->render('sortie/index.html.twig', [
            'formulaireSortie' =>  $formulaireSortie->createView(),
        ]);
    }


}
