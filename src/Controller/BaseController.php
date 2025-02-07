<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\OffreEmploi;
use App\Entity\Chomeur;
use App\Entity\Adresse;

ini_set('date.timezone', 'america/new_york');


final class BaseController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function accueil(ManagerRegistry $doctrine): Response
    {
        $offresEmplois = $doctrine->getManager()
                                  ->getRepository(OffreEmploi::class)
                                  ->findAll();

        $chomeurs = $doctrine->getManager()->getRepository(Chomeur::class)->findAll();                                  
                       
        return $this->render('accueil.html.twig', 
        [
            'message' => "L'avenir vous appartient",
            'tabOE' => $offresEmplois,
            'chomeurs' =>$chomeurs
        ]);
    }

    #[Route('/creerChomeurHC')]
    public function creerChomeurHC(ManagerRegistry $doctrine): Response
    {
        $chomeur = new Chomeur; 
        $chomeur->setNom('Dan');
        $chomeur->setCourriel('dan@gmail.com');
        $chomeur->setTelephone('4509345855');
        $chomeur->setDateInscription(new \DateTime);
        $chomeur->setNaissance(\DateTime::createFromFormat('Y-m-d', '1995-12-15'));

        $adresse = new Adresse();
        $adresse->setnumCivique("123 app1");
        $adresse->setRue(" de la Joie");
        $adresse->setVille("Saint-Lin");

        $chomeur->setAdresse($adresse);

        $em = $doctrine->getManager();
        $em->persist($chomeur);
        $em->flush();

        return $this->RedirectToRoute('accueil');
    }


}
