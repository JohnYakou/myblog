<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ArticleController extends AbstractController
{

    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;
    }
    
    /**
     *@Route("/article", name="app_article")
     */
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // Pour l'image | On écrit 'photo", car c'est le nom dans ArticleType.php
            $photoArticle = $form->get('photo')->getData();
       
            if($photoArticle){
           $originalFilename = pathinfo($photoArticle->getClientOriginalName(),PATHINFO_FILENAME);
           $safeFilename = $slugger->slug($originalFilename);
           // Pour concaténé le fichier | uniqid permet d'avoir un id, un nom unique
           $newFilename = $safeFilename.'-'.uniqid().'.'.$photoArticle->guessExtension();
             try {
                $photoArticle->move(
                    // parameter entre dans parameter dans config/service.yaml
                    $this->getParameter('photo'),
                    $newFilename
                );
             }catch (FileException $e){

             }
             // Pour envoyer en BDD
              $article->setPhoto($newFilename);
            }else{
                dd('aucune photo disponible');
            }
            

        // Pour récupérer dynamiquement le nom de l'auteur
        $article->setAuteur($this->getUser()->getNomComplet());

            // Pour envoyer automatiquement la date d'aujourd'hui
            $article->setPublication(new \datetime);
            $this->manager->persist($article);
            $this->manager->flush();
        }
        return $this->render('article/index.html.twig', [
            'articleForm' => $form->createView()
        ]);
    }

    // ---------- DELETE ----------

    /**
     *@Route("admin/article/delete/{id}", name="app_article_delete")
     */
    public function articleDelete(Article $article): Response
    {

        $this->manager->remove($article);
        $this->manager->flush();

        // UNE REDIRECTION
        return $this->redirectToRoute('app_home');
    }

    // ---------- EDIT ----------

    /**
     *@Route("admin/article/edit/{id}", name="app_article_edit")
     */
    public function articleEdit(Article $article, Request $request, SluggerInterface $slugger): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
                        // Pour l'image | On écrit 'photo", car c'est le nom dans ArticleType.php
                        $photoArticle = $form->get('photo')->getData();
       
                        if($photoArticle){
                       $originalFilename = pathinfo($photoArticle->getClientOriginalName(),PATHINFO_FILENAME);
                       $safeFilename = $slugger->slug($originalFilename);
                       // Pour concaténé le fichier | uniqid permet d'avoir un id, un nom unique
                       $newFilename = $safeFilename.'-'.uniqid().'.'.$photoArticle->guessExtension();
                         try {
                            $photoArticle->move(
                                // parameter entre dans parameter dans config/service.yaml
                                $this->getParameter('photo'),
                                $newFilename
                            );
                         }catch (FileException $e){
            
                         }
                         // Pour envoyer en BDD
                          $article->setPhoto($newFilename);
                        }else{
                            dd('aucune photo disponible');
                        };

            $this->manager->persist($article);
            $this->manager->flush();
            return $this->redirectToRoute('app_home');
        };

        return $this->render("article/editArticle.html.twig", [
            "formArticle" => $form->createView(),
        ]);
    }

    // ----------------------------------

    /**
     * @Route("/all/article", name="app_all_article")
     */
    public function allArticle(): Response
    {
        // Logique stocker dans une variable avec tout les articles // Afficher tous les articles de la BDD
        $articles = $this->manager->getRepository(Article::class)->findAll();

        // Equivalent d'un var_dump
        // dd($articles);

        // On ne peut qu'avoir qu'un seul return par public function
        return $this->render('article/allArticle.html.twig', [
            'articles' => $articles,
        ]);
    }

    // ----------------------------------

    /**
     * @Route("/single/article/{id}", name="app_view_article")
     */
    public function singleArticle(Article $article, Request $request): Response
    {
        // METTRE LE FORM DE COMMENTAIRE
        // Lors de la soumission envoyer en base de donné le com
        $commentaire = new Commentaire();

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $commentaire->setDate(new \DateTime());
            $commentaire->setAuteur($this->getUser());
            $commentaire->setArticle($article);

            $this->manager->persist($commentaire);
            $this->manager->flush();

            return $this->redirectToRoute('app_view_article',[
                'id' => $article->getId(),
            ]);
        }

        return $this->render('article/singleArticle.html.twig', [
            'articles' => $article,
            'form' => $form->createView()
        ]);
    }
}