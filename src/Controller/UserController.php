<?php

namespace App\Controller;

use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
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
}
