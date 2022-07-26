<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Services\EtatSortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_home")
     */
    public function home(SortieRepository $sortieRepo, EtatSortie $etatSortie): Response
    {
        $etatSortie->checkAndUpdateEtatAll();
        $sorties = $sortieRepo->findSortiesOuverte(16);


        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'listSortie' => $sorties
        ]);
    }


}
