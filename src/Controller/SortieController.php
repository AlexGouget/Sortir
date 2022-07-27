<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\CreeLieuType;
use App\Form\CreeSortieType;
use App\Form\EditModifSortieType;
use App\Form\SupprSortieType;
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

        $user = $this->getUser();
        $sortie->setOrganisateur($user);


        $formulaireSortie=$this->createForm(CreeSortieType::class, $sortie);
        $formulaireSortie->handleRequest($request);




        $lieu = new Lieu();
        $formulaireLieu=$this->createForm(CreeLieuType::class,$lieu );
        $formulaireLieu->handleRequest($request);

        if($formulaireLieu->isSubmitted()&&$formulaireLieu->isValid()) {


            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Votre lieu est bien enregistré !');

        }

            if($formulaireSortie->get('enregistrer')->isClicked()&&$formulaireSortie->isValid())

            {
                $newlieu =$formulaireSortie->get('newLieu')->getData();

                if($newlieu){
                    $sortie->setLieu($newlieu);
                }

                if($formulaireSortie->isValid()){

                    $etat = $etatRepository->findOneBy(array('libelle'=> 'Brouillon'));
                    $sortie->setEtat($etat);
                    $entityManager->persist($sortie);
                    $entityManager->flush();

                    $this->addFlash('success', 'Votre sortie est enregistrée en brouillon !');
                    return  $this->redirectToRoute('main_home');
                }


            }




        if($formulaireSortie->get('publier')->isClicked()&&$formulaireSortie->isSubmitted())

        {


            $newlieu =$formulaireSortie->get('newLieu')->getData();

            if($newlieu){
                $sortie->setLieu($newlieu);
            }

            if($formulaireSortie->isValid()){

                $etat = $etatRepository->findOneBy(array('libelle'=> 'Ouverte'));
                $sortie->setEtat($etat);

                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('success', 'Votre sortie est visible par les autres utilisateurs  !');

                return  $this->redirectToRoute('sortie_detail',['id'=>$sortie->getId()]);
            }


        }


        if($formulaireSortie->get('annuler')->isClicked())

        {
          return  $this->redirectToRoute('main_home');
        }

        return $this->render('sortie/index.html.twig', [
            'formulaireSortie' =>  $formulaireSortie->createView(),


        ]);
    }

    /**
     * @Route ("/editSortie/{id}", name="edit")
     */

    public function editSortie(SortieRepository $sortieRepository,Sortie  $sortie,EntityManagerInterface $em, Request $request): Response
    {
        $sortie = $sortieRepository->find($sortie);
        $formulaireSortie=$this->createForm(CreeSortieType::class, $sortie);
        $formulaireSortie->handleRequest($request);


        if($formulaireSortie->get('enregistrer')->isClicked()&&$formulaireSortie->isValid()){

           $lieu =$formulaireSortie->get('newLieu')->getData();
           if($lieu){
                $newlieu = new Lieu();

                $newlieu->setNom($lieu->getNom());
                $newlieu->setRue($lieu->getRue());
                $newlieu->setVille($lieu->getVille());
                $newlieu->setLatitude($lieu->getLatitude());
                $newlieu->setLongitude($lieu->getLongitude());

               $em->refresh($sortie->getLieu());
               
                $sortie->setLieu($newlieu);

            }

            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success','Sortie modifiée(s) avec succes!');

            return  $this->redirectToRoute('sortie_detail',['id'=>$sortie->getId()]);
        }

        return $this->render('sortie/editSortie.html.twig', [
            'formulaireSortie'=> $formulaireSortie->createView()
        ]);


    }







    /**
     * @Route("/detail/{id}", name="detail")
     */
    public function detailSortie(EtatRepository $etatRepository,Request $request,Sortie $sortie,SortieRepository  $sortieRepo,EntityManagerInterface $em):Response{

        $sortie = $sortieRepo->find($sortie);

        $formulaireMotif=$this->createForm(EditModifSortieType::class, $sortie);
        $formulaireMotif->handleRequest($request);

        $supprSortie = $this->createForm(SupprSortieType::class,$sortie);
        $supprSortie->handleRequest($request);


        //Annulation d'article via modales (voir pour le mettre dans un service)


        if($formulaireMotif->isSubmitted()){

            $etat = $etatRepository->findOneBy(array('libelle'=> 'Annule'));
            $sortie->setEtat($etat);
            $em->flush($sortie);

            $this->addFlash('success', 'Votre sortie a était annuler!');
           return$this->redirectToRoute('main_home');



        }

        if(!$sortie){throw  $this->createNotFoundException('Sortie introuvable');}

        if($supprSortie->isSubmitted()){
            $sortieRepo->remove($sortie);
            $em->flush();

            $this->addFlash('success', 'Votre sortie a était supprimer!');

            return  $this->redirectToRoute('main_home');

        }





        return $this-> render('sortie/detail.html.twig',['sortie' => $sortie,
            'formulaireMotif' =>  $formulaireMotif->createView(),
            'supprSortie' => $supprSortie->createView(), ]);


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



}
