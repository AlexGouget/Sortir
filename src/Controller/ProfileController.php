<?php

namespace App\Controller;

use App\Form\EditMdpType;
use App\Form\EditUserType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Services\EtatSortie;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/list", name="profile_list")
     */
    public function list(UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $users = $userRepository->findAll();

        return $this->render('profile/listUtilisateur.html.twig', [
            'users' => $users
        ]);
    }


    /**
     * @Route("/admin/profile/suppression/{id}", name="profile_suppression")
     */
    public function suppUser(int $id, UserRepository $userRepository, EntityManagerInterface $em, Request $request): Response
    {
        $user = $userRepository->find($id);
        $userRepository->remove($user, true);

        $this->addFlash('success', 'utilisateur supprimé!');
        return $this->redirectToRoute('profile_list');

    }

    /**
     * @Route("/admin/profile/suspendre/{id}", name="profile_suspendre")
     */
    public function suspendreUser(int $id,EtatRepository $etatRepo,SortieRepository $sortieRepo, UserRepository $userRepository, EntityManagerInterface $em, Request $request): Response
    {

        //on suspend l'utilisateur
        $user = $userRepository->find($id);
        $user->setActif(false);
        $em->persist($user);
        //on recherche toutes les sortie organisé par ce dernier
        $etatAnnule = $etatRepo->find(5);
        $sorties = $sortieRepo->findEventForDisableUser($user);

        foreach ($sorties as $sortie){

                $sortie->setEtat($etatAnnule);
                $sortie->setMotif("Organisateur suspendu");
                $em->persist($sortie);

        }
        $em->flush();

        $this->addFlash('success', 'utilisateur suspendu!');
        return $this->redirectToRoute('profile_list');

    }

    /**
     * @Route("/admin/profile/enlever-suspension/{id}", name="profile_enleverSuspendre")
     */
    public function EnleverSuspendreUser(int $id,EtatSortie $etatSortie,EtatRepository $etatRepo,SortieRepository $sortieRepo, UserRepository $userRepository, EntityManagerInterface $em, Request $request): Response
    {

        //on suspend l'utilisateur
        $user = $userRepository->find($id);
        $user->setActif(true);
        $em->persist($user);
        //on recherche toutes les sortie organisé par ce dernier
        $etatOuverte = $etatRepo->find(6);
        $sorties = $sortieRepo->findForUnableUser($user);
        foreach ($sorties as $sortie){
                $sortie->setEtat($etatOuverte);
                $sortie->setMotif(null);
                $em->persist($sortie);



        }
        $em->flush();

        $this->addFlash('success', 'utilisateur et sortie retablie!');
        return $this->redirectToRoute('profile_list');

    }



    /**
     * @Route("/profile/editmdp/", name="profile_editMdp")
     */
    public function editMdp(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $user = $this->getUser();
        $editMDPForm = $this->createForm(EditMdpType::class, $user);
        $editMDPForm->handleRequest($request);
        if ($editMDPForm->isSubmitted() && $editMDPForm->isValid()){
            $oldMdp = $editMDPForm->get('plainPassword')->getData();
            $newMdp = $editMDPForm->get('newPassword')->getData();
            if ($hasher->isPasswordValid($user, $oldMdp)){
                $user->setPassword(
                    $hasher->hashPassword($user, $newMdp)
                   );
                $entityManager->flush();
                $this->addFlash('success','Mot de passe modifié avec succes!');
                $this->redirectToRoute('profile_editMdp', ['id'=> $user->getId()] );

            } else {
                $this->addFlash('warning', 'le mot de passe initial est incorrect');
            }
        }
        return $this->render('profile/editmdp.html.twig', [
            'user' => $user,
            'editMdpForm'=> $editMDPForm->createView()
        ]);
    }

    /**
     * @Route("/profile/editdetails/", name="profile_edit_details")
     */
    public function editDetails(EntityManagerInterface $em, Request $request): Response
    {
        $user = $this->getUser();
        $editUserForm = $this->createForm(EditUserType::class, $user);
        $editUserForm->handleRequest($request);

        if ($editUserForm->isSubmitted() && $editUserForm->isValid()) {
            //$user->setImageFile($editUserForm->get('imageFile')->getData()) ;
            $em->flush();
            $this->addFlash('success','Informations modifiée(s) avec succes!');
            $this->redirectToRoute('profile_details', ['id'=> $user->getId()]);
        }

        return $this->render('profile/editDetails.html.twig', [
            'user' => $user,
            'editUserForm'=> $editUserForm->createView()
        ]);


    }

    /**
     * @Route("/profile/details/{id}", name="profile_details")
     */
    public function details(int $id, UserRepository $userRepository, EntityManagerInterface $em, Request $request): Response
    {
        $user = $userRepository->find($id);
        return $this->render('profile/details.html.twig', [
            'user' => $user,
            ]);
    }

}
