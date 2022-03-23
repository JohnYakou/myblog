<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{

    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;
    }
    
    /**
     *@Route("/article", name="app_article")
     */
    public function index(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

        // Pour récupérer dynamiquement le nom de l'auteur
        $article->setAuteur($this->getUser()->getNomComplet());

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
    public function articleEdit(Article $article, Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->manager->persist($article);
            $this->manager->flush();
            return $this->redirectToRoute('app_home');
        };

        return $this->render("article/editArticle.html.twig", [
            "formArticle" => $form->createView(),
        ]);
    }

    /**
     * @Route("/all/article", name="app_all_article")
     */
    public function allArticle(): Response
    {
        // Logique stocker dans une variable avec tout les articles // Afficher tous les articles de la BDD
        $articles = $this->manager->getRepository(Article::class)->findAll();

        // Equivalent d'un var_dump
        // dd($articles);

        // On ne peut qu'avoir qu'un seul return
        return $this->render('article/allArticle.html.twig', [
            'articles' => $articles,
        ]);
    
    }
}


