<?php

namespace App\Controller;

use ApiPlatform\Core\Api\IriConverterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/api/login", name="api_login", methods={"POST"})
     */
    public function login(IriConverterInterface $iriConverter)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json([
                'error' => 'Invalid login request: check that the Content-Type header is "application/json".',
            ], 400);
        }

        return $this->json([
            'user' => 'moflo',
        ]);
    }

    /**
     * @Route("logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        throw new \Exception('Should not be reached. The route should be intercepted by the firewall.');
    }
}
