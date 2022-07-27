<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Categorie;
use App\Entity\User;
use App\Form\CreeSortieType;
use App\Form\FormulaireRecherche;
use App\Form\RegistrationFormType;
use App\Repository\SortieRepository;
use App\Services\RechercheService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class EventResearchController extends AbstractController
{




    /**
     * @Route("/trouvez-une-sortie", name="app_event_research")
     */
    public function index(Request $request,EntityManagerInterface $em,SortieRepository $sortieRepo, RechercheService $recherche): Response
    {


        $formulaireRecherche=$this->createForm(FormulaireRecherche::class);
        $formulaireRecherche->handleRequest($request);

        if($formulaireRecherche->isSubmitted()&&$formulaireRecherche->isValid()){

            if(!$this->getUser()){
                $user = new User();
            }else{
                $user = $this->getUser();
            }

            $sorties = $recherche->recherche($formulaireRecherche->get('query')->getData(),
                                             $user,
                                            $formulaireRecherche->get('campus')->getData(),
                                            $formulaireRecherche->get('categorie')->getData(),
                                            $formulaireRecherche->get('dateDebut')->getData(),
                                            $formulaireRecherche->get('dateFin')->getData(),
                                            $formulaireRecherche->get('CBorganisateur')->getData(),
                                            $formulaireRecherche->get('CBinscrit')->getData(),
                                            $formulaireRecherche->get('CBnonInscrit')->getData(),
                                            $formulaireRecherche->get('CBarchive')->getData());
        }else{
            $sorties = $sortieRepo->findSortiesOuverte(16);
        }

        return $this->render('event_research/index.html.twig', [
            'listSortie'=>$sorties,
            'searchForm'=>$formulaireRecherche->createView()

        ]);
    }

}
