<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{

    public function __construct(EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHash){
        $this->manager = $manager;
        $this->passwordHash = $passwordHash;
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

            // Hashage du mot de passe
            $user->setPassword($this->passwordHash->hashPassword($user, $user->getPassword()));
            // On persiste l'utilisateur => prépare l'envoi des données
            $this->manager->persist($user);
            // On flush => Envoyer les données
            $this->manager->flush();

            // $passwordEncod = $this->passwordHash->hashPassword($user, $user->getPassword());

        }

        return $this->render('register/index.html.twig', [
            // On passe le formulaire à la vue avec createView()
            'myForm' => $form->createView()
        ]);
    }
}
