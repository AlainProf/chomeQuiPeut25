<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, Request, Session\SessionInterface};
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\OffreEmploi;
use App\Entity\Chomeur;
use App\Entity\Adresse;
use App\Entity\Entreprise;

//ini_set('date.timezone', 'america/new_york');


final class BaseController extends AbstractController
{
    #[Route('/', name: 'racine')]
    public function racine(ManagerRegistry $doctrine, SessionInterface $sess): Response
    {
        $sess->clear();
        
        $offresEmplois = $doctrine->getManager()
                                  ->getRepository(OffreEmploi::class)
                                  ->findAll();

        $chomeurs = $doctrine->getManager()->getRepository(Chomeur::class)->findAll();                                  
        $entreprises = $doctrine->getManager()->getRepository(Entreprise::class)->findAll();                                  
              
        return $this->render('racine.html.twig', 
        [
            'tabOE' => $offresEmplois,
            'chomeurs' =>$chomeurs,
            'entreprises' => $entreprises
        ]);
    }

    

    #[Route('/accueilChomeur', name: 'accueilChomeur')]
    public function accueilChomeur(ManagerRegistry $doctrine, Request $req, SessionInterface $sess): Response
    {
        $idChomeur = $req->query->get('chomeurConnecte');

        if (empty($idChomeur))
        {
            $chomeurConnecte = $sess->get('chomeurConnecte');
        }
        else
        {
            $chomeurConnecte =  $doctrine->getManager()
                 ->getRepository(Chomeur::class)
                 ->find($idChomeur);
        }

        


        if ($chomeurConnecte)
        {
            $this->addFlash('succes', 'Bienvenue ' . $chomeurConnecte->getNom());
            $req->getSession()->set('chomeurConnecte', $chomeurConnecte);
        }
        
        

        $offresEmplois = $doctrine->getManager()
                                  ->getRepository(OffreEmploi::class)
                                  ->findAll();
        $entreprises = $doctrine->getManager()->getRepository(Entreprise::class)->findAll();                                  


        if (count($offresEmplois) == 0)
        {
            $this->addFlash('notice', 'Aucune offres d\'emploi présentement affichée sur le site');
        }

        return $this->render('accueilChomeur.html.twig', 
        [
            'tabOE' => $offresEmplois,
            'tabEntrep' => $entreprises
        ]);
    }

    #[Route('/accueilEntreprise', name: 'accueilEntreprise')]
    public function accueilEntreprise(ManagerRegistry $doctrine, Request $req): Response
    {
       $idEntreprise = $req->query->get('entrepriseConnectee');
       if (empty($idEntreprise))
       {
           $entrepriseConnectee = $req->getSession()->get('entrepriseConnectee');
       }
       else
       {
           $entrepriseConnectee =  $doctrine->getManager()
                ->getRepository(Entreprise::class)
                ->find($idEntreprise);
       }
        
        if ($entrepriseConnectee)
        {
            $this->addFlash('succes', 'Bienvenue ' . $entrepriseConnectee->getNom());
            $req->getSession()->set('entrepriseConnectee', $entrepriseConnectee);
        }

        return $this->render('accueilEntreprise.html.twig', 
        [
            "entrepriseConnectee" => $entrepriseConnectee
        ]);
    }





    #[Route('/accueil', name: 'accueil')]
    public function accueil(ManagerRegistry $doctrine): Response
    {
        $offresEmplois = $doctrine->getManager()
                                  ->getRepository(OffreEmploi::class)
                                  ->findAll();

        $chomeurs = $doctrine->getManager()->getRepository(Chomeur::class)->findAll();                                  
        $entreprises = $doctrine->getManager()->getRepository(Entreprise::class)->findAll();                                  
              
         
        if (count($offresEmplois) == 0)
        {
            $this->addFlash('notice', 'Aucune offres d\'emploi présentement affichée sur le site');
        }

        return $this->render('accueil.html.twig', 
        [
            'tabOE' => $offresEmplois,
            'chomeurs' =>$chomeurs,
            'tabEntrep' => $entreprises
        ]);
    }

    #[Route('/accueilFiltre/{idEntrep}', name: 'accueil_filtre')]
    public function accueilFiltre(ManagerRegistry $doctrine, Request $req, $idEntrep): Response
    {
        //$valFiltre = $req->request->get('filtreEntrep');
        if ($idEntrep == '1000')
           return $this->redirectToRoute('accueil');

        $em = $doctrine->getManager();
        $chomeurs = $doctrine->getManager()->getRepository(Chomeur::class)->findAll();                                  
        $entrepFiltree = $em->getRepository(Entreprise::class)->find($idEntrep); 

        $entreprises = $em->getRepository(Entreprise::class)->findAll();

        $tabOE = $entrepFiltree->getOffreEmplois();
        if (count($tabOE) == 0)
        {
            $this->addFlash('notice', 'Aucune offres d\'emploi affichée par ' . $entrepFiltree->getNom());
        }
        else
        {
            $this->addFlash('success', ' ' . count($tabOE) . ' offres d\'emplois affichées par ' . $entrepFiltree->getNom());
        }
        return $this->render('accueil.html.twig', 
        [
            'tabOE' => $tabOE,
            'chomeurs' =>$chomeurs,
            'tabEntrep' => $entreprises,
            'entrepFiltree' => $entrepFiltree
        ]);
    }

   /* #[Route('/creerChomeurHC', name:'rte_creer_chomeurHC')]
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

    #[Route('/creerOffreEmploiHC', name:'rte_creer_offre_emploiHC')]
    public function creerOffreEmploiHC(ManagerRegistry $doctrine): Response
    {
        $entrep1 = new Entreprise;
        $entrep1->setNom("St-Hubert BBQ");
        $entrep1->setContact("Félix");

        $oeA = new OffreEmploi;
        $oeA->setTitre("Adjoint admin");
        $oeA->setDescription("Apporter le café");
        $oeA->setSalaireAnnuel("40000");
        $oeA->setDatePublication(new \DateTime);

        $oeB = new OffreEmploi;
        $oeB->setTitre("Opérateur info");
        $oeB->setDescription("Dire oui à Alain M.");
        $oeB->setSalaireAnnuel("35000");
        $oeB->setDatePublication(new \DateTime);

        //$oeA->setEntreprise($entrep1);
        //$oeB->setEntreprise($entrep1);
         $entrep1->addOffreEmploi($oeA);
         $entrep1->addOffreEmploi($oeB);

        $em = $doctrine->getManager();
        


        $em->persist($entrep1);
        //$em->persist($oeA);
        //$em->persist($oeB);

        $em->flush();

        return $this->RedirectToRoute('accueil');
    }*/

    #[Route('/voirSession', name:'voirSession')]
    public function voirSession(Request $req): Response
    {
        $chomeur = $req->getSession()->get('chomeurConnecte');
        $entreprise = $req->getSession()->get('entrepriseConnectee');

        $info = "";
        if ($chomeur)
          $info = "Chomeur:" . $chomeur->getNom() . "(" . $chomeur->getId() . ")";
        if ($entreprise)
          $info .= "Entreprise:" . $entreprise->getNom() . "(" . $entreprise->getId() . ")";
        dd($info);
    }

    #[Route('/viderSession', name:'viderSession')]
    public function viderSession(Request $req): Response
    {
        $req->getSession()->clear();

        return $this->redirectToRoute('racine');
    }



}
