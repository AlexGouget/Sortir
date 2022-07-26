<?php

namespace App\Services;


use App\Entity\User;
use App\Repository\SortieRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RechercheService
{


        private SortieRepository $sortieRepository;

    public function __construct( SortieRepository $sortieRepository)
    {
       $this->sortieRepository = $sortieRepository;
    }

    public function recherche($query, $user, $campus , $cat , $dateDebut , $dateFin , $CBorga, $CBinscrit, $CBnonInscrit, $CBfini): array
    {
      return  $sorties = $this->sortieRepository->findSortiebyName( $query, $user, $campus , $cat , $dateDebut , $dateFin , $CBorga, $CBinscrit, $CBnonInscrit, $CBfini);
    }




}