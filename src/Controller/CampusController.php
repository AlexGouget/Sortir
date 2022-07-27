<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\Campus\CreeCampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    public function manageCampus(EntityManagerInterface $em, CreeCampusType $creeCampusType, Request $request, CampusRepository $campusRepository): Response
    {

        $campusAll = $campusRepository->findAll();




        $campus = new Campus();
        $formulaireCampus=$this->createForm(CreeCampusType::class,$campus);
        $formulaireCampus->handleRequest($request);

        if($formulaireCampus->isSubmitted()) {
            $em->persist($campus);
            $em->flush();


            return $this->redirectToRoute('campus_');
        }





        return $this->render('campus/index.html.twig', [
            'listCampus' => $campusAll,
            'formulaireCampus'=>$formulaireCampus->createView()]);
    }


    /**
     * @Route("/supprimer/{id}", name="supprimer")
     */
    public function supprimerCampus(int $id,EntityManagerInterface $em, CampusRepository $campusRepository): Response
    {
        $campus= $campusRepository->find($id);
        $campusRepository->remove($campus);

        $em->flush();
        $this->addFlash('succes', 'Ce campus a été supprimé!');

        return $this->redirectToRoute('campus_');



    }
    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function editCampus(int $id,Request $request,EntityManagerInterface $em, CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->find($id);





        $formulaireCampus=$this->createForm(CreeCampusType::class,$campus);
        $formulaireCampus->handleRequest($request);

        if($formulaireCampus->isSubmitted()) {
            $em->persist($campus);
            $em->flush();




            return $this->redirectToRoute('campus_');
        }

        return $this->render('campus/edit.html.twig', [
            'formulaireCampus'=>$formulaireCampus->createView()]);




    }




}
