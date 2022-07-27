<?php

namespace App\Controller;

use App\Entity\ville;
use App\Form\CreeVilleType;;
use App\Form\SupprSortieType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/admin/ville", name="ville_")
 */
class VilleController extends AbstractController
{
    /**
     * @Route("/", name="")
     */

    public function manageVille(EntityManagerInterface $em, CreeVilleType  $creeVilleType, Request $request, villeRepository $villeRepository): Response
    {

        $villeAll = $villeRepository->findAll();




        $ville = new ville();
        $formulaireville=$this->createForm(CreevilleType::class,$ville);
        $formulaireville->handleRequest($request);

        if($formulaireville->isSubmitted()&&$formulaireville->isValid()) {
            $em->persist($ville);
            $em->flush();


            return $this->redirectToRoute('ville_');
        }





        return $this->render('ville/index.html.twig', [
            'listville' => $villeAll,
            'formulaireville'=>$formulaireville->createView()]);
    }


    /**
     * @Route("/supprimer/{id}", name="supprimer")
     */
    public function supprimerville(int $id,EntityManagerInterface $em, villeRepository $villeRepository): Response
    {
        $ville= $villeRepository->find($id);
        $villeRepository->remove($ville);

        $em->flush();
        $this->addFlash('succes', 'Ce ville a été supprimé!');

        return $this->redirectToRoute('ville_');



    }
    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function editville(int $id,Request $request,EntityManagerInterface $em, villeRepository $villeRepository): Response
    {
        $ville = $villeRepository->find($id);





        $formulaireville=$this->createForm(CreevilleType::class,$ville);
        $formulaireville->handleRequest($request);

        if($formulaireville->isSubmitted()&&$formulaireville->isValid()){
            $em->persist($ville);
            $em->flush();




            return $this->redirectToRoute('ville_');
        }

        return $this->render('ville/edit.html.twig', [
            'formulaireville'=>$formulaireville->createView()]);




    }




}
