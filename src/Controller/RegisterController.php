<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{

    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function index(Request $request): Response
    {
        // Nouvelle instance de user
        $user = new User();
        // Création du formulaire
        $form = $this->createForm(RegisterType::class, $user);
        // Traitement du formulaire
        $form->handleRequest($request);
        // Si le formulaire est soumis et validé alors...
        if($form->isSubmitted() && $form->isValid()){
            // dd($form->getData());

            // On persiste l'utilisateur
            $this->manager->persist($user);
            // On flush
            $this->manager->flush();
        }

        return $this->render('register/index.html.twig', [
            // On passe le formulaire à la vue avec createView()
            'myForm' => $form->createView()
        ]);
    }
}
