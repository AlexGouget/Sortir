<?php

namespace App\Controller;

use App\Entity\User;


use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class CSVimportController extends AbstractController
{
    /**
     * @Route("/import", name="app_import")
     */
    function upload(Request $request, EntityManagerInterface $em, CampusRepository $campRepo, UserPasswordHasherInterface $userPasswordHasher)
    {
        // Form creation ommited

        $form2 = $this->createFormBuilder()
            ->add('submitFile', FileType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $form2->handleRequest($request);

        if ($form2->isSubmitted() && $form2->isValid()) {
            /** @var UploadedFile */
            $file = $form2->get('submitFile')->getData();

            // Open the file
            if (($handle = fopen($file->getPathname(), "r")) !== false) {
                // Read and process the lines.
                // Skip the first line if the file includes a header
                fgets($handle);
                while (($data = fgetcsv($handle)) !== false) {
                    // Do the processing: Map line to entity, validate if needed
                    $user = new User();
                    // Assign fields

                    $campus = $campRepo->findOneBy(['nom'=>$data[0]]);
                    if($campus){
                        $user->setCampus($campus);
                    }

                    $user->setNom($data[1]);
                    $user->setPrenom($data[2]);
                    $user->setEmail($data[3]);
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            'G4bse4xpnzLYeL7i'
                        )
                    );
                    $user->setTelephone($data[4]);
                    $user->setActif(true);
                    $user->setPseudo(uniqid());
                    $user->setRoles(["ROLE_USER"]);


                    $em->persist($user);
                }
                fclose($handle);
                $em->flush();
            }
        }
        return $this->render('cs_vimport/index.html.twig', [

            'Form'=>$form2->createView()
        ]);


    }
}
