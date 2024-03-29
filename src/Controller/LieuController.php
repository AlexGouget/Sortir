<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\CreeLieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Message;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lieu", name="lieu_")
 */

class LieuController extends AbstractController
{
    /**
     * @Route("/cree", name="cree")
     */
    public function cree(Request $request,EntityManagerInterface $entityManager,LieuRepository $lieuRepository,Notification $notification): Response
    {


        $lieu = new Lieu();
        $formulaireLieu=$this->createForm(CreeLieuType::class,$lieu );
        $formulaireLieu->handleRequest($request);
        $message = new Message();

        if($formulaireLieu->isSubmitted()&&$formulaireLieu->isValid()){

            
            $entityManager->persist($lieu);
            $entityManager->flush();
            $this->addFlash('success', 'Votre lieu est bien enregistré !');



            return  $this->redirectToRoute('sortie_cree');

    }


        return $this->render('lieu/index.html.twig', [
            'formulaireLieu' =>  $formulaireLieu->createView(),
                ]);
            }
}
