<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CreeCampusType;
use App\Form\SupprSortieType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/campus", name="campus_")
 */
class CampusController extends AbstractController
{
    /**
     * @Route("/", name="")
     */

    public function manageCampus(EntityManagerInterface $em, CreeCampusType $editCampusType, Request $request, CampusRepository $campusRepository): Response
    {

        $campus = $campusRepository->findAll();


       // $campus = $campusRepository>find($campus);
      //  $formulaireSuppresion = $this->createForm(SupprSortieType::class,)



        //$formulaireMotif=$this->createForm(EditModifSortieType::class, $sortie);
      //  $formulaireMotif->handleRequest($request);




        return $this->render('campus/index.html.twig', [
            'listCampus' => $campus        ]);
    }
    /**
     * @Route("/edit", name="edit")
     */
    public function editCampus(EntityManagerInterface $em, CreeCampusType $editCampusType, Request $request, CampusRepository $campusRepository): Response
    {
        $campus = new Campus();
        $formulaireCampus=$this->createForm(CreeCampusType::class,$campus);
        $formulaireCampus->handleRequest($request);

        if($formulaireCampus->isSubmitted()&&$formulaireCampus->isValid()) {
            $em->persist($campus);
            $em->flush();


            return  $this->redirectToRoute('main_home');

        }

        if($formulaireCampus->get('')&&$formulaireCampus->isValid()) {
            $em->persist($campus);
            $em->flush();


            return  $this->redirectToRoute('main_home');

        }






        return $this->render('campus/edit.html.twig', [
            'formulaireCampus' =>  $formulaireCampus->createView(),    ]);
    }
}
