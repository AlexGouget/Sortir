<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\CreeSortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/sortie", name="sortie_")
 */

class SortieController extends AbstractController
{


    /**
     * @Route("/cree", name="cree")
     */
    public function cree(Request $request,
                         EntityManagerInterface $entityManager,
                         UserRepository $userRepository,
                         EtatRepository $etatRepository,
                         SortieRepository $sortieRepository,
                         Security $security
                          ): Response
    {
        $sortie = new Sortie();

        //TODO Recupérer l'instance de la personne connecté


        $userTest = $this->get('security.token_storage')->getToken()->getUser();
        $etat = $etatRepository->find(1);

        $sortie->setOrganisateur($userTest);
        $sortie->setEtat($etat);





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
