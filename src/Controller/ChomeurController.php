<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, Request};
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\OffreEmploi;
use App\Entity\Chomeur;
use App\Entity\Adresse;
use App\Entity\Entreprise;

use App\Form\ChomeurType;

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





    
    #[Route('/chomeurInscription', name:'chomeur_inscription')]
    public function chomeurInscription(ManagerRegistry $doctrine, Request $req ): Response
    {
        $chomeurCandidat = new Chomeur;
        $chomeurCandidat->setDateInscription(new \DateTime);
        $chomeurCandidat->setNaissance(new \DateTime);

        $adresse = new Adresse;
        $adresse->setNumCivique("1234");
        $adresse->setRue("sans issue");
        $adresse->setVille("pandemonium");
        $adresse->setcodePostal("G1Q1Q9");
        

        $chomeurCandidat->setAdresse($adresse);

        $formChomeur = $this->createForm(ChomeurType::class, $chomeurCandidat);
   
        $formChomeur->handleRequest($req);
        if ($formChomeur->isSubmitted()   )
        {
            //2- Nous somme bien en mode soumission
            // on valide les info du post
            if ($formChomeur->isValid())
            {
                //3- Le fom soumis est valide on sauvegarde $entreCandidat en BD
                $em  = $doctrine->getManager();
                $em->persist($chomeurCandidat);
                $em->flush();

                $sess = $req->getSession();
                $sess->set('chomeurConnecte', $chomeurCandidat );

                return $this->redirectToRoute('accueilChomeur');
            }
            else
            {
                $this->addFlash('erreur', 'Info invalide');
            }
        }
        return $this->render('chomeurInscription.html.twig', ['formulaire' => $formChomeur] );
    }
       



    
    #[Route('/rechercheTexte', name:'rte_recherche_textuelle')]
    public function rechercheTexte(ManagerRegistry $doctrine, Request $req ): Response
    {
        $texteRecherche = $req->request->get('filtreTexte');

        //dd($texteRecherche);

        $tousChomeurs = $doctrine->getManager()->getRepository(Chomeur::class)->findAll();

        $tabChomeurs = array();
        foreach($tousChomeurs as $unChomeur)
        {
            if (str_contains(strtoupper($unChomeur->getNom()), strtoupper($texteRecherche) ))
            {
                $tabChomeurs[] = $unChomeur;
            }
        }

        $offresEmplois = $doctrine->getManager()
                                  ->getRepository(OffreEmploi::class)
                                  ->findAll();

        $entreprises = $doctrine->getManager()->getRepository(Entreprise::class)->findAll();                                  
              
        if (count($offresEmplois) == 0)
        {
            $this->addFlash('notice', 'Aucune offres d\'emploi présentement affichée sur le site');
        }

        return $this->render('accueil.html.twig', 
        [
            'tabOE' => $offresEmplois,
            'chomeurs' =>$tabChomeurs,
            'tabEntrep' => $entreprises
        ]);
    }
}
