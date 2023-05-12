<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email; // cette ligne devient inutile
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AjoutType;
use App\Entity\Livre;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Ajouter;




class BaseController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(EntityManagerInterface $entityManagerInterface ): Response
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findAll();
        return $this->render('base/index.html.twig', [
            'livre'=>$livre          
        ]);
    }

    #[Route('/contact', name: 'contact')] // étape 1
    public function contact(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManagerInterface): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $email = (new TemplatedEmail())
                ->from($contact->getEmail())
                ->to('cheztristan@gmail.com')
                ->subject($contact->getSujet())
                ->htmlTemplate('emails/email.html.twig')
                ->context([
                    'nom'=> $contact->getNom(),
                    'sujet'=> $contact->getSujet(),
                    'message'=> $contact->getMessage()
                ]);
              
                $entityManagerInterface->persist($contact);
                $entityManagerInterface->flush();

                $mailer->send($email);
                $this->addFlash('notice','Message envoyé');
                return $this->redirectToRoute('contact');
               
            }
        }

        return $this->render('base/contact.html.twig', [ // étape 3
            'form' => $form->createView()
            
        ]);
    }

    #[Route('/private-ajout', name: 'ajout')]
    public function ajout(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManagerInterface): Response
    {
        $livre= new Livre();

        $form = $this->createForm(AjoutType::class,$livre);
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $fichier = $form->get('Couverture')->getData();
                
                if($fichier){
                 $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                 $nomFichier = $slugger->slug($nomFichier);
                 $nomFichier = $nomFichier.'-'.uniqid().'.'.$fichier->guessExtension();
                    try{                 
                        $fichier->move($this->getParameter('file_directory'), $nomFichier);
                        $this->addFlash('notice', 'Fichier envoyé');
                    }
                    catch(FileException $e){
                        $this->addFlash('notice', 'Erreur d\'envoi');
                    }        
                }
                $livre->setCouverture($nomFichier);
                $entityManagerInterface->persist($livre);
                $entityManagerInterface->flush();

                return $this->redirectToRoute('ajout');
            }
        }

        return $this->render('base/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    } 

    #[Route('/favorie/{id}', name: 'favorie')] // étape 1
    public function favorie(Request $request, EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $id=$request -> get('id');
        $action = $request -> get('action');
        $livre=$entityManagerInterface->getRepository(Livre::class)->find($id);

        if($action == 'ajouter'){
            $this->getUser()->addFavory($livre);
        }
        if($action == 'supprimer'){
            $this->getUser()->removeFavory($livre);
        }

        $entityManagerInterface->persist($this->getUser());
        $entityManagerInterface->flush();

        return $this->render('base/mes-favorie.html.twig', [ // étape 3
            
        ]);
    } 

    #[Route('/mes-favorie', name: 'mes-favorie')] // étape 1
    public function mesFavorie(): Response // étape 2
    {
        $user = $this->getuser();
        $favorie = $user->getFavories();

        return $this->render('base/mes-favorie.html.twig', [ // étape 3
            'favorie' => $favorie,
            
        ]);
    }

    #[Route ('/detail/{id}', name: 'detail')]
    public function detail(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $id=$request -> get('id');
        $livre = $entityManagerInterface ->getRepository(Livre::class)->find($id);

        if (!$livre) {
            throw $this->createNotFoundException(
                'Aucun article trouvé pour l\'id '.$id
            );
        }

        return $this->render('base/detail.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/monPanier', name: 'monPanier')] // étape 1
    public function monPanier(Request $request, EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findAll();

        return $this->render('base/monPanier.html.twig', [ // étape 3
            'livres' => $livre,
            
        ]);
    }

    #[Route('/ajoutPanier/{id}', name:'ajoutPanier')]
    public function ajoutPanier(Request $request, EntityManagerInterface $entityManagerInterface): Response 
       
    {
        $id = $request->get('id');
        if ($this->getUser()->getPanier()==null) {
            $panier = new Panier();
            $this->getUser()->setPanier($panier);
        }
        $ajouter = new Ajouter();
        $ajouter = setQte(1);
        $livre= $entityManagerInterface->getRepository(Livre::class)->find($id);
        if ($livre != null) {
            $ajouter -> setLivre($livre);
            $ajouter -> setLivre($this->getuser()->getPanier());
            $entityManagerInterface->persist($ajouter);
            $entityManagerInterface->flush();
        }

        return $this->redirectToRoute('monPanier');
            
    }


    #[Route('/faq', name: 'faq')] // étape 1
    public function faq(): Response // étape 2
    {
        return $this->render('base/faq.html.twig', [ // étape 3
            
        ]);
    }

    #[Route('/mentionslegales', name: 'mentionslegales')] // étape 1
    public function mentionslegales(): Response // étape 2
    {
        return $this->render('base/mentionslegales.html.twig', [ // étape 3
            
        ]);
    }

    #[Route('/cgu', name: 'cgu')] // étape 1
    public function cgu(): Response // étape 2
    {
        return $this->render('base/cgu.html.twig', [ // étape 3
            
        ]);
    }

    #[Route('/charte', name: 'charte')] // étape 1
    public function charte(): Response // étape 2
    {
        return $this->render('base/charte.html.twig', [ // étape 3
            
        ]);
    }


    #[Route('/Rdystopie', name: 'Rdystopie')] // étape 1
    public function Rdystopie(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'dystopie', 'type' => 'romans']);
        return $this->render('genres/Rdystopie.html.twig', [ // étape 3
            'livre'=>$livre          
        ]);
    
    }

    #[Route('/Raventure', name: 'Raventure')] // étape 1
    public function Raventure(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'aventure', 'type' => 'romans']);
        return $this->render('genres/Raventure.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/Rpostapo', name: 'Rpostapo')] // étape 1
    public function Rpostapo(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'postapo', 'type' => 'romans']);
        return $this->render('genres/Rpostapo.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/Rfantasy', name: 'Rfantasy')] // étape 1
    public function Rfantasy(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'fantasy', 'type' => 'romans']);
        return $this->render('genres/Rfantasy.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/LCtheatre', name: 'LCtheatre')] // étape 1
    public function LCtheatre(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'theatre', 'type' => 'classique']);
        return $this->render('genres/LCtheatre.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/LCaventure', name: 'LCaventure')] // étape 1
    public function LCaventure(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'aventure', 'type' => 'classique']);
        return $this->render('genres/LCaventure.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/LCSF', name: 'LCSF')] // étape 1
    public function LCSF(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'Science Fiction', 'type' => 'classique']);
        return $this->render('genres/LCSF.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/BDaventure', name: 'BDaventure')] // étape 1
    public function BDaventure(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'aventure', 'type' => 'BD']);
        return $this->render('genres/BDaventure.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/BDhumoristique', name: 'BDhumoristique')] // étape 1
    public function BDhumoristique(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'humoristique', 'type' => 'BD']);
        return $this->render('genres/BDhumoristique.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/BDhistorique', name: 'BDhistorique')] // étape 1
    public function BDhistorique(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'historique', 'type' => 'BD']);
        return $this->render('genres/BDhistorique.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/BDSF', name: 'BDSF')] // étape 1
    public function BDSF(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'SF', 'type' => 'BD']);
        return $this->render('genres/BDSF.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/BDwestern', name: 'BDwestern')] // étape 1
    public function BDwestern(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'western', 'type' => 'BD']);
        return $this->render('genres/BDwestern.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/BDfantasy', name: 'BDfantasy')] // étape 1
    public function BDfantasy(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'fantasy', 'type' => 'BD']);
        return $this->render('genres/BDfantasy.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/Mshonen', name: 'Mshonen')] // étape 1
    public function Mshonen(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'shonen', 'type' => 'manga']);
        return $this->render('genres/Mshonen.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/Mshojo', name: 'Mshojo')] // étape 1
    public function Mshojo(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'shojo', 'type' => 'manga']);
        return $this->render('genres/Mshojo.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/Mseinen', name: 'Mseinen')] // étape 1
    public function Mseinen(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'seinen', 'type' => 'manga']);
        return $this->render('genres/Mseinen.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/Mjosei', name: 'Mjosei')] // étape 1
    public function Mjosei(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'josei', 'type' => 'manga']);
        return $this->render('genres/Mjosei.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/Phaiku', name: 'Phaiku')] // étape 1
    public function Phaiku(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'haiku', 'type' => 'poesie']);
        return $this->render('genres/Phaiku.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/Psurrealiste', name: 'Psurrealiste')] // étape 1
    public function Psurrealiste(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'surréaliste', 'type' => 'poesie']);
        return $this->render('genres/Psurrealiste.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/Pengage', name: 'Pengage')] // étape 1
    public function Pengage(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'haiku', 'type' => 'poesie']);
        return $this->render('genres/Pengage.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/MLreligion', name: 'MLreligion')] // étape 1
    public function MLreligion(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'religion', 'type' => 'mythes']);
        return $this->render('genres/MLreligion.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/MLgrecque', name: 'MLgrecque')] // étape 1
    public function MLgrecque(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'grecque', 'type' => 'mythes']);
        return $this->render('genres/MLgrecque.html.twig', [ // étape 3
            'livre'=>$livre 

        ]);
    }

    #[Route('/MLnordique', name: 'MLnordique')] // étape 1
    public function MLnordique(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'nordique', 'type' => 'mythes']);
        return $this->render('genres/MLnordique.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }

    #[Route('/MLegyptienne', name: 'MLegyptienne')] // étape 1
    public function MLegyptienne(EntityManagerInterface $entityManagerInterface): Response // étape 2
    {
        $repoLivre = $entityManagerInterface->getRepository(Livre::class);
        $livre = $repoLivre->findBy(['genre' => 'egyptienne', 'type' => 'mythes']);
        return $this->render('genres/MLegyptienne.html.twig', [ // étape 3
            'livre'=>$livre 
            
        ]);
    }
} 

