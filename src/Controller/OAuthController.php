<?php

// src/Controller/OAuthController.php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;

class OAuthController extends AbstractController
{

    /**
     * @Route("/connect/google", name="connect_google_start")
     */
    public function connectAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        // Redirige al usuario a Google
        return $clientRegistry
            ->getClient('google') // Clave del cliente como se definió en config/packages/knpu_oauth2_client.yaml
            ->redirect([
                'email', 'profile' // Los scopes que desees solicitar
            ]);
    }

    /**
     * @Route("/connect/google/check", name="connect_google_check")
     */
    public function connectGoogleCheck(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, JWTTokenManagerInterface $JWTManager)
    {
        // Assuming you have configured KnpUOAuth2ClientBundle or another OAuth client
        $client = $clientRegistry->getClient('google');
        $googleUser = $client->fetchUser();

        // Find or create your user entity
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $googleUser->getEmail()]);
        if (!$user) {
            $user = new User();
            // Set user properties
            $user->setEmail($googleUser->getEmail());
            $user->setUsername($googleUser->getName());
            $entityManager->persist($user);
            $entityManager->flush();
        }

        // Generate JWT for the user
        $token = $JWTManager->create($user);

        $frontendUrl = 'http://localhost:4200/auth/callback';
        return $this->redirect($frontendUrl . '?token=' . $token);
    }
}
