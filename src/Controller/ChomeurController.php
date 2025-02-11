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

final class ChomeurController extends AbstractController
{
    #[Route('/chomeurDetail/{idChomeur}')]
    public function chomeurDetail($idChomeur, ManagerRegistry $doctrine ): Response
    {
        //return new Response("id du chomeur: $idChomeur");

        $chomeur = $doctrine->getManager()->getRepository(Chomeur::class)->find($idChomeur);
        return $this->render('chomeurDetails.html.twig', [
            'chomeur' => $chomeur
        ]);
    }
}
