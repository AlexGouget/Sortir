<?php

namespace App\Controller;

use App\Form\EditMdpType;
use App\Form\UserEditFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{

    /**
     * @Route("/profile/details/mdp/{id}", name="profile_editMdp")
     */
    public function editMdp(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $user = $userRepository->find($id);
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
     * @Route("/profile/details/{id}", name="profile_details")
     */
    public function details(int $id, UserRepository $userRepository, EntityManagerInterface $em, Request $request): Response
    {
        $user = $userRepository->find($id);
        $editUserForm = $this->createForm(UserEditFormType::class, $user);
        $editUserForm->handleRequest($request);

        if ($editUserForm->isSubmitted() && $editUserForm->isValid()) {
            $user->setPrenom($editUserForm->get('prenom')->getData());
            $user->setNom($editUserForm->get('nom')->getData());
            $user->setTelephone($editUserForm->get('telephone')->getData());
            $em->flush();
            $this->addFlash('success','Informations modifiée(s) avec succes!');
            $this->redirectToRoute('profile_details', ['id'=> $user->getId()]);
        }

        return $this->render('profile/details.html.twig', [
            'user' => $user,
            'editUserForm'=> $editUserForm->createView()
        ]);
    }


}
