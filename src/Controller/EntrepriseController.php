<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\OffreEmploi;
use App\Entity\Chomeur;
use App\Entity\Adresse;
use App\Entity\Entreprise;

final class EntrepriseController extends AbstractController
{
    #[Route('/entrepriseEtSesOffres')]
    public function entrepriseEtSesOffres(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $tabEntrep = $em->getRepository(Entreprise::class)->findAll();


        return $this->render('entreprise.html.twig', [ 'tabEntrep'
            => $tabEntrep
        ]);
    }
}
