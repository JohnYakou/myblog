<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;
    }
    // Enlever le "home" après la /
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        // Logique stocker dans une variable avec tout les articles // Afficher tous les articles de la BDD
        $articles = $this->manager->getRepository(Article::class)->findAll();

        // Equivalent d'un var_dump
        // dd($articles);

        $users = $this->manager->getRepository(User::class)->findAll();

        // On ne peut qu'avoir qu'un seul return
        return $this->render('home/index.html.twig', [
            'articles' => $articles,
            'users' => $users,
        ]);
    
    }
}
