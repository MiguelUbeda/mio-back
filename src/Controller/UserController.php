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
        //Return all the users.
        $users = $this->userRepository->findAll();
        $token = $this->tokenStorage->getToken();
        $currentUser = $token->getUser();
        $filteredUsers = [];
        
        // Filter current user in the users list
        foreach ($users as $user) {
            if ($user->getId() !== $currentUser->getId()) {
                $filteredUsers[] = [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'biography' => $user->getBiography(),
                    'username' => $user->getUsername()
                ];
            }
        }
        

        return $this->json(['users' => $filteredUsers]);
    }

     /**
     * @Route("/user/config", name="user-config", methods={"GET"})
     */
    public function usersConfig(): Response
    {
        //Get current user by accessing token.
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();
        return $this->json(['user' => $user]);
    }

    /**
     * @Route("/user/config", name="user-config-update", methods={"POST"})
     */
    public function updateUsersConfig(Request $request): Response
    {
        //Get current user by accessing token.
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();
        // Update the user in database with the new data from the form 
        $data = json_decode($request->getContent(), true);

        $user->setBiography($data['biography']);

        $this->entityManager->flush();

        return $this->json($user);
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
        $user->setBiography($data['biography']);

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