<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @Route("/api", name="api_")
 */
class UserController extends AbstractController
{
    private $Repository;
    private $entityManager;
    private $tokenStorage;
    //This will also get the cacheDir by env symfony parameters
    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, 
        #[Autowire('%kernel.cache_dir%')]
        private $cacheDir)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/users", name="users", methods={"GET"})
     */
    public function index(): Response
    {
        //Get current user by accessing token and return all the users.
        // $token = $this->tokenStorage->getToken();
        // $user = $token->getUser();
        //TO DO: QUE SE DEVUELVAN TODOS LOS USERS;
        $users = $this->userRepository->findAll();

        return $this->json(['users' => $users]);
    }

    /**
     * @Route("/users/{id}", name="update_user", methods={"PUT"})
     */
    public function update(Request $request, User $user): Response
    {   
        // Update the user in database with the new data from the form 
        $data = json_decode($request->getContent(), true);

        $user->setEmail($data['email']);
        $user->setUsername($data['username']);

        $this->entityManager->flush();

        return $this->json($user);
    }

    /**
     * @Route("/users/{id}", name="delete_user", methods={"DELETE"})
     */
    public function delete(User $user): Response
    {   
        // Delete the selected user from database
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}