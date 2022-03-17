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
            $this->manager->persist($article);
            $this->manager->flush();
        }
        return $this->render('article/index.html.twig', [
            'articleForm' => $form->createView()
        ]);
    }

    /**
     *@Route("/article/delete/{id}", name="app_article_delete")
     */
    public function articleDelete(Article $article): Response
    {
        $this->manager->remove($article);
        $this->manager->flush();

        // UNE REDIRECTION
        return $this->redirectToRoute('app_home');
    }
}


