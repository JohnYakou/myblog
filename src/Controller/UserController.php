<?php

namespace App\Controller;

use App\Entity\User;

use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    public function __construct(EntityManagerInterface $managerUser){
        $this->managerUser = $managerUser;
    }
    
    /**
     * @Route("/user", name="app_user")
     */
    public function index(): Response
    {

        // $user = new User();
        // $form = $this->createForm(UserType::class, $user);
        // $form->handleRequest($request);
        // if($form->isSubmitted() && $form->isValid()){
        //     $this->manager->persist($user);
        //     $this->manager->flush();
        // }

        $users = $this->managerUser->getRepository(User::class)->findAll();
        
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    // POUR MODIFIER UN USER
    /**
     * @Route("/user/edit/{id}", name="app_user_edit")
     */
    public function userEdit(User $user,Request $request): Response
    {
          $form = $this->createForm(RegisterType::class,$user); // Création du formulaire
           $form->handleRequest($request); // Traitement du formulaire
           if($form->isSubmitted() && $form->isValid()){ 

            // Recup le champ password
            $emptyPassword = $form->get('password')->getData();

            if($emptyPassword == null){
                // recup le password de l'user en bdd et le renvoyer
                $user->setPassword($user->getPassword());
            }

            // Quand le form est soumis, verifier le champ password
            // Si il vide alors ont recup le mot de passe de l'user a modifier et on le renvoi
            
               $this->managerUser->persist($user);
               $this->managerUser->flush();
               return $this->redirectToRoute('app_user');
           };
            
           return $this->render('user/editUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // POUR SUPPRIMER UN USER
    /**
     * @Route("/user/delete/{id}", name="app_user_delete")
     */

    public function deleteUser(User $user): Response
    {
        $this->managerUser->remove($user); // Effacer l'utilisateur
        $this->managerUser->flush();

        return $this->redirectToRoute('app_user');
    }
}