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
use App\Services\EtatSortie;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @IsGranted("ROLE_USER", message="accés refusé")
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
                    $sortie->addParticipant($this->getUser());
                    $entityManager->flush();

                    $this->addFlash('successs', 'Votre sortie est enregistrée en brouillon !');
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
                $sortie->addParticipant($this->getUser());
                $entityManager->flush();

                $this->addFlash('successs', 'Votre sortie est visible par les autres utilisateurs  !');

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
     * @IsGranted("ROLE_USER", message="accés refusé")
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
            $this->addFlash('success','Sortie modifiée(s) avec succés!');

            return  $this->redirectToRoute('sortie_detail',['id'=>$sortie->getId()]);
        }

        return $this->render('sortie/editSortie.html.twig', [
            'formulaireSortie'=> $formulaireSortie->createView()
        ]);


    }







    /**
     * @Route("/detail/{id}", name="detail")
     */
    public function detailSortie(EtatSortie $etatSortie,EtatRepository $etatRepository,Request $request,Sortie $sortie,SortieRepository  $sortieRepo,EntityManagerInterface $em):Response{
        $etatSortie->checkAndUpdateEtatAll();
        $sortie = $sortieRepo->find($sortie);

        $formulaireMotif=$this->createForm(EditModifSortieType::class, $sortie);
        $formulaireMotif->handleRequest($request);

        $supprSortie = $this->createForm(SupprSortieType::class,$sortie);
        $supprSortie->handleRequest($request);


        //Annulation d'article via modales (voir pour le mettre dans un service)


        if($formulaireMotif->isSubmitted()){
            $this->denyAccessUnlessGranted("ROLE_USER");
            $etat = $etatRepository->findOneBy(array('libelle'=> 'Annule'));
            $sortie->setEtat($etat);
            $em->flush($sortie);

            $this->addFlash('successs', 'Votre sortie a était annuler!');
           return$this->redirectToRoute('main_home');



        }

        if(!$sortie){throw  $this->createNotFoundException('Sortie introuvable');}

        if($supprSortie->isSubmitted()){
            $sortieRepo->remove($sortie);
            $em->flush();

            $this->addFlash('successs', 'Votre sortie a était supprimer!');

            return  $this->redirectToRoute('main_home');

        }





        return $this-> render('sortie/detail.html.twig',['sortie' => $sortie,
            'formulaireMotif' =>  $formulaireMotif->createView(),
            'supprSortie' => $supprSortie->createView(), ]);


    }



    /**
     * @IsGranted("ROLE_USER", message="accés refusé")
     * @Route("/sortie/inscription/{id}", name="inscription")
     */
    public function inscriptionSortie(int $id, Request $request, SortieRepository $sortieRepository, EntityManagerInterface $em ): Response
    {
        $sortie = $sortieRepository->find($id);
        $user = $this->getUser();
        if($sortie->getId() === $id){
            $sortie->addParticipant($user);
            $em->flush();
            $this->addFlash('success', 'Inscription à la sortie réussie!');
        } else {
            $this->addFlash('warning', "une erreur est survenue a l'incription");
        }

        return $this->redirectToRoute('sortie_detail',['id'=>$id]);
    }

    /**
     * @IsGranted("ROLE_USER", message="accés refusé")
     * @Route("/desister/{id}", name="desister")
     */
    public function desisterSortie(int $id, Request $request, SortieRepository $sortieRepository, EntityManagerInterface $em ): Response
    {
        $sortie = $sortieRepository->find($id);
        $user = $this->getUser();
        if($sortie->getId() === $id){

            $sortie->removeParticipant($user);
            $em->flush();
            $this->addFlash('successs', 'Votre desistement a été pris en compte!');
        } else {
            $this->addFlash('warning', "une erreur est survenue a l'incription");
        }

        return $this->redirectToRoute('sortie_detail',['id'=>$id]);
    }

    /**
     * @IsGranted("ROLE_USER", message="accés refusé")
     * @Route("/admin/desinscrire/{idUser}/{idSortie}", name="desinscrire")
     */
    public function desinscrireSortie(int $idUser,int $idSortie, Request $request, SortieRepository $sortieRepository,UserRepository $userRepo, EntityManagerInterface $em ): Response
    {
        $sortie = $sortieRepository->find($idSortie);
        $user = $userRepo->find($idUser);
        if($sortie->getId() === $idSortie){

            $sortie->removeParticipant($user);
            $em->flush();
            $this->addFlash('success', "Utilisateur retiré de l'évènement!");
        } else {
            $this->addFlash('warning', "une erreur est survenue de la désincription");
        }

        return $this->redirectToRoute('sortie_detail',['id'=>$idSortie]);
    }

    /**
     * @Route("/publier/{id}", name="publier")
     */
    public function publierSortie(int $id,EtatRepository $etatRepository,Request $request, SortieRepository $sortieRepository, EntityManagerInterface $em ): Response
    {
        $sortie = $sortieRepository->find($id);
        $etatOuvert = $etatRepository->findOneBy(array('libelle'=> 'Ouverte'));

        if($sortie->getDateHeureDebut() >= new \DateTime('now')){

            if($sortie->getId() === $id){
                $sortie->setEtat($etatOuvert);
                $em->flush();
                $this->addFlash('success', 'Votre sortie a été publié!');
            } else {
                $this->addFlash('warning', "une erreur est survenue au moment de la publication");
            }
            return $this->redirectToRoute('main_home');

        }
        else {

            $this->addFlash('warning', 'La date de votre sortie est déjà passé ! Veuillez la modifier pour la publier');
            return $this->redirectToRoute('sortie_edit',['id'=>$id]);

        }



    }

}
