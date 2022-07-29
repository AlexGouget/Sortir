<?php

namespace App\Services;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class EtatSortie
{

    private EtatRepository $etatRepo;
    private SortieRepository $sortieRepo;
    private EntityManagerInterface $em;

    private $ETAT_BROUILLON = 1;
    private $ETAT_FINI = 2;
    private $ETAT_EN_COUR = 3;
    private $ETAT_ARCHIVE = 4;
    private $ETAT_ANNULE = 5;
    private $ETAT_OUVERTE = 6;
    private $ETAT_CLOTURE = 7;

    public function __construct(EtatRepository $etatRepo, SortieRepository $sortieRepository, EntityManagerInterface $em)
    {
        $this->etatRepo = $etatRepo;
        $this->sortieRepo = $sortieRepository;
        $this->em = $em;
        date_default_timezone_set("Europe/Paris");

        //on initialise chaque Etat avec son objet correspondant
        $etat = $this->etatRepo->findAll();
        $index = 0;
        $this->ETAT_BROUILLON = $etat[$index++];
        $this->ETAT_FINI = $etat[$index++];
        $this->ETAT_EN_COUR = $etat[$index++];
        $this->ETAT_ARCHIVE = $etat[$index++];
        $this->ETAT_ANNULE = $etat[$index++];
        $this->ETAT_OUVERTE = $etat[$index++];
        $this->ETAT_CLOTURE = $etat[$index++];

    }


    /**
     * @throws \Exception
     */
    public function checkAndUpdateEtatByOne(Sortie $sortie):void{

        $this->checkAndUpdateSortieCloture($sortie);
        $this->checkAndUpdateEndEvent($sortie);
       $this->checkAndUpdateSortieArchive($sortie);
    }


    /**
     * @throws \Exception
     */
    public function checkAndUpdateEtatAll():void
    {
        $this->checkAndUpdateSortiesOuverteCloture();
        $this->checkAndUpdateSortiesOuverteTermine();
        $this->checkAndUpdateSortiesArchive();
        $this->em->flush();
    }


    /**
     * @return \App\Entity\Sortie
     */
    public function checkAndUpdateSortiesOuverteTermine(): void
    {
        //on cherche les sorties encore ouverte pour les cloturer
        $sorties = $this->sortieRepo->findCloseAndOpen();


        foreach ($sorties as $sortie){
            //on regarde si elles sont finie
            $this->CheckAndUpdateEndEvent($sortie);
        }

        }
    /**
     * @param Sortie $sortie
     * @return void
     */
    public function CheckAndUpdateEndEvent(Sortie $sortie): void
    {
        if ($sortie->getDateHeureDebut() <= new \DateTime('now')) {
            $sortie->setEtat($this->ETAT_FINI);
            $this->em->persist($sortie);
        }
    }



    public function checkAndUpdateSortiesOuverteCloture() :void
    {
        $sorties = $this->sortieRepo->findCloseAndOpen();


        //on regarde si l'inscription est encore possible

        foreach ($sorties as $sortie) {
            $this->checkAndUpdateSortieCloture($sortie);
        }

    }

    /**
     * @param Sortie $sortie
     * @return void
     */
    public function checkAndUpdateSortieCloture(Sortie $sortie): void
    {
        if ($sortie->getDateLimiteInscription() > new \DateTime('now')) {
            $sortie->setEtat($this->ETAT_OUVERTE);
            if( count($sortie->getParticipant()) != $sortie->getNbInscriptionMax()) {
                $sortie->setEtat($this->ETAT_OUVERTE);
            }else{
                $sortie->setEtat($this->ETAT_CLOTURE);
            }
        }else{
            $sortie->setEtat($this->ETAT_CLOTURE);
        }
        $this->em->persist($sortie);

    }



    /**
     * @return void
     *
     * @throws \Exception
     */
    public function checkAndUpdateSortiesArchive(): void
    {


        //on chercher les sorties finies pour archivers celle de plus de 30 jours

        $sortiesFini = $this->sortieRepo->findBy(['etat' => $this->ETAT_FINI]);
        foreach ($sortiesFini as $sortie) {
            $this->checkAndUpdateSortieArchive($sortie);
        }
    }

    /**
     * @param Sortie $sortie
     * @return void
     * @throws \Exception
     */
    public function checkAndUpdateSortieArchive(Sortie $sortie): void
    {
        if ($sortie->getDateHeureDebut() < new \DateTime('now' . "-30 days")) {
            $sortie->setEtat($this->ETAT_ARCHIVE);
            $this->em->persist($sortie);
        }
    }


}