<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\CreeLieuType;
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
        $lieu = new Lieu();

        //TODO Recupérer l'instance de la personne connecté


        $userTest = $this->get('security.token_storage')->getToken()->getUser();
        $sortie->setOrganisateur($userTest);






        $formulaireSortie=$this->createForm(CreeSortieType::class, $sortie);
        $formulaireLieu=$this->createForm(CreeLieuType::class,$lieu );




        //TODO Seter avec le nom de l'utilisateur courant

        $formulaireSortie->handleRequest($request);
        $formulaireLieu->handleRequest($request);

        if($formulaireSortie->get('enregistrer')->isClicked())

            {
                $etat = $etatRepository->find(1);
            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Votre sortie est enregistrée en brouillon !');
            }

        if($formulaireSortie->get('publier')->isClicked())

        {
            $etat = $etatRepository->find(6);
            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Votre sortie est visible par les autres utilisateurs  !');
        }



        if($formulaireSortie->get('annuler')->isClicked())

        {
          return  $this->redirectToRoute('main_home');
        }






        return $this->render('sortie/index.html.twig', [
            'formulaireSortie' =>  $formulaireSortie->createView(),
            'formulaireLieu' =>  $formulaireLieu->createView(),
        ]);
    }


}
