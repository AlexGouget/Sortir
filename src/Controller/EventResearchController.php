<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventResearchController extends AbstractController
{
    /**
     * @Route("/trouvez-une-sortie", name="app_event_research")
     */
    public function index(EntityManagerInterface $em,SortieRepository $sortieRepo): Response
    {
        $sorties = $sortieRepo->findSortiesOuverte(16);
        return $this->render('event_research/index.html.twig', [
            'controller_name' => 'EventResearchController',
            'listSortie'=>$sorties
        ]);
    }
}
