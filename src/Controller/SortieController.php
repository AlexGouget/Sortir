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



        $userTest = $this->get('security.token_storage')->getToken()->getUser();
        $sortie->setOrganisateur($userTest);



        $formulaireSortie=$this->createForm(CreeSortieType::class, $sortie);
        $formulaireLieu=$this->createForm(CreeLieuType::class,$lieu );


        $formulaireSortie->handleRequest($request);
        $formulaireLieu->handleRequest($request);

        if($formulaireSortie->get('enregistrer')->isClicked()&&$formulaireSortie->isValid())

            {
                $etat = $etatRepository->findOneBy(array('libelle'=> 'Brouillon'));
            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Votre sortie est enregistrée en brouillon !');
            }

        if($formulaireSortie->get('publier')->isClicked()&&$formulaireSortie->isSubmitted()&&$formulaireSortie->isValid())

        {
            $etat = $etatRepository->findOneBy(array('libelle'=> 'Ouverte'));
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

<<<<<<< Updated upstream
=======
<<<<<<< HEAD

=======
>>>>>>> 4e9eac91501afe12878b013ba207681afe543c33
>>>>>>> Stashed changes
    /**
     * @Route("/detail/{id}", name="detail")
     */
    public function detailSortie(Request $request,Sortie $sortie,SortieRepository  $sortieRepo,EntityManagerInterface $em):Response{

        $sortie = $sortieRepo->find($sortie);
        if(!$sortie){throw  $this->createNotFoundException('Sortie introuvable');}
        return $this-> render('sortie/detail.html.twig',['sortie' => $sortie]);
    }


    /**
     * @Route("/sortie/inscription/{id}", name="inscription")
     */
    public function inscriptionSortie(int $id, Request $request, SortieRepository $sortieRepository, EntityManagerInterface $em ): Response
    {
        $sortie = $sortieRepository->find($id);
        $user = $this->getUser();
        if($sortie->getId() === $id){
            $sortie->addParticipant($user);
            $em->flush();
            $this->addFlash('succes', 'Inscription à la sortie réussie!');
        } else {
            $this->addFlash('warning', "une erreur est survenue a l'incription");
        }

        return $this->redirectToRoute('sortie_detail',['id'=>$id]);
    }

    /**
     * @Route("/desister/{id}", name="desister")
     */
    public function desisterSortie(int $id, Request $request, SortieRepository $sortieRepository, EntityManagerInterface $em ): Response
    {
        $sortie = $sortieRepository->find($id);
        $user = $this->getUser();
        if($sortie->getId() === $id){

            $sortie->removeParticipant($user);
            $em->flush();
            $this->addFlash('succes', 'Votre desistement a été pris en compte!');
        } else {
            $this->addFlash('warning', "une erreur est survenue a l'incription");
        }

        return $this->redirectToRoute('sortie_detail',['id'=>$id]);
    }

    /**
     * @Route("/admin/desinscrire/{idUser}/{idSortie}", name="desinscrire")
     */
    public function desinscrireSortie(int $idUser,int $idSortie, Request $request, SortieRepository $sortieRepository,UserRepository $userRepo, EntityManagerInterface $em ): Response
    {
        $sortie = $sortieRepository->find($idSortie);
        $user = $userRepo->find($idUser);
        if($sortie->getId() === $idSortie){

            $sortie->removeParticipant($user);
            $em->flush();
            $this->addFlash('succes', "Utilisateur retiré de l'évènement!");
        } else {
            $this->addFlash('warning', "une erreur est survenue de la désincription");
        }

        return $this->redirectToRoute('sortie_detail',['id'=>$idSortie]);
    }


    /**
     * @Route("/annuler/{idUser}/{idSortie}", name="annuler")
     */

    public function annulerSortie(int $idUser,int $idSortie,Request $request, SortieRepository $sortieRepository, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        $sortie = $sortieRepository->find($idSortie);
        $user = $userRepository->find($idUser);

        if ($sortie->getOrganisateur()->getId() === $user->getId()){

            $sortieRepository->remove($sortie);
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('succes', "Votre évenement à été retiré !");
        } else {
            $this->addFlash('warning', "une erreur est survenue lors de la suppression de votre évenement");
        }




        return $this->redirectToRoute('main_home');
    }
<<<<<<< Updated upstream
=======
<<<<<<< HEAD

=======
>>>>>>> 4e9eac91501afe12878b013ba207681afe543c33
>>>>>>> Stashed changes

}
